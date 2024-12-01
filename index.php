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
</head>

<body>
  <!-- Nav bar  -->
  <?php
  require_once("includ/header.php");
  ?>
  <!-- Swiper js  -->
  <swiper-container class="mySwiper" navigation="true">
    <swiper-slide><img src="image/1.jpg" alt="image riad" /></swiper-slide>
    <swiper-slide><img src="image/2.jpg" alt="image riad" /></swiper-slide>
    <swiper-slide><img src="image/3.jpeg" alt="image riad" /></swiper-slide>
    <swiper-slide><img src="image/4.jpeg" alt="image riad" /></swiper-slide>
    <swiper-slide><img src="image/5.jpeg" alt="image riad" /></swiper-slide>
    <swiper-slide><img src="image/6.jpeg" alt="image riad" /></swiper-slide>
    <swiper-slide><img src="image/7.jpeg" alt="image riad" /></swiper-slide>
    <swiper-slide><img src="image/8.jpeg" alt="image riad" /></swiper-slide>
  </swiper-container>

  <!-- Check-in avaibility -->
  <div class="container aviablity-form">
    <div class="row">
      <div class="bg-white shadow p-4 rounded">
        <form action="room.php" method="GET">
          <div class="row align-items-end col-lg-12">
            <div class="col-lg-3">
              <label class="form-label" style="font-weight: 500">Check-in</label>
              <input type="date" name="checkin" class="form-control shadow-none w-100" required />
            </div>
            <div class="col-lg-3">
              <label class="form-label" style="font-weight: 500">Check-out</label>
              <input type="date" name="checkout" class="form-control shadow-none" required />
            </div>
            <div class="col-lg-3">
              <label class="form-label" style="font-weight: 500">Adults</label>
              <select class="form-select shadow-none" name="adults">
                <option value="1" selected>1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
              </select>
            </div>
            <div class="col-lg-2">
              <label class="form-label" style="font-weight: 500">Children</label>
              <select class="form-select shadow-none" name="children">
                <option value="0" selected>0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
            </div>
            <div class="col-lg-1">
              <button type="submit" class="btn text-white custom-bg drg">
                <a class="text-white" style="text-decoration: none; " href="room.php">Submit</a>

              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- OUR ROOM -->
  <div id="p" class="bg-light p-2 mb-5 mt-5">
    <h2 class="m-4 h-font ps-4 custom-border">
      <span class="fw-bold">OUR</span> ROOMS
    </h2>
  </div>

  <!-- Room swipper -->
  <div class="swiper1 mySwiper2">
    <?php
    require 'db_conection.php';

    // Fetch rooms from database
    $query = "SELECT * FROM rooms";
    $result = $conn->query($query);
    ?>

    <div class="swiper-wrapper">
      <?php while ($room = $result->fetch_assoc()): ?>
        <div class="swiper-slide">
          <div class="card position-relative" style="width: 250px; height: 370px">
            <!-- Price Tag -->
            <h5 class="card-title position-absolute top-0 end-0 p-2 m-2 bg-white rounded shadow fs-6 fw-bold text-warning border border-2 border-warning" style="z-index: 10">
              $<?= number_format($room['price'], 2) ?>
            </h5>

            <!-- Swiper for Room Images -->
            <div class="swiper mySwiper" style="height: 370px;">
              <div class="swiper-wrapper">
                <?php
                // Fetch images for this room
                $img_query = "SELECT image_path FROM room_images WHERE room_id = ?";
                $stmt = $conn->prepare($img_query);
                $stmt->bind_param("i", $room['id']);
                $stmt->execute();
                $images = $stmt->get_result();

                while ($image = $images->fetch_assoc()): ?>
                  <div class="swiper-slide">
                    <img class="img-fluid w-100 h-100"
                      src="/riad/images/rooms/<?= htmlspecialchars($image['image_path']) ?>"
                      alt="Room image"
                      style="object-fit: cover;">
                  </div>
                <?php endwhile;
                $stmt->close();
                ?>
              </div>
              <div class="swiper-button-next"></div>
              <div class="swiper-button-prev"></div>
              <div class="swiper-pagination"></div>
            </div>

            <!-- Card Body -->
            <div class="card-body">

              <p class="card-text"><?= htmlspecialchars($room['bed_type']) ?></p>

              <div class="d-flex gap-2">
                <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn text-white bg-black">Book now</a>
                <a href="room_details.php?id=<?= $room['id'] ?>" class="btn border text-black white-bg">Show more</a>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <div class="swiper-pagination"></div>
  </div>
  <!-- Ou events -->
  <div id="events" class="bg-light p-2 mb-5 mt-5">
    <h2 class="m-4 h-font ps-4 custom-border">
      <span class="fw-bold">OUR</span> Events
    </h2>
  </div>

  <?php
  require 'db_conection.php';

  // Fetch special events for the swiper
  $specialEvents = [];
  $specialQuery = $conn->query("SELECT * FROM events WHERE event_type = 'special' ORDER BY start_date ASC");
  if ($specialQuery) {
    while ($row = $specialQuery->fetch_assoc()) {
      $specialEvents[] = $row;
    }
  }

  // Fetch regular events for the side list
  $regularEvents = [];
  $regularQuery = $conn->query("SELECT * FROM events WHERE event_type = 'regular' ORDER BY posted_at DESC LIMIT 4");
  if ($regularQuery) {
    while ($row = $regularQuery->fetch_assoc()) {
      $regularEvents[] = $row;
    }
  }

  // Function to get correct image path
  function getImageUrl($imagePath)
  {
    if (empty($imagePath)) {
      return '/riad/images/default-event.jpg';
    }

    // If path already contains full URL, return as-is
    if (strpos($imagePath, 'http') === 0) {
      return $imagePath;
    }

    // Remove any leading slashes or dots
    $cleanPath = ltrim($imagePath, '/.');
    $cleanPath = ltrim($cleanPath, './');

    // Check if file exists in images directory
    $fullPath = 'C:/xampp/htdocs/riad/images/' . basename($cleanPath);
    if (file_exists($fullPath)) {
      return '/riad/images/' . basename($cleanPath);
    }

    // Default fallback
    return '/riad/images/default-event.jpg';
  }
  ?>

  <!-- special events -->
  <style>
    .swiper-slide {
      background: #fff;
    }

    .moreEvents {
      background-color: #f8f9fa;
    }

    .event-card {
      transition: all 0.3s ease;
    }

    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .object-fit-cover {
      object-fit: cover;
    }
  </style>

  <div class="container-fluid my-4" id="event">
    <div class="row mx-4">
      <div class="col-lg-7 col-md-12 mb-4">
        <div>
          <h3 class="border border-4 border-bottom-0 border-end-0 border-top-0 p-2">
            <span class="fw-semibold">Special</span> events
          </h3>
        </div>

        <div class="swiper mySwiper" style="height: 600px">
          <div class="swiper-wrapper">
            <?php foreach ($specialEvents as $event): ?>
              <div class="swiper-slide position-relative">
                <img class="img-thumbnail w-100 h-100 object-fit-cover"
                  src="<?= getImageUrl($event['image_path']) ?>"
                  alt="<?= htmlspecialchars($event['title']) ?>">
                <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent)">
                  <h4 class="text-white mb-1"><?= htmlspecialchars($event['title']) ?></h4>
                  <p class="text-white-50 mb-0">
                    <?= date('F j', strtotime($event['start_date'])) ?> -
                    <?= date('F j, Y', strtotime($event['end_date'])) ?>
                  </p>
                </div>
              </div>
            <?php endforeach; ?>

            <?php if (empty($specialEvents)): ?>
              <!-- Default slides if no special events -->
              <div class="swiper-slide position-relative">
                <img class="img-thumbnail w-100 h-100 object-fit-cover" src="/riad/images/shara.jpg" alt="sahar" />
                <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent)">
                  <h4 class="text-white mb-1">Sahara Desert</h4>
                  <p class="text-white-50 mb-0">January 15-30, 2024</p>
                </div>
              </div>
              <div class="swiper-slide position-relative">
                <div class="bg-secondary w-100 h-100"></div>
                <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent)">
                  <h4 class="text-white mb-1">Mountain Expedition</h4>
                  <p class="text-white-50 mb-0">March 5-15, 2024</p>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>

      <!-- More Events -->
      <div class="col-lg-5 col-md-12">
        <div>
          <h3 class="text-capitalize p-2">More events</h3>
        </div>
        <div class="moreEvents d-flex flex-column gap-4 p-4 rounded-4">
          <?php foreach ($regularEvents as $index => $event): ?>
            <div class="d-flex border border-2 border-black p-2 rounded-4 bg-white event-card">
              <div class="d-flex flex-column gap-2 justify-content-center">
                <div class="header"><span>Logo</span> Event <?= $index + 1 ?></div>
                <div class="title text-capitalize fw-semibold fs-5">
                  <?= htmlspecialchars($event['title']) ?>
                </div>
                <div style="max-width: 400px" class="pe-2">
                  <?= htmlspecialchars(substr($event['description'], 0, 150)) ?>...
                </div>
                <span class="text-muted">
                  Posted <?= time_elapsed_string($event['posted_at']) ?> ago
                </span>
              </div>
              <div class="flex-shrink-0">
                <img src="<?= getImageUrl($event['image_path']) ?>"
                  alt="<?= htmlspecialchars($event['title']) ?>"
                  style="width: 150px; height: 150px;"
                  class="rounded-4 object-fit-cover">
              </div>
            </div>
          <?php endforeach; ?>

          <?php if (empty($regularEvents)): ?>
            <!-- Default cards if no regular events -->
            <div class="d-flex border border-2 border-black p-2 rounded-4 bg-white">
              <div class="d-flex flex-column gap-2 justify-content-center">
                <div class="header"><span>Logo</span> Event 1</div>
                <div class="title text-capitalize fw-semibold fs-5">
                  Quad Riding South Morocco
                </div>
                <div style="max-width: 400px" class="pe-2">
                  Experience the thrill of quad biking through the stunning landscapes of Southern Morocco.
                </div>
                <span class="text-muted">Posted 2 days ago</span>
              </div>
              <div class="flex-shrink-0">
                <img src="/riad/images/quad.jpg" alt="quad riding" style="width: 150px; height: 150px;" class="rounded-4 object-fit-cover">
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>




  <?php
  function time_elapsed_string($datetime, $full = false)
  {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calculate weeks and remaining days
    $weeks = floor($diff->d / 7);
    $remaining_days = $diff->d % 7;

    // Build the time string array
    $string = [
      'y' => $diff->y . ' year' . ($diff->y != 1 ? 's' : ''),
      'm' => $diff->m . ' month' . ($diff->m != 1 ? 's' : ''),
      'w' => $weeks . ' week' . ($weeks != 1 ? 's' : ''),
      'd' => $remaining_days . ' day' . ($remaining_days != 1 ? 's' : ''),
      'h' => $diff->h . ' hour' . ($diff->h != 1 ? 's' : ''),
      'i' => $diff->i . ' minute' . ($diff->i != 1 ? 's' : ''),
      's' => $diff->s . ' second' . ($diff->s != 1 ? 's' : '')
    ];

    // Remove empty values
    $string = array_filter($string, function ($value) {
      return strpos($value, '0 ') !== 0;
    });

    if (!$full) {
      $string = array_slice($string, 0, 1);
    }

    return $string ? implode(', ', $string) . ' ago' : 'just now';
  }
  ?>

  <!-- our guides -->
  <div id="p" class="bg-light p-2 mb-5 mt-5">
    <h2 class="m-4 h-font ps-4 custom-border">
      <span class="fw-bold">OUR</span> Guides
    </h2>
  </div>

  <!-- cards guides -->
  <div class="row justify-content-evenly px-lg-0 px-md-0 px-5 gap-2">
    <div class="card p-4 shadow-lg border-0" style="width: 300px">
      <div class="card-body d-flex flex-column align-items-center p-4 gap-2">
        <img
          class="img-thumbnail"
          src="image/abed.jpg"
          alt="abed"
          style="border-radius: 50%; width: 100px" />
        <h5 class="card-title">Abed Mohammed</h5>
        <a
          href="abedmohammed@gmail.com "
          style="text-decoration: none; color: gray">abedmohammed@gmail.com</a>

        <div>
          <a href=""><img src="image/facebook.png" alt="media " width="60px" /></a>
          <a href=""><img src="image/insta.png" alt="media" width="60px" /></a>
          <a href=""><img src="image/x.png" alt="media" width="60px" /></a>
        </div>
      </div>
    </div>

    <div class="card p-4 shadow-lg border-0" style="width: 300px">
      <div class="card-body d-flex flex-column align-items-center p-4 gap-2">
        <img
          class="img-thumbnail"
          src="image/abed.jpg"
          alt="abed"
          style="border-radius: 50%; width: 100px" />
        <h5 class="card-title">Abed Mohammed</h5>
        <a
          href="abedmohammed@gmail.com "
          style="text-decoration: none; color: gray">abedmohammed@gmail.com</a>
        <div>
          <a href=""><img src="image/facebook.png" alt="media " width="60px" /></a>
          <a href=""><img src="image/insta.png" alt="media" width="60px" /></a>
          <a href=""><img src="image/x.png" alt="media" width="60px" /></a>
        </div>
      </div>
    </div>

    <div class="card p-4 shadow-lg border-0" style="width: 300px">
      <div class="card-body d-flex flex-column align-items-center p-4 gap-2">
        <img
          class="img-thumbnail"
          src="image/abed.jpg"
          alt="abed"
          style="border-radius: 50%; width: 100px" />
        <h5 class="card-title">Abed Mohammed</h5>
        <a
          href="abedmohammed@gmail.com "
          style="text-decoration: none; color: gray">abedmohammed@gmail.com</a>

        <div>
          <a href=""><img src="image/facebook.png" alt="media " width="60px" /></a>
          <a href=""><img src="image/insta.png" alt="media" width="60px" /></a>
          <a href=""><img src="image/x.png" alt="media" width="60px" /></a>
        </div>
      </div>
    </div>

    <div class="card p-4 shadow-lg border-0" style="width: 300px">
      <div class="card-body d-flex flex-column align-items-center p-4 gap-2">
        <img
          class="img-thumbnail"
          src="image/abed.jpg"
          alt="abed"
          style="border-radius: 50%; width: 100px" />
        <h5 class="card-title">Abed Mohammed</h5>
        <a
          href="abedmohammed@gmail.com "
          style="text-decoration: none; color: gray">abedmohammed@gmail.com</a>

        <div>
          <a href=""><img src="image/facebook.png" alt="media " width="60px" /></a>
          <a href=""><img src="image/insta.png" alt="media" width="60px" /></a>
          <a href=""><img src="image/x.png" alt="media" width="60px" /></a>
        </div>
      </div>
    </div>

    <div class="card p-4 shadow-lg border-0" style="width: 300px">
      <div class="card-body d-flex flex-column align-items-center p-4 gap-2">
        <img
          class="img-thumbnail"
          src="image/abed.jpg"
          alt="abed"
          style="border-radius: 50%; width: 100px" />
        <h5 class="card-title">Abed Mohammed</h5>
        <a
          href="abedmohammed@gmail.com "
          style="text-decoration: none; color: gray">abedmohammed@gmail.com</a>

        <div>
          <a href=""><img src="image/facebook.png" alt="media " width="60px" /></a>
          <a href=""><img src="image/insta.png" alt="media" width="60px" /></a>
          <a href=""><img src="image/x.png" alt="media" width="60px" /></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Our servise -->
  <div id="p" class="bg-light p-2 mb-5 mt-5">
    <h2 class="m-4 h-font ps-4 custom-border">
      <span class="fw-bold">OUR</span> Services
    </h2>
  </div>
  <!-- Our servise container  -->
  <section class="container">
    <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
      <div
        class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
        <img src="image/wifi.svg" alt="" width="80px" />

        <h5 class="mt-3">Wifi</h5>
      </div>
      <div
        class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
        <img src="image/spa.svg" alt="" width="80px" />

        <h5 class="mt-3">Spa</h5>
      </div>
      <div
        class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
        <img src="image/porter.svg" alt="porter" width="80px" />

        <h5 class="mt-3">Porter</h5>
      </div>
      <div
        class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
        <img src="image/chef.svg" alt="Restaurant" width="80px" />

        <h5 class="mt-3 serv-name">Restaurant</h5>
      </div>
      <div
        class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
        <img
          src="image/Air Conditioner.svg"
          alt="Air Conditioner"
          width="80px" />

        <h5 class="mt-3 serv-name">Air Conditioner</h5>
      </div>
    </div>
  </section>

  <!-- Our Avis -->
  <div id="p" class="bg-light p-2 mb- mt-5">
    <h2 class="m-4 h-font ps-4 custom-border">
      <span class="fw-bold">Some</span> Testimonials
    </h2>
  </div>

  <!-- Testimonials  -->
  <?php
  require 'db_conection.php';

  // Fetch testimonials from database
  $query = "SELECT * FROM testimonials ORDER BY created_at DESC";
  $result = $conn->query($query);
  $testimonials = $result->fetch_all(MYSQLI_ASSOC);
  ?>

  <div class="container my-5">
    <div class="swiper mySwiper1">
      <div class="swiper-wrapper">
        <?php foreach ($testimonials as $testimonial): ?>
          <div class="swiper-slide bg-light p-4">
            <div class="profile d-flex align-items-center mb-3">
              <img class="img-star-avis" src="image/star.svg" width="30px" />
              <h6 class="m-0 ms-2"><?= htmlspecialchars($testimonial['client_name']) ?></h6>
            </div>
            <div>
              <p><?= htmlspecialchars($testimonial['content']) ?></p>
              <div class="rating">
                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                  <i class="bi bi-star-fill text-warning"></i>
                <?php endfor; ?>
                <?php for ($i = $testimonial['rating']; $i < 5; $i++): ?>
                  <i class="bi bi-star text-warning"></i>
                <?php endfor; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>

  <!-- Initialize Swiper -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new Swiper('.mySwiper1', {
        slidesPerView: 1,
        spaceBetween: 20,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        breakpoints: {
          768: {
            slidesPerView: 2,
            spaceBetween: 30,
          },
          992: {
            slidesPerView: 3,
            spaceBetween: 40,
          }
        }
      });
    });
  </script>

  <!-- Footer -->
  <?php require_once("includ/footer.php") ?>




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

  <script>
    var swiper = new Swiper(".mySwiper1", {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      slidesPerView: 3,
      loop: true,
      coverflowEffect: {
        rotate: 50,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: false,
      },
      pagination: {
        el: ".swiper-pagination",
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
          spaceBetween: 10,
        },

        640: {
          slidesPerView: 3,
          spaceBetween: 20,
        },

        768: {
          slidesPerView: 2,
          spaceBetween: 30,
        },

        1024: {
          slidesPerView: 3,
          spaceBetween: 40,
        },
      },
    });
  </script>
</body>

</html>