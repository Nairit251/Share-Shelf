<?php
session_start();
require "config/db.php";
require "includes/auth.php";
require_admin();

$page_title = "Support Tickets";
$admin_id = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = (int)$_POST['ticket_id'];
    $reply = trim($_POST['reply']);
    $stmt = mysqli_prepare($conn, "UPDATE support_ticket SET Reply=?, Reply_Date=NOW(), Status='Resolved', Admin_ID=? WHERE Ticket_ID=?");
    mysqli_stmt_bind_param($stmt, "sii", $reply, $admin_id, $ticket_id);
    mysqli_stmt_execute($stmt);
    header("Location: admin_tickets.php");
    exit;
}

$tickets = mysqli_query($conn, "
    SELECT t.*, u.First_Name, u.Last_Name, u.Email AS User_Email, u.Phone AS User_Phone
    FROM support_ticket t
    LEFT JOIN user u ON t.User_ID = u.User_ID
    ORDER BY (t.Status='Open') DESC, t.Ticket_ID DESC
");

include "includes/header.php";
?>

<a href="admin.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Dashboard</a>
<h3 class="mb-3">Support Tickets</h3>

<?php while ($t = mysqli_fetch_assoc($tickets)): ?>
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between">
        <strong><?= htmlspecialchars($t['Subject']) ?></strong>
        <span class="badge bg-<?= $t['Status']==='Resolved'?'success':'warning' ?>"><?= htmlspecialchars($t['Status']) ?></span>
      </div>
      <div class="text-muted small mb-2">
        <?php if ($t['User_ID']): ?>
          From: <?= htmlspecialchars(trim(($t['First_Name'] ?? '') . ' ' . ($t['Last_Name'] ?? ''))) ?>
          <?php if (!empty($t['User_Email'])): ?> · <?= htmlspecialchars($t['User_Email']) ?><?php endif; ?>
          <?php if (!empty($t['User_Phone'])): ?> · <?= htmlspecialchars($t['User_Phone']) ?><?php endif; ?>
        <?php else: ?>
          From (guest): <?= htmlspecialchars($t['Contact_Name'] ?? 'Unknown') ?>
          <?php if (!empty($t['Contact_Email'])): ?> · <?= htmlspecialchars($t['Contact_Email']) ?><?php endif; ?>
          <?php if (!empty($t['Contact_Phone'])): ?> · <?= htmlspecialchars($t['Contact_Phone']) ?><?php endif; ?>
        <?php endif; ?>
      </div>
      <p><?= nl2br(htmlspecialchars($t['Message'])) ?></p>

      <?php if (!empty($t['Reply'])): ?>
        <div class="alert alert-light border small">
          <strong>Your reply:</strong> <?= nl2br(htmlspecialchars($t['Reply'])) ?>
          <?php if (!empty($t['Reply_Date'])): ?>
            <div class="text-muted mt-1"><?= htmlspecialchars($t['Reply_Date']) ?></div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <form method="post" class="d-flex gap-2">
          <input type="hidden" name="ticket_id" value="<?= $t['Ticket_ID'] ?>">
          <input type="text" name="reply" class="form-control" placeholder="Type a reply..." required>
          <button class="btn btn-success" type="submit">Reply & Resolve</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
<?php endwhile; ?>

<?php include "includes/footer.php"; ?>
