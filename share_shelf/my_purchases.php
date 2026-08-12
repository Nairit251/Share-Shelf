<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$page_title = "My Purchases";
$user_id = current_user_id();

$stmt = mysqli_prepare($conn, "SELECT * FROM purchase WHERE Buyer_ID = ? ORDER BY Purchase_ID DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$purchases = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

include "includes/header.php";
?>

<h3 class="mb-4">My Purchases</h3>

<?php if (empty($purchases)): ?>
  <p class="text-muted">You haven't made any purchases yet.</p>
<?php endif; ?>

<?php foreach ($purchases as $p):
    $items = mysqli_query($conn, "
        SELECT pi.*, i.Title FROM purchase_item pi
        LEFT JOIN item i ON pi.Item_ID = i.Item_ID
        WHERE pi.Purchase_ID = {$p['Purchase_ID']}
    ")->fetch_all(MYSQLI_ASSOC);
?>
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between flex-wrap">
        <div>
          <strong>Purchase #<?= $p['Purchase_ID'] ?></strong> — <?= htmlspecialchars($p['Purchase_Date']) ?>
          <span class="badge bg-<?= $p['Status'] === 'Completed' ? 'success' : 'warning' ?> ms-2"><?= htmlspecialchars($p['Status']) ?></span>
        </div>
        <div class="fw-bold">৳<?= number_format($p['Total_Amount'], 2) ?></div>
      </div>
      <ul class="mt-2 mb-2 small">
        <?php foreach ($items as $it): ?>
          <li><?= htmlspecialchars($it['Title'] ?? '(item no longer listed)') ?> × <?= $it['Quantity'] ?> — ৳<?= number_format($it['Unit_Price'], 2) ?>
            <?php if ($p['Status'] === 'Completed'): ?>
              <a href="rate.php?purchase_item_id=<?= $it['Purchase_Item_ID'] ?>" class="btn btn-sm btn-outline-success ms-2 py-0">Rate Seller</a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="small text-muted">Pickup: <?= htmlspecialchars($p['Pickup_Location']) ?>, <?= htmlspecialchars($p['Pickup_Date']) ?> <?= htmlspecialchars($p['Pickup_Time']) ?></div>
    </div>
  </div>
<?php endforeach; ?>

<?php include "includes/footer.php"; ?>
