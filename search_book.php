<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Search Books</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
  <style>
    body {
      background-color: #f8f9fa;
      font-family: Arial, sans-serif;
    }
    .container {
      margin-top: 80px;
      max-width: 600px;
    }
    .card {
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      background: #fff;
    }
    .form-control, .form-select {
      border-radius: 10px;
    }
    .btn-search {
      background-color: #ffc107;
      color: #000;
      border-radius: 10px;
      font-weight: bold;
    }
    .btn-search:hover {
      background-color: #e0a800;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card">
    <h3 class="text-center mb-4">Search Books</h3>
    <form action="search_results.php" method="GET">
      <div class="mb-3">
        <label for="keyword" class="form-label">Keyword (Title / Author / ISBN)</label>
        <input type="text" class="form-control" name="keyword" id="keyword" placeholder="Enter keyword..." required>
      </div>
      <div class="mb-4">
        <label for="category" class="form-label">Category (optional)</label>
        <select class="form-select" name="category" id="category">
          <option value="">-- All Categories --</option>
          <option value="Fiction">Fiction</option>
          <option value="Non-fiction">Non-fiction</option>
          <option value="Science">Science</option>
          <option value="Technology">Technology</option>
          <option value="History">History</option>
          <!-- Add more categories as needed -->
        </select>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-search">Search</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
