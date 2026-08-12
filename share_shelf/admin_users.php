<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_admin();

$page_title = "Manage Users";

if (isset($_GET['ban'])) {
    $id = (int)$_GET['ban'];
    mysqli_query($conn, "UPDATE user SET Is_Banned=1 WHERE User_ID=$id");
    // Per spec: a banned user's listings are removed too
    mysqli_query($conn, "DELETE FROM item WHERE User_ID=$id");
    $_SESSION['flash'] = ['type' => 'warning', 'msg' => 'User banned and their listings removed.'];
    header("Location: admin_users.php");
    exit;
}
if (isset($_GET['unban'])) {
    $id = (int)$_GET['unban'];
    mysqli_query($conn, "UPDATE user SET Is_Banned=0 WHERE User_ID=$id");
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'User unbanned.'];
    header("Location: admin_users.php");
    exit;
}

// Detail view for one user
$viewId = (int)($_GET['view'] ?? 0);
$viewUser = null;
if ($viewId) {
    $viewUser = mysqli_query($conn, "SELECT * FROM user WHERE User_ID=$viewId")->fetch_assoc();
    $viewPurchases = mysqli_query($conn, "SELECT * FROM purchase WHERE Buyer_ID=$viewId ORDER BY Purchase_ID DESC");
    $viewPayments = mysqli_query($conn, "
        SELECT pay.* FROM payment pay JOIN purchase p ON pay.Purchase_ID=p.Purchase_ID WHERE p.Buyer_ID=$viewId ORDER BY pay.Payment_ID DESC");
    $viewClaims = mysqli_query($conn, "SELECT cl.*, i.Title FROM claim cl LEFT JOIN item i ON cl.Item_ID=i.Item_ID WHERE cl.User_ID=$viewId ORDER BY cl.Claim_ID DESC");
    $viewListings = mysqli_query($conn, "SELECT * FROM item WHERE User_ID=$viewId ORDER BY Item_ID DESC");
    $viewReports = mysqli_query($conn, "SELECT * FROM report WHERE User_ID=$viewId ORDER BY Report_ID DESC");
    $viewRating = mysqli_query($conn, "SELECT AVG(Score) avg_score, COUNT(*) cnt FROM rating WHERE To_User_ID=$viewId")->fetch_assoc();
    // Reports filed against this user's listings (i.e. complaints they've received as a seller)
    $viewReportsReceived = mysqli_query($conn, "
        SELECT r.*, i.Title FROM report r JOIN item i ON r.Item_ID = i.Item_ID WHERE i.User_ID=$viewId ORDER BY r.Report_ID DESC");
}

$users = mysqli_query($conn, "
    SELECT u.*, (a.Admin_ID IS NOT NULL) AS is_admin
    FROM user u LEFT JOIN admin a ON a.Admin_ID = u.User_ID
    ORDER BY u.User_ID
");

include "includes/header.php";
?>

<a href="admin.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Dashboard</a>

<?php if ($viewUser): ?>
  <h3>Profile: <?= htmlspecialchars($viewUser['First_Name'] . ' ' . $viewUser['Last_Name']) ?></h3>
  <p class="text-muted">
    <?= htmlspecialchars($viewUser['Email'] ?? 'no email') ?> · <?= htmlspecialchars($viewUser['Phone']) ?> ·
    <?= htmlspecialchars($viewUser['District'] . ', ' . $viewUser['Area']) ?>
    <?php if ($viewUser['Is_Banned']): ?><span class="badge bg-danger ms-2">Banned</span><?php endif; ?>
  </p>
  <div class="d-flex gap-4 mb-3">
    <div>
      <div class="text-muted small">Seller Rating</div>
      <div class="fw-bold">
        <?= $viewRating['cnt'] > 0 ? '⭐ ' . number_format($viewRating['avg_score'], 1) . ' (' . $viewRating['cnt'] . ' reviews)' : 'No ratings yet' ?>
      </div>
    </div>
    <div>
      <div class="text-muted small">Reports Received (as seller)</div>
      <div class="fw-bold <?= mysqli_num_rows($viewReportsReceived) > 0 ? 'text-danger' : '' ?>">
        <?= mysqli_num_rows($viewReportsReceived) ?>
      </div>
    </div>
  </div>
  <?php if (mysqli_num_rows($viewReportsReceived) > 0): ?>
    <div class="alert alert-warning small">
      <strong>Reports against this seller's listings:</strong>
      <ul class="mb-0">
        <?php mysqli_data_seek($viewReportsReceived, 0); while ($rr = mysqli_fetch_assoc($viewReportsReceived)): ?>
          <li><?= htmlspecialchars($rr['Title']) ?> — <?= htmlspecialchars($rr['Reason']) ?> (<?= htmlspecialchars($rr['Status']) ?>)</li>
        <?php endwhile; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-md-6">
      <h6>Purchases</h6>
      <ul class="list-group mb-3">
        <?php while ($p = mysqli_fetch_assoc($viewPurchases)): ?>
          <li class="list-group-item d-flex justify-content-between">
            #<?= $p['Purchase_ID'] ?> — ৳<?= number_format($p['Total_Amount'],2) ?> <span class="badge bg-secondary"><?= $p['Status'] ?></span>
          </li>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($viewPurchases) === 0): ?><li class="list-group-item text-muted">None</li><?php endif; ?>
      </ul>

      <h6>Payments</h6>
      <ul class="list-group mb-3">
        <?php while ($p = mysqli_fetch_assoc($viewPayments)): ?>
          <li class="list-group-item d-flex justify-content-between">
            ৳<?= number_format($p['Amount'],2) ?> via <?= htmlspecialchars($p['Payment_Method']) ?> <span class="badge bg-secondary"><?= $p['Payment_Status'] ?></span>
          </li>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($viewPayments) === 0): ?><li class="list-group-item text-muted">None</li><?php endif; ?>
      </ul>

      <h6>Claims</h6>
      <ul class="list-group mb-3">
        <?php while ($c = mysqli_fetch_assoc($viewClaims)): ?>
          <li class="list-group-item"><?= htmlspecialchars($c['Title'] ?? '(removed)') ?> — <?= $c['Status'] ?></li>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($viewClaims) === 0): ?><li class="list-group-item text-muted">None</li><?php endif; ?>
      </ul>
    </div>

    <div class="col-md-6">
      <h6>Listings</h6>
      <ul class="list-group mb-3">
        <?php while ($i = mysqli_fetch_assoc($viewListings)): ?>
          <li class="list-group-item d-flex justify-content-between">
            <?= htmlspecialchars($i['Title']) ?> <span class="badge bg-secondary"><?= $i['Approval_Status'] ?></span>
          </li>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($viewListings) === 0): ?><li class="list-group-item text-muted">None</li><?php endif; ?>
      </ul>

      <h6>Reports Filed</h6>
      <ul class="list-group mb-3">
        <?php while ($r = mysqli_fetch_assoc($viewReports)): ?>
          <li class="list-group-item"><?= htmlspecialchars($r['Reason']) ?> — <?= $r['Status'] ?></li>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($viewReports) === 0): ?><li class="list-group-item text-muted">None</li><?php endif; ?>
      </ul>
    </div>
  </div>
  <hr>
