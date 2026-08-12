<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$page_title = "Checkout";
$user_id = current_user_id();

$stmt = mysqli_prepare($conn, "
    SELECT ci.Cart_Item_ID, ci.Quantity, i.Item_ID, i.Title, i.Price, i.Pickup_Location, i.Status,
           i.Quantity AS Stock_Quantity, i.User_ID AS Seller_ID
    FROM cart c
    JOIN cart_item ci ON ci.Cart_ID = c.Cart_ID
    JOIN item i ON ci.Item_ID = i.Item_ID
    WHERE c.User_ID = ?
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cartItems = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}

$total = 0;
foreach ($cartItems as $r) $total += $r['Price'] * $r['Quantity'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $pickup_date = $_POST['pickup_date'];
    $pickup_time = $_POST['pickup_time'];
    $pickup_location = $cartItems[0]['Pickup_Location']; // simple: use first item's location

    // Re-check availability
    $unavailable = array_filter($cartItems, fn($r) => $r['Status'] !== 'Available');
    if (!empty($unavailable)) {
        $errors[] = "Some items in your cart are no longer available. Please review your cart.";
    }
    // Validate pickup time is a real HH:MM (24hr) value
    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $pickup_time)) {
        $errors[] = "Please enter a valid pickup time.";
    }

    if (empty($errors)) {
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare($conn, "
                INSERT INTO purchase (Buyer_ID, Purchase_Date, Total_Amount, Status, Pickup_Date, Pickup_Time, Pickup_Location, Pickup_Status)
                VALUES (?, CURDATE(), ?, 'Completed', ?, ?, ?, 'Picked Up')
            ");
            mysqli_stmt_bind_param($stmt, "idsss", $user_id, $total, $pickup_date, $pickup_time, $pickup_location);
            mysqli_stmt_execute($stmt);
            $purchase_id = mysqli_insert_id($conn);

            foreach ($cartItems as $r) {
                $stmt = mysqli_prepare($conn, "INSERT INTO purchase_item (Purchase_ID, Item_ID, Quantity, Unit_Price, Seller_ID) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iiidi", $purchase_id, $r['Item_ID'], $r['Quantity'], $r['Price'], $r['Seller_ID']);
                mysqli_stmt_execute($stmt);
            }

            $stmt = mysqli_prepare($conn, "INSERT INTO payment (Purchase_ID, Amount, Payment_Method, Payment_Status, Payment_Date) VALUES (?, ?, ?, 'Paid', CURDATE())");
            mysqli_stmt_bind_param($stmt, "ids", $purchase_id, $total, $payment_method);
            mysqli_stmt_execute($stmt);

            // Keep the item row so purchase history can still show the title.
            // When stock hits zero, mark Unavailable; otherwise just reduce quantity.
            // Always clear this buyer's cart lines for the items they purchased.
            foreach ($cartItems as $r) {
                $iid = (int)$r['Item_ID'];
                $remaining = (int)$r['Stock_Quantity'] - (int)$r['Quantity'];
                if ($remaining <= 0) {
                    mysqli_query($conn, "DELETE FROM cart_item WHERE Item_ID = $iid");
                    mysqli_query($conn, "UPDATE item SET Quantity = 0, Status = 'Unavailable' WHERE Item_ID = $iid");
                } else {
                    mysqli_query($conn, "UPDATE item SET Quantity = $remaining WHERE Item_ID = $iid");
                    mysqli_query($conn, "DELETE FROM cart_item WHERE Item_ID = $iid AND Cart_ID = (SELECT Cart_ID FROM cart WHERE User_ID = $user_id)");
                }
            }

            mysqli_commit($conn);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Purchase confirmed! You can rate your seller from My Purchases.'];
            header("Location: my_purchases.php");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = "Checkout failed. Please try again.";
        }
    }
}

include "includes/header.php";
?>

<h3 class="mb-4">Checkout</h3>

<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="row">
  <div class="col-md-7">
    <div class="card shadow-sm p-4">
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Payment Method</label>
          <select name="payment_method" class="form-select" required>
            <option>bKash</option>
            <option>Nagad</option>
            <option>Bank Transfer</option>
            <option>Card</option>
            <option>COD</option>
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Pickup Date</label>
            <input type="date" name="pickup_date" class="form-control" required min="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Pickup Time</label>
            <input type="time" name="pickup_time" class="form-control" step="60" required>
          </div>
        </div>
        <button class="btn btn-success w-100" type="submit">Confirm Purchase</button>
      </form>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card shadow-sm p-4">
      <h6>Order Summary</h6>
      <?php foreach ($cartItems as $r): ?>
        <div class="d-flex justify-content-between small">
          <span><?= htmlspecialchars($r['Title']) ?> × <?= $r['Quantity'] ?></span>
          <span>৳<?= number_format($r['Price'] * $r['Quantity'], 2) ?></span>
        </div>
      <?php endforeach; ?>
      <hr>
      <div class="d-flex justify-content-between fw-bold">
        <span>Total</span><span>৳<?= number_format($total, 2) ?></span>
      </div>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>
