<?php
include '../../db_conection.php';

if (
    isset($_POST['roomnumber']) &&
    isset($_POST['bedType']) &&
    isset($_POST['roomfloor']) &&
    isset($_POST['roomStatus']) &&
    isset($_POST['roomdescription']) &&
    isset($_POST['max_adult']) &&
    isset($_POST['max_children']) &&
    isset($_POST['price']) &&
    isset($_POST['quantity'])
) {
    $roomNumber = $_POST['roomnumber'];
    $bedType = $_POST['bedType'];
    $roomFloor = $_POST['roomfloor'];
    $roomStatus = $_POST['roomStatus'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $maxAdult = $_POST['max_adult'];
    $maxChildren = $_POST['max_children'];
    $roomDescription = $_POST['roomdescription'];
    $facilities = isset($_POST['facilities']) ? $_POST['facilities'] : [];

    // Insert the room
    $stmt = $conn->prepare("INSERT INTO rooms (room_number, bed_type, floor, price, quantity, adult, children, description, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiiiiss", $roomNumber, $bedType, $roomFloor, $price, $quantity, $maxAdult, $maxChildren, $roomDescription, $roomStatus);

    if ($stmt->execute()) {
        $roomId = $conn->insert_id;

        // After successful room insertion
        $roomId = $conn->insert_id;

        // Facility handling
        if (!empty($facilities) && is_array($facilities)) {
            $facilityMap = [
                'WiFi' => 1,
                'Air Conditioning' => 2,
                'Room Service' => 3,
                'Coffee Maker' => 4,
                'TV' => 5,
                'Balcony' => 6,
                'Jacuzzi' => 7,
                'Breakfast' => 8
            ];

            // Bulk insert approach
            $values = [];
            foreach ($facilities as $facility) {
                if (isset($facilityMap[$facility])) {
                    $values[] = "($roomId, {$facilityMap[$facility]})";
                }
            }

            if (!empty($values)) {
                $sql = "INSERT IGNORE INTO room_facilities (room_id, facilities_id) 
                VALUES " . implode(',', $values);
                if (!$conn->query($sql)) {
                    echo "Facility error: " . $conn->error;
                }
            }
        }

        header("Location: ../room.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Missing required fields.";
}
