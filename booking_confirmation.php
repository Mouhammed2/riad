<?php
require 'db_conection.php';

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($booking_id <= 0) {
    header("Location: rooms.php?error=invalid_booking");
    exit();
}

// Fetch booking details
$booking_query = "SELECT b.*, r.room_number, r.price, r.bed_type 
                 FROM bookings b
                 JOIN rooms r ON b.room_id = r.id
                 WHERE b.id = ?";
$stmt = $conn->prepare($booking_query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    header("Location: rooms.php?error=booking_not_found");
    exit();
}

// Calculate number of days
$date1 = new DateTime($booking['check_in']);
$date2 = new DateTime($booking['check_out']);
$interval = $date1->diff($date2);
$num_days = $interval->days;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Confirmation #<?= $booking_id ?> - Raid-2.0</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .confirmation-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .confirmation-header {
            background: linear-gradient(180deg, #dba135, #f2ca7e) !important;
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
        }

        .confirmation-body {
            padding: 30px;
            background-color: white;
        }

        .confirmation-footer {
            background-color: #f8f9fa;
            border-radius: 0 0 10px 10px;
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .booking-detail {
            margin-bottom: 15px;
        }

        .booking-detail-label {
            font-weight: 600;
            color: #6c757d;
        }

        .booking-detail-value {
            font-weight: 500;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .status-badge {
            padding: 3px 6px;
            border-radius: 20px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>

<body>
    <!-- Nav bar -->
    <?php require_once("includ/header.php"); ?>

    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmation-card">
                    <div class="confirmation-header text-center">
                        <h2><i class="fas fa-check-circle me-2"></i>Booking Confirmed</h2>
                        <p class="mb-0">Your reservation has been received</p>
                    </div>

                    <div class="confirmation-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Booking Reference</div>
                                    <div class="booking-detail-value">#<?= $booking_id ?></div>
                                </div>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Status</div>
                                    <span class="status-badge status-pending"><?= $booking['status'] ?></span>
                                </div>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Room Number</div>
                                    <div class="booking-detail-value"><?= htmlspecialchars($booking['room_number']) ?></div>
                                </div>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Bed Type</div>
                                    <div class="booking-detail-value"><?= htmlspecialchars($booking['bed_type']) ?></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Check-in Date</div>
                                    <div class="booking-detail-value"><?= date('F j, Y', strtotime($booking['check_in'])) ?></div>
                                </div>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Check-out Date</div>
                                    <div class="booking-detail-value"><?= date('F j, Y', strtotime($booking['check_out'])) ?></div>
                                </div>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Duration</div>
                                    <div class="booking-detail-value"><?= $num_days ?> night<?= $num_days > 1 ? 's' : '' ?></div>
                                </div>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Price per night</div>
                                    <div class="booking-detail-value">$<?= number_format($booking['price'], 2) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Guest Information</h5>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Name</div>
                                    <div class="booking-detail-value"><?= htmlspecialchars($booking['name']) ?></div>
                                </div>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Email</div>
                                    <div class="booking-detail-value"><?= htmlspecialchars($booking['email']) ?></div>
                                </div>
                                <div class="booking-detail">
                                    <div class="booking-detail-label">Phone</div>
                                    <div class="booking-detail-value"><?= htmlspecialchars($booking['phone']) ?></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <?php if (!empty($booking['special_requests'])): ?>
                                    <h5>Special Requests</h5>
                                    <div class="booking-detail">
                                        <p><?= nl2br(htmlspecialchars($booking['special_requests'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="confirmation-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Total Amount</h5>
                                <div class="total-amount">$<?= number_format($booking['total_amount'], 2) ?></div>
                            </div>
                            <!-- <div>
                                <a href="rooms.php" class="btn btn-outline-dark me-2">
                                    <i class="fas fa-search me-2"></i>Browse More Rooms
                                </a> -->
                                <a href="index.php" class="btn text-white" style="   background: linear-gradient(180deg, #dba135, #f2ca7e) !important;">
                                    <i class="fas fa-home me-2"></i>Return Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <h5><i class="fas fa-info-circle me-2"></i>What's Next?</h5>
                    <p class="mb-1">1. You'll receive a confirmation email with your booking details</p>
                    <p class="mb-1">2. Our team will contact you to confirm your reservation</p>
                    <p class="mb-0">3. Payment will be collected upon arrival</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once("includ/footer.php") ?>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>