<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_admin();

$page_title = "Admin Dashboard";

$stats = [
    'users'    => mysqli_query($conn, "SELECT COUNT(*) c FROM user")->fetch_assoc()['c'],
    'pending'  => mysqli_query($conn, "SELECT COUNT(*) c FROM item WHERE Approval_Status='Pending'")->fetch_assoc()['c'],
    'items'    => mysqli_query($conn, "SELECT COUNT(*) c FROM item WHERE Approval_Status='Approved'")->fetch_assoc()['c'],
    'reports'  => mysqli_query($conn, "SELECT COUNT(*) c FROM report WHERE Status='Pending'")->fetch_assoc()['c'],
    'tickets'  => mysqli_query($conn, "SELECT COUNT(*) c FROM support_ticket WHERE Status='Open'")->fetch_assoc()['c'],
    'banned'   => mysqli_query($conn, "SELECT COUNT(*) c FROM user WHERE Is_Banned=1")->fetch_assoc()['c'],
];

include "includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h3>Admin Dashboard</h3>
  <div>
    Logged in as <strong><?= htmlspecialchars($_SESSION['admin_name']) ?></strong>
    &nbsp;|&nbsp; <a href="admin_logout.php" class="text-danger">Logout</a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-2"><div class="card stat-card p-3 shadow-sm"><div class="text-muted small">Total Users</div><div class="fs-4 fw-bold"><?= $stats['users'] ?></div></div></div>
  <div class="col-6 col-md-2"><div class="card stat-card p-3 shadow-sm"><div class="text-muted small">Pending Listings</div><div class="fs-4 fw-bold text-warning"><?= $stats['pending'] ?></div></div></div>
  <div class="col-6 col-md-2"><div class="card stat-card p-3 shadow-sm"><div class="text-muted small">Live Listings</div><div class="fs-4 fw-bold text-success"><?= $stats['items'] ?></div></div></div>
  <div class="col-6 col-md-2"><div class="card stat-card p-3 shadow-sm"><div class="text-muted small">Open Reports</div><div class="fs-4 fw-bold text-danger"><?= $stats['reports'] ?></div></div></div>
  <div class="col-6 col-md-2"><div class="card stat-card p-3 shadow-sm"><div class="text-muted small">Open Tickets</div><div class="fs-4 fw-bold text-info"><?= $stats['tickets'] ?></div></div></div>
  <div class="col-6 col-md-2"><div class="card stat-card p-3 shadow-sm"><div class="text-muted small">Banned Users</div><div class="fs-4 fw-bold text-secondary"><?= $stats['banned'] ?></div></div></div>
</div>

<div class="row g-3">
  <div class="col-md-3">
    <a href="admin_listings.php" class="card text-decoration-none text-dark shadow-sm p-4 h-100">
      <h5>📦 Manage Listings</h5><p class="small text-muted mb-0">Approve or reject pending items</p>
    </a>
  </div>
  <div class="col-md-3">
    <a href="admin_users.php" class="card text-decoration-none text-dark shadow-sm p-4 h-100">
      <h5>👤 Manage Users</h5><p class="small text-muted mb-0">View profiles, history, ban users</p>
    </a>
  </div>
  <div class="col-md-3">
    <a href="admin_reports.php" class="card text-decoration-none text-dark shadow-sm p-4 h-100">
      <h5>🚩 Reports</h5><p class="small text-muted mb-0">Review reported listings</p>
    </a>
  </div>
  <div class="col-md-3">
    <a href="admin_tickets.php" class="card text-decoration-none text-dark shadow-sm p-4 h-100">
      <h5>🎫 Support Tickets</h5><p class="small text-muted mb-0">Respond to user tickets</p>
    </a>
  </div>
</div>

<?php include "includes/footer.php"; ?>
