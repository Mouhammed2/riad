<?php
session_start();
require '../../db_conection.php';

$base_url = '/riad/admin_panel/';

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'])) {
    $room_id = (int)$_POST['room_id'];
    
    try {
        // Verify room exists
        $check_stmt = $conn->prepare("SELECT id FROM rooms WHERE id = ?");
        $check_stmt->bind_param("i", $room_id);
        $check_stmt->execute();
        if (!$check_stmt->get_result()->num_rows) {
            throw new Exception("Room does not exist");
        }

        // Image upload handling
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/riad/images/rooms/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($_FILES['image']['tmp_name']);
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            
            if (!in_array($mime_type, $allowed_types)) {
                throw new Exception("Invalid file type");
            }

            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'room_' . $room_id . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $stmt = $conn->prepare("INSERT INTO room_images (room_id, image_path) VALUES (?, ?)");
                $stmt->bind_param("is", $room_id, $filename);
                $stmt->execute();
                $_SESSION['success'] = "Image uploaded successfully!";
            } else {
                throw new Exception("File upload failed");
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header("Location: " . $base_url . "room.php?show_modal=" . $room_id);
    exit;
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    try {
        $image_id = (int)$_GET['delete_image'];
        
        $stmt = $conn->prepare("SELECT room_id, image_path FROM room_images WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows) {
            $image = $result->fetch_assoc();
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/riad/images/rooms/' . $image['image_path'];
            
            $del_stmt = $conn->prepare("DELETE FROM room_images WHERE id = ?");
            $del_stmt->bind_param("i", $image_id);
            $del_stmt->execute();
            
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $_SESSION['success'] = "Image deleted successfully!";
            $redirect_id = $image['room_id'];
        } else {
            throw new Exception("Image not found");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    $redirect_url = isset($redirect_id) ? $base_url . "room.php?show_modal=" . (int)$redirect_id : $base_url . "room.php";
    header("Location: " . $redirect_url);
    exit;
}

header("Location: " . $base_url . "room.php");
exit;
?>