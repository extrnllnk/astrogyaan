<?php
// DB connection details
$host = 'localhost';
$db   = 'astro';
$user = 'root'; // change if needed
$pass = '';     // change if needed

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	session_start();
    $mobile = $_POST['mobile'];
    $cameraPhotoPath = '';
	$uploadPhotoPath = '';

    if (isset($_FILES['camera_photo']) && $_FILES['camera_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tmpName = $_FILES['camera_photo']['tmp_name'];
        $fileName = basename($_FILES['camera_photo']['name']);
        $uniqueName = uniqid() . '-' . $fileName;
        $cameraPhotoPath = $uploadDir . $uniqueName;

        move_uploaded_file($tmpName, $cameraPhotoPath);
    }
	if (isset($_FILES['upload_photo']) && $_FILES['upload_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tmpName = $_FILES['upload_photo']['tmp_name'];
        $fileName = basename($_FILES['upload_photo']['name']);
        $uniqueName = uniqid() . '-' . $fileName;
        $uploadPhotoPath = $uploadDir . $uniqueName;

        move_uploaded_file($tmpName, $uploadPhotoPath);
    }

    // Insert into DB
    $status = '1';
	$created_at = date('Y-m-d H:i:s');

	$stmt = $conn->prepare("INSERT INTO palm_reading_submissions (mobile, camera_photo, upload_photo, status, created_at) VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param("sssss", $mobile, $cameraPhotoPath, $uploadPhotoPath, $status, $created_at);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
		$_SESSION['success'] = true;
        echo "Data saved successfully.<br>";
    } else {
		$_SESSION['success'] = false;
        echo "Error saving data.<br>";
    }

    $stmt->close();
    $conn->close();
	header("Location: palm.php");
	exit();
}
?>
