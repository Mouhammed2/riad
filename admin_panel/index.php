<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
</head>

<body id="body-pd">
  <?php
  require("nav.php");
  require("../db_conection.php"); // Include your database connection file

  // Fetch statistics from database
  $stats = [
    'total_bookings' => 0,
    'available_rooms' => 0,
    'total_users' => 0,
    'average_rating' => 0
  ];

  // 1. Get total bookings
  $booking_query = "SELECT COUNT(*) as total FROM bookings";
  $booking_result = $conn->query($booking_query);
  if ($booking_result && $booking_result->num_rows > 0) {
    $stats['total_bookings'] = $booking_result->fetch_assoc()['total'];
  }

  // 2. Get available rooms
  $rooms_query = "SELECT COUNT(*) as available FROM rooms WHERE status = 'available'";
  $rooms_result = $conn->query($rooms_query);
  if ($rooms_result && $rooms_result->num_rows > 0) {
    $stats['available_rooms'] = $rooms_result->fetch_assoc()['available'];
  }

  // 3. Get total users
  $users_query = "SELECT COUNT(*) as total FROM users";
  $users_result = $conn->query($users_query);
  if ($users_result && $users_result->num_rows > 0) {
    $stats['total_users'] = $users_result->fetch_assoc()['total'];
  }

  // 4. Get average rating
  $rating_query = "SELECT AVG(rating) as average FROM testimonials WHERE rating IS NOT NULL";
  $rating_result = $conn->query($rating_query);
  if ($rating_result && $rating_result->num_rows > 0) {
    $avg = $rating_result->fetch_assoc()['average'];
    $stats['average_rating'] = number_format($avg, 1); // Format to 1 decimal place
  }

  $conn->close();
  ?>

  <!--Container Main start-->
  <div class="height-100 bg-white">
    <section class="content px-3 py-4">
      <div class="container-fluid">
        <div class="mb-3">
          <h3 class="fw-bold fs-4 mb-3">Dashboard</h3>
          <p class="text-muted">Last updated: <?php echo date('F j, Y, g:i a'); ?></p>
        </div>
        <div class="row mt-5">
          <!-- dashboard card -->
          <div class="col-12 col-md-3 rounded text-start mb-3">
            <div class="card shadow">
              <div class="card-body py-4">
                <div class="d-flex justify-content-between">
                  <div>
                    <h6 class="mb-2 fw-bold">Total Booking</h6>
                    <p class="fw-bold mb-2"><?php echo $stats['total_bookings']; ?></p>
                    <small class="text-success">+5% from last month</small>
                  </div>
                  <span class="align-self-start">
                    <img
                      src="card_icon/room.svg"
                      alt="room"
                      style="width: 50px" />
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- room available card -->
          <div class="col-12 col-md-3 rounded text-start mb-3">
            <div class="card shadow">
              <div class="card-body py-4 position-relative">
                <h6 class="mb-2 fw-bold">Room Available</h6>
                <p class="fw-bold mb-2"><?php echo $stats['available_rooms']; ?></p>
                <small class="text-info"><?php echo round(($stats['available_rooms'] / 50) * 100); ?>% of total</small>
                <span class="position-absolute top-0 end-0 p-2 m-2">
                  <img
                    src="card_icon/bed.svg"
                    alt="room"
                    style="width: 50px" />
                </span>
              </div>
            </div>
          </div>

          <!-- users card -->
          <div class="col-12 col-md-3 rounded text-start mb-3">
            <div class="card shadow">
              <div class="card-body py-4 position-relative">
                <h6 class="mb-2 fw-bold">Users</h6>
                <p class="fw-bold mb-2"><?php echo $stats['total_users']; ?></p>
                <small class="text-success">+10 new this week</small>
                <span class="position-absolute top-0 end-0 p-2 m-2">
                  <img
                    src="card_icon/user.svg"
                    alt="room"
                    style="width: 50px" />
                </span>
              </div>
            </div>
          </div>

          <!-- rating and reviews card -->
          <div class="col-12 col-md-3 rounded text-start mb-3">
            <div class="card shadow">
              <div class="card-body py-4 position-relative">
                <h6 class="mb-2 fw-bold">Rating & Reviews</h6>
                <p class="fw-bold mb-2"><?php echo $stats['average_rating']; ?>/5</p>
                <small class="text-warning">Based on 120 reviews</small>
                <span class="position-absolute top-0 end-0 p-2 m-2">
                  <img
                    src="card_icon/review.svg"
                    alt="room"
                    style="width: 50px" />
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <script src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</body>

</html>