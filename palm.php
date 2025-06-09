<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palm Reading</title>
    <link rel="stylesheet" href="style.css">
	<style>
    .upload-options { display: none; margin-top: 10px; }
    .upload-btn {
      background-color: #4CAF50;
      color: white;
      padding: 10px;
      margin: 5px;
      display: inline-block;
      cursor: pointer;
    }
    .upload-btn input {
      display: none;
    }
	#successMessage {
      display: none;
      color: green;
      font-weight: bold;
    }
  </style>
</head>
<body>
    
   
    <header>
      <nav class="navbar">
        <div class="logo">AstroGyann</div>
        <div class="menu-toggle" id="mobile-menu">☰</div>
        <ul class="nav-links" id="nav-links">
          <li><a href="index.html">Home</a></li>
          <li><a href="index.html">Services</a></li>
          <li><a href="#footer-about">AboutUs</a></li>
          <li><a href="#" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">Jyotish Onboard</a></li>
          <li><a href="#">Free Kundli</a></li>
          <li><a href="#">Kundli Matching</a></li>
          <li><a href="#">Daily Horoscope</a></li>
        </ul>
      </nav>
    </header>


    <section class="palm-read-section">
    <div class="palm-content">
      <h1 class="palm-title">Read your Future through Your Hand</h1>
      <p class="palm-description">
        Aapke haathon ki rekhaayein chhupaaye hue hain aapka bhavishya! Bas ek image upload kijiye apne haath ki, aur jaane apne career, love life, health aur personality ke baare mein woh sab kuch jo sirf ek sacha hastrekha expert hi bata sakta hai. Shuruaat kijiye apne safar ki sirf ek click mein!



      </p>
	  <?php if (!empty($_SESSION['success']) && $_SESSION['success'] === true): ?>
  <div id="successMessage">Submission successful! Thank you.</div>
  <script>
    // Hide the form after successful submission
    document.addEventListener('DOMContentLoaded', function () {
      document.getElementById('uploadForm').style.display = 'none';
      document.getElementById('successMessage').style.display = 'block';
    });
  </script>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>
	  <form id="uploadForm" action="submit.php" method="POST" enctype="multipart/form-data">
    <label>
      Mobile Number:
      <input type="text" name="mobile" id="mobile" required />
    </label>

    <div class="upload-options" id="uploadOptions">
      <label class="upload-btn">
        Open Camera
        <input type="file" name="camera_photo" accept="image/*" capture="environment" />
      </label>

      <label class="upload-btn">
        Upload from File
        <input type="file" name="upload_photo" accept="image/*" />
      </label>
    </div>

    <br>
    <button type="submit" id="submitBtn" style="display: none;">Submit</button>
  </form>

    </div>
    <div class="palm-image">
      <img src="images/palm_read.jpg" alt="Palm Reading" />
    </div>
  </section>
 <script>
    const mobileInput = document.getElementById("mobile");
    const uploadOptions = document.getElementById("uploadOptions");
    const submitBtn = document.getElementById("submitBtn");

    mobileInput.addEventListener("input", () => {
      if (mobileInput.value.length == 10) {
        uploadOptions.style.display = "block";
      }
    });

    // Show submit button when a file is selected
    const fileInputs = uploadOptions.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
      input.addEventListener("change", () => {
        submitBtn.style.display = "block";
      });
    });
  </script>

</body>
</html>