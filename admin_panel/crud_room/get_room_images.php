<?php
require '../../db_conection.php';

header('Content-Type: text/html');

if (isset($_GET['room_id'])) {
    $room_id = (int)$_GET['room_id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM room_images WHERE room_id = ?");
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $images = $stmt->get_result();

        if ($images->num_rows > 0) {
            while ($image = $images->fetch_assoc()) {
                $image_url = "/riad/images/rooms/" . $image['image_path'];
                echo '
                <tr>
                    <td>' . htmlspecialchars($image['image_path']) . '</td>
                    <td>
                        <img src="' . $image_url . '" 
                             style="max-height:100px"
                             onerror="this.onerror=null; this.style.display=\'none\'">
                    </td>
                    <td>
                        <a href="/riad/admin_panel/crud_room/room_img.php?delete_image=' . $image['id'] . '&room_id=' . $room_id . '" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm(\'Delete this image?\')">
                            Delete
                        </a>
                    </td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="3" class="text-center">No images found</td></tr>';
        }
    } catch (Exception $e) {
        echo '<tr><td colspan="3" class="text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
    }
}
