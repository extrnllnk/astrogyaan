<?php
echo "<pre>";
print_r($_POST);
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'astro');

// action_page.php
header('Content-Type: text/html; charset=UTF-8');

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
	$success = false;

	// Enable error reporting for debugging
	error_reporting(E_ALL);
	ini_set('display_errors', 1);


    // 1. BASIC INFORMATION VALIDATION
    if (empty($_POST['fullname'])) {
        $errors['fullname'] = "Full name is required";
    }

    if (empty($_POST['dob'])) {
        $errors['dob'] = "Date of birth is required";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['dob'])) {
		$errors['dob'] = "Invalid date format (YYYY-MM-DD)";
    }

    if (empty($_POST['gender'])) {
        $errors['gender'] = "Gender is required";
    }

    // Photo upload validation
    if (empty($_FILES['photo']['name'])) {
        $errors['photo'] = "Photo is required";
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            $errors['photo'] = "Only JPG, PNG or GIF files are allowed";
        } elseif ($_FILES['photo']['size'] > $max_size) {
            $errors['photo'] = "File size must be less than 2MB";
        }
    }

    if (!empty($_POST['aadhaar']) && !preg_match('/^\d{12}$/', $_POST['aadhaar'])) {
        $errors['aadhaar'] = "Aadhaar must be 12 digits";
    }

    // 2. CONTACT INFORMATION VALIDATION
    if (empty($_POST['mobile'])) {
        $errors['mobile'] = "Mobile number is required";
    } elseif (!preg_match('/^\d{10}$/', $_POST['mobile'])) {
        $errors['mobile'] = "Mobile must be 10 digits";
    }

    if (empty($_POST['alternate_mobile'])) {
        $errors['alternate_mobile'] = "Alternate mobile is required";
    } elseif (!preg_match('/^\d{10}$/', $_POST['alternate_mobile'])) {
        $errors['alternate_mobile'] = "Alternate mobile must be 10 digits";
    }

    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($_POST['address'])) {
        $errors['address'] = "Address is required";
    }

    if (empty($_POST['area_of_service'])) {
        $errors['area_of_service'] = "City/Area is required";
    }

    // 3. RELIGIOUS/PROFESSIONAL VALIDATION
    if (empty($_POST['years_of_experience'])) {
        $errors['years_of_experience'] = "Years of experience is required";
    } elseif (!is_numeric($_POST['years_of_experience'])) {
        $errors['years_of_experience'] = "Must be a number";
    }

    if (empty($_POST['languages_spoken'])) {
        $errors['languages_spoken'] = "Languages spoken is required";
    }

    if (empty($_POST['types_of_poojas'])) {
        $errors['types_of_poojas'] = "Types of poojas is required";
    }

    // 4. AVAILABILITY VALIDATION
    if (empty($_POST['days_available'])) {
        $errors['days_available'] = "Days available is required";
    }

    if (empty($_POST['preferred_time_slots'])) {
        $errors['preferred_time_slots'] = "Preferred time slots is required";
    }

    // 5. LOGISTICS VALIDATION
    if (empty($_POST['vehicle'])) {
        $errors['vehicle'] = "Vehicle information is required";
    }

    if (empty($_POST['willing_to_travel'])) {
        $errors['willing_to_travel'] = "Travel willingness is required";
    }

    if (!empty($_POST['distance_to_travel']) && !is_numeric($_POST['distance_to_travel'])) {
        $errors['distance_to_travel'] = "Distance must be a number";
    }

    // 6. CHARGES VALIDATION
    if (empty($_POST['standard_fee'])) {
        $errors['standard_fee'] = "Standard fee is required";
    } elseif (!is_numeric($_POST['standard_fee'])) {
        $errors['standard_fee'] = "Fee must be a number";
    }

    if (!empty($_POST['additional_charges']) && !is_numeric($_POST['additional_charges'])) {
        $errors['additional_charges'] = "Additional charges must be a number";
    }

    // 7. MEDIA VALIDATION
    if (empty($_POST['short_bio'])) {
        $errors['short_bio'] = "Short bio is required";
    }

    // Video upload validation (if required)
    if (!empty($_FILES['introduction_video']['name'])) {
        $allowed_video_types = ['video/mp4', 'video/quicktime'];
        $max_video_size = 10 * 1024 * 1024; // 10MB
        
        if (!in_array($_FILES['introduction_video']['type'], $allowed_video_types)) {
            //$errors['introduction_video'] = "Only MP4 or MOV videos are allowed";
        } elseif ($_FILES['introduction_video']['size'] > $max_video_size) {
            $errors['introduction_video'] = "Video size must be less than 10MB";
        }
    }

    // 8. DECLARATION VALIDATION
    //if (empty($_POST['true_info'])) {
      //  $errors['true_info'] = "You must confirm the information is true";
    //}

    //if (empty($_POST['terms_conditions'])) {
      //  $errors['terms_conditions'] = "You must agree to the terms and conditions";
    //}

    // If no errors, process the form
    if (empty($errors)) {
        // Process file uploads
        $photo_path = 'uploads/' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
        
        if (!empty($_FILES['introduction_video']['name'])) {
            $video_path = 'uploads/' . basename($_FILES['introduction_video']['name']);
            move_uploaded_file($_FILES['introduction_video']['tmp_name'], $video_path);
        }
        
        // TODO: Save other form data to database
        // Connect to MySQL database
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

		// 3. PREPARE SQL STATEMENT (prevent SQL injection)
		$stmt = $conn->prepare("INSERT INTO jyotish_onboard (
			fullname, dob, gender, photo, aadhaar, 
			mobile, alternate_mobile, email, address, area_of_service,
			years_of_experience, languages_spoken, types_of_poojas, training_certification,
			days_available, preferred_time_slots, vehicle, 
			willing_to_travel, distance_to_travel, standard_fee, 
			additional_charges, short_bio, introduction_video,
			 created_at
		) VALUES (
			?, ?, ?, ?, ?, 
			?, ?, ?, ?, ?,
			?, ?, ?, ?,
			?, ?, ?, 
			?, ?, ?, 
			?, ?, ?,
			NOW()
		)");
		
		// 4. BIND PARAMETERS
		$stmt->bind_param(
			"sssssssssssssssssssssss",
			$_POST['fullname'],
			$_POST['dob'],
			$_POST['gender'],
			$photo_path,
			$_POST['aadhaar'],
			$_POST['mobile'],
			$_POST['alternate_mobile'],
			$_POST['email'],
			$_POST['address'],
			$_POST['area_of_service'],
			$_POST['years_of_experience'],
			$_POST['languages_spoken'],
			$_POST['types_of_poojas'],
			$_POST['training_certification'],
			$_POST['days_available'],
			$_POST['preferred_time_slots'],
			$_POST['vehicle'],
			$_POST['willing_to_travel'],
			$_POST['distance_to_travel'],
			$_POST['standard_fee'],
			$_POST['additional_charges'],
			$_POST['short_bio'],
			$video_path
		);
		
		// 5. EXECUTE AND CHECK RESULT
		if ($stmt->execute()) {
			$success = true;
			$last_id = $conn->insert_id;
		} else {
			$errors['database'] = "Error: " . $stmt->error;
		}
		
		$stmt->close();
	}
	
		// Return JSON response if AJAX request
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			header('Content-Type: application/json');
			echo json_encode(['success' => $success, 'errors' => $errors]);
			exit;
		}

		// For non-AJAX requests, handle redirects
		if ($success) {
			header('Location: index.html');
			exit;
		} else {
			// Store errors in session to display on form page
			session_start();
			$_SESSION['form_errors'] = $errors;
			$_SESSION['form_data'] = $_POST;
			header('Location: index.html'); // Redirect back to form
			exit;
		}


        $conn->close();
        
        $success = true;
    }
    
if (empty($errors)) {
	// Process successful submission
	echo '<!DOCTYPE html>
	<html>
	<head>
		<title>Submission Successful</title>
		<style>
			body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
			.success { background: #e8f5e9; padding: 20px; border-radius: 5px; }
		</style>
	</head>
	<body>
		<div class="success">
			<h2>Thank You!</h2>
			<p>Your form has been submitted successfully.</p>
			<p><a href="index.html">Return to form</a></p>
		</div>
	</body>
	</html>';
	exit;
}

// If errors or GET request, show form again with errors
echo '<!DOCTYPE html>
<html>
<head>
    <title>Form Submission Error</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Please correct the following errors:</h2>';
    
foreach ($errors as $error) {
    echo '<div class="error">' . htmlspecialchars($error) . '</div>';
}

echo '<p><a href="index.html">Return to form</a></p>
</body>
</html>';
?>