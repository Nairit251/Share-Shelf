<?php
session_start();
require "config/db.php";
require "includes/auth.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "
  SELECT i.*, c.Category_Name, u.First_Name, u.Last_Name, u.User_ID AS Seller_ID,
         u.District, u.Area
  FROM item i
  JOIN category c ON i.Category_ID = c.Category_ID
  JOIN user u ON i.User_ID = u.User_ID
  WHERE i.Item_ID = ?
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$item = mysqli_stmt_get_result($stmt)->fetch_assoc();

$isOwner = is_logged_in() && isset($item['User_ID']) && $item['User_ID'] == current_user_id();

if (!$item || ($item['Approval_Status'] !== 'Approved' && !$isOwner)) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'That listing is not available.'];
    header("Location: browse.php");
    exit;
}

$images = mysqli_query($conn, "SELECT * FROM item_image WHERE Item_ID = $id ORDER BY Is_Primary DESC");
$imageList = mysqli_fetch_all($images, MYSQLI_ASSOC);
if (empty($imageList)) {
    $imageList = [['Image_URL' => 'https://via.placeholder.com/600x380?text=No+Image+Available']];
}

// Seller's average rating
$ratingRow = mysqli_query($conn, "SELECT AVG(Score) as avg_score, COUNT(*) as cnt FROM rating WHERE To_User_ID = " . (int)$item['Seller_ID'])->fetch_assoc();

$page_title = $item['Title'];
include "includes/header.php";
?>

<div class="row g-4">
  <div class="col-md-6">
    <div id="itemCarousel" class="carousel slide shadow-sm rounded overflow-hidden">
      <div class="carousel-inner">
        <?php $first = true; foreach ($imageList as $img): ?>
          <div class="carousel-item <?= $first ? 'active' : '' ?>">
            <img src="<?= htmlspecialchars($img['Image_URL']) ?>" class="d-block w-100" style="height:380px;object-fit:cover;background:#e9ecef;" alt="">
          </div>
          <?php $first = false; endforeach; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#itemCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </div>

  <div class="col-md-6">
    <?php if ($isOwner && $item['Approval_Status'] !== 'Approved'): ?>
      <div class="alert alert-<?= $item['Approval_Status'] === 'Rejected' ? 'danger' : 'warning' ?>">
        This listing is <strong><?= htmlspecialchars($item['Approval_Status']) ?></strong> and isn't visible to other users yet.
      </div>
    <?php endif; ?>
    <h3><?= htmlspecialchars($item['Title']) ?></h3>
    <p class="text-muted mb-1"><?= htmlspecialchars($item['Category_Name']) ?> · Condition: <?= htmlspecialchars($item['Condition']) ?></p>

    <h4 class="mb-3">
      <?php if ($item['Item_Type'] === 'Donation'): ?>
        <span class="badge badge-free fs-6">Free / Donation</span>
      <?php else: ?>
        ৳<?= number_format($item['Price'], 2) ?>
      <?php endif; ?>
    </h4>

    <p><?= nl2br(htmlspecialchars($item['Description'])) ?></p>

    <ul class="list-unstyled small text-muted">
      <li>📍 Pickup: <?= htmlspecialchars($item['Pickup_Location']) ?></li>
      <li>📦 Quantity available: <?= (int)$item['Quantity'] ?></li>
      <li>👤 Seller: <?= htmlspecialchars($item['First_Name'] . ' ' . $item['Last_Name']) ?>
        <?php if ($ratingRow['cnt'] > 0): ?>
          — ⭐ <?= number_format($ratingRow['avg_score'], 1) ?> (<?= $ratingRow['cnt'] ?> ratings)
        <?php else: ?>
          — no ratings yet
        <?php endif; ?>
      </li>
    </ul>

    <?php if (!is_logged_in()): ?>
      <a href="login.php" class="btn btn-success">Login to Buy / Claim</a>
    <?php elseif ($_SESSION['user_id'] == $item['Seller_ID']): ?>
      <div class="alert alert-secondary">This is your own listing.</div>
    <?php elseif ($item['Status'] !== 'Available'): ?>
      <div class="alert alert-warning">This item is currently <?= strtolower($item['Status']) ?>.</div>
    <?php else: ?>
      <div class="d-flex gap-2 flex-wrap">
        <?php if ($item['Item_Type'] === 'Donation'): ?>
          <form method="post" action="claim.php">
            <input type="hidden" name="item_id" value="<?= $item['Item_ID'] ?>">
            <button class="btn btn-success" onclick="return confirm('Claim this free item? It will be removed from listings immediately.')">Claim Item</button>
          </form>
        <?php else: ?>
          <form method="post" action="add_to_cart.php" class="d-flex align-items-end gap-2 flex-wrap">
            <input type="hidden" name="item_id" value="<?= $item['Item_ID'] ?>">
            <div>
              <label class="form-label small mb-1">Quantity</label>
              <input type="number" name="quantity" class="form-control form-control-sm" style="width:90px;"
                     min="1" max="<?= max(1, (int)$item['Quantity']) ?>" value="1" required>
            </div>
            <button class="btn btn-success">Add to Cart</button>
          </form>
        <?php endif; ?>
        <a href="report_item.php?item_id=<?= $item['Item_ID'] ?>" class="btn btn-outline-danger">🚩 Report</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include "includes/footer.php"; ?>
