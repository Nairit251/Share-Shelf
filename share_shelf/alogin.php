<?php
// ---------------------------------------------------------------
// Admin login. Intentionally NOT linked from any page or navbar —
// an admin reaches this by typing the URL directly, e.g.
// http://localhost/share-shelf/alogin.php
// ---------------------------------------------------------------
session_start();
require "config/db.php";
require "includes/auth.php";

if (is_admin_logged_in()) { header("Location: admin.php"); exit; }

$page_title = "Admin Login";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password   = $_POST['password'];

    $stmt = mysqli_prepare($conn, "
        SELECT u.User_ID, u.First_Name, a.Admin_ID
        FROM user u
        JOIN admin a ON a.Admin_ID = u.User_ID
        WHERE (u.Email = ? OR u.Phone = ?) AND u.Password = ?
    ");
    mysqli_stmt_bind_param($stmt, "sss", $identifier, $identifier, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);

    if ($admin) {
        // Clear any regular-user session lingering in this browser so a
        // tab can't end up half-logged-in as both a user and an admin.
        unset($_SESSION['user_id'], $_SESSION['user_name']);
        $_SESSION['admin_id']   = $admin['Admin_ID'];
        $_SESSION['admin_name'] = $admin['First_Name'];
        header("Location: admin.php");
        exit;
    } else {
        $error = "Invalid admin credentials.";
    }
}

include "includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm border-dark">
      <div class="card-body p-4">
        <h3 class="mb-3">🔒 Admin Login</h3>
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
          <button type="submit" class="btn btn-dark w-100">Login as Admin</button>
        </form>
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
