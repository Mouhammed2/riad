<?php
require 'db_conection.php';

// Get room ID from URL
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

if ($room_id <= 0) {
    header("Location: rooms.php?error=invalid_room");
    exit();
}

// Fetch room details
$room_query = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($room_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$room) {
    header("Location: rooms.php?error=room_not_found");
    exit();
}

// Fetch room images
$images_query = "SELECT image_path FROM room_images WHERE room_id = ?";
$stmt = $conn->prepare($images_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$images = $stmt->get_result();
$stmt->close();

// Fetch room facilities
$facilities_query = "SELECT f.facility_name 
                    FROM facilities f
                    JOIN room_facilities rf ON f.id = rf.facilities_id
                    WHERE rf.room_id = ?";
$stmt = $conn->prepare($facilities_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$facilities = $stmt->get_result();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $special_requests = $_POST['special_requests'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($check_in) || empty($check_out)) {
        $error = "Please fill all required fields";
    } else {
        // Calculate number of days and total amount
        $date1 = new DateTime($check_in);
        $date2 = new DateTime($check_out);
        $interval = $date1->diff($date2);
        $num_days = $interval->days;
        $total_amount = $room['price'] * $num_days;

        // Insert booking into database
        $insert_query = "INSERT INTO bookings (room_id, name, email, phone, check_in, check_out, total_amount, special_requests, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("isssssds", $room_id, $name, $email, $phone, $check_in, $check_out, $total_amount, $special_requests);

        if ($stmt->execute()) {
            $booking_id = $conn->insert_id;
            header("Location: booking_confirmation.php?id=$booking_id");
            exit();
        } else {
            $error = "Failed to create booking. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Room <?= htmlspecialchars($room['room_number']) ?> - Raid-2.0</title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css" />

    <style>
        .room-gallery {
            height: 500px;
        }

        .facility-badge {
            margin: 0 5px 5px 0;
        }

        .price-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 2px solid #ffc107;
            font-weight: bold;
            color: #ffc107;
            z-index: 10;
        }

        .booking-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
        }

        .btn-book {
            background-color: #2c3e50;
            color: white;
            font-weight: 600;
            padding: 12px;
            transition: all 0.3s;
        }

        .btn-book:hover {
            background-color: #3498db;
            transform: translateY(-2px);
        }

        .required-field::after {
            color: red;
        }

        @media (max-width: 768px) {
            .room-gallery {
                height: 350px;
            }
        }
    </style>
</head>

<body>
    <!-- Nav bar -->
    <?php require_once("includ/header.php"); ?>

    <div class="container py-5 mt-5">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center py-3">
                <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Image Gallery -->
            <div class="col-md-7 position-relative">
                <span class="price-tag">$<?= number_format($room['price'], 2) ?> </span>
                <div class="swiper room-gallery">
                    <div class="swiper-wrapper">
                        <?php while ($image = $images->fetch_assoc()): ?>
                            <div class="swiper-slide">
                                <img src="/riad/images/rooms/<?= htmlspecialchars($image['image_path']) ?>"
                                    class="img-fluid w-100 h-100"
                                    style="object-fit: cover;"
                                    alt="Room <?= htmlspecialchars($room['room_number']) ?>">
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>

            </div>

            <!-- Booking Form -->
             
            <div class="col-md-5">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-calendar-check me-2"></i>Booking Information
                        </h3>

                        <form method="POST" action="booking.php?room_id=<?= $room_id ?>">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label required-field">Full Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Check-in</label>
                                    <input type="date" name="check_in" class="form-control" required min="<?= date('Y-m-d') ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Check-out</label>
                                    <input type="date" name="check_out" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label">Special Requests</label>
                                    <textarea name="special_requests" class="form-control" rows="3" placeholder="Any special requests?"></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                        <div>
                                            <h5 class="mb-1">Total for <?= $interval->days ?? '0' ?> nights:</h5>
                                            <h3 class="text-primary mb-0">$<?= number_format($total_amount ?? $room['price'], 2) ?></h3>
                                        </div>
                                        <button type="submit" class="btn btn-dark btn-lg">
                                            <i class="fas fa-credit-card me-2"></i>Book Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <script>
        // Initialize Swiper
        document.addEventListener('DOMContentLoaded', function() {
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

            // Date validation
            const checkIn = document.querySelector('input[name="check_in"]');
            const checkOut = document.querySelector('input[name="check_out"]');

            checkIn.addEventListener('change', function() {
                if (this.value) {
                    const minCheckOut = new Date(this.value);
                    minCheckOut.setDate(minCheckOut.getDate() + 1);
                    checkOut.min = minCheckOut.toISOString().split('T')[0];

                    if (checkOut.value && new Date(checkOut.value) <= new Date(this.value)) {
                        checkOut.value = '';
                    }
                }
            });
        });
    </script>

    <!-- Footer -->
    <?php require_once("includ/footer.php") ?>
</body>

</html>