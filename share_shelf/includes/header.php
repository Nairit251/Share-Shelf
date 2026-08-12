<?php
// Prevent the browser from showing a cached (stale) navbar after
// login/logout/switching accounts — this was causing the ribbon to
// keep showing "Login / Register" or a previous user's name.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? htmlspecialchars($page_title) . " · Share Shelf" : "Share Shelf" ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color:<?= is_admin_logged_in() ? '#212529' : '#2f6f4f' ?>;">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= is_admin_logged_in() ? 'admin.php' : 'index.php' ?>">
      <?= is_admin_logged_in() ? '🔒 Share Shelf Admin' : '♻️ Share Shelf' ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <?php if (!is_admin_logged_in()): ?>
        <form class="d-flex mx-auto my-2 my-lg-0" style="max-width:400px;width:100%;" method="get" action="browse.php">
          <input class="form-control" type="search" name="q" placeholder="Search items..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
          <button class="btn btn-light ms-2" type="submit">Search</button>
        </form>
      <?php endif; ?>
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <?php if (is_admin_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="admin.php">Dashboard</a></li>
          <li class="nav-item"><span class="nav-link text-light">👤 <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span></li>
          <li class="nav-item"><a class="nav-link text-danger" href="admin_logout.php">Logout</a></li>
        <?php elseif (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="browse.php">Browse</a></li>
          <li class="nav-item"><a class="nav-link" href="add_item.php">Sell / Donate</a></li>
          <li class="nav-item"><a class="nav-link" href="cart.php">🛒 Cart</a></li>
          <li class="nav-item"><a class="nav-link" href="support.php">Support</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <?= htmlspecialchars($_SESSION['user_name'] ?? 'Account') ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
              <li><a class="dropdown-item" href="my_listings.php">My Listings</a></li>
              <li><a class="dropdown-item" href="my_purchases.php">My Purchases</a></li>
              <li><a class="dropdown-item" href="my_claims.php">My Claims</a></li>
              <li><a class="dropdown-item" href="support.php">Support</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="browse.php">Browse</a></li>
          <li class="nav-item"><a class="nav-link" href="support.php">Support</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show">
    <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
