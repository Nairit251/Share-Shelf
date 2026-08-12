<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$page_title = "My Claims";
$user_id = current_user_id();

$stmt = mysqli_prepare($conn, "
    SELECT cl.*, i.Title FROM claim cl
    LEFT JOIN item i ON cl.Item_ID = i.Item_ID
    WHERE cl.User_ID = ? ORDER BY cl.Claim_ID DESC
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$claims = mysqli_stmt_get_result($stmt);

include "includes/header.php";
?>

<h3 class="mb-4">My Claims</h3>

<div class="table-responsive">
<table class="table bg-white shadow-sm align-middle">
  <thead><tr><th>Item</th><th>Claim Date</th><th>Status</th><th>Pickup Location</th></tr></thead>
  <tbody>
    <?php if (mysqli_num_rows($claims) === 0): ?>
      <tr><td colspan="4" class="text-muted text-center py-4">You haven't claimed anything yet.</td></tr>
    <?php endif; ?>
    <?php while ($c = mysqli_fetch_assoc($claims)): ?>
      <tr>
        <td><?= htmlspecialchars($c['Title'] ?? '(listing removed)') ?></td>
        <td><?= htmlspecialchars($c['Claim_Date']) ?></td>
        <td><span class="badge bg-success"><?= htmlspecialchars($c['Status']) ?></span></td>
        <td><?= htmlspecialchars($c['Pickup_Location']) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>

<?php include "includes/footer.php"; ?>
