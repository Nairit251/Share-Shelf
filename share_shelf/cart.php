<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$page_title = "My Cart";
$user_id = current_user_id();

if (isset($_GET['remove'])) {
    $ci_id = (int)$_GET['remove'];
    mysqli_query($conn, "
        DELETE ci FROM cart_item ci
        JOIN cart c ON ci.Cart_ID = c.Cart_ID
        WHERE ci.Cart_Item_ID = $ci_id AND c.User_ID = $user_id
    ");
    header("Location: cart.php");
    exit;
}

// Dynamic quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $ci_id = (int)($_POST['cart_item_id'] ?? 0);
    $qty   = (int)($_POST['quantity'] ?? 1);
    if ($qty < 1) $qty = 1;

    // Cap at available stock for this user's cart line
    $stmt = mysqli_prepare($conn, "
        SELECT i.Quantity AS MaxQty
        FROM cart_item ci
        JOIN cart c ON ci.Cart_ID = c.Cart_ID
        JOIN item i ON ci.Item_ID = i.Item_ID
        WHERE ci.Cart_Item_ID = ? AND c.User_ID = ?
    ");
    mysqli_stmt_bind_param($stmt, "ii", $ci_id, $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_stmt_get_result($stmt)->fetch_assoc();
    if ($row) {
        $max = max(1, (int)$row['MaxQty']);
        if ($qty > $max) $qty = $max;
        $stmt = mysqli_prepare($conn, "
            UPDATE cart_item ci
            JOIN cart c ON ci.Cart_ID = c.Cart_ID
            SET ci.Quantity = ?
            WHERE ci.Cart_Item_ID = ? AND c.User_ID = ?
        ");
        mysqli_stmt_bind_param($stmt, "iii", $qty, $ci_id, $user_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: cart.php");
    exit;
}

$stmt = mysqli_prepare($conn, "
    SELECT ci.Cart_Item_ID, ci.Quantity, i.Item_ID, i.Title, i.Price, i.Status, i.Quantity AS Max_Qty,
           (SELECT Image_URL FROM item_image WHERE Item_ID = i.Item_ID AND Is_Primary = 1 LIMIT 1) AS Image_URL
    FROM cart c
    JOIN cart_item ci ON ci.Cart_ID = c.Cart_ID
    JOIN item i ON ci.Item_ID = i.Item_ID
    WHERE c.User_ID = ?
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$rows = mysqli_stmt_get_result($stmt);

$total = 0;
$cartItems = [];
while ($r = mysqli_fetch_assoc($rows)) {
    $cartItems[] = $r;
    $total += $r['Price'] * $r['Quantity'];
}

include "includes/header.php";
?>

<h3 class="mb-4">My Cart</h3>

<?php if (empty($cartItems)): ?>
  <p class="text-muted">Your cart is empty. <a href="browse.php">Browse items</a>.</p>
<?php else: ?>
  <div class="table-responsive">
  <table class="table align-middle bg-white shadow-sm">
    <thead><tr><th>Item</th><th>Unit Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($cartItems as $r): ?>
        <tr>
          <td class="d-flex align-items-center gap-2">
            <img src="<?= htmlspecialchars($r['Image_URL'] ?? 'https://via.placeholder.com/50') ?>" style="width:50px;height:50px;object-fit:cover;" class="rounded">
            <?= htmlspecialchars($r['Title']) ?>
            <?php if ($r['Status'] !== 'Available'): ?><span class="badge bg-warning text-dark ms-2">No longer available</span><?php endif; ?>
          </td>
          <td>৳<?= number_format($r['Price'], 2) ?></td>
          <td style="min-width:140px;">
            <form method="post" class="d-flex align-items-center gap-1">
              <input type="hidden" name="cart_item_id" value="<?= (int)$r['Cart_Item_ID'] ?>">
              <input type="number" name="quantity" class="form-control form-control-sm" style="width:70px;"
                     min="1" max="<?= max(1, (int)$r['Max_Qty']) ?>" value="<?= (int)$r['Quantity'] ?>" required>
              <button type="submit" name="update_qty" value="1" class="btn btn-sm btn-outline-secondary">Update</button>
            </form>
            <div class="form-text small">Max: <?= (int)$r['Max_Qty'] ?></div>
          </td>
          <td>৳<?= number_format($r['Price'] * $r['Quantity'], 2) ?></td>
          <td><a href="cart.php?remove=<?= $r['Cart_Item_ID'] ?>" class="btn btn-sm btn-outline-danger">Remove</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <div class="d-flex justify-content-end">
    <div class="card p-3 shadow-sm" style="min-width:280px;">
      <div class="d-flex justify-content-between mb-2"><span>Total:</span><strong>৳<?= number_format($total, 2) ?></strong></div>
      <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
  </div>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
