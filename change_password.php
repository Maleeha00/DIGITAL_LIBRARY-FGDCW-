<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "library";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// For now using hardcoded user ID (replace with session in real app)
$userId = 8;

$successMsg = $errorMsg = "";

// Form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errorMsg = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMsg = "New password and confirm password do not match.";
    } else {
        // Fetch user's current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($dbPassword);
        $stmt->fetch();
        $stmt->close();

        // Compare current password with database (plain comparison)
        if ($currentPassword !== $dbPassword) {
            $errorMsg = "Current password is incorrect.";
        } else {
            // Update to new password
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $newPassword, $userId);
            if ($stmt->execute()) {
                $successMsg = "Password updated successfully.";
            } else {
                $errorMsg = "Failed to update password.";
            }
            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-image: url('bg.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
            margin-top: 50px;
            max-width: 400px;
        }
        .password-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
    </style>
</head>
<body>

<div class="container">
    <h4 class="text-center">Change Password</h4>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo $successMsg; ?></div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Current Password:</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" name="current_password" required id="current">
                <i class="fas fa-eye toggle-password" toggle="#current"></i>
            </div>
        </div>
        <div class="form-group">
            <label>New Password:</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" name="new_password" required id="new">
                <i class="fas fa-eye toggle-password" toggle="#new"></i>
            </div>
        </div>
        <div class="form-group">
            <label>Confirm New Password:</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" name="confirm_password" required id="confirm">
                <i class="fas fa-eye toggle-password" toggle="#confirm"></i>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Change Password</button>
    </form>
</div>

<script>
    // Toggle show/hide password
    document.querySelectorAll(".toggle-password").forEach(function (icon) {
        icon.addEventListener("click", function () {
            const input = document.querySelector(this.getAttribute("toggle"));
            const type = input.getAttribute("type") === "password" ? "text" : "password";
            input.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    });
</script>

</body>
</html>
