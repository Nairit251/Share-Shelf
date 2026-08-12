<?php
session_start();
require "config/db.php";
require "includes/auth.php";

if (is_logged_in()) { header("Location: index.php"); exit; }

$page_title = "Register";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first   = trim($_POST['first_name']);
    $last    = trim($_POST['last_name']);
    $email   = trim($_POST['email']);
    $phone   = trim($_POST['phone']);
    $pass    = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $district = trim($_POST['district']);
    $area     = trim($_POST['area']);
    $street   = trim($_POST['street']);

    if ($first === '' || $phone === '' || $pass === '' || $district === '' || $area === '' || $street === '') {
        $errors[] = "Please fill in all required fields.";
    }
    if ($phone !== '' && !preg_match('/^01[0-9]{9}$/', $phone)) {
        $errors[] = "Phone number must be exactly 11 digits (e.g. 01XXXXXXXXX).";
    }
    if ($pass !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check for duplicate phone/email (schema has UNIQUE keys on both)
        $stmt = mysqli_prepare($conn, "SELECT User_ID, Is_Banned FROM user WHERE Phone = ? OR (Email = ? AND Email IS NOT NULL AND Email != '')");
        mysqli_stmt_bind_param($stmt, "ss", $phone, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing = mysqli_fetch_assoc($result);

        if ($existing) {
            if (!empty($existing['Is_Banned'])) {
                $errors[] = "This account has been banned and cannot be re-registered. Please contact support.";
            } else {
                $errors[] = "An account with this phone number or email already exists.";
            }
        } else {
            $emailVal = $email === '' ? null : $email;
            $stmt = mysqli_prepare($conn, "INSERT INTO user (First_Name, Last_Name, Email, Phone, Password, District, Area, Street) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssssss", $first, $last, $emailVal, $phone, $pass, $district, $area, $street);
            if (mysqli_stmt_execute($stmt)) {
                $newId = mysqli_insert_id($conn);
                // Clear any admin session lingering in this browser
                unset($_SESSION['admin_id'], $_SESSION['admin_name']);
                $_SESSION['user_id'] = $newId;
                $_SESSION['user_name'] = $first;
                $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Welcome to Share Shelf! Your account has been created.'];
                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}

include "includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h3 class="mb-3">Create an Account</h3>

        <?php foreach ($errors as $e): ?>
          <div class="alert alert-danger py-2"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <form method="post">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">First Name *</label>
              <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone *</label>
            <input type="text" name="phone" class="form-control" required pattern="01[0-9]{9}" maxlength="11" minlength="11"
                   title="11-digit phone number starting with 01" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            <div class="form-text">11 digits, e.g. 01XXXXXXXXX</div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Password *</label>
              <div class="input-group">
                <input type="password" name="password" id="pwField1" class="form-control" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePw('pwField1')">👁</button>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Confirm Password *</label>
              <div class="input-group">
                <input type="password" name="confirm_password" id="pwField2" class="form-control" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePw('pwField2')">👁</button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">District *</label>
              <input type="text" name="district" class="form-control" required value="<?= htmlspecialchars($_POST['district'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Area *</label>
              <input type="text" name="area" class="form-control" required value="<?= htmlspecialchars($_POST['area'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Street *</label>
              <input type="text" name="street" class="form-control" required value="<?= htmlspecialchars($_POST['street'] ?? '') ?>">
            </div>
          </div>
          <button type="submit" class="btn btn-success w-100">Register</button>
        </form>
        <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php">Login</a></p>
      </div>
    </div>
  </div>
</div>

<script>
function togglePw(id) {
  const f = document.getElementById(id);
  f.type = f.type === 'password' ? 'text' : 'password';
}
</script>

<?php include "includes/footer.php"; ?>
