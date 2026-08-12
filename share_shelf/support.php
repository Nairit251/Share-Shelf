<?php
session_start();
require "config/db.php";
require "includes/auth.php";

$page_title = "Support";
$logged_in = is_logged_in();
$user_id = current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $contact_name = trim($_POST['contact_name'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');

    $errors = [];
    if ($subject === '' || $message === '') {
        $errors[] = 'Subject and message are required.';
    }

    if ($logged_in) {
        $stmt = mysqli_prepare($conn, "INSERT INTO support_ticket (User_ID, Contact_Name, Contact_Email, Contact_Phone, Subject, Message, Status) VALUES (?, NULL, NULL, NULL, ?, ?, 'Open')");
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $subject, $message);
    } else {
        if ($contact_name === '' || ($contact_email === '' && $contact_phone === '')) {
            $errors[] = 'Please provide your name and at least an email or phone number so we can reach you.';
        }
        if (empty($errors)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO support_ticket (User_ID, Contact_Name, Contact_Email, Contact_Phone, Subject, Message, Status) VALUES (NULL, ?, ?, ?, ?, ?, 'Open')");
            mysqli_stmt_bind_param($stmt, "sssss", $contact_name, $contact_email, $contact_phone, $subject, $message);
        }
    }

    if (empty($errors)) {
        mysqli_stmt_execute($stmt);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Your ticket has been submitted. We will get back to you soon.'];
        header("Location: support.php");
        exit;
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'msg' => implode(' ', $errors)];
    }
}

$tickets = null;
if ($logged_in) {
    $tickets = mysqli_query($conn, "SELECT * FROM support_ticket WHERE User_ID = $user_id ORDER BY Ticket_ID DESC");
}

include "includes/header.php";
?>

<div class="row">
  <div class="col-md-5 mb-4">
    <div class="card shadow-sm p-4">
      <h4>Contact Support</h4>
      <?php if (!$logged_in): ?>
        <p class="text-muted small">Can't log in or don't have an account? Submit a ticket with your contact details and we will help you.</p>
      <?php endif; ?>
      <form method="post">
        <?php if (!$logged_in): ?>
          <div class="mb-3">
            <label class="form-label">Your Name *</label>
            <input type="text" name="contact_name" class="form-control" required value="<?= htmlspecialchars($_POST['contact_name'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>" placeholder="you@example.com">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>" placeholder="01XXXXXXXXX">
            <div class="form-text">Provide at least one of email or phone.</div>
          </div>
        <?php endif; ?>
        <div class="mb-3">
          <label class="form-label">Subject *</label>
          <input type="text" name="subject" class="form-control" required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Message *</label>
          <textarea name="message" class="form-control" rows="4" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        </div>
        <button class="btn btn-success" type="submit">Submit Ticket</button>
      </form>
    </div>
  </div>
  <div class="col-md-7">
    <?php if ($logged_in): ?>
      <h5>My Tickets</h5>
      <?php if ($tickets && mysqli_num_rows($tickets) === 0): ?>
        <p class="text-muted">You have not submitted any tickets yet.</p>
      <?php endif; ?>
      <?php if ($tickets): while ($t = mysqli_fetch_assoc($tickets)): ?>
        <div class="card shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <strong><?= htmlspecialchars($t['Subject']) ?></strong>
              <span class="badge bg-<?= $t['Status'] === 'Resolved' ? 'success' : ($t['Status'] === 'Open' ? 'warning' : 'secondary') ?>"><?= htmlspecialchars($t['Status']) ?></span>
            </div>
            <p class="mb-1 mt-2"><?= nl2br(htmlspecialchars($t['Message'])) ?></p>
            <?php if (!empty($t['Reply'])): ?>
              <div class="alert alert-light border mt-2 mb-0 small">
                <strong>Support reply:</strong> <?= nl2br(htmlspecialchars($t['Reply'])) ?>
                <?php if (!empty($t['Reply_Date'])): ?>
                  <div class="text-muted mt-1"><?= htmlspecialchars($t['Reply_Date']) ?></div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; endif; ?>
    <?php else: ?>
      <div class="alert alert-info">
        <strong>Need help logging in?</strong>
        <p class="mb-0 mt-2">Use the form to the left with your name and email/phone. If you already have an account, <a href="login.php">log in</a> to see your previous tickets.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include "includes/footer.php"; ?>
