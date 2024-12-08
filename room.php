<?php
require("db_conection.php");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get filter parameters (if any)
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : '';
$adults = isset($_GET['adults']) ? intval($_GET['adults']) : 0;
$children = isset($_GET['children']) ? intval($_GET['children']) : 0;
$selected_facilities = isset($_GET['facilities']) ? $_GET['facilities'] : [];

// Fetch all facilities for filter sidebar
$facilities_query = "SELECT * FROM facilities ORDER BY facility_name";
$facilities_result = $conn->query($facilities_query);
$all_facilities = [];
if ($facilities_result->num_rows > 0) {
  while ($row = $facilities_result->fetch_assoc()) {
    $all_facilities[] = $row;
  }
}

// Build query to fetch rooms with filter conditions
$query = "SELECT DISTINCT r.* FROM rooms r";

// Join with room_facilities if facility filter is applied
if (!empty($selected_facilities)) {
  $query .= " JOIN room_facilities rf ON r.id = rf.room_id 
               WHERE rf.facilities_id IN (" . implode(',', array_map('intval', $selected_facilities)) . ")";
}

// Add adult/children filter if provided
if ($adults > 0 || $children > 0) {
  if (strpos($query, 'WHERE') === false) {
    $query .= " WHERE";
  } else {
    $query .= " AND";
  }

  if ($adults > 0) {
    $query .= " r.adult >= $adults";
  }

  if ($children > 0) {
    if ($adults > 0) {
      $query .= " AND";
    }
    $query .= " r.children >= $children";
  }
}

// Order by price (you can add more sorting options)
$query .= " ORDER BY r.price ASC";

$rooms_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Room Page</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="css/style.css" />
</head>

<header>
  <?php require("includ/header.php"); ?>
</header>

