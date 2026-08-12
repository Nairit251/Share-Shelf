<?php
session_start();
require "config/db.php";
require "includes/auth.php";

$page_title = "Browse Items";

$q = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';
$type = $_GET['type'] ?? '';

$sql = "
  SELECT i.*, c.Category_Name,
         (SELECT Image_URL FROM item_image WHERE Item_ID = i.Item_ID AND Is_Primary = 1 LIMIT 1) AS Image_URL
  FROM item i
  JOIN category c ON i.Category_ID = c.Category_ID
  WHERE i.Approval_Status = 'Approved' AND i.Status = 'Available'
";
$params = [];
$types = "";

if ($q !== '') {
    // Whole-word match so "men" doesn't also match "women". We pad the
    // title/description with spaces and look for " term " so it also
    // catches the term at the very start or end of the text.
    $sql .= " AND (CONCAT(' ', i.Title, ' ') LIKE ? OR CONCAT(' ', i.Description, ' ') LIKE ?) ";
    $like = "% $q %";
    $params[] = $like; $params[] = $like;
    $types .= "ss";
}
if ($category !== '') {
    // If the chosen category is a top-level (main) category, also include
    // items filed under any of its subcategories.
    $catCheck = mysqli_query($conn, "SELECT Parent_Category_ID FROM category WHERE Category_ID = " . (int)$category);
    $catRow = $catCheck ? mysqli_fetch_assoc($catCheck) : null;

    if ($catRow && $catRow['Parent_Category_ID'] === null) {
        $childIds = [(int)$category];
        $childRes = mysqli_query($conn, "SELECT Category_ID FROM category WHERE Parent_Category_ID = " . (int)$category);
        while ($row = mysqli_fetch_assoc($childRes)) $childIds[] = (int)$row['Category_ID'];
        $placeholders = implode(',', array_fill(0, count($childIds), '?'));
        $sql .= " AND i.Category_ID IN ($placeholders) ";
        foreach ($childIds as $cid) { $params[] = $cid; $types .= "i"; }
    } else {
        $sql .= " AND i.Category_ID = ? ";
        $params[] = $category;
        $types .= "i";
    }
}
if ($type !== '') {
    $sql .= " AND i.Item_Type = ? ";
    $params[] = $type;
    $types .= "s";
}
$sql .= " ORDER BY i.Item_ID DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$items = mysqli_stmt_get_result($stmt);

// Build category tree (main categories with their subcategories nested)
$mains = mysqli_query($conn, "SELECT * FROM category WHERE Parent_Category_ID IS NULL ORDER BY Category_Name");
$categoryTree = [];
while ($m = mysqli_fetch_assoc($mains)) {
    $subs = mysqli_query($conn, "SELECT * FROM category WHERE Parent_Category_ID = " . (int)$m['Category_ID'] . " ORDER BY Category_Name");
    $m['subs'] = mysqli_fetch_all($subs, MYSQLI_ASSOC);
    $categoryTree[] = $m;
}

include "includes/header.php";
?>

<div class="row">
  <div class="col-md-3 mb-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6>Filter</h6>
        <form method="get">
          <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
          <label class="form-label small mt-2">Category</label>
          <select name="category" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($categoryTree as $m): ?>
              <optgroup label="<?= htmlspecialchars($m['Category_Name']) ?>">
                <option value="<?= $m['Category_ID'] ?>" <?= $category == $m['Category_ID'] ? 'selected' : '' ?>>
                  All <?= htmlspecialchars($m['Category_Name']) ?>
                </option>
                <?php foreach ($m['subs'] as $s): ?>
                  <option value="<?= $s['Category_ID'] ?>" <?= $category == $s['Category_ID'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['Category_Name']) ?>
                  </option>
                <?php endforeach; ?>
              </optgroup>
            <?php endforeach; ?>
          </select>
          <label class="form-label small">Type</label>
          <select name="type" class="form-select form-select-sm mb-2" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="Sale" <?= $type === 'Sale' ? 'selected' : '' ?>>For Sale</option>
            <option value="Donation" <?= $type === 'Donation' ? 'selected' : '' ?>>Free / Donation</option>
          </select>
          <button class="btn btn-sm btn-success w-100" type="submit">Apply</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-9">
    <h4 class="mb-3">
      <?= $q !== '' ? 'Results for "' . htmlspecialchars($q) . '"' : 'All Items' ?>
    </h4>
    <div class="row g-4">
      <?php if (mysqli_num_rows($items) === 0): ?>
        <p class="text-muted">No items found.</p>
      <?php endif; ?>
      <?php while ($item = mysqli_fetch_assoc($items)): ?>
        <div class="col-6 col-md-4">
          <div class="card card-item h-100 shadow-sm">
            <img src="<?= htmlspecialchars($item['Image_URL'] ?? 'https://via.placeholder.com/300x180?text=No+Image') ?>" class="card-img-top" alt="">
            <div class="card-body d-flex flex-column">
              <div class="card-title text-truncate"><?= htmlspecialchars($item['Title']) ?></div>
              <div class="small text-muted mb-1"><?= htmlspecialchars($item['Category_Name']) ?> · <?= htmlspecialchars($item['Condition']) ?></div>
              <div class="mb-2">
                <?php if ($item['Item_Type'] === 'Donation'): ?>
                  <span class="badge badge-free">Free</span>
                <?php else: ?>
                  <span class="fw-bold">৳<?= number_format($item['Price'], 2) ?></span>
                <?php endif; ?>
              </div>
              <a href="item.php?id=<?= $item['Item_ID'] ?>" class="btn btn-sm btn-outline-success mt-auto">View Details</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>
