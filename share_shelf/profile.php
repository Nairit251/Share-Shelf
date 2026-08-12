<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$page_title = "My Profile";
$user_id = current_user_id();
$errors = [];
$success = "";

if (isset($_POST['delete_account'])) {
    // Hard-delete: remove the user row and every dependent row so nothing is left in the DB.
    // mysqli does not throw on FK errors by default — check each step and abort on failure.
    $ok = true;
    $run = function ($sql) use ($conn, &$ok) {
        if (!$ok) return;
        if (!mysqli_query($conn, $sql)) {
            $ok = false;
        }
    };

    mysqli_begin_transaction($conn);

    // Cart
    $cartIds = mysqli_query($conn, "SELECT Cart_ID FROM cart WHERE User_ID = $user_id");
    if ($cartIds) {
        while ($c = mysqli_fetch_assoc($cartIds)) {
            $run("DELETE FROM cart_item WHERE Cart_ID = " . (int)$c['Cart_ID']);
        }
    }
    $run("DELETE FROM cart WHERE User_ID = $user_id");

    // Claims this user made
    $run("DELETE FROM claim WHERE User_ID = $user_id");

    // Items they listed (+ dependents)
    $itemIds = [];
    $itemsRes = mysqli_query($conn, "SELECT Item_ID FROM item WHERE User_ID = $user_id");
    if ($itemsRes) {
        while ($i = mysqli_fetch_assoc($itemsRes)) {
            $itemIds[] = (int)$i['Item_ID'];
        }
    }
    if (!empty($itemIds)) {
        $idList = implode(',', $itemIds);
        $run("DELETE FROM cart_item WHERE Item_ID IN ($idList)");
        $run("DELETE FROM item_image WHERE Item_ID IN ($idList)");
        $run("DELETE FROM report WHERE Item_ID IN ($idList)");
        $run("UPDATE claim SET Item_ID = NULL WHERE Item_ID IN ($idList)");
        $run("UPDATE purchase_item SET Item_ID = NULL WHERE Item_ID IN ($idList)");
        $run("DELETE FROM item WHERE User_ID = $user_id");
    }

    // Purchases they made
    $purchaseIds = [];
    $purchRes = mysqli_query($conn, "SELECT Purchase_ID FROM purchase WHERE Buyer_ID = $user_id");
    if ($purchRes) {
        while ($p = mysqli_fetch_assoc($purchRes)) {
            $purchaseIds[] = (int)$p['Purchase_ID'];
        }
    }
    if (!empty($purchaseIds)) {
        $pList = implode(',', $purchaseIds);
        $run("DELETE FROM payment WHERE Purchase_ID IN ($pList)");
        $run("DELETE FROM purchase_item WHERE Purchase_ID IN ($pList)");
        $run("DELETE FROM purchase WHERE Buyer_ID = $user_id");
    }

    // Ratings, reports filed by them, their support tickets
    $run("DELETE FROM rating WHERE From_User_ID = $user_id OR To_User_ID = $user_id");
    $run("DELETE FROM report WHERE User_ID = $user_id");
    $run("DELETE FROM support_ticket WHERE User_ID = $user_id");

    // Clear admin FK pointers before removing admin role (if any)
    $run("UPDATE item SET Admin_ID = NULL WHERE Admin_ID = $user_id");
    $run("UPDATE report SET Admin_ID = NULL WHERE Admin_ID = $user_id");
    $run("UPDATE support_ticket SET Admin_ID = NULL WHERE Admin_ID = $user_id");
    $run("DELETE FROM admin WHERE Admin_ID = $user_id");

    // Any purchase lines where they were the seller
    $run("UPDATE purchase_item SET Seller_ID = NULL WHERE Seller_ID = $user_id");

    // Remove the user row itself — must succeed for the account to "disappear"
    $run("DELETE FROM user WHERE User_ID = $user_id");

    // Confirm the row is gone
    $stillThere = mysqli_query($conn, "SELECT 1 FROM user WHERE User_ID = $user_id");
    if ($stillThere && mysqli_num_rows($stillThere) > 0) {
        $ok = false;
    }

    if ($ok) {
        mysqli_commit($conn);
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Your account and all associated data have been permanently deleted from the database.'];
        header("Location: login.php");
        exit;
    }

    mysqli_rollback($conn);
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Could not delete your account. Please contact support. Error: ' . mysqli_error($conn)];
    header("Location: profile.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_account'])) {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $district = trim($_POST['district']);
    $area = trim($_POST['area']);
    $street = trim($_POST['street']);

    $stmt = mysqli_prepare($conn, "UPDATE user SET First_Name=?, Last_Name=?, Email=?, District=?, Area=?, Street=? WHERE User_ID=?");
    mysqli_stmt_bind_param($stmt, "ssssssi", $first, $last, $email, $district, $area, $street, $user_id);
    mysqli_stmt_execute($stmt);
    $_SESSION['user_name'] = $first;
    $success = "Profile updated.";
}

$stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE User_ID = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_stmt_get_result($stmt)->fetch_assoc();

$ratingRow = mysqli_query($conn, "SELECT AVG(Score) avg_score, COUNT(*) cnt FROM rating WHERE To_User_ID = $user_id")->fetch_assoc();

$reviewsReceived = mysqli_query($conn, "
    SELECT r.*, u.First_Name, u.Last_Name FROM rating r
    JOIN user u ON r.From_User_ID = u.User_ID
    WHERE r.To_User_ID = $user_id ORDER BY r.Rating_Date DESC
");
$reviewsGiven = mysqli_query($conn, "
    SELECT r.*, u.First_Name, u.Last_Name FROM rating r
    JOIN user u ON r.To_User_ID = u.User_ID
    WHERE r.From_User_ID = $user_id ORDER BY r.Rating_Date DESC
");

include "includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card shadow-sm p-4 mb-4">
      <h3 class="mb-3">My Profile</h3>
      <?php if ($success): ?><div class="alert alert-success py-2"><?= $success ?></div><?php endif; ?>

      <p class="text-muted">
        <?php if ($ratingRow['cnt'] > 0): ?>
          ⭐ <?= number_format($ratingRow['avg_score'], 1) ?> average rating from <?= $ratingRow['cnt'] ?> reviews
        <?php else: ?>
          No ratings received yet
        <?php endif; ?>
      </p>

      <form method="post">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['First_Name']) ?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['Last_Name']) ?>">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['Email']) ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($user['Phone']) ?>" disabled>
          <div class="form-text">Phone number can't be changed. Contact support if it's wrong.</div>
        </div>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">District</label>
            <input type="text" name="district" class="form-control" value="<?= htmlspecialchars($user['District']) ?>" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Area</label>
            <input type="text" name="area" class="form-control" value="<?= htmlspecialchars($user['Area']) ?>" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Street</label>
            <input type="text" name="street" class="form-control" value="<?= htmlspecialchars($user['Street']) ?>" required>
          </div>
        </div>
        <button class="btn btn-success" type="submit">Save Changes</button>
      </form>
    </div>

    <div class="card shadow-sm p-4 mb-4">
      <h5>Reviews I've Received</h5>
      <?php if (mysqli_num_rows($reviewsReceived) === 0): ?>
        <p class="text-muted small mb-0">No reviews yet.</p>
      <?php endif; ?>
      <?php while ($r = mysqli_fetch_assoc($reviewsReceived)): ?>
        <div class="border-bottom py-2">
          <div class="d-flex justify-content-between">
            <strong><?= htmlspecialchars($r['First_Name'] . ' ' . $r['Last_Name']) ?></strong>
            <span><?= str_repeat('⭐', (int)$r['Score']) ?></span>
          </div>
          <div class="small text-muted"><?= htmlspecialchars($r['Comment']) ?></div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="card shadow-sm p-4 mb-4">
      <h5>Reviews I've Given</h5>
      <?php if (mysqli_num_rows($reviewsGiven) === 0): ?>
        <p class="text-muted small mb-0">You haven't rated anyone yet.</p>
      <?php endif; ?>
      <?php while ($r = mysqli_fetch_assoc($reviewsGiven)): ?>
        <div class="border-bottom py-2">
          <div class="d-flex justify-content-between">
            <strong>To: <?= htmlspecialchars($r['First_Name'] . ' ' . $r['Last_Name']) ?></strong>
            <span><?= str_repeat('⭐', (int)$r['Score']) ?></span>
          </div>
          <div class="small text-muted"><?= htmlspecialchars($r['Comment']) ?></div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="card shadow-sm p-4 border-danger">
      <h5 class="text-danger">Danger Zone</h5>
      <p class="small text-muted">Permanently deleting your account removes your profile, listings, claims, purchases, ratings, reports, and support tickets from the database. This cannot be undone.</p>
      <form method="post" onsubmit="return confirm('Are you sure you want to permanently delete your account and all associated data? This cannot be undone.')">
        <button type="submit" name="delete_account" value="1" class="btn btn-outline-danger">Delete My Account Permanently</button>
      </form>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>
