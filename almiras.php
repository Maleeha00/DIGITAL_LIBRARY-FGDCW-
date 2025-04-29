<?php 
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search if AJAX request
if (isset($_POST['action']) && $_POST['action'] == 'search') {
    $search = $conn->real_escape_string($_POST['search']);
    // Case insensitive search using LOWER() for book_name, author, and publisher_name
    $sql = "SELECT * FROM resources WHERE 
            LOWER(book_name) LIKE LOWER('%$search%') 
            OR LOWER(author) LIKE LOWER('%$search%') 
            OR LOWER(publisher_name) LIKE LOWER('%$search%')";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($book = $result->fetch_assoc()) {
            echo "<tr>
                <td>".htmlspecialchars($book['book_no'])."</td>
                <td>".htmlspecialchars($book['book_name'])."</td>
                <td>".htmlspecialchars($book['author'])."</td>
                <td>".htmlspecialchars($book['publisher_name'])."</td>
                <td>";
            if (strtolower($book['status']) == 'available') {
                echo "<span style='color:green; font-weight:bold;'>Available</span>";
            } else {
                echo "<span style='color:red; font-weight:bold;'>Not Available</span>";
            }
            echo "</td>
                <td><a href='request_book.php' class='btn btn-sm' style='background-color: #4b2a2a; color: white;'>Request Book</a></td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No books found.</td></tr>";
    }
    exit;
}

// Fetch all books initially
$sql = "SELECT * FROM resources";
$result = $conn->query($sql);
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- jQuery added -->

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
            font-weight: normal; /* Unbold heading */
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
            width: 90%;
            margin: 50px auto;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
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

<!-- Navbar -->
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<style>
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
        font-weight: normal; /* Unbold */
    }
    .nav-links {
        list-style: none;
        display: flex;
        gap: 20px;
        margin: 0;
        padding: 0;
    }
    .nav-links li {
        display: inline;
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
</style>

<nav class="navbar-custom">
    <div class="navbar-brand">
        <img src="log.png" alt="Logo">
        <span>Digital Library</span>
    </div>
    <ul class="nav-links">
        <li><a href="dashboard.php">Dashboard</a></li>
    </ul>
</nav>

<!-- Search -->
<div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search books, authors, publishers...">
    <button type="button" id="searchBtn">Search</button>
</div>

<!-- Almira Section -->
<div class="container">
    <?php 
    $shelf_names = [
        "English", "Urdu", "Islamiat", "Computer", "IT",
        "Science", "Social Studies", "Biology", "Chemistry", "Physics",
        "Maths", "Ebooks", "History", "Geography", "General Knowledge"
    ];
    foreach ($shelf_names as $name): ?>
        <div>
            <div class="almira" onclick="showBooks(this)">
                <div class="door left-door"></div>
                <div class="door right-door"></div>
                <div class="handle"></div>
            </div>
            <div class="almira-name"><?= htmlspecialchars($name) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Book Section -->
<div id="book-section" style="display:none;">
    <div id="table-All" style="display:none;">
        <h1>All Books</h1>

        <table>
            <thead>
            <tr>
                <th>Book No</th>
                <th>Book Name</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Status</th>
                <th>Request</th>
            </tr>
            </thead>
            <tbody id="bookData">
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['book_no']) ?></td>
                    <td><?= htmlspecialchars($book['book_name']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['publisher_name']) ?></td>
                    <td>
                        <?php if (strtolower($book['status']) == 'available'): ?>
                            <span style="color:green; font-weight:bold;">Available</span>
                        <?php else: ?>
                            <span style="color:red; font-weight:bold;">Not Available</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="request_book.php" class="btn btn-sm" style="background-color: #4b2a2a; color: white;">Request Book</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <button class="close-btn" onclick="goBack()">Back to Library</button>

    </div>
</div>

<script>
// Open doors animation
function showBooks(element) {
    let leftDoor = element.querySelector(".left-door");
    let rightDoor = element.querySelector(".right-door");
    leftDoor.style.transform = "translateX(-100%)";
    rightDoor.style.transform = "translateX(100%)";

    setTimeout(() => {
        document.querySelector('.container').style.display = 'none';
        document.querySelector('.search-bar').style.display = 'flex';
        document.getElementById('book-section').style.display = 'block';
        document.getElementById('table-All').style.display = 'block';
    }, 900);
}

// Back button
function goBack() {
    window.location.href = '<?= $_SERVER['PHP_SELF'] ?>';
}

// Live search
$(document).ready(function(){
    $('#searchInput').keyup(function(){
        var search = $(this).val();
        $.ajax({
            url: '',
            method: 'POST',
            data: {action:'search', search:search},
            success: function(response){
                $('#bookData').html(response);
            }
        });
    });
    $('#searchBtn').click(function(){
        $('#searchInput').trigger('keyup');
    });
});
</script>

</body>
</html>
