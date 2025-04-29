<?php
session_start();
include 'db.php'; // Ensure this file is correctly named and in the same directory

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $borrower_id = $_POST['borrower_id'];
    $return_date = date('Y-m-d');

    // Check if the book was actually borrowed
    $loan_check = $conn->prepare("SELECT * FROM loans WHERE book_id = ? AND borrower_id = ? AND return_date IS NULL");
    $loan_check->bind_param("ii", $book_id, $borrower_id);
    $loan_check->execute();
    $result_loan = $loan_check->get_result();

    if ($result_loan->num_rows == 0) {
        $message = "<div class='alert alert-danger'>Error: No active loan record found for this book!</div>";
    } else {
        $loan_data = $result_loan->fetch_assoc();
        $due_date = $loan_data['due_date'];

        $fine_amount = 0;
        if (strtotime($return_date) > strtotime($due_date)) {
            $days_late = (strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24);
            $fine_amount = $days_late * 2; // $2 per day late fee
        }

        // Update the loan record
        $update_loan = $conn->prepare("UPDATE loans SET return_date = ?, fine_amount = ? WHERE book_id = ? AND borrower_id = ?");
        $update_loan->bind_param("sdii", $return_date, $fine_amount, $book_id, $borrower_id);
        $update_loan->execute();

        // Update the book status
        $update_book = $conn->prepare("UPDATE books SET status = 'available' WHERE id = ?");
        $update_book->bind_param("i", $book_id);
        $update_book->execute();

        $message = "<div class='alert alert-success'>Book returned successfully!";
        if ($fine_amount > 0) {
            $message .= " Fine due: <strong>$$fine_amount</strong>";
        }
        $message .= "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1c92d2, #f2fcfe);
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 500px;
            margin: 80px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .form-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 25px;
            color: #004080;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-title">Return a Book</div>
        <?= $message ?>
        <form action="return_book.php" method="post">
            <div class="mb-3">
                <label for="borrower_id" class="form-label">Borrower ID</label>
                <input type="number" class="form-control" name="borrower_id" required>
            </div>
            <div class="mb-3">
                <label for="book_id" class="form-label">Book ID</label>
                <input type="number" class="form-control" name="book_id" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Return Book</button>
        </form>
    </div>
</body>
</html>
