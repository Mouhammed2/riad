<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>admin</title>
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

  ?>
  <!--Container Main start-->
  <div
    class="height-100 bg-white d-flex justify-content-center align-items-center"
    style="width: 100%">
    <!-- table of roonm -->
    <?php
    require '../db_conection.php';
    $facilityMap = array(
      1 => 'WiFi',
      2 => 'Air Conditioning',
      3 => 'Room Service',
      4 => 'Coffee Maker',
      5 => 'TV',
      6 => 'Balcony',
      7 => 'Jacuzzi',
      8 => 'Breakfast'
    );
    // Query to get the room data
    $query = "SELECT r.id, r.room_number, r.bed_type, r.floor, r.price, r.quantity, r.adult, r.children, r.description, r.status
      FROM rooms r";
    $result = mysqli_query($conn, $query);
    if (!$result) {
      echo "Error fetching rooms: " . mysqli_error($conn);
      exit;
    }
    ?>
    <div class="container py-4">
      <div class="text-end mb-3">
        <button type="button" class="btn btn-color" data-bs-toggle="modal" data-bs-target="#addRoomModal">
          <i class="bx bx-plus me-1"></i> Add New Room
        </button>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table table-hover room-table">
              <thead class="table-secondary">
                <tr>
                  <th>Room Id</th>
                  <th>Bed Type</th>
                  <th>Floor</th>
                  <th>Facilities</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($room = mysqli_fetch_assoc($result)): ?>
                  <tr>
                    <td><strong><?php echo $room['room_number']; ?></strong></td>
                    <td><?php echo $room['bed_type']; ?></td>
                    <td><?php echo $room['floor']; ?></td>
                    <td>
                      <?php
                      // Query to get the facilities for this room - using prepared statement for security
                      $facilities_query = "SELECT facilities_id FROM room_facilities WHERE room_id = ?";
                      $stmt = mysqli_prepare($conn, $facilities_query);
                      mysqli_stmt_bind_param($stmt, "i", $room['id']);
                      mysqli_stmt_execute($stmt);
                      $facilities_result = mysqli_stmt_get_result($stmt);

                      // Check if there are facilities for this room
                      if (mysqli_num_rows($facilities_result) > 0) {
                        // Loop through the facilities and display them all
                        while ($facility = mysqli_fetch_assoc($facilities_result)) {
                          $facilityId = $facility['facilities_id'];
                          $facilityName = isset($facilityMap[$facilityId]) ? $facilityMap[$facilityId] : 'Unknown Facility';
                          echo "<span class='badge bg-secondary me-1'>" . $facilityName . "</span>";
                        }
                      } else {
                        echo "No facilities available";
                      }
                      mysqli_stmt_close($stmt);
                      ?>
                    </td>
                    <td>
                      <span class="badge rounded-pill <?php echo $room['status'] == 'Available' ? 'bg-success' : 'bg-danger'; ?> status-badge">
                        <?php echo $room['status']; ?>
                      </span>
                    </td>
                    <td class="action-buttons">
                      <!-- Edit Button -->
                      <a href="#"
                        class="action-btn"
                        title="Edit Room"
                        data-bs-toggle="modal"
                        data-bs-target="#editRoomModal"
                        data-id="<?= $room['id'] ?>"
                        data-room_number="<?= $room['room_number'] ?>"
                        data-bed_type="<?= $room['bed_type'] ?>"
                        data-floor="<?= $room['floor'] ?>"
                        data-status="<?= $room['status'] ?>"
                        data-adult="<?= $room['adult'] ?>"
                        data-children="<?= $room['children'] ?>"
                        data-quantity="<?= $room['quantity'] ?>"
                        data-price="<?= $room['price'] ?>"
                        data-description="<?= htmlspecialchars($room['description']) ?>"
                        data-facilities="<?= !empty($room['facilities']) ? htmlspecialchars(is_array($room['facilities']) ? implode(',', $room['facilities']) : $room['facilities']) : '' ?>">
                        <i class="bx bx-edit"></i>
                      </a>

                      <!-- Image Button -->
                      <a href="#"
                        class="action-btn"
                        title="Manage Images"
                        data-bs-toggle="modal"
                        data-bs-target="#ImageRoomModal"
                        data-room-id="<?= $room['id'] ?>">
                        <i class="bx bx-image-add"></i>
                      </a>

                      <!-- Delete Button -->
                      <a href="crud_room/delete.php?id=<?= $room['id'] ?>"
                        class="action-btn"
                        title="Delete Room"
                        onclick="return confirm('Are you sure you want to delete this room?');">
                        <i class="bx bx-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    </tbody>
    </table>
  </div>
  </div>
  </div>
  </div>

  <style>
    .action-buttons {
      white-space: nowrap;
    }

    .action-btn {
      text-decoration: none !important;
      display: inline-block;
      cursor: pointer;
      margin: 0 4px;
      padding: 4px;
      border-radius: 4px;
      transition: all 0.2s ease;
    }

    .action-btn:hover {
      background-color: #f5f5f5;
    }

    .action-btn i {
      font-size: 22px;
      color: #6c757d;
      vertical-align: middle;
    }

    .action-btn:hover i.bx-edit {
      color: #0d6efd !important;
    }

    .action-btn:hover i.bx-image-add {
      color: #6f42c1 !important;
    }

    .action-btn:hover i.bx-trash {
      color: #dc3545 !important;
    }
  </style>
  <?php
  mysqli_close($conn);
  ?>

  <!-- script js for update -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const editRoomModal = document.getElementById('editRoomModal');

      editRoomModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const id = button.getAttribute('data-id');
        const roomNumber = button.getAttribute('data-room_number');
        const bedType = button.getAttribute('data-bed_type');
        const floor = button.getAttribute('data-floor');
        const status = button.getAttribute('data-status');
        const adult = button.getAttribute('data-adult');
        const children = button.getAttribute('data-children');
        const quantity = button.getAttribute('data-quantity');
        const price = button.getAttribute('data-price');
        const description = button.getAttribute('data-description');
        const facilities = button.getAttribute('title'); // Get facilities from title attribute

        document.getElementById('roomId').value = id;
        document.getElementById('roomNumber').value = roomNumber;
        document.getElementById('bedType').value = bedType;
        document.getElementById('roomFloor').value = floor;
        document.getElementById('roomStatus').value = status;
        document.getElementById('maxAdult').value = adult;
        document.getElementById('maxChildren').value = children;
        document.getElementById('quantity').value = quantity;
        document.getElementById('price').value = price;
        document.getElementById('roomDescription').value = description;


        if (facilities) {
          const facilitiesArray = facilities.split(',');
          const checkboxes = document.querySelectorAll('input[name="facilities[]"]');

          checkboxes.forEach(checkbox => {
            checkbox.checked = facilitiesArray.includes(checkbox.value);
          });
        }
      });
    });
  </script>



  <!-- room modal for add room -->
  <form action="crud_room/add_room.php" method="post">
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="roomNumber" class="form-label">Room Number</label>
                <input name="roomnumber" type="number" class="form-control" id="roomNumber" required min="1">
              </div>
              <div class="col-md-6">
                <label for="bedType" class="form-label">Bed Type</label>
                <select name="bedType" class="form-select" id="bedType" required>
                  <option value="" selected disabled>Select bed type</option>
                  <option value="Single bed">Single bed</option>
                  <option value="Double bed">Double bed</option>
                  <option value="Twin bed">Twin bed</option>
                  <option value="VIP">VIP</option>
                  <option value="Suite">Suite</option>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="roomFloor" class="form-label">Room Floor</label>
                <select name="roomfloor" class="form-select" id="roomFloor" required>
                  <option value="" selected disabled>Select floor</option>
                  <option value="Floor-1">Floor-1</option>
                  <option value="Floor-2">Floor-2</option>
                  <option value="Floor-3">Floor-3</option>
                  <option value="Floor-4">Floor-4</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="roomStatus" class="form-label">Status</label>
                <select name="roomStatus" class="form-select" id="roomStatus" required>
                  <option value="Available" selected>Available</option>
                  <option value="Occupied">Occupied</option>
                  <option value="Maintenance">Maintenance</option>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="maxAdult" class="form-label">Adult (Max)</label>
                <input name="max_adult" type="number" class="form-control" id="maxAdult" required min="1">
              </div>
              <div class="col-md-6">
                <label for="maxChildren" class="form-label">Children (Max)</label>
                <input name="max_children" type="number" class="form-control" id="maxChildren" required min="0">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="quantity" class="form-label">Quantity</label>
                <input name="quantity" type="number" class="form-control" id="maxAdult" required min="1">
              </div>
              <div class="col-md-6">
                <label for="price" class="form-label">Price</label>
                <input name="price" type="number" class="form-control" id="maxChildren" required min="0">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Room Facilities</label>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="WiFi" id="wifi" checked>
                    <label class="form-check-label" for="wifi">WiFi</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Air Conditioning" id="ac" checked>
                    <label class="form-check-label" for="ac">Air Conditioning</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Room Service" id="roomService" checked>
                    <label class="form-check-label" for="roomService">Room Service</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Mini Bar" id="miniBar" checked>
                    <label class="form-check-label" for="coffeemaker">Coffee Maker</label>
                  </div>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="TV" id="tv">
                    <label class="form-check-label" for="tv">TV</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Balcony" id="balcony">
                    <label class="form-check-label" for="balcony">Balcony</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Jacuzzi" id="jacuzzi">
                    <label class="form-check-label" for="jacuzzi">Jacuzzi</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Breakfast" id="breakfast">
                    <label class="form-check-label" for="breakfast">Breakfast</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="roomDescription" class="form-label">Room Description (Optional)</label>
              <textarea name="roomdescription" class="form-control" id="roomDescription" rows="3" placeholder="Enter additional details about the room"></textarea>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-color">Save Room</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Edit Room Modal -->
  <div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form action="crud_room/edite_room.php" method="post">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Room</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <!-- Hidden field to hold Room ID -->
            <input type="hidden" name="room_id" id="roomId">

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="roomNumber" class="form-label">Room Number</label>
                <input name="roomnumber" type="number" class="form-control" id="roomNumber" required min="1">
              </div>
              <div class="col-md-6">
                <label for="bedType" class="form-label">Bed Type</label>
                <select name="bedType" class="form-select" id="bedType" required>
                  <option value="" disabled>Select bed type</option>
                  <option value="Single bed">Single bed</option>
                  <option value="Double bed">Double bed</option>
                  <option value="Twin bed">Twin bed</option>
                  <option value="VIP">VIP</option>
                  <option value="Suite">Suite</option>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="roomFloor" class="form-label">Room Floor</label>
                <select name="roomfloor" class="form-select" id="roomFloor" required>
                  <option value="" disabled>Select floor</option>
                  <option value="Floor-1">Floor-1</option>
                  <option value="Floor-2">Floor-2</option>
                  <option value="Floor-3">Floor-3</option>
                  <option value="Floor-4">Floor-4</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="roomStatus" class="form-label">Status</label>
                <select name="roomStatus" class="form-select" id="roomStatus" required>
                  <option value="Available">Available</option>
                  <option value="Occupied">Occupied</option>
                  <option value="Maintenance">Maintenance</option>
                </select>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="maxAdult" class="form-label">Adult (Max)</label>
                <input name="max_adult" type="number" class="form-control" id="maxAdult" required min="1">
              </div>
              <div class="col-md-6">
                <label for="maxChildren" class="form-label">Children (Max)</label>
                <input name="max_children" type="number" class="form-control" id="maxChildren" required min="0">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="quantity" class="form-label">Quantity</label>
                <input name="quantity" type="number" class="form-control" id="quantity" required min="1">
              </div>
              <div class="col-md-6">
                <label for="price" class="form-label">Price</label>
                <input name="price" type="number" class="form-control" id="price" required min="0">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Room Facilities</label>
              <div class="row">
                <!-- Repeat for all facilities -->
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="WiFi" id="wifi">
                    <label class="form-check-label" for="wifi">WiFi</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Air Conditioning" id="ac">
                    <label class="form-check-label" for="ac">Air Conditioning</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Room Service" id="roomService">
                    <label class="form-check-label" for="roomService">Room Service</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Mini Bar" id="miniBar">
                    <label class="form-check-label" for="miniBar">Mini Bar</label>
                  </div>
                </div>
                <div class="col-md-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="TV" id="tv">
                    <label class="form-check-label" for="tv">TV</label>
                  </div>
                </div>
                <div class="col-md-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Balcony" id="balcony">
                    <label class="form-check-label" for="balcony">Balcony</label>
                  </div>
                </div>
                <div class="col-md-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Jacuzzi" id="jacuzzi">
                    <label class="form-check-label" for="jacuzzi">Jacuzzi</label>
                  </div>
                </div>
                <div class="col-md-3 mt-2">
                  <div class="form-check">
                    <input class="form-check-input" name="facilities[]" type="checkbox" value="Breakfast" id="breakfast">
                    <label class="form-check-label" for="breakfast">Breakfast</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="roomDescription" class="form-label">Room Description</label>
              <textarea name="roomdescription" class="form-control" id="roomDescription" rows="3" placeholder="Enter additional details about the room"></textarea>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-color">Save Changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- images room Modal -->
  <div class="modal fade" id="ImageRoomModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="crud_room/room_img.php" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel">Upload Room Picture</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Hidden room ID field -->
            <input type="hidden" name="room_id" id="modalRoomId" value="">

            <!-- Upload Section -->
            <div class="mb-3">
              <label for="roomImage" class="form-label">Select Image</label>
              <input type="file" class="form-control" id="roomImage" name="image" required accept="image/*">
            </div>
            <button type="submit" class="btn btn-dark shadow-none">Upload Image</button>

            <!-- Image Display Table -->
            <div class="mt-4">
              <table class="table">
                <thead class="table-dark">
                  <tr>
                    <th>Image</th>
                    <th>Preview</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="roomImagesTable">
                  <?php
                  if (isset($_GET['room_id'])) {
                    $room_id = (int)$_GET['room_id'];
                    $stmt = $conn->prepare("SELECT * FROM room_images WHERE room_id = ?");
                    $stmt->bind_param("i", $room_id);
                    $stmt->execute();
                    $images = $stmt->get_result();

                    while ($image = $images->fetch_assoc()) {
                      echo '<tr>
                    <td>' . htmlspecialchars($image['image_path']) . '</td>
                    <td>
                        <img src="/riad/images/rooms/' . htmlspecialchars($image['image_path']) . '" 
                             style="max-height:100px;width:auto;"
                             onerror="this.style.display=\'none\'">
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
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('ImageRoomModal');

      modal.addEventListener('show.bs.modal', function(event) {
        // Get the room ID from the button
        const button = event.relatedTarget;
        const roomId = button.getAttribute('data-room-id');

        // Set the room ID in the hidden field
        document.getElementById('modalRoomId').value = roomId;

        // Load images for this room
        fetch(`crud_room/get_room_images.php?room_id=${roomId}`)
          .then(response => response.text())
          .then(html => {
            document.getElementById('roomImagesTable').innerHTML = html;
          })
          .catch(error => {
            console.error('Error loading images:', error);
            document.getElementById('roomImagesTable').innerHTML = `
                    <tr>
                        <td colspan="3" class="text-danger">Error loading images</td>
                    </tr>`;
          });
      });
    });


    
  </script>
  <script src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  </form>
</body>

</html>