<?php endif; ?>

<h3 class="mb-3">All Users</h3>
<div class="table-responsive">
<table class="table bg-white shadow-sm align-middle">
  <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Location</th><th>Role</th><th>Status</th><th></th></tr></thead>
  <tbody>
    <?php while ($u = mysqli_fetch_assoc($users)): ?>
      <tr>
        <td><?= $u['User_ID'] ?></td>
        <td><?= htmlspecialchars($u['First_Name'] . ' ' . $u['Last_Name']) ?></td>
        <td><?= htmlspecialchars($u['Phone']) ?></td>
        <td><?= htmlspecialchars($u['District'] . ', ' . $u['Area']) ?></td>
        <td><?= $u['is_admin'] ? '<span class="badge bg-dark">Admin</span>' : 'User' ?></td>
        <td><?= $u['Is_Banned'] ? '<span class="badge bg-danger">Banned</span>' : '<span class="badge bg-success">Active</span>' ?></td>
        <td>
          <a href="admin_users.php?view=<?= $u['User_ID'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
          <?php if (!$u['is_admin']): ?>
            <?php if ($u['Is_Banned']): ?>
              <a href="admin_users.php?unban=<?= $u['User_ID'] ?>" class="btn btn-sm btn-outline-success">Unban</a>
            <?php else: ?>
              <a href="admin_users.php?ban=<?= $u['User_ID'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Ban this user? Their listings will be removed.')">Ban</a>
            <?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>

<?php include "includes/footer.php"; ?>
