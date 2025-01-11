<?php
require 'db_conection.php';

$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch room details
$room_query = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($room_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch room images
$images_query = "SELECT image_path FROM room_images WHERE room_id = ?";
$stmt = $conn->prepare($images_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$images = $stmt->get_result();
$stmt->close();

// Fetch room facilities - UPDATED to use facility_name
$facilities_query = "SELECT f.facility_name 
                    FROM facilities f
                    JOIN room_facilities rf ON f.id = rf.facilities_id
                    WHERE rf.room_id = ?";
$stmt = $conn->prepare($facilities_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$facilities = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Raid-2.0</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="css/style.css" />

    <style>
        .room-gallery { height: 500px; }
        .facility-badge { margin: 0 5px 5px 0; }
        .price-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: 2px solid #ffc107;
            font-weight: bold;
            color: #ffc107;
            z-index: 10;
        }
    </style>
</head>
 <!-- Nav bar  -->
  <?php
  require_once("includ/header.php");
  ?>
<body>
   

    <div class="container py-5 mt-5">
        <?php if ($room): ?>
            <div class="row">
                <!-- Image Gallery -->
                <div class="col-md-7 position-relative">
                    <span class="price-tag">$<?= number_format($room['price'], 2) ?></span>
                    <div class="swiper room-gallery">
                        <div class="swiper-wrapper">
                            <?php while ($image = $images->fetch_assoc()): ?>
                                <div class="swiper-slide">
                                    <img src="/riad/images/rooms/<?= htmlspecialchars($image['image_path']) ?>"
                                        class="img-fluid w-100 h-100"
                                        style="object-fit: cover;"
                                        alt="Room Image">
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

                <!-- Room Details -->
                <div class="col-md-5">
                    <h1 class="mb-3">Room <?= htmlspecialchars($room['room_number']) ?></h1>

                    <div class="d-flex align-items-center mb-4">
                        <span class="badge bg-<?= $room['status'] == 'Available' ? 'success' : 'danger' ?> me-3">
                            <?= htmlspecialchars($room['status']) ?>
                        </span>
                    </div>

                    <div class="mb-4">
                        <h4>Details</h4>
                        <ul class="list-unstyled">
                            <li><strong>Bed Type:</strong> <?= htmlspecialchars($room['bed_type']) ?></li>
                            <li><strong>Floor:</strong> <?= htmlspecialchars($room['floor']) ?></li>
                            <li><strong>Max Adults:</strong> <?= htmlspecialchars($room['adult']) ?></li>
                            <li><strong>Max Children:</strong> <?= htmlspecialchars($room['children']) ?></li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h4>Facilities</h4>
                        <div>
                            <?php while ($facility = $facilities->fetch_assoc()): ?>
                                <span class="badge facility-badge bg-light text-dark">
                                    <?= htmlspecialchars($facility['facility_name']) ?>
                                </span>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4>Description</h4>
                        <p><?= nl2br(htmlspecialchars($room['description'])) ?></p>
                    </div>

                    <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn btn-lg btn-dark w-100">Book Now</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">Room not found!</div>
        <?php endif; ?>
    </div>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        new Swiper('.room-gallery', {
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>


    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script src="js/main.js"></script>
          <!-- Footer -->
  <?php require_once("includ/footer.php") ?>
</body>

</html>