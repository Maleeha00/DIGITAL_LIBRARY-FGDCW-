<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";

// Fetch books
$sql = "SELECT * FROM resources";
if ($search) {
    $sql .= " WHERE book_name LIKE '%$search%' OR author LIKE '%$search%' OR publisher_name LIKE '%$search%'";
}
$result = $conn->query($sql);

// Store all books
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Resources</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f5f5dc;
        }
        .container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 50px;
        }
        h1 {
            margin-top: 20px;
        }
        .almira {
            position: relative;
            width: 200px;
            height: 300px;
            background-color: brown;
            border: 5px solid #5a3d2b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .almira-name {
            margin-top: 10px;
            font-weight: bold;
        }
        .door {
            position: absolute;
            width: 50%;
            height: 100%;
            background-color: #8b5e3c;
            border: 2px solid #5a3d2b;
            transition: transform 0.8s ease-in-out;
        }
        .left-door { left: 0; }
        .right-door { right: 0; }
        .handle {
            position: absolute;
            width: 10px;
            height: 30px;
            background-color: black;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        table {
            width: 80%;
            margin: 50px auto;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #8b5e3c;
            color: white;
        }
        .close-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .navbar {
            padding: 20px 15px;
            font-size: 17px;
        }
        .navbar a {
            margin-right: 15px;
        }
        .search-bar {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        .search-bar input {
            width: 500px;
            padding: 10px;
            border: 2px solid #8b5e3c;
            border-radius: 20px 0 0 20px;
            outline: none;
            background-color: #fff;
            color: #333;
        }
        .search-bar button {
            padding: 10px 20px;
            border: none;
            background-color: #8b5e3c;
            color: white;
            font-weight: bold;
            border-radius: 0 20px 20px 0;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #5a3d2b;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <b><a class="navbar-brand">Digital Library System</a></b>
    <div class="ms-auto d-flex align-items-center">
        <b><a href="user_interface.php" class="text-white text-decoration-none me-3">My Dashboard</a></b>
        <b><a href="#" class="text-white text-decoration-none">Logout</a></b>
    </div>
</nav>

<!-- Search -->
<div class="search-bar">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search books, authors, publishers..." value="<?= htmlspecialchars($search) ?>" />
        <button type="submit">Search</button>
    </form>
</div>

<!-- Almira Section -->
<div class="container">
    <?php
    $shelves = ['English', 'Urdu', 'Computer', 'Science', 'Novels', 'History'];
    foreach ($shelves as $shelf): ?>
        <div>
            <div class="almira" onclick="showBooks(this)">
                <div class="door left-door"></div>
                <div class="door right-door"></div>
                <div class="handle"></div>
            </div>
            <div class="almira-name"><?= htmlspecialchars($shelf) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Book Section -->
<div id="book-section" style="display:none;">
    <div id="table-All" style="display:none;">
        <h1>All Books</h1>
        <table>
            <tr>
                <th>Book No</th>
                <th>Book Name</th>
                <th>Author</th>
                <th>Publisher</th>
            </tr>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['book_no']) ?></td>
                    <td><?= htmlspecialchars($book['book_name']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['publisher_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <button class="close-btn" onclick="goBack()">Back to Library</button>
    </div>
</div>

<script>
function showBooks(element) {
    let leftDoor = element.querySelector(".left-door");
    let rightDoor = element.querySelector(".right-door");
    leftDoor.style.transform = "translateX(-100%)";
    rightDoor.style.transform = "translateX(100%)";

    setTimeout(() => {
        document.querySelector('.container').style.display = 'none';
        document.querySelector('.search-bar').style.display = 'none';
        document.getElementById('book-section').style.display = 'block';
        document.getElementById('table-All').style.display = 'block';
    }, 900);
}

function goBack() {
    document.querySelector('.container').style.display = 'flex';
    document.querySelector('.search-bar').style.display = 'flex';
    document.getElementById('book-section').style.display = 'none';
    document.getElementById('table-All').style.display = 'none';
}
</script>

</body>
</html>
