<?php
require '../../db_conection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required = ['room_id', 'roomnumber', 'bedType', 'roomfloor', 
                    'roomStatus', 'max_adult', 'max_children', 'quantity', 'price'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Get facilities
        $facilities = isset($_POST['facilities']) && is_array($_POST['facilities']) 
            ? implode(',', $_POST['facilities']) 
            : '';

        // Prepare update query
        $stmt = $conn->prepare("UPDATE rooms SET
            room_number = ?,
            bed_type = ?,
            floor = ?,
            status = ?,
            adult = ?,
            children = ?,
            quantity = ?,
            price = ?,
            description = ?,
            facilities = ?
            WHERE id = ?
        ");

        // Execute query
        $stmt->execute([
            $_POST['roomnumber'],
            $_POST['bedType'],
            $_POST['roomfloor'],
            $_POST['roomStatus'],
            $_POST['max_adult'],
            $_POST['max_children'],
            $_POST['quantity'],
            $_POST['price'],
            $_POST['roomdescription'] ?? '',
            $facilities,
            $_POST['room_id']
        ]);

        // Redirect back
        header("Location: ../room.php");
        exit;

    } catch (Exception $e) {
        // Handle error
        die("Error updating room: " . $e->getMessage());
    }
}