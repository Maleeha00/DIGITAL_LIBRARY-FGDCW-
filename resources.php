<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";

// Fetch books with or without search
$sql = "SELECT * FROM resources";
if ($search) {
    $sql .= " WHERE book_name LIKE '%$search%' OR author LIKE '%$search%' OR publisher_name LIKE '%$search%'";
}
$result = $conn->query($sql);
$books = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Resources</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5dc; text-align: center; font-family: Arial; }
        h1 { margin-top: 30px; }
        table { width: 80%; margin: 30px auto; border-collapse: collapse; background-color: white; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #8b5e3c; color: white; }
        .navbar { padding: 20px 15px; font-size: 17px; }
        .search-bar { display: flex; justify-content: center; margin: 30px 0; }
        .search-bar input { width: 500px; padding: 10px; border: 2px solid #8b5e3c; border-radius: 20px 0 0 20px; outline: none; }
        .search-bar button { padding: 10px 20px; border: none; background-color: #8b5e3c; color: white; font-weight: bold; border-radius: 0 20px 20px 0; }
        .search-bar button:hover { background-color: #5a3d2b; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <b><a class="navbar-brand">Digital Library System</a></b>
    <div class="ms-auto d-flex align-items-center">
        <b><a href="user_interface.php" class="text-white text-decoration-none me-3">My Dashboard</a></b>
        <b><a href="#" class="text-white text-decoration-none">Logout</a></b>
    </div>
</nav>

<!-- Search -->
<div class="search-bar">
    <form method="GET" action="resources.php" class="d-flex">
        <input type="text" name="search" placeholder="Search books, authors, publishers..." value="<?= htmlspecialchars($search) ?>" />
        <button type="submit">Search</button>
    </form>
</div>

<h1>Library Books</h1>
<table>
    <tr>
        <th>Book No</th>
        <th>Book Name</th>
        <th>Author</th>
        <th>Publisher Name</th>
    </tr>
    <?php if (count($books) > 0): ?>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= htmlspecialchars($book['book_no']) ?></td>
                <td><?= htmlspecialchars($book['book_name']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= htmlspecialchars($book['publisher_name']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4">No results found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
