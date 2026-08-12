<?php
session_start();
require "config/db.php";
require "includes/auth.php";

$page_title = "Home";

// Featured items: latest 6 approved & available
$items = mysqli_query($conn, "
  SELECT i.*, c.Category_Name,
         (SELECT Image_URL FROM item_image WHERE Item_ID = i.Item_ID AND Is_Primary = 1 LIMIT 1) AS Image_URL
  FROM item i
  JOIN category c ON i.Category_ID = c.Category_ID
  WHERE i.Approval_Status = 'Approved' AND i.Status = 'Available'
  ORDER BY i.Item_ID DESC
  LIMIT 6
");

$categories = mysqli_query($conn, "SELECT * FROM category WHERE Parent_Category_ID IS NULL ORDER BY Category_Name");

include "includes/header.php";
?>

<div class="p-5 mb-4 rounded-3" style="background: linear-gradient(135deg,#2f6f4f,#4c9a6a); color:#fff;">
  <h1 class="display-6 fw-bold">Give things a second life.</h1>
  <p class="lead mb-4">Buy, sell, or donate second-hand items in your community — Share Shelf connects neighbors with what they no longer need.</p>
  <a href="browse.php" class="btn btn-light btn-lg me-2">Browse Items</a>
  <?php if (!is_logged_in()): ?>
    <a href="register.php" class="btn btn-outline-light btn-lg">Join Now</a>
  <?php else: ?>
    <a href="add_item.php" class="btn btn-outline-light btn-lg">List an Item</a>
  <?php endif; ?>
</div>

<h4 class="mb-3">Browse by Category</h4>
<div class="d-flex flex-wrap gap-2 mb-5">
  <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
    <a href="browse.php?category=<?= $cat['Category_ID'] ?>" class="btn btn-outline-secondary btn-sm">
      <?= htmlspecialchars($cat['Category_Name']) ?>
    </a>
  <?php endwhile; ?>
</div>

<h4 class="mb-3">Recently Listed</h4>
<div class="row g-4">
  <?php while ($item = mysqli_fetch_assoc($items)): ?>
    <div class="col-6 col-md-4 col-lg-2">
      <div class="card card-item h-100 shadow-sm">
        <img src="<?= htmlspecialchars($item['Image_URL'] ?? 'https://via.placeholder.com/300x180?text=No+Image') ?>" class="card-img-top" alt="">
        <div class="card-body d-flex flex-column">
          <div class="card-title text-truncate"><?= htmlspecialchars($item['Title']) ?></div>
          <div class="small text-muted mb-2"><?= htmlspecialchars($item['Category_Name']) ?></div>
          <div class="mt-auto">
            <?php if ($item['Item_Type'] === 'Donation'): ?>
              <span class="badge badge-free">Free</span>
            <?php else: ?>
              <span class="fw-bold">৳<?= number_format($item['Price'], 2) ?></span>
            <?php endif; ?>
          </div>
          <a href="item.php?id=<?= $item['Item_ID'] ?>" class="btn btn-sm btn-outline-success mt-2">View</a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<?php include "includes/footer.php"; ?>
