<?php
// admin/booking.php
require '../db_conection.php';
session_start();

// Handle status updates
if (isset($_POST['update_status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $booking_id);
    $stmt->execute();
    $stmt->close();

    header("Location: booking.php?updated=$booking_id");
    exit();
}

// Handle booking deletion
if (isset($_GET['delete'])) {
    $booking_id = (int)$_GET['delete'];
    
    // First verify booking exists
    $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ?");
    $check_stmt->bind_param("i", $booking_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Booking exists, proceed with deletion
        $delete_stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $delete_stmt->bind_param("i", $booking_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }
    $check_stmt->close();
    
    header("Location: booking.php?deleted=$booking_id");
    exit();
}

// Fetch all bookings with room details
$query = "SELECT b.*, r.room_number, r.price, r.bed_type 
          FROM bookings b
          JOIN rooms r ON b.room_id = r.id
          ORDER BY b.created_at DESC";
$bookings = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />

    <style>
        body {
            font-size: 1rem;
        }
        .table th, .table td {
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
        .action-btn:hover i.bx-trash {
            color: #dc3545 !important;
        }
        .room-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .room-table thead {
            background-color: #f8f9fa;
        }
        .room-table th {
            padding: 15px 10px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }
        .room-table td {
            padding: 12px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }
        .room-table tbody tr:last-child td {
            border-bottom: none;
        }
        .room-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            font-size: 0.8rem;
            border-radius: 20px;
        }
        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>

<body id="body-pd">
    <?php require("nav.php"); ?>

    <div class="container-fluid mt-4">
        <div class="bg-white p-4 mb- mt-5">
            <h2 class="m-4 h-font ps-4">
                <span class="fw-bold">Booking</span> Management
            </h2>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Booking status updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
          
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover room-table">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Room</th>
                                <th>Guest</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings->fetch_assoc()):
                                $date1 = new DateTime($booking['check_in']);
                                $date2 = new DateTime($booking['check_out']);
                                $nights = $date1->diff($date2)->days;
                            ?>
                                <tr>
                                    <td>#<?= $booking['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($booking['room_number']) ?></strong>
                                        <div class="text-muted small"><?= htmlspecialchars($booking['bed_type']) ?></div>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($booking['name']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($booking['email']) ?></div>
                                    </td>
                                    <td>
                                        <?= date('M j', strtotime($booking['check_in'])) ?> -
                                        <?= date('M j', strtotime($booking['check_out'])) ?>
                                        <div class="text-muted small"><?= $nights ?> nights</div>
                                    </td>
                                    <td>$<?= number_format($booking['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $booking['status'] == 'Pending' ? 'warning' : ($booking['status'] == 'Confirmed' ? 'success' : 'danger') ?>">
                                            <?= $booking['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($booking['created_at'])) ?></td>
                                    <td class="action-buttons">
                                        <a href="view_booking.php?id=<?= $booking['id'] ?>" 
                                           class="action-btn" title="View">
                                            <i class='bx bx-show'></i>
                                        </a>
                                        <a href="edit_booking.php?id=<?= $booking['id'] ?>" 
                                           class="action-btn" title="Edit">
                                            <i class='bx bx-edit-alt'></i>
                                        </a>
                                        <a href="booking.php?delete=<?= $booking['id'] ?>" 
                                           class="action-btn" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this booking?')">
                                            <i class='bx bx-trash'></i>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            // Navbar toggle
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId)

                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        nav.classList.toggle('show');
                        toggle.classList.toggle('bx-x');
                        bodypd.classList.toggle('body-pd');
                        headerpd.classList.toggle('body-pd');
                    });
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

            // Active link highlighting
            const linkColor = document.querySelectorAll('.nav_link ,.last-nav');

            function colorLink() {
                if (linkColor) {
                    linkColor.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            }
            linkColor.forEach(l => l.addEventListener('click', colorLink));
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