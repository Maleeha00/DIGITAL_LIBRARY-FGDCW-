<?php
include 'db.php'; // make sure this connects to your database

$successMessage = "";

// Create notifications table if not exists
$conn->query("
    CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $user_id = (int)$_POST['user_id']; // Ensure integer input

    if ($user_id === 0) {
        // Send to all patrons
        $users = $conn->query("SELECT id FROM patrons");
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");

        while ($row = $users->fetch_assoc()) {
            $uid = $row['id'];
            $stmt->bind_param("is", $uid, $message);
            $stmt->execute();
        }

        $successMessage = "✅ Notification sent to all users!";
    } else {
        // Send to specific user
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();

        $successMessage = "✅ Notification sent to user ID {$user_id}!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f2f6;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 80px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }
        textarea {
            resize: vertical;
        }
    </style>
</head>
<body>

<div class="container">
    <h3 class="mb-4 text-center">📣 Send Notification</h3>

    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <div class="card p-4">
        <form method="POST">
            <div class="mb-3">
                <label for="message" class="form-label">🔔 Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label for="user_id" class="form-label">👤 Send to (User ID, or 0 for All)</label>
                <input type="number" class="form-control" id="user_id" name="user_id" value="0" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Send Notification</button>
        </form>
    </div>
</div>

</body>
</html>