<body>
  <div class="container " style="margin-top: 90px;">
    <div class="row">
      <!-- Filters Column -->
      <div class="col-lg-3 col-md-12 mb-lg-0 mb-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded shadow">
          <div class="container-fluid flex-lg-column align-items-stretch">
            <h4 class="mt-2">FILTERS</h4>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#filterdrop"
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-column align-items-stretch mt-3" id="filterdrop">
              <form action="" method="GET" class="d-flex flex-column gap-3"> <!-- Added flex-column and gap-3 -->

                <!-- Check Availability Section -->
                <div class="border bg-white p-3 rounded w-100">
                  <h5 class="mb-3" style="font-size: 18px">Check availability</h5>
                  <div class="mb-3">
                    <label class="form-label">Check-in</label>
                    <input type="date" name="checkin" value="<?= htmlspecialchars($checkin) ?>" class="form-control shadow-none" />
                  </div>
                  <div>
                    <label class="form-label">Check-out</label>
                    <input type="date" name="checkout" value="<?= htmlspecialchars($checkout) ?>" class="form-control shadow-none" />
                  </div>
                </div>

                <!-- Facilities Section -->
                <div class="border bg-white p-3 rounded w-100">
                  <h5 class="mb-3" style="font-size: 18px">Facilities</h5>
                  <div class="d-flex flex-column"> <!-- Changed to flex-column -->
                    <?php foreach ($all_facilities as $facility): ?>
                      <div class="mb-2 form-check">
                        <input type="checkbox" id="f<?= $facility['id'] ?>" name="facilities[]" value="<?= $facility['id'] ?>"
                          class="form-check-input shadow-none me-2"
                          <?= in_array($facility['id'], $selected_facilities) ? 'checked' : '' ?> />
                        <label class="form-check-label" for="f<?= $facility['id'] ?>"><?= htmlspecialchars($facility['facility_name']) ?></label>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Guests Section -->
                <div class="border bg-white p-3 rounded w-100">
                  <h5 class="mb-3" style="font-size: 18px">GUESTS</h5>
                  <div class="row g-2"> <!-- Changed to row with gutter -->
                    <div class="col-6">
                      <label class="form-label">Adults</label>
                      <input type="number" name="adults" min="0" value="<?= $adults ?>" class="form-control shadow-none" />
                    </div>
                    <div class="col-6">
                      <label class="form-label">Children</label>
                      <input type="number" name="children" min="0" value="<?= $children ?>" class="form-control shadow-none" />
                    </div>
                  </div>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-2">Apply Filters</button>
              </form>
            </div>
          </div>
        </nav>
      </div>
      <!-- Rooms Column -->
      <div class="col-lg-9 col-md-12 px-4">
        <?php if ($rooms_result && $rooms_result->num_rows > 0): ?>
          <?php while ($room = $rooms_result->fetch_assoc()): ?>
            <?php
            // Get room facilities
            $room_facilities_query = "SELECT f.facility_name 
                              FROM facilities f 
                              JOIN room_facilities rf ON f.id = rf.facilities_id 
                              WHERE rf.room_id = {$room['id']}";
            $room_facilities_result = $conn->query($room_facilities_query);
            $room_facilities = [];

            if ($room_facilities_result->num_rows > 0) {
              while ($facility = $room_facilities_result->fetch_assoc()) {
                $room_facilities[] = $facility['facility_name'];
              }
            }

            // Get room images
            $room_images_query = "SELECT image_path FROM room_images WHERE room_id = {$room['id']}";
            $room_images_result = $conn->query($room_images_query);
            $room_images = [];

            if ($room_images_result->num_rows > 0) {
              while ($image = $room_images_result->fetch_assoc()) {
                $room_images[] = $image['image_path'];
              }
            }
            ?>

            <!-- Room Card -->
            <div class="card mb-4 border-0 shadow">
              <div class="row g-0 p-3 align-items-center">
                <!-- Room Images -->
                <div class="col-md-5 mb-lg-0 mb-md-0 mb-3">
                  <div class="swiper roomSwiper-<?= $room['id'] ?>" style="height: 270px;">
                    <div class="swiper-wrapper">
                      <?php
                      // Fetch images for this room
                      $img_query = "SELECT image_path FROM room_images WHERE room_id = ?";
                      $stmt = $conn->prepare($img_query);
                      $stmt->bind_param("i", $room['id']);
                      $stmt->execute();
                      $images = $stmt->get_result();

                      if ($images->num_rows > 0) {
                        while ($image = $images->fetch_assoc()): ?>
                          <div class="swiper-slide">
                            <img class="img-fluid w-100 h-100"
                              src="/riad/images/rooms/<?= htmlspecialchars($image['image_path']) ?>"
                              alt="Room <?= htmlspecialchars($room['room_number']) ?>"
                              style="object-fit: cover;">
                          </div>
                        <?php endwhile;
                      } else { ?>
                        <div class="swiper-slide">
                          <img class="img-fluid w-100 h-100"
                            src="/riad/images/rooms/default-room.webp"
                            alt="Default room image"
                            style="object-fit: cover;">
                        </div>
                      <?php }
                      $stmt->close();
                      ?>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                  </div>
                </div>
                <!-- Room Details -->
                <div class="col-md-5 px-lg-3 px-0">
                  <h5 class="mb-3 fw-bold">Room <?= htmlspecialchars($room['room_number']) ?> - <?= htmlspecialchars($room['bed_type']) ?></h5>

                  <div class="features mb-3">
                    <h6 class="mb-1 text-secondary">
                      <i class="bi bi-building"></i> Features
                    </h6>
                    <div>
                      <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1"><?= htmlspecialchars($room['bed_type']) ?></span>
                      <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1">Floor <?= htmlspecialchars($room['floor']) ?></span>
                      <?php if (strpos($room['description'], 'balcony') !== false): ?>
                        <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1">Balcony</span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="facilities mb-3">
                    <h6 class="mb-1 text-secondary">
                      <i class="bi bi-stars"></i> Facilities
                    </h6>
                    <div>
                      <?php foreach ($room_facilities as $facility): ?>
                        <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1"><?= htmlspecialchars($facility) ?></span>
                      <?php endforeach; ?>
                    </div>
                  </div>

                  <div class="guests">
                    <h6 class="mb-1 text-secondary">
                      <i class="bi bi-people"></i> Guests
                    </h6>
                    <div>
                      <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1"><?= $room['adult'] ?> Adults</span>
                      <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1"><?= $room['children'] ?> Children</span>
                      <?php if (strpos(strtolower($room['description']), 'pet') !== false): ?>
                        <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1">Pet Friendly</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <!-- Price and Booking -->
                <div class="col-md-2 d-flex flex-column justify-content-between">
                  <div class="price text-center mb-2">
                    <h5 class="mb-0 fw-bold text-success">$<?= $room['price'] ?></h5>
                    <small class="text-muted">per night</small>
                  </div>

                  <div class="booking-buttons">
                    <a href="book.php?room_id=<?= $room['id'] ?>" class="btn btn-sm text-white bg-dark w-100 mb-2 py-2">Book Now</a>
                    <a href="room_details.php?id=<?= $room['id'] ?>" class="btn btn-sm btn-outline-dark w-100">View Details</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Initialize Swiper for this specific room -->
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                new Swiper(".roomSwiper-<?= $room['id'] ?>", {
                  navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                  },
                  pagination: {
                    el: ".swiper-pagination",
                  },
                });
              });
            </script>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="alert alert-info">No rooms found matching your criteria.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/lucide@0.487.0/dist/cjs/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>

<footer>
  <?php require("includ/footer.php"); ?>
</footer>

</html>

<?php
// Close connection
$conn->close();
?>