<?php
session_start();
include 'db.php'; // Include your DB connection

$message = ""; // For status messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $borrower_id = $_POST['borrower_id'];
    $book_id = $_POST['book_id'];
    $loan_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime("+14 days"));

    // Check borrower exists
    $stmt = $conn->prepare("SELECT * FROM borrowers WHERE id = ?");
    $stmt->bind_param("i", $borrower_id);
    $stmt->execute();
    $borrower_result = $stmt->get_result();

    if ($borrower_result->num_rows == 0) {
        $message = "<div class='alert alert-danger'>Borrower not found.</div>";
    } else {
        // Check book availability
        $stmt = $conn->prepare("SELECT * FROM books WHERE id = ? AND status = 'available'");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $book_result = $stmt->get_result();

        if ($book_result->num_rows == 0) {
            $message = "<div class='alert alert-warning'>Book is not available.</div>";
        } else {
            // Issue the book
            $stmt = $conn->prepare("INSERT INTO loans (book_id, borrower_id, loan_date, due_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $book_id, $borrower_id, $loan_date, $due_date);
            $stmt->execute();

            // Update book status
            $stmt = $conn->prepare("UPDATE books SET status = 'checked out' WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();

            $message = "<div class='alert alert-success'>Book issued successfully!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Issue Book</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #00416A, #E4E5E6);
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      max-width: 600px;
      margin-top: 80px;
      background: #ffffff;
      color: #333;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .form-label {
      font-weight: bold;
    }

    .btn-custom {
      background-color: #00416A;
      color: white;
      border-radius: 30px;
      padding: 10px 30px;
    }

    .btn-custom:hover {
      background-color: #033b5c;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2 class="text-center mb-4">📚 Issue a Book</h2>

    <!-- Display messages -->
    <?php if (!empty($message)) echo $message; ?>

    <form action="issue_book.php" method="post">
      <div class="mb-3">
        <label for="borrower_id" class="form-label">Borrower ID</label>
        <input type="text" class="form-control" name="borrower_id" id="borrower_id" required>
      </div>

      <div class="mb-3">
        <label for="book_id" class="form-label">Book ID</label>
        <input type="text" class="form-control" name="book_id" id="book_id" required>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-custom">Issue Book</button>
      </div>
    </form>
  </div>

</body>
</html>
