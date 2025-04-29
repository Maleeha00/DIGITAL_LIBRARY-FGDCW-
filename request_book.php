<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch books for dropdown
$sql = "SELECT * FROM resources";
$result = $conn->query($sql);

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Handle form submission
if (isset($_POST['request_book'])) {
    $book_id = $_POST['book_id'];
    $user_email = $_SESSION['user_email']; // Assuming the user's email is stored in session

    // Insert request into the database
    $stmt = $conn->prepare("INSERT INTO book_requests (book_id, user_email) VALUES (?, ?)");
    $stmt->bind_param("ss", $book_id, $user_email);
    if ($stmt->execute()) {
        echo "<script>alert('Request submitted successfully for Book ID: $book_id'); window.location.href='books.php';</script>";
    } else {
        echo "<script>alert('Error submitting request. Please try again later.');</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request a Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5dc;
            text-align: center;
        }
        .navbar-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .navbar-brand span {
            font-family: 'Pacifico', cursive;
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: #007BFF;
        }
        .request-form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
        }
        .request-form {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            width: 400px;
        }
        .request-form h2 {
            margin-bottom: 20px;
        }
        .request-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            text-align: left; /* Left align text */
        }
        .request-form button {
            background-color: #b68e4f; /* Light brown color */
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
        }
        .request-form button:hover {
            background-color: #9e7a3d;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar-custom">
    <div class="navbar-brand">
        <img src="log.png" alt="Logo">
        <span>Digital Library</span>
    </div>
    <ul class="nav-links">
        <li><a href="dash.php">Dashboard</a></li>
    </ul>
</nav>

<!-- Request Form -->
<div class="request-form-container">
    <form class="request-form" method="POST" action="">
        <h2>Request a Book</h2>
        <select name="book_id" required>
            <option value="">Select Book</option>
            <?php foreach ($books as $book): ?>
                <option value="<?= htmlspecialchars($book['book_no']) ?>">
                    <?= htmlspecialchars($book['book_no'] . ' - ' . $book['book_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="request_book">Submit Request</button>
    </form>
</div>

</body>
</html>
