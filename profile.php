<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "library";

// Connect to MySQL
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch one user (you can modify this to get specific user via session or GET param)
$sql = "SELECT name, id, email, department FROM users LIMIT 1";
$result = $conn->query($sql);

// Initialize variables
$name = $studentId = $email = $department = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["name"];
    $studentId = $row["id"];
    $email = $row["email"];
    $department = $row["department"];
} else {
    echo "No user found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: rgba(245, 245, 245, 0.4);
            background-image: url("https://img.freepik.com/free-photo/abundant-collection-antique-books-wooden-shelves-generated-by-ai_188544-29660.jpg?size=626&ext=jpg");
            background-size: cover;
            background-position: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px gray;
        }

        .navbar {
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
</head>
<body>

    <!-- Custom Navbar Start -->
    <nav class="navbar">
        <div class="navbar-brand">
            <img src="log.png" alt="Logo">
            <span>Digital Library</span>
        </div>
        <ul class="nav-links">
            <li><a href="a.html">Dashboard</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </nav>
    <!-- Custom Navbar End -->

    <div class="container mt-4">
        <h4 class="text-center">Profile Details</h4>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form>
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($name); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="studentid">Student ID:</label>
                        <input type="text" class="form-control" id="studentid" value="<?php echo htmlspecialchars($studentId); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="department">Department:</label>
                        <input type="text" class="form-control" id="department" value="<?php echo htmlspecialchars($department); ?>" disabled>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
