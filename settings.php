<?php
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? "Guest";
$user_email = "";

// Database connection
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user email from database
if ($user_id) {
    $sql = "SELECT email FROM patrons WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_email);
    $stmt->fetch();
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f1f2f6;
        }
        .settings-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.05);
        }
        h3 {
            font-weight: bold;
            color: #2f3542;
        }
        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .dark-mode {
            background-color: #1e272e;
            color: #dcdde1;
        }

        .dark-mode .settings-box {
            background-color: #2f3640;
            color: #f5f6fa;
        }

        .dark-mode input,
        .dark-mode .form-control {
            background-color: #353b48;
            color: #f5f6fa;
            border-color: #4b6584;
        }

    </style>
</head>
<body>
<div class="container mt-5 mb-5">
    <h3 class="mb-4">⚙️ Settings</h3>

    <div class="settings-box">

        <!-- Section: Profile Info -->
        <h5 class="section-title">👤 Profile Info</h5>
        <form>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user_name) ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user_email) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Info</button>
        </form>

        <!-- Section: Change Password -->
        <h5 class="section-title">🔒 Change Password</h5>
        <form>
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Change Password</button>
        </form>

        <!-- Section: Preferences -->
        <h5 class="section-title">🌗 Appearance</h5>
        <form>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                <label class="form-check-label" for="darkModeSwitch">Enable Dark Mode</label>
            </div>
        </form>

        <!-- Section: Notifications -->
        <h5 class="section-title">🔔 Notification Settings</h5>
        <form>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" checked>
                <label class="form-check-label">Email me when books are overdue</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox">
                <label class="form-check-label">Send newsletter updates</label>
            </div>
        </form>

        <!-- Section: Danger Zone -->
        <h5 class="section-title text-danger">⚠️ Danger Zone</h5>
        <button class="btn btn-outline-danger">Delete My Account</button>

    </div>
</div>
<script>
    // Check for saved dark mode preference
    const darkModeEnabled = localStorage.getItem("darkMode") === "true";

    // Apply on page load
    if (darkModeEnabled) {
        document.body.classList.add("dark-mode");
        document.getElementById("darkModeSwitch").checked = true;
    }

    // Toggle switch event
    document.getElementById("darkModeSwitch").addEventListener("change", function () {
        document.body.classList.toggle("dark-mode");
        localStorage.setItem("darkMode", this.checked);
    });
</script>

</body>
</html>
