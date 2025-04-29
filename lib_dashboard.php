<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total books
$total_books_result = $conn->query("SELECT COUNT(*) AS total FROM books");
$total_books = $total_books_result->fetch_assoc()['total'];

// Get username
$username = 'HINA';  // No session, so defaulting to 'Guest'
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Librarian Dashboard</title>
  
  <!-- Bootstrap and Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #eef2f5;
    }
    .sidebar {
      width: 200px;
      background: linear-gradient(to bottom, #1d3557, #457b9d);
      color: white;
      height: 100vh;
      position: fixed;
      padding-top: 20px;
      transition: all 0.3s ease;
      border-radius: 0 10px 10px 0;
      z-index: 1000;
      left: 0;
    }
    .sidebar a {
      display: block;
      color: white;
      padding: 15px;
      text-decoration: none;
      transition: 0.3s;
      border-radius: 5px;
    }
    .sidebar a:hover,
    .sidebar .active {
      background: #a8dadc;
      color: #1d3557;
    }
    .sidebar .logo {
      font-size: 22px;
      font-weight: bold;
      text-align: center;
      margin-bottom: 30px;
    }
    .main-content {
      margin-left: 220px;
      padding: 20px;
    }
    .stat-card {
      background: #457b9d;
      color: white;
      padding: 20px;
      text-align: center;
      border-radius: 10px;
    }
    .icon {
      font-size: 30px;
      margin-bottom: 10px;
    }
    .logout-button {
      position: absolute;
      bottom: 90px;
      left: 20px;
      background: #1d3557;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
    }
    .logout-button:hover {
      background: #a8dadc;
      color: #1d3557;
    }
    /* Navbar */
    .skin-navbar {
      background-color: rgb(195, 224, 245);
    }
    .navbar-brand img {
      width: 50px;
      height: 50px;
      margin-right: 10px;
      border-radius: 50%;
    }
    .navbar-brand span {
      font-family: 'Pacifico', cursive;
      font-size: 24px;
    }
    .nav-link {
      font-weight: 500;
    }
    .welcome-msg {
      font-weight: 600;
      text-align: center;
    }
    /* Responsive Sidebar */
    @media (max-width: 768px) {
      .sidebar {
        left: -220px;
      }
      .sidebar.active {
        left: 0;
      }
      .main-content {
        margin-left: 0 !important;
      }
    }
    /* Overlay for mobile */
    #overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 900;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg skin-navbar">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center text-dark" href="#">
        <img src="log.png" alt="Logo" />
        <span>Digital Library</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
        <span class="welcome-msg text-dark me-3">Welcome: <?= htmlspecialchars($username) ?></span>
      </div>
    </div>
  </nav>

  <!-- Toggle Button for Sidebar -->
  <button class="btn btn-dark d-md-none m-3" id="toggleSidebar">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="logo">📚 Librarian</div>
    <a href="manage_books.php"><i class="fas fa-book"></i> Manage Books</a>
    <a href="search_book.php"><i class="fas fa-search"></i> Search Books</a>
    <a href="patrons.php"><i class="fas fa-user"></i> Users</a>
    <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
    <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Overlay -->
  <div id="overlay"></div>

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <div class="dashboard-header">
      <h1>Dashboard</h1>
    </div>

    <!-- Dashboard Stats -->
    <div class="row mt-4">
      <div class="col-md-3">
        <div class="stat-card">
          <i class="fas fa-book icon"></i>
          <h4>Total Books</h4>
          <p><?= $total_books ?></p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="fas fa-user icon"></i>
          <h4>Active Patrons</h4>
          <p>0</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="fas fa-hand-holding icon"></i>
          <h4>Books Issued</h4>
          <p>0</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <i class="fas fa-clock icon"></i>
          <h4>Overdue Books</h4>
          <p>0</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <i class="fas fa-book-reader fa-2x text-primary"></i>
          <h5 class="mt-2">Issue Book</h5>
          <p>Manage book lending process.</p>
          <a href="issue_book.php" class="btn btn-primary">Go</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <i class="fas fa-book-open fa-2x text-primary"></i>
          <h5 class="mt-2">Return Book</h5>
          <p>Process returned books.</p>
          <a href="return_book.php" class="btn btn-primary">Go</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <i class="fas fa-bell fa-2x text-primary"></i>
          <h5 class="mt-2">Send Notifications</h5>
          <p>Remind patrons of due dates.</p>
          <a href="send_notifications.php" class="btn btn-primary">Go</a>
        </div>
      </div>
    </div>

    <!-- Reports Section -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card p-4 text-center">
          <i class="fas fa-chart-bar fa-2x text-secondary"></i>
          <h5 class="mt-2">Circulation Statistics</h5>
          <p>Track borrowing trends.</p>
          <a href="circulation_statistics.php" class="btn btn-secondary">View</a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-4 text-center">
          <i class="fas fa-chart-pie fa-2x text-secondary"></i>
          <h5 class="mt-2">Inventory Report</h5>
          <p>Monitor library stock.</p>
          <a href="inventory_report.php" class="btn btn-secondary">View</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Script -->
  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const toggleBtn = document.getElementById('toggleSidebar');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
      document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : 'auto';
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('active');
      overlay.style.display = 'none';
      document.body.style.overflow = 'auto';
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.
