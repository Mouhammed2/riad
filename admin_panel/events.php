<?php
require '../db_conection.php';
session_start();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_event'])) {
        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'C:/xampp/htdocs/riad/images/';
            $fileName = uniqid() . '_' . basename($_FILES['event_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $targetPath)) {
                $imagePath = 'images/' . $fileName;
            } else {
                $_SESSION['error'] = "Failed to upload image. Check directory permissions.";
            }
        }

        // Insert new event
        $stmt = $conn->prepare("INSERT INTO events 
                              (title, description, event_type, start_date, end_date, image_path) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssss",
            $_POST['title'],
            $_POST['description'],
            $_POST['event_type'],
            $_POST['start_date'],
            $_POST['end_date'],
            $imagePath
        );
        $stmt->execute();

        header("Location: events.php?success=1");
        exit;
    }

    // Handle delete action
    if (isset($_POST['delete_event'])) {
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $_POST['event_id']);
        $stmt->execute();
        header("Location: events.php?deleted=1");
        exit;
    }

    // Handle toggle status
    if (isset($_POST['toggle_status'])) {
        $stmt = $conn->prepare("UPDATE events SET is_active = NOT is_active WHERE id = ?");
        $stmt->bind_param("i", $_POST['event_id']);
        $stmt->execute();
        header("Location: events.php");
        exit;
    }
}

// Handle GET requests for actions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && isset($_GET['event_id'])) {
        $event_id = (int)$_GET['event_id'];

        if ($_GET['action'] === 'toggle_status') {
            $stmt = $conn->prepare("UPDATE events SET is_active = NOT is_active WHERE id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            header("Location: events.php");
            exit;
        }

        if ($_GET['action'] === 'delete_event') {
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            header("Location: events.php?deleted=1");
            exit;
        }
    }
}

// Fetch all events for listing
$events = [];
$result = $conn->query("SELECT * FROM events ORDER BY event_type DESC, start_date ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link rel="stylesheet" href="style.css" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    

    <style>
        body {
            font-size: 1rem;
        }

        .table th,
        .table td {
            font-size: 1rem;
            padding: 8px;
        }

        .action-btn {
            text-decoration: none !important;
            display: inline-block;
            cursor: pointer;
            margin: 0 4px;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
            background-color: transparent;
        }

        .action-btn:hover {
            background-color: #f5f5f5;
        }

        .action-btn i {
            font-size: 20px;
            color: #6c757d;
            vertical-align: middle;
            transition: color 0.2s ease;
        }

        .action-btn:hover i.bx-edit-alt {
            color: #0d6efd !important;
        }

        .action-btn:hover i.bx-power-off {
            color: #198754 !important;
        }

        .action-btn:hover i.bx-trash {
            color: #dc3545 !important;
        }

        .custom-border {
            border-left: 7px solid #0d6efd;
        }

        .room-table th {
            padding: 15px 10px;
        }

        .room-table td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        .event-image-preview {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .btn-color {
            background: linear-gradient(180deg, #dba135, #f2ca7e) !important;
            color: #f8f9fa !important;
        }

        .empty-table-message {
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>

<body>
    <?php
    require("nav.php");

    ?>
    <div class="container-fluid mt-4">
        <div class="bg-white p-4 mb- mt-5">
            <h2 class="m-4 h-font ps-4">
                <span class="fw-bold">OUR</span> Events
            </h2>
        </div>
        <div class="text-end mb-3">
            <button type="button" class="btn btn-color" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class='bx bx-plus'></i> Add New Event
            </button>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Events List -->
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover room-table">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Image</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?= $event['id'] ?></td>
                                    <td><?= htmlspecialchars($event['title']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $event['event_type'] === 'special' ? 'warning' : 'primary' ?>">
                                            <?= ucfirst($event['event_type']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($event['image_path']): ?>
                                            <img src="/<?= htmlspecialchars($event['image_path']) ?>"
                                                alt="Event image"
                                                class="event-image-preview rounded">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= date('M j', strtotime($event['start_date'])) ?> -
                                        <?= date('M j, Y', strtotime($event['end_date'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $event['is_active'] ? 'success' : 'danger' ?>">
                                            <?= $event['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <!-- Toggle Status Button -->
                                        <a href="events.php?action=toggle_status&event_id=<?= $event['id'] ?>" class="action-btn" title="<?= $event['is_active'] ? 'Deactivate' : 'Activate' ?>" onclick="return confirm('Are you sure you want to <?= $event['is_active'] ? 'deactivate' : 'activate' ?> this event?');">
                                            <i class='bx bx-power-off'></i>
                                        </a>

                                        <!-- Delete Button -->
                                        <a href="events.php?action=delete_event&event_id=<?= $event['id'] ?>" class="action-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this event?');">
                                            <i class='bx bx-trash'></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-white" style="background: linear-gradient(180deg, #dba135, #f2ca7e) !important; color: #f8f9fa !important;">
                        <h5 class="modal-title" id="addEventModalLabel">Add New Event</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title*</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Event Type*</label>
                                <select name="event_type" class="form-select" required>
                                    <option value="regular">Regular Event</option>
                                    <option value="special">Special Event</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date*</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date*</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description*</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Event Image</label>
                                <input type="file" name="event_image" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2MB (JPEG, PNG, GIF)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_event" class="btn btn-color">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {

            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId)

                // Validate that all variables exist
                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        // show navbar
                        nav.classList.toggle('show')
                        // change icon
                        toggle.classList.toggle('bx-x')
                        // add padding to body
                        bodypd.classList.toggle('body-pd')
                        // add padding to header
                        headerpd.classList.toggle('body-pd')
                    })
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')

            /*===== LINK ACTIVE =====*/
            const linkColor = document.querySelectorAll('.nav_link ,.last-nav')

            function colorLink() {
                if (linkColor) {
                    linkColor.forEach(l => l.classList.remove('active'))
                    this.classList.add('active')
                }
            }
            linkColor.forEach(l => l.addEventListener('click', colorLink))
        });
    </script>
      <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId)

                // Validate that all variables exist
                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        // show navbar
                        nav.classList.toggle('show')
                        // change icon
                        toggle.classList.toggle('bx-x')
                        // add padding to body
                        bodypd.classList.toggle('body-pd')
                        // add padding to header
                        headerpd.classList.toggle('body-pd')
                    })
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')

            /*===== LINK ACTIVE =====*/
            const linkColor = document.querySelectorAll('.nav_link, .last-nav')

            function colorLink() {
                if (linkColor) {
                    linkColor.forEach(l => l.classList.remove('active'))
                    this.classList.add('active')
                }
            }
            linkColor.forEach(l => l.addEventListener('click', colorLink))

            // Set active link based on current page
            function setActiveNavItem() {
                const currentPage = window.location.pathname.split('/').pop();
                const navLinks = document.querySelectorAll('.nav_link, .last-nav');

                navLinks.forEach(link => {
                    const linkHref = link.getAttribute('href');
                    if (linkHref) {
                        const linkPage = linkHref.split('/').pop();
                        if (currentPage === linkPage ||
                            (currentPage === '' && linkPage === 'index.php') ||
                            (currentPage.includes(linkPage) && linkPage !== '')) {
                            link.classList.add('active');
                        }
                    }
                });
            }

            // Call the function when page loads
            setActiveNavItem();
        });
    </script>
</body>

</html>