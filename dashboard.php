<?php 
// Start the session to use session variables
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = "Iqra Noureen"; // Default name
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];

    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Library Dashboard</title>
  
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"/>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;500&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f4f8, #dfe6e9);
      margin: 0;
      padding: 0;
      color: #444;
    }

    nav {
      background-color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
      display: flex;
      align-items: center;
      font-family: 'Pacifico', cursive;
      font-size: 2rem;
      color: #2c3e50;
      letter-spacing: 1px;
    }

    .navbar-brand img {
      width: 45px;
      height: 45px;
      margin-right: 12px;
    }

    .tabs {
      display: flex;
      justify-content: center;
      gap: 25px;
    }

    .tab {
      padding: 10px 20px;
      font-weight: 500;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
      color: black;
      text-decoration: none;
    }

    .tab:hover {
      color: #f39c12;
      border-color: #f39c12;
    }

    .tab.active {
      border-color: #f39c12;
      color: #f39c12;
    }

    .notification-dropdown-menu {
      position: absolute;
      transform: translateX(-50%);
      left: 50%;
      min-width: 250px;
      top: 48px !important;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      border-radius: 6px;
      padding: 10px 0;
      z-index: 1000;
    }

    .dropdown-item {
      background-color: #f8f9fa;
      padding: 12px;
      border-bottom: 1px solid #ddd;
    }

    .dropdown-item button {
      background: none;
      border: none;
      color: #333;
      font-size: 18px;
      float: right;
      cursor: pointer;
    }

    .dropdown-item button:hover {
      color: red;
    }

    .table-container {
      margin: 40px auto;
      max-width: 90%;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .table {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .table th,
    .table td {
      text-align: center;
      padding: 15px;
    }

    .table thead {
      background: linear-gradient(45deg, #34495e, #2c3e50);
      color: white;
    }

    .welcome-message {
      margin-top: 30px;
      text-align: center;
      font-size: 1.8rem;
      font-weight: 600;
      color: #2c3e50;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav>
    <a class="navbar-brand" href="#">
      <img src="log.png" alt="Logo"> Digital Library
    </a>

    <div class="tabs">
      <a href="profile.php" class="tab"><i class="bi bi-person"></i> My Profile</a>
      <a href="edit_profile.php" class="tab"><i class="bi bi-pencil-square"></i> Edit Profile</a>
      <a href="change_password.php" class="tab"><i class="bi bi-key"></i> Change Password</a>
      <a href="request_book.php" class="tab"><i class="bi bi-book"></i> Request Book</a>

      <!-- Notification Dropdown -->
      <div class="dropdown">
        <a class="tab" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-bell"></i> Notifications
        </a>
        <ul class="dropdown-menu notification-dropdown-menu" aria-labelledby="notificationDropdown">
          <li class="dropdown-item">New book added. <button onclick="this.parentElement.remove()">❌</button></li>
          <li class="dropdown-item">Reminder: Return book soon. <button onclick="this.parentElement.remove()">❌</button></li>
          <li class="dropdown-item">Library will close at 5 PM. <button onclick="this.parentElement.remove()">❌</button></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Welcome -->
  <div class="welcome-message">Welcome, <strong><?php echo htmlspecialchars($name); ?></strong>!</div>

  <!-- Table -->
  <div class="table-container">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Book Title</th>
          <th>Issue Date</th>
          <th>Return Date</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>Introduction to AI</td><td>March 1, 2025</td><td>April 1, 2025</td></tr>
        <tr><td>Web Dev with PHP</td><td>Feb 20, 2025</td><td>Mar 20, 2025</td></tr>
        <tr><td>Database Systems</td><td>Jan 15, 2025</td><td>Feb 15, 2025</td></tr>
        <tr><td>Machine Learning Basics</td><td>Mar 5, 2025</td><td>Apr 5, 2025</td></tr>
        <tr><td>Cloud Computing</td><td>Feb 25, 2025</td><td>Mar 25, 2025</td></tr>
        <tr><td>Big Data Analysis</td><td>Jan 10, 2025</td><td>Feb 10, 2025</td></tr>
        <tr><td>Python Programming</td><td>Feb 12, 2025</td><td>Mar 12, 2025</td></tr>
        <tr><td>Cyber Security</td><td>Mar 2, 2025</td><td>Apr 2, 2025</td></tr>
        <tr><td>Linux Fundamentals</td><td>Feb 1, 2025</td><td>Mar 1, 2025</td></tr>
        <tr><td>JavaScript Deep Dive</td><td>Mar 10, 2025</td><td>Apr 10, 2025</td></tr>
        <tr><td>Operating Systems</td><td>Jan 22, 2025</td><td>Feb 22, 2025</td></tr>
        <tr><td>Discrete Mathematics</td><td>Feb 18, 2025</td><td>Mar 18, 2025</td></tr>
        <tr><td>Data Structures</td><td>Mar 3, 2025</td><td>Apr 3, 2025</td></tr>
        <tr><td>Network Security</td><td>Feb 14, 2025</td><td>Mar 14, 2025</td></tr>
        <tr><td>Advanced Java</td><td>Mar 8, 2025</td><td>Apr 8, 2025</td></tr>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
