<?php
require '../../db_conection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // First delete from room_facilities
        $delete_facilities = "DELETE FROM room_facilities WHERE room_id = ?";
        $stmt1 = mysqli_prepare($conn, $delete_facilities);
        mysqli_stmt_bind_param($stmt1, 'i', $id);
        mysqli_stmt_execute($stmt1);

        // Then delete from rooms
        $delete_room = "DELETE FROM rooms WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $delete_room);
        mysqli_stmt_bind_param($stmt2, 'i', $id);
        mysqli_stmt_execute($stmt2);

        // Commit transaction
        mysqli_commit($conn);
        
        header("Location: ../room.php");
        exit;
    } catch (mysqli_sql_exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }

    mysqli_stmt_close($stmt1);
    mysqli_stmt_close($stmt2);
} else {
    echo "No ID provided.";
}

mysqli_close($conn);
?>