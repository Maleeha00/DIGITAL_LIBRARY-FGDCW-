<?php
session_start();
include 'db.php';

$error         = '';
$report        = [];
$username      = '';
$recordCleared = false;
$userDeleted   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $action     = $_POST['action'] ?? '';

    // 1) Fetch user name
    $u = $conn->prepare("SELECT name FROM patrons WHERE id = ?");
    $u->bind_param("i",$student_id);
    $u->execute();
    $ur = $u->get_result();
    if ($ur->num_rows) {
        $username = $ur->fetch_assoc()['name'];
    } else {
        $error = "No such user.";
    }

    if (!$error && $action === 'view') {
        // 2) Load report
        $s = $conn->prepare(
          "SELECT b.title, l.issue_date, l.return_date, l.status, l.fine
           FROM loans l
           JOIN books b ON l.book_id=b.id
           WHERE l.user_id=?"
        );
        $s->bind_param("i",$student_id);
        $s->execute();
        $report = $s->get_result()->fetch_all(MYSQLI_ASSOC);

    } elseif (!$error && $action === 'clear') {
        // 3) Check that every loan is returned and fine=0
        $allReturned = true;
        foreach ($report as $r) {
            if ($r['return_date']===null || $r['fine']>0) {
                $allReturned = false; break;
            }
        }
        if ($allReturned) {
            $recordCleared = true;
        } else {
            $error = "Cannot clear record until all books are returned and fines paid.";
        }

    } elseif (!$error && $action === 'delete_user') {
        // 4) Delete user
        $d = $conn->prepare("DELETE FROM patrons WHERE id=?");
        $d->bind_param("i",$student_id);
        $d->execute();
        $userDeleted = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Student Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="text-center mb-4">Student Borrowing Report</h3>

  <?php if ($userDeleted): ?>
    <div class="alert alert-success">User <strong><?=htmlspecialchars($username)?></strong> deleted.</div>
  <?php else: ?>
    <form method="POST" class="mb-4">
      <div class="input-group">
        <input type="hidden" name="action" value="view">
        <input type="text" name="student_id" class="form-control" placeholder="Enter Student/User ID" required>
        <button type="submit" class="btn btn-primary">View Report</button>
      </div>
    </form>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($username && !$userDeleted): ?>
      <div class="alert alert-info text-center">
        <strong>Records for <?=htmlspecialchars($username)?></strong>
      </div>
    <?php endif; ?>

    <?php if (!empty($report)): ?>
      <table class="table table-bordered mb-4">
        <thead>
          <tr>
            <th>Book Title</th>
            <th>Issue Date</th>
            <th>Return Date</th>
            <th>Status</th>
            <th>Fine</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($report as $r): ?>
            <tr>
              <td><?=htmlspecialchars($r['title'])?></td>
              <td><?=$r['issue_date']?></td>
              <td><?= $r['return_date'] ?: 'Not Returned' ?></td>
              <td><?=$r['status']?></td>
              <td><?= $r['fine']>0 ? '₹'.$r['fine'] : 'None' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Clear Record -->
      <?php if (!$recordCleared): ?>
        <form method="POST" class="mb-3">
          <input type="hidden" name="action" value="clear">
          <input type="hidden" name="student_id" value="<?=$student_id?>">
          <button type="submit" class="btn btn-success">Clear Record</button>
        </form>
      <?php endif; ?>

      <!-- Delete User (only after clear) -->
      <?php if ($recordCleared): ?>
        <div class="alert alert-success">All books returned & fines paid. Record is clear.</div>
        <form method="POST">
          <input type="hidden" name="action" value="delete_user">
          <input type="hidden" name="student_id" value="<?=$student_id?>">
          <button type="submit" class="btn btn-danger">Delete User</button>
        </form>
      <?php endif; ?>

    <?php elseif ($username && empty($report)): ?>
      <div class="alert alert-warning">No borrowing records found for <strong><?=htmlspecialchars($username)?></strong>.</div>
    <?php endif; ?>
  <?php endif; ?>
</div>
</body>
</html>
