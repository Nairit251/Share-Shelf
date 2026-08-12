<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_login();

$page_title = "Rate Seller";
$user_id = current_user_id();
$pi_id = (int)($_GET['purchase_item_id'] ?? $_POST['purchase_item_id'] ?? 0);

// Find the purchase_item, confirm it belongs to this buyer, and resolve the seller
// via the Seller_ID captured at checkout time (item may be Unavailable after sale).
$stmt = mysqli_prepare($conn, "
    SELECT pi.*, p.Buyer_ID, pi.Seller_ID, i.Title
    FROM purchase_item pi
    JOIN purchase p ON pi.Purchase_ID = p.Purchase_ID
    LEFT JOIN item i ON pi.Item_ID = i.Item_ID
    WHERE pi.Purchase_Item_ID = ?
");
mysqli_stmt_bind_param($stmt, "i", $pi_id);
mysqli_stmt_execute($stmt);
$pi = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$pi || $pi['Buyer_ID'] != $user_id) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'That purchase could not be found.'];
    header("Location: my_purchases.php");
    exit;
}

if (!$pi['Seller_ID']) {
    // Only orders placed before this feature existed (no Seller_ID
    // captured at checkout, and the item is already gone) hit this.
    $_SESSION['flash'] = ['type' => 'warning', 'msg' => 'Seller information is no longer available for this order.'];
    header("Location: my_purchases.php");
    exit;
}

// Prevent duplicate ratings for the same purchase item
$already = mysqli_query($conn, "SELECT 1 FROM rating WHERE From_User_ID = $user_id AND To_User_ID = {$pi['Seller_ID']} AND Rating_Date = CURDATE()")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = (int)$_POST['score'];
    $comment = trim($_POST['comment']);

    $stmt = mysqli_prepare($conn, "INSERT INTO rating (From_User_ID, To_User_ID, Score, Comment, Rating_Date) VALUES (?, ?, ?, ?, CURDATE())");
    mysqli_stmt_bind_param($stmt, "iiis", $user_id, $pi['Seller_ID'], $score, $comment);
    mysqli_stmt_execute($stmt);

    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Thanks for rating your seller!'];
    header("Location: my_purchases.php");
    exit;
}

include "includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm p-4">
      <h4>Rate your seller</h4>
      <p class="text-muted">For: <?= htmlspecialchars($pi['Title'] ?? 'Purchased item') ?></p>
      <form method="post">
        <input type="hidden" name="purchase_item_id" value="<?= $pi_id ?>">
        <div class="mb-3">
          <label class="form-label">Score</label>
          <select name="score" class="form-select" required>
            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
            <option value="4">⭐⭐⭐⭐ Good</option>
            <option value="3">⭐⭐⭐ Average</option>
            <option value="2">⭐⭐ Poor</option>
            <option value="1">⭐ Very Poor</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Comment</label>
          <textarea name="comment" class="form-control" rows="3"></textarea>
        </div>
        <button class="btn btn-success" type="submit">Submit Rating</button>
      </form>
    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>
