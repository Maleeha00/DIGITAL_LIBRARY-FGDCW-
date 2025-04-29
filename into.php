<?php
// index.php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Digital Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
  <style>
    .carousel-item img { width: 100vw; height: 90vh; }
    .custom-heading { font-size: 2.5rem; text-align: center; margin: 20px auto; display: block; }
    .d-flex .m-3 { text-align: center; position: relative; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .d-flex .m-3 img { width: 150px; height: 150px; border-radius: 50%; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .d-flex .m-3 p { font-size: 18px; font-weight: bold; color: #333; margin-top: 10px; text-align: center; }
    .d-flex .m-3:hover img { transform: scale(1.1); box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3); }
    body { margin: 0; font-family: Arial, sans-serif; background: #fff; }
    .main-container { display: flex; justify-content: space-between; padding: 50px; flex-wrap: wrap; background: linear-gradient(to right, #00416A, #E4E5E6); color: #ffffff; }
    .left-section { flex: 1; max-width: 45%; }
    .logo-section { text-align: left; margin-bottom: 30px; margin-left: 200px; }
    .logo-section img { width: 200px; }
    .about-section { text-align: left; margin-left: 30px; }
    .about-section h2 { font-size: 30px; font-weight: bold; margin-bottom: 20px; }
    .about-section p { font-size: 16px; line-height: 1.6; }
    .right-section { flex: 1; max-width: 45%; display: flex; flex-direction: column; }
    .contact-section { margin-top: 250px; }
    .contact-section h2 { font-size: 26px; font-weight: bold; margin-bottom: 20px; }
    .contact-info div { margin-bottom: 15px; display: flex; align-items: center; }
    .contact-info i { margin-right: 10px; font-size: 18px; }
    footer { background: linear-gradient(to right, #00416A, #E4E5E6); color: #fff; text-align: center; padding: 20px 10px; font-size: 14px; }
    hr { border: 0.5px solid #ccc; margin: 0; }
    .navbar-brand img { width: 50px; height: 50px; margin-right: 10px; border-radius: 50%; }
    .navbar-brand span { font-family: 'Pacifico', cursive; font-size: 24px; }
  </style>
</head>

<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">

    <a class="navbar-brand d-flex align-items-center text-dark" href="into.php">
  <img src="log.png" alt="Logo" />
  <span>Digital Library</span>
</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto me-5">
          <li class="nav-item"><a class="nav-link text-dark" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="gallery.php">Gallery</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Carousel -->
  <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
    <div class="carousel-indicators">
      <?php for ($i = 0; $i < 6; $i++): ?>
        <button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active"' : '' ?>></button>
      <?php endfor; ?>
    </div>
    <div class="carousel-inner">
      <?php
      $images = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg'];
      foreach ($images as $index => $img) {
        $active = $index === 0 ? 'active' : '';
        echo "<div class='carousel-item $active'><img src='$img' class='d-block w-100' alt='Slide ".($index + 1)."' /></div>";
      }
      ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <!-- Best Collections -->
  <section class="my-5">
    <h2 class="custom-heading">What Does the Library Have?</h2>
    <div class="d-flex justify-content-center flex-wrap">
      <?php
      $subjects = [
        ['eng.jpg', 'ENGLISH'],
        ['magazines.jpg', 'MONTHLY MAGAZINES'],
        ['eco.jpg', 'ECONOMICS'],
        ['hpe.jpg', 'HEALTH AND PHYSICAL'],
        ['novels.webp', 'ENGLISH NOVELS'],
        ['maths.jpg', 'MATHS']
      ];
      foreach ($subjects as [$img, $label]) {
        echo "<div class='m-3'><img src='$img' class='rounded-circle' onclick='showLoginPrompt()'><p>$label</p></div>";
      }
      ?>
    </div>
  </section>

  <section class="my-5">
    <div class="d-flex justify-content-center flex-wrap">
      <?php
      $subjects2 = [
        ['urdu.jpg', 'URDU'],
        ['engn.jpg', 'ENGLISH NOVELS'],
        ['comp.jpg', 'COMPUTER'],
        ['hpe.png', 'HPE'],
        ['isl.jpg', 'ISLAMIAT'],
        ['IT.jpg', 'IT']
      ];
      foreach ($subjects2 as [$img, $label]) {
        echo "<div class='m-3'><img src='$img' class='rounded-circle' onclick='showLoginPrompt()'><p>$label</p></div>";
      }
      ?>
    </div>
  </section>

  <!-- About and Contact -->
  <div class="main-container">
    <div class="left-section">
      <div class="logo-section"><img src="logo.png" alt="College Logo"></div>
      <div class="about-section">
        <h2>About FGDC(W)</h2>
        <p>Federal Government Degree College is affiliated with Federal Board of Intermediate & Secondary Education,
        Islamabad at Intermediate level and at BS level it is affiliated with National University of Pakistan (NUP)...</p>
      </div>
    </div>
    <div class="right-section">
      <div class="contact-section">
        <h2>Contact Info</h2>
        <div class="contact-info">
          <div><i class="fas fa-map-marker-alt"></i> Kharian Cantt, Punjab, Pakistan</div>
          <div><i class="fas fa-phone-alt"></i> (053) 9240102</div>
          <div><i class="fas fa-envelope"></i> fgkharian@gmail.com</div>
        </div>
      </div>
    </div>
  </div>

  <hr />
  <footer>
    © <?= date("Y") ?> F.G. Degree College For Women, Kharian Cantt. All Rights Reserved.
    Digital Library | Made with ❤ by <b>Iqra Noureen, Aqsa Hakeem and Maleeha</b>
  </footer>

  <!-- JS -->
  <script>
    function showLoginPrompt() {
      if (confirm("Kindly login to access the items.")); 
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
