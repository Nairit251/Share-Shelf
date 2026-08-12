<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_admin();

$page_title = "Reports";
$admin_id = $_SESSION['admin_id'];

if (isset($_GET['resolve'])) {
    $id = (int)$_GET['resolve'];
    $stmt = mysqli_prepare($conn, "UPDATE report SET Status='Resolved', Admin_ID=? WHERE Report_ID=?");
    mysqli_stmt_bind_param($stmt, "ii", $admin_id, $id);
    mysqli_stmt_execute($stmt);
    header("Location: admin_reports.php");
    exit;
}
if (isset($_GET['remove_item'])) {
    // Admin decides the reported listing itself should come down
    $itemId = (int)$_GET['remove_item'];
    mysqli_query($conn, "DELETE FROM item WHERE Item_ID=$itemId");
    $_SESSION['flash'] = ['type' => 'warning', 'msg' => 'Listing removed.'];
    header("Location: admin_reports.php");
    exit;
}

$reports = mysqli_query($conn, "
    SELECT r.*, u.First_Name, u.Last_Name, i.Title AS Item_Title
    FROM report r
    JOIN user u ON r.User_ID = u.User_ID
    LEFT JOIN item i ON r.Item_ID = i.Item_ID
    ORDER BY (r.Status='Pending') DESC, r.Report_ID DESC
");

include "includes/header.php";
?>

<a href="admin.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Dashboard</a>
<h3 class="mb-3">Reports</h3>

<div class="table-responsive">
<table class="table bg-white shadow-sm align-middle">
  <thead><tr><th>Item</th><th>Reported By</th><th>Reason</th><th>Description</th><th>Status</th><th></th></tr></thead>
  <tbody>
    <?php while ($r = mysqli_fetch_assoc($reports)): ?>
      <tr>
        <td><?= htmlspecialchars($r['Item_Title'] ?? '(listing removed)') ?></td>
        <td><?= htmlspecialchars($r['First_Name'] . ' ' . $r['Last_Name']) ?></td>
        <td><?= htmlspecialchars($r['Reason']) ?></td>
        <td class="small"><?= htmlspecialchars($r['Description']) ?></td>
        <td><span class="badge bg-<?= $r['Status']==='Resolved'?'success':'warning' ?>"><?= htmlspecialchars($r['Status']) ?></span></td>
        <td>
          <?php if ($r['Status'] === 'Pending'): ?>
            <a href="admin_reports.php?resolve=<?= $r['Report_ID'] ?>" class="btn btn-sm btn-success">Mark Resolved</a>
            <?php if ($r['Item_ID']): ?>
              <a href="admin_reports.php?remove_item=<?= $r['Item_ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove the reported listing?')">Remove Listing</a>
            <?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>

<?php include "includes/footer.php"; ?>
