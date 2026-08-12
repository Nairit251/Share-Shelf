<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$item_id = (int)($_GET['item_id'] ?? $_POST['item_id'] ?? 0);
$page_title = "Report Item";

$stmt = mysqli_prepare($conn, "SELECT Title FROM item WHERE Item_ID = ?");
mysqli_stmt_bind_param($stmt, "i", $item_id);
mysqli_stmt_execute($stmt);
$item = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$item) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Item not found.'];
    header("Location: browse.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason']);
    $description = trim($_POST['description']);
    $user_id = current_user_id();

    $stmt = mysqli_prepare($conn, "INSERT INTO report (User_ID, Item_ID, Reason, Description, Status) VALUES (?, ?, ?, ?, 'Pending')");
    mysqli_stmt_bind_param($stmt, "iiss", $user_id, $item_id, $reason, $description);
    mysqli_stmt_execute($stmt);

    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Thanks — your report has been submitted to our admin team.'];
    header("Location: item.php?id=$item_id");
    exit;
}

include "includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4>Report "<?= htmlspecialchars($item['Title']) ?>"</h4>
        <form method="post">
          <input type="hidden" name="item_id" value="<?= $item_id ?>">
          <div class="mb-3">
            <label class="form-label">Reason</label>
            <select name="reason" class="form-select" required>
              <option value="Misleading Description">Misleading Description</option>
              <option value="Duplicate Listing">Duplicate Listing</option>
              <option value="Seller Unresponsive">Seller Unresponsive</option>
              <option value="Inappropriate Content">Inappropriate Content</option>
              <option value="Incorrect Category">Incorrect Category</option>
              <option value="Spam Listing">Spam Listing</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Details</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
          </div>
          <button class="btn btn-danger" type="submit">Submit Report</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>
