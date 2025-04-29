<?php
session_start();
include 'db.php';

$error = '';
$report = [];
$username = '';
$recordCleared = false;
$userDeleted = false;
$student_id = 0;
$action = '';
$showActions = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    // Fetch username
    $u = $conn->prepare("SELECT name FROM patrons WHERE id = ?");
    $u->bind_param("i", $student_id);
    $u->execute();
    $ur = $u->get_result();

    if ($ur->num_rows) {
        $username = $ur->fetch_assoc()['name'];
    } else {
        $error = "No such user.";
    }

    if (!$error && $action === 'view') {
        $s = $conn->prepare("SELECT b.title, l.issue_date, l.return_date, l.status, l.fine FROM loans l JOIN books b ON l.book_id=b.id WHERE l.user_id=?");
        $s->bind_param("i", $student_id);
        $s->execute();
        $report = $s->get_result()->fetch_all(MYSQLI_ASSOC);
        $showActions = true;

    } elseif (!$error && $action === 'clear') {
        $s = $conn->prepare("SELECT return_date, fine FROM loans WHERE user_id=?");
        $s->bind_param("i", $student_id);
        $s->execute();
        $results = $s->get_result();
        $allReturned = true;
        while ($row = $results->fetch_assoc()) {
            if (is_null($row['return_date']) || $row['fine'] > 0) {
                $allReturned = false;
                break;
            }
        }
        if ($allReturned) {
            $recordCleared = true;
        } else {
            $error = "Cannot clear record until all books are returned and fines paid.";
        }
        $showActions = true;

    } elseif (!$error && $action === 'delete_user') {
        $d = $conn->prepare("DELETE FROM patrons WHERE id=?");
        $d->bind_param("i", $student_id);
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
    <h3 class="mb-4">Student Report</h3>

    <?php if ($userDeleted): ?>
        <div class="alert alert-success">User deleted successfully.</div>
    <?php endif; ?>

    <form method="POST" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="number" name="student_id" value="<?= htmlspecialchars($student_id) ?>" class="form-control" placeholder="Student ID" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="action" value="view" class="btn btn-primary w-100">View Report</button>
            </div>
        </div>
    </form>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($username): ?>
        <div class="alert alert-info"><strong>Username:</strong> <?= htmlspecialchars($username) ?></div>
    <?php endif; ?>

    <?php if (!empty($report)): ?>
        <table class="table table-bordered">
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
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= $r['issue_date'] ?></td>
                    <td><?= $r['return_date'] ?? 'Not Returned' ?></td>
                    <td><?= $r['status'] ?></td>
                    <td><?= $r['fine'] > 0 ? '₹' . $r['fine'] : 'None' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($username && $action === 'view'): ?>
        <div class="alert alert-warning">No records found for this user.</div>
    <?php endif; ?>

    <?php if ($username && $action === 'view'): ?>
        <form method="POST" class="mt-3 d-flex gap-3">
            <input type="hidden" name="student_id" value="<?= $student_id ?>">
            <button type="submit" name="action" value="clear" class="btn btn-success">Clear Record</button>
            <button type="submit" name="action" value="delete_user" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete User</button>
        </form>
    <?php endif; ?>

    <?php if ($recordCleared): ?>
        <div class="alert alert-success mt-3">✅ All books returned and no fines pending. Record is clear.</div>
    <?php endif; ?>
</div>
</body>
</html>
