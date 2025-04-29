<?php
// gallery.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Digital Library</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #fef9f4, #f4e8dc);
      color: #333;
      overflow-x: hidden;
    }

    .header {
      height: 100vh;
      background: linear-gradient(135deg, #d2b48c, #a0522d);
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 40px;
    }

    .header h1 {
      font-size: 3.5rem;
      margin-bottom: 20px;
      text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
    }

    .header p {
      font-size: 1.3rem;
      max-width: 700px;
      margin-bottom: 40px;
    }

    .btn {
      padding: 15px 30px;
      background: #fff;
      color: #a0522d;
      font-size: 1.1rem;
      font-weight: bold;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      transition: background 0.3s ease, color 0.3s ease;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn:hover {
      background: #a0522d;
      color: white;
    }

    .gallery-section {
      padding: 60px 40px;
      background: #fdfdfd;
    }

    .gallery-section h2 {
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 50px;
      color: #5a3e2b;
    }

    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: auto;
    }

    .gallery-item {
      position: relative;
      overflow: hidden;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .gallery-item:hover {
      transform: scale(1.03);
    }

    .gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .gallery-item:hover img {
      transform: scale(1.08);
    }

    html {
      scroll-behavior: smooth;
    }

    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.9);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .modal img {
      max-width: 90%;
      max-height: 80%;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(255,255,255,0.3);
      animation: zoomIn 0.3s ease;
    }

    .modal:target {
      display: flex;
    }

    .close {
      position: absolute;
      top: 40px;
      right: 50px;
      color: white;
      font-size: 3rem;
      text-decoration: none;
      z-index: 10000;
      font-weight: bold;
    }

    @keyframes zoomIn {
      from {
        transform: scale(0.7);
        opacity: 0;
      }
      to {
        transform: scale(1);
        opacity: 1;
      }
    }
  </style>
</head>
<body>

  <!-- Main Landing Page -->
  <section class="header">
    <h1>Welcome to the Digital Library Gallery</h1>
    <p>Explore a curated collection of artwork that showcases creativity, history, and digital craftsmanship.</p>
    <a href="#gallery"><button class="btn">View Gallery</button></a>
  </section>

  <!-- Gallery Section -->
  <section id="gallery" class="gallery-section">
    <h2>Gallery</h2>
    <div class="gallery">
      <?php
        for ($i = 1; $i <= 9; $i++) {
          echo '
            <a href="#img'.$i.'" class="gallery-item">
              <img src="'.$i.'.jpg" alt="Art '.$i.'">
            </a>
          ';
        }
      ?>
    </div>
  </section>

  <!-- Modal Lightboxes -->
  <?php
    for ($i = 1; $i <= 9; $i++) {
      echo '
        <div id="img'.$i.'" class="modal">
          <a href="#gallery" class="close">&times;</a>
          <img src="'.$i.'.jpg" alt="Enlarged Art '.$i.'">
        </div>
      ';
    }
  ?>

</body>
</html>
