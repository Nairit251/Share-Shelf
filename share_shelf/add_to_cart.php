<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$item_id = (int)($_POST['item_id'] ?? 0);
$add_qty = max(1, (int)($_POST['quantity'] ?? 1));
$user_id = current_user_id();

// Load item stock and type
$stmt = mysqli_prepare($conn, "SELECT Quantity, Item_Type, Status FROM item WHERE Item_ID = ?");
mysqli_stmt_bind_param($stmt, "i", $item_id);
mysqli_stmt_execute($stmt);
$item = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$item || $item['Status'] !== 'Available' || $item['Item_Type'] === 'Donation') {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'This item cannot be added to the cart.'];
    header("Location: item.php?id=$item_id");
    exit;
}

$max = max(1, (int)$item['Quantity']);
if ($add_qty > $max) $add_qty = $max;

// Get or create this user's cart
$stmt = mysqli_prepare($conn, "SELECT Cart_ID FROM cart WHERE User_ID = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($cart) {
    $cart_id = $cart['Cart_ID'];
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO cart (User_ID) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $cart_id = mysqli_insert_id($conn);
}

// Avoid duplicate cart_item rows for the same item; bump quantity instead
$stmt = mysqli_prepare($conn, "SELECT Cart_Item_ID, Quantity FROM cart_item WHERE Cart_ID = ? AND Item_ID = ?");
mysqli_stmt_bind_param($stmt, "ii", $cart_id, $item_id);
mysqli_stmt_execute($stmt);
$existing = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($existing) {
    $newQty = min($max, $existing['Quantity'] + $add_qty);
    $stmt = mysqli_prepare($conn, "UPDATE cart_item SET Quantity = ? WHERE Cart_Item_ID = ?");
    mysqli_stmt_bind_param($stmt, "ii", $newQty, $existing['Cart_Item_ID']);
    mysqli_stmt_execute($stmt);
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO cart_item (Cart_ID, Item_ID, Quantity) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iii", $cart_id, $item_id, $add_qty);
    mysqli_stmt_execute($stmt);
}

$_SESSION['flash'] = ['type' => 'success', 'msg' => 'Item added to your cart.'];
header("Location: item.php?id=$item_id");
exit;
