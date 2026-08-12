<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_admin();

$page_title = "Manage Listings";
$admin_id = $_SESSION['admin_id'];

if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $stmt = mysqli_prepare($conn, "UPDATE item SET Approval_Status='Approved', Status='Available', Admin_ID=? WHERE Item_ID=?");
    mysqli_stmt_bind_param($stmt, "ii", $admin_id, $id);
    mysqli_stmt_execute($stmt);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Listing approved.'];
    header("Location: admin_listings.php");
    exit;
}
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    // Keep the row so the seller can see it was rejected, rather than
    // silently vanishing from their "My Listings" page.
    $stmt = mysqli_prepare($conn, "UPDATE item SET Approval_Status='Rejected', Status='Unavailable', Admin_ID=? WHERE Item_ID=?");
    mysqli_stmt_bind_param($stmt, "ii", $admin_id, $id);
    mysqli_stmt_execute($stmt);
    $_SESSION['flash'] = ['type' => 'warning', 'msg' => 'Listing rejected.'];
    header("Location: admin_listings.php");
    exit;
}

$pending = mysqli_query($conn, "
    SELECT i.*, c.Category_Name, u.First_Name, u.Last_Name
    FROM item i JOIN category c ON i.Category_ID=c.Category_ID JOIN user u ON i.User_ID=u.User_ID
    WHERE i.Approval_Status='Pending' ORDER BY i.Item_ID
");
$live = mysqli_query($conn, "
    SELECT i.*, c.Category_Name, u.First_Name, u.Last_Name
    FROM item i JOIN category c ON i.Category_ID=c.Category_ID JOIN user u ON i.User_ID=u.User_ID
    WHERE i.Approval_Status='Approved' ORDER BY i.Item_ID DESC LIMIT 30
");

include "includes/header.php";
?>

<a href="admin.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Dashboard</a>
<h3 class="mb-3">Pending Approval</h3>

<div class="mb-5">
  <?php if (mysqli_num_rows($pending) === 0): ?>
    <p class="text-center text-muted py-4">No pending listings 🎉</p>
  <?php endif; ?>
  <?php while ($i = mysqli_fetch_assoc($pending)): ?>
    <div class="card shadow-sm mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap">
          <div>
            <h5 class="mb-1"><?= htmlspecialchars($i['Title']) ?></h5>
            <div class="text-muted small mb-2">
              <?= htmlspecialchars($i['Category_Name']) ?> · Seller: <?= htmlspecialchars($i['First_Name'] . ' ' . $i['Last_Name']) ?>
            </div>
          </div>
          <div class="text-end">
            <?= $i['Item_Type']==='Donation' ? '<span class="badge badge-free">Free</span>' : '<span class="fw-bold">৳'.number_format($i['Price'],2).'</span>' ?>
          </div>
        </div>
        <dl class="row small mb-2">
          <dt class="col-sm-2">Condition</dt><dd class="col-sm-10"><?= htmlspecialchars($i['Condition']) ?></dd>
          <dt class="col-sm-2">Description</dt><dd class="col-sm-10"><?= nl2br(htmlspecialchars($i['Description'] ?: '(none provided)')) ?></dd>
          <dt class="col-sm-2">Quantity</dt><dd class="col-sm-10"><?= (int)$i['Quantity'] ?></dd>
          <dt class="col-sm-2">Pickup</dt><dd class="col-sm-10"><?= htmlspecialchars($i['Pickup_Location']) ?></dd>
        </dl>
        <a href="admin_listings.php?approve=<?= $i['Item_ID'] ?>" class="btn btn-sm btn-success">Approve</a>
        <a href="admin_listings.php?reject=<?= $i['Item_ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this listing?')">Reject</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<h3 class="mb-3">Recently Approved</h3>
<div class="table-responsive">
<table class="table bg-white shadow-sm align-middle">
  <thead><tr><th>Title</th><th>Category</th><th>Seller</th><th>Status</th></tr></thead>
  <tbody>
    <?php while ($i = mysqli_fetch_assoc($live)): ?>
      <tr>
        <td><?= htmlspecialchars($i['Title']) ?></td>
        <td><?= htmlspecialchars($i['Category_Name']) ?></td>
        <td><?= htmlspecialchars($i['First_Name'] . ' ' . $i['Last_Name']) ?></td>
        <td><?= htmlspecialchars($i['Status']) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>

<?php include "includes/footer.php"; ?>
