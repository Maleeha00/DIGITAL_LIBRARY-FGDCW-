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

// Hardcoded user ID (replace with session variable later)
$userId = 8;

$name = $email = $studentId = $department = "";
$successMsg = $errorMsg = "";

// Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $studentId = trim($_POST["studentid"]);
    $department = trim($_POST["department"]);

    // Validations
    if (empty($name) || empty($email) || empty($studentId) || empty($department)) {
        $errorMsg = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, id=?, department=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $email, $studentId, $department, $userId);
        if ($stmt->execute()) {
            $successMsg = "Profile updated successfully.";
        } else {
            $errorMsg = "Error updating profile: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch user data
$sql = "SELECT name, email, id as studentid, department FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $email, $studentId, $department);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
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
    </style>
</head>
<body>

<div class="container">
    <h4 class="text-center">Edit Profile</h4>

    <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo $successMsg; ?></div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label>Student ID:</label>
            <input type="text" class="form-control" name="studentid" value="<?php echo htmlspecialchars($studentId); ?>" required>
        </div>
        <div class="form-group">
            <label>Department:</label>
            <input type="text" class="form-control" name="department" value="<?php echo htmlspecialchars($department); ?>" required>
        </div>
        <button type="submit" class="btn btn-success btn-block">Save Changes</button>
    </form>
</div>

</body>
</html>
