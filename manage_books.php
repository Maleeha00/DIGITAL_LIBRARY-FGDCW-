<?php
session_start();
$conn = new mysqli("localhost", "root", "", "library");  // Changed database name here
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $author = $_POST["author"];
    $isbn = $_POST["isbn"];
    $category = $_POST["category"];

    // Prepare the SQL query without the id field
    $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $author, $isbn, $category); // Corrected binding
    $stmt->execute();
    $stmt->close();
}

// Fetch all books
$result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-section {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .table-section {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
        }
        h2 {
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">📚 Manage Books</h2>

    <!-- Add Book Form -->
    <div class="form-section">
        <form method="post">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Book Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Author</label>
                    <input type="text" name="author" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <input type="number" name="category" class="form-control" required>
                </div>
            </div>
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success px-4">Add Book</button>
            </div>
        </form>
    </div>

    <!-- Book List -->
    <div class="table-section mt-4">
        <h5>📖 Book Inventory</h5>
        <table class="table table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>ISBN</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['category']) ?></td>
                        <td><?= htmlspecialchars($book['isbn']) ?></td>
                        <td>
                            <!-- Edit and Delete Buttons -->
                            <a href="edit_book.php?id=<?= $book['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_book.php?id=<?= $book['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
