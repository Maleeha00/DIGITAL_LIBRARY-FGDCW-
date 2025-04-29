<?php
// Start session and DB connection
session_start();
$conn = new mysqli("localhost", "root", "", "library");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_stmt = $conn->prepare("DELETE FROM patrons WHERE id = ?");
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        $delete_stmt->close();
        header("Location: ".$_SERVER['PHP_SELF']); // Refresh the page
        exit();
    } else {
        echo "<script>alert('Error deleting user.');</script>";
    }
}

// Handle user addition
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    // Check if fields are empty
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Generate a new user ID (e.g., STD001, STD002, etc.)
        $stmt = $conn->prepare("SELECT MAX(id) AS max_id FROM patrons");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $max_id = $result['max_id'] ? $result['max_id'] + 1 : 1;
        $user_id = "STD" . str_pad($max_id, 4, '0', STR_PAD_LEFT);

        // Insert new user with user_id
        $stmt = $conn->prepare("INSERT INTO patrons (user_id, name, email, password, registered_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $user_id, $name, $email, $password_hash);
        if ($stmt->execute()) {
            echo "<script>alert('User added successfully!');</script>";
            $stmt->close();
        } else {
            echo "<script>alert('Error adding user.');</script>";
        }
    }
}

// Fetch all users
$sql = "SELECT * FROM patrons ORDER BY registered_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patron List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body class="p-4 bg-light">
    <div class="container">
        <h2 class="mb-4">📋 Registered Users</h2>

        <!-- Add New User Form -->
        <div class="mb-4">
            <h4>Add New User</h4>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Add User</button>
                </div>
            </form>
        </div>

        <!-- Patron List -->
        <h4 class="mb-3">User List</h4>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= $row['registered_at'] ?></td>
                            <td>
                                <a href="?delete_id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No users registered yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
