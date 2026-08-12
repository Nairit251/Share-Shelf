<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$page_title = "List an Item";
$errors = [];
$user_id = current_user_id();

// Build category tree for the cascading main/sub selects
$mainRes = mysqli_query($conn, "SELECT * FROM category WHERE Parent_Category_ID IS NULL ORDER BY Category_Name");
$categoryTree = [];
while ($m = mysqli_fetch_assoc($mainRes)) {
    $subs = mysqli_query($conn, "SELECT * FROM category WHERE Parent_Category_ID = " . (int)$m['Category_ID'] . " ORDER BY Category_Name");
    $m['subs'] = mysqli_fetch_all($subs, MYSQLI_ASSOC);
    $categoryTree[] = $m;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $condition = $_POST['condition'];
    $item_type = $_POST['item_type']; // Sale or Donation
    $price = $item_type === 'Donation' ? 0 : (float)$_POST['price'];
    $quantity = max(1, (int)$_POST['quantity']);
    if ($item_type === 'Donation') {
        $quantity = 1; // Donations are limited to quantity 1
    }
    $pickup_location = trim($_POST['pickup_location']);
    $image_urls = array_filter(array_map('trim', $_POST['image_urls'] ?? []), fn($u) => $u !== '');

    if ($title === '' || $category_id === 0 || $pickup_location === '') {
        $errors[] = "Please fill in all required fields, including a category.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO item (User_ID, Category_ID, Title, Description, `Condition`, Item_Type, Price, Quantity, Pickup_Location, Status, Approval_Status, Admin_ID)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Unavailable', 'Pending', NULL)
        ");
        mysqli_stmt_bind_param($stmt, "iissssdis",
            $user_id, $category_id, $title, $description, $condition, $item_type, $price, $quantity, $pickup_location
        );
        mysqli_stmt_execute($stmt);
        $item_id = mysqli_insert_id($conn);

        $first = true;
        foreach ($image_urls as $url) {
            $isPrimary = $first ? 1 : 0;
            $stmt = mysqli_prepare($conn, "INSERT INTO item_image (Item_ID, Image_URL, Is_Primary) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isi", $item_id, $url, $isPrimary);
            mysqli_stmt_execute($stmt);
            $first = false;
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Your listing was submitted and is pending admin approval.'];
        header("Location: my_listings.php");
        exit;
    }
}

include "includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h3 class="mb-3">List an Item</h3>
        <p class="text-muted small">Your listing will be reviewed by an admin before it appears publicly.</p>

        <?php foreach ($errors as $e): ?>
          <div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
          </div>
          <div class="row">
            <div class="col-md-3 mb-3">
              <label class="form-label">Main Category *</label>
              <select id="mainCategory" class="form-select" required onchange="updateSubcategories()">
                <option value="">Select...</option>
                <?php foreach ($categoryTree as $m): ?>
                  <option value="<?= $m['Category_ID'] ?>"><?= htmlspecialchars($m['Category_Name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Subcategory</label>
              <select id="subCategory" class="form-select" onchange="syncCategoryId()">
                <option value="">(none)</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Condition *</label>
              <select name="condition" class="form-select" required>
                <option>New</option>
                <option>Like New</option>
                <option>Good</option>
                <option>Used</option>
              </select>
            </div>
          </div>
          <input type="hidden" name="category_id" id="categoryId" required>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Listing Type *</label>
              <select name="item_type" class="form-select" id="itemType" required onchange="togglePrice()">
                <option value="Sale">For Sale</option>
                <option value="Donation">Free / Donation</option>
              </select>
            </div>
            <div class="col-md-4 mb-3" id="priceField">
              <label class="form-label">Price (৳)</label>
              <input type="number" step="0.01" min="0" name="price" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Quantity *</label>
              <input type="number" min="1" name="quantity" id="quantityField" class="form-control" value="1" required>
              <div class="form-text" id="qtyHint" style="display:none;">Donations are limited to quantity 1.</div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Pickup Location *</label>
            <input type="text" name="pickup_location" class="form-control" required>
          </div>

          <label class="form-label">Photos <span class="text-muted small">(paste up to 3 image links; the first is used as the cover photo)</span></label>
          <div class="mb-2"><input type="text" name="image_urls[]" class="form-control" placeholder="images/items/photo1.jpg"></div>
          <div class="mb-2"><input type="text" name="image_urls[]" class="form-control" placeholder="images/items/photo2.jpg (optional)"></div>
          <div class="mb-3"><input type="text" name="image_urls[]" class="form-control" placeholder="images/items/photo3.jpg (optional)"></div>

          <button type="submit" class="btn btn-success">Submit for Approval</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const categoryTree = <?= json_encode($categoryTree) ?>;

function updateSubcategories() {
  const mainId = document.getElementById('mainCategory').value;
  const subSelect = document.getElementById('subCategory');
  subSelect.innerHTML = '';

  const main = categoryTree.find(m => String(m.Category_ID) === mainId);
  if (main && main.subs && main.subs.length > 0) {
    const noneOpt = document.createElement('option');
    noneOpt.value = mainId;
    noneOpt.textContent = 'General ' + main.Category_Name;
    subSelect.appendChild(noneOpt);
    main.subs.forEach(s => {
      const opt = document.createElement('option');
      opt.value = s.Category_ID;
      opt.textContent = s.Category_Name;
      subSelect.appendChild(opt);
    });
  } else {
    const opt = document.createElement('option');
    opt.value = mainId;
    opt.textContent = '(no subcategories)';
    subSelect.appendChild(opt);
  }
  syncCategoryId();
}

function syncCategoryId() {
  const subVal = document.getElementById('subCategory').value;
  const mainVal = document.getElementById('mainCategory').value;
  document.getElementById('categoryId').value = subVal || mainVal;
}

function togglePrice() {
  const type = document.getElementById('itemType').value;
  document.getElementById('priceField').style.display = type === 'Donation' ? 'none' : 'block';
  const qty = document.getElementById('quantityField');
  const hint = document.getElementById('qtyHint');
  if (type === 'Donation') {
    qty.value = 1;
    qty.max = 1;
    qty.readOnly = true;
    hint.style.display = 'block';
  } else {
    qty.max = '';
    qty.readOnly = false;
    hint.style.display = 'none';
  }
}
togglePrice();
</script>

<?php include "includes/footer.php"; ?>
