<?php
session_start();
include 'db.php'; // Ensure this connects to your database correctly

// Initialize inventory counts
$inventory = [
    'total_books' => 0,
    'available_books' => 0,
    'issued_books' => 0,
    'damaged_books' => 0,
    'missing_books' => 0
];

// Status-based book count
$statuses = ['Available', 'Issued', 'Damaged', 'Missing'];
foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM books WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $inventory[strtolower($status) . '_books'] = $result['total'];
}

// Total books count
$result = $conn->query("SELECT COUNT(*) AS total FROM books");
$inventory['total_books'] = $result->fetch_assoc()['total'];

// Fetch all books
$books = [];
$result = $conn->query("SELECT * FROM books ORDER BY category, title");
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Inventory Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            padding: 40px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 15px;
            border: none;
        }
        .card-title {
            font-size: 1rem;
        }
        h1, h3 {
            color: #2c3e50;
        }
        .badge {
            font-size: 0.85rem;
            padding: 6px 10px;
        }
        .table thead th {
            background-color: #37474f;
            color: #fff;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .mb-5 {
            margin-bottom: 3rem !important;
        }
        .summary-card {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        .summary-card.available {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .summary-card.issued {
            background-color: #fff3e0;
            color: #ef6c00;
        }
        .summary-card.damaged {
            background-color: #ffebee;
            color: #c62828;
        }
        .summary-card.missing {
            background-color: #ede7f6;
            color: #5e35b1;
        }
        .card-header {
        background-color: #f0f4ff !important;
    }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-5">📚 Library Inventory Report</h1>

    <!-- Summary Cards -->
    <div class="row g-4 mb-5 justify-content-center">
        <div class="col-md-2 col-sm-6">
            <div class="card summary-card text-center shadow">
                <div class="card-body">
                    <h6 class="card-title">Total Books</h6>
                    <h3><?= $inventory['total_books'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card summary-card available text-center shadow">
                <div class="card-body">
                    <h6 class="card-title">Available</h6>
                    <h3><?= $inventory['available_books'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card summary-card issued text-center shadow">
                <div class="card-body">
                    <h6 class="card-title">Issued</h6>
                    <h3><?= $inventory['issued_books'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card summary-card damaged text-center shadow">
                <div class="card-body">
                    <h6 class="card-title">Damaged</h6>
                    <h3><?= $inventory['damaged_books'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card summary-card missing text-center shadow">
                <div class="card-body">
                    <h6 class="card-title">Missing</h6>
                    <h3><?= $inventory['missing_books'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="card shadow">
        <div class="card-header bg-light border-bottom">
         <h3 class="mb-0 text-primary">📖  Details</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Title</th>
                            <th style="width: 25%;">Author</th>
                            <th style="width: 20%;">Category</th>
                            <th style="width: 20%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($books) > 0): ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td><?= htmlspecialchars($book['category']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $book['status'] == 'Available' ? 'success' :
                                            ($book['status'] == 'Issued' ? 'warning' :
                                            ($book['status'] == 'Damaged' ? 'danger' : 'secondary')) 
                                        ?>">
                                            <?= ucfirst($book['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No books found in the inventory.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>
