<?php
session_start();
require '../../db_conection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    // Validate required fields
    $required = ['user_id', 'name', 'email'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Required field $field is missing");
        }
    }

    $user_id = (int)$_POST['user_id'];
    $picture_path = null;

    // Handle file upload
    if (!empty($_FILES['picture']['name']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../images/'; // Absolute path

        // Create directory if needed
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }

        // Validate image type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['picture']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            throw new Exception("Only JPG, PNG, and GIF files are allowed");
        }

        // Generate secure filename
        $ext = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
        $filename = 'user_' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $target_path = $upload_dir . $filename;

        // Move the file
        if (!move_uploaded_file($_FILES['picture']['tmp_name'], $target_path)) {
            throw new Exception("Failed to move uploaded file");
        }

        $picture_path = 'images/' . $filename;

        // Delete old picture if exists
        $stmt = $conn->prepare("SELECT picture FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $old_pic = $result->fetch_assoc()['picture'];
        $stmt->close();

        if ($old_pic && $old_pic !== 'images/default-user.jpg') {
            $old_path = __DIR__ . '/../../' . $old_pic;
            if (file_exists($old_path)) {
                unlink($old_path);
            }
        }
    }

    // Build and execute update query
    $query = "UPDATE users SET name=?, email=?, phone=?, addrese=?, zip_code=?, dob=?, role=?";
    $types = "sssssss";
    $params = [
        $_POST['name'],
        $_POST['email'],
        $_POST['phone'] ?? null,
        $_POST['addrese'] ?? null,
        $_POST['zip_code'] ?? null,
        $_POST['dob'] ?? null,
        $_POST['role'] ?? 'client'
    ];

    if ($picture_path) {
        $query .= ", picture=?";
        $types .= "s";
        $params[] = $picture_path;
    }

    $query .= " WHERE id=?";
    $types .= "i";
    $params[] = $user_id;

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception("Database error: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("No changes made to user record");
    }

    $_SESSION['success'] = "User updated successfully";
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    error_log("Error in update_user.php: " . $e->getMessage());
}

// Redirect back to user page
header("Location: ../user.php");
exit;
