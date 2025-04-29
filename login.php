<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$maxAttempts = 3;
$blockDuration = 60; // Block for 60 seconds (1 minute)

if (isset($_SESSION['blocked_time'])) {
    $elapsed = time() - $_SESSION['blocked_time'];
    if ($elapsed < $blockDuration) {
        $remaining = $blockDuration - $elapsed;
        $error = "Too many failed attempts. Try again in {$remaining} seconds.";
    } else {
        unset($_SESSION['blocked_time'], $_SESSION['login_attempts']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $user_id = trim($_POST['user_id'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($user_id !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $valid = false;
        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if ($password === $user['password']) { // Plain-text password check
                $valid = true;
                unset($_SESSION['login_attempts'], $_SESSION['blocked_time']);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'student') {
                    header('Location: almiras.php');
                    exit;
                } elseif ($user['role'] === 'librarian') {
                    header('Location: lib_dashboard.php');
                    exit;
                } else {
                    $error = "Access denied for this role.";
                }
            }
        }

        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $attemptsLeft = $maxAttempts - $_SESSION['login_attempts'];

        if (!$valid && $_SESSION['login_attempts'] >= $maxAttempts) {
            $_SESSION['blocked_time'] = time();
            $error = "Too many failed attempts. You are blocked for 60 seconds.";
        } else {
            $error = "Invalid ID or password. {$attemptsLeft} attempt(s) left.";
        }
    } else {
        $error = 'Please enter both User ID and Password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      background-image: url('bg.jpg');
      background-size: cover;
      background-position: center;
    }
    .div1, .div2 {
      height: auto;
      min-height: 400px;
      display: flex;
    }
    .skin-navbar {
      background-color: #ffffff;
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
    .btn-brown {
      background-color: #6d4c41;
      color: white;
      border: none;
    }
    .btn-brown:hover {
      background-color: #5d4037;
    }
    .password-wrapper {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 70%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #888;
      font-size: 1.2rem;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg skin-navbar">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center text-dark" href="">
      <img src="log.png" alt="Logo" />
      <span>Digital Library</span>
    </a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto me-5">
        <li class="nav-item">
          <a class="nav-link text-dark" href="">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="gallery.php">Gallery</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="about.php">About Us</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
  <div class="row">
    <!-- Quote -->
    <div class="col-md-5 div1">
      <div class="container bg-light p-4">
        <h5>Today's Quote</h5>
        <p id="quote"></p>
        <p><b id="author"></b></p>
        <p><b>Date:</b> <span id="date"></span></p>
        <p><b>Time:</b> <span id="time"></span></p>
      </div>
    </div>

    <!-- Login Form -->
    <div class="col-md-7 div2">
      <div class="container bg-light p-4 w-100">
        <form method="POST" action="">
          <h3 class="text-center mb-4">Login Form</h3>

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <div class="mb-3">
            <label for="userIdInput" class="form-label">User ID:</label>
            <input type="number" name="user_id" class="form-control" id="userIdInput" required>
          </div>

          <div class="mb-4 password-wrapper">
            <label for="passwordInput" class="form-label">Password:</label>
            <input type="password" name="password" class="form-control" id="passwordInput" required>
            <i class="toggle-password bi bi-eye-slash" id="togglePassword"></i>
          </div>

          <button type="submit" class="btn btn-brown w-100 mb-2">Submit</button>
          <a href="forgot_password.html" class="btn btn-brown w-100">Forgot Password?</a>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script>
const quotes = [
  { text: "When in doubt, go to the library.", author: "J.K. Rowling" },
  { text: "A library is not a luxury but one of the necessities of life.", author: "Henry Ward Beecher" },
  { text: "I have always imagined that Paradise will be a kind of library.", author: "Jorge Luis Borges" },
  { text: "The only thing that you absolutely have to know, is the location of the library.", author: "Albert Einstein" },
  { text: "A reader lives a thousand lives before he dies.", author: "George R.R. Martin" },
  { text: "Libraries store the energy that fuels the imagination.", author: "Sidney Sheldon" }
];

const random = quotes[Math.floor(Math.random() * quotes.length)];
document.getElementById("quote").textContent = `"${random.text}"`;
document.getElementById("author").textContent = `~ ${random.author}`;

function updateDateTime() {
  const now = new Date();
  document.getElementById("date").textContent = now.toLocaleDateString();
  document.getElementById("time").textContent = now.toLocaleTimeString();
}
setInterval(updateDateTime, 1000);
updateDateTime();

// Toggle password visibility
const toggle = document.getElementById('togglePassword');
const passwordInput = document.getElementById('passwordInput');
toggle.addEventListener('click', function () {
  const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordInput.setAttribute('type', type);
  this.classList.toggle('bi-eye');
  this.classList.toggle('bi-eye-slash');
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
