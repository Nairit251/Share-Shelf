<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$item_id = (int)($_POST['item_id'] ?? 0);
$user_id = current_user_id();

$stmt = mysqli_prepare($conn, "SELECT * FROM item WHERE Item_ID = ? AND Item_Type = 'Donation' AND Status = 'Available' AND Approval_Status = 'Approved'");
mysqli_stmt_bind_param($stmt, "i", $item_id);
mysqli_stmt_execute($stmt);
$item = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$item) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'This item can no longer be claimed.'];
    header("Location: browse.php");
    exit;
}

mysqli_begin_transaction($conn);
try {
    // Record the claim (per instructions: claim is immediate, no approval queue)
    $stmt = mysqli_prepare($conn, "
        INSERT INTO claim (Item_ID, User_ID, Claim_Date, Status, Pickup_Location, Pickup_Status)
        VALUES (?, ?, CURDATE(), 'Completed', ?, 'Picked Up')
    ");
    mysqli_stmt_bind_param($stmt, "iis", $item_id, $user_id, $item['Pickup_Location']);
    mysqli_stmt_execute($stmt);

    // Keep the item row so claim/purchase history can still show the title.
    // When the last unit is claimed, mark it Unavailable and clear carts;
    // otherwise just decrement stock and leave it listed.
    mysqli_query($conn, "DELETE FROM cart_item WHERE Item_ID = $item_id");
    if ((int)$item['Quantity'] <= 1) {
        mysqli_query($conn, "UPDATE item SET Quantity = 0, Status = 'Unavailable' WHERE Item_ID = $item_id");
    } else {
        mysqli_query($conn, "UPDATE item SET Quantity = Quantity - 1 WHERE Item_ID = $item_id");
    }

    mysqli_commit($conn);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'You have successfully claimed this item!'];
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Something went wrong. Please try again.'];
}

header("Location: my_claims.php");
exit;
