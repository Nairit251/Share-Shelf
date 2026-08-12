<?php
session_start();
require "config/db.php";
require "includes/auth.php";

if (is_logged_in()) { header("Location: index.php"); exit; }

$page_title = "Login";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']); // email or phone
    $password   = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE (Email = ? OR Phone = ?) AND Password = ?");
    mysqli_stmt_bind_param($stmt, "sss", $identifier, $identifier, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if (!empty($user['Is_Banned'])) {
            $error = "This account has been banned. Please contact support.";
        } elseif (!empty($user['Is_Deleted'])) {
            $error = "This account has been permanently deleted.";
        } else {
            // Clear any admin session lingering in this browser so a tab
            // can't end up half-logged-in as both a user and an admin.
            unset($_SESSION['admin_id'], $_SESSION['admin_name']);
            $_SESSION['user_id']   = $user['User_ID'];
            $_SESSION['user_name'] = $user['First_Name'];
            header("Location: index.php");
            exit;
        }
    } else {
        $error = "Invalid email/phone or password.";
    }
}

include "includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h3 class="mb-3">Login</h3>
        <?php if ($error): ?>
          <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Email or Phone</label>
            <input type="text" name="identifier" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" name="password" id="pwField" class="form-control" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePw()">👁</button>
            </div>
          </div>
          <button type="submit" class="btn btn-success w-100">Login</button>
        </form>
        <p class="text-center mt-3 mb-1">New here? <a href="register.php">Create an account</a></p>
        <p class="text-center mb-0 small text-muted">Can't log in? <a href="support.php">Contact support</a> with your name and email/phone.</p>
      </div>
    </div>
  </div>
</div>

<script>
function togglePw() {
  const f = document.getElementById('pwField');
  f.type = f.type === 'password' ? 'text' : 'password';
}
</script>

<?php include "includes/footer.php"; ?>
