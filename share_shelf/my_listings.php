<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$page_title = "My Listings";
$user_id = current_user_id();

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM item WHERE Item_ID = ? AND User_ID = ?");
    mysqli_stmt_bind_param($stmt, "ii", $del_id, $user_id);
    mysqli_stmt_execute($stmt);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Listing removed.'];
    header("Location: my_listings.php");
    exit;
}

$stmt = mysqli_prepare($conn, "
    SELECT i.*, c.Category_Name FROM item i
    JOIN category c ON i.Category_ID = c.Category_ID
    WHERE i.User_ID = ? ORDER BY i.Item_ID DESC
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$items = mysqli_stmt_get_result($stmt);

include "includes/header.php";

function statusBadge($status) {
    $map = ['Approved' => 'success', 'Pending' => 'warning', 'Rejected' => 'danger'];
    $c = $map[$status] ?? 'secondary';
    return "<span class='badge bg-$c'>$status</span>";
}
?>

<h3 class="mb-4">My Listings</h3>

<div class="table-responsive">
<table class="table table-hover align-middle bg-white shadow-sm">
  <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Price</th><th>Status</th><th>Approval</th><th></th></tr></thead>
  <tbody>
    <?php if (mysqli_num_rows($items) === 0): ?>
      <tr><td colspan="7" class="text-muted text-center py-4">You haven't listed anything yet. <a href="add_item.php">List your first item</a>.</td></tr>
    <?php endif; ?>
    <?php while ($i = mysqli_fetch_assoc($items)): ?>
      <tr>
        <td><?= htmlspecialchars($i['Title']) ?></td>
        <td><?= htmlspecialchars($i['Category_Name']) ?></td>
        <td><?= htmlspecialchars($i['Item_Type']) ?></td>
        <td><?= $i['Item_Type'] === 'Donation' ? 'Free' : '৳' . number_format($i['Price'], 2) ?></td>
        <td><?= htmlspecialchars($i['Status']) ?></td>
        <td><?= statusBadge($i['Approval_Status']) ?></td>
        <td>
          <a href="item.php?id=<?= $i['Item_ID'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
          <a href="my_listings.php?delete=<?= $i['Item_ID'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this listing?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>

<?php include "includes/footer.php"; ?>
