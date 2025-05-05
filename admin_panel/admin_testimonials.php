<?php

require '../db_conection.php';

// Handle approval/rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    try {
        $valid_actions = ['approve', 'reject', 'delete'];
        if (!in_array($action, $valid_actions)) {
            throw new Exception("Invalid action");
        }
        
        if ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
            $stmt->bind_param("i", $id);
        } else {
            $status = $action === 'approve' ? 'approved' : 'rejected';
            $stmt = $conn->prepare("UPDATE testimonials SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
        }
        
        $stmt->execute();
        $_SESSION['success'] = "Testimonial " . $action . "d successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header("Location: admin_testimonials.php");
    exit;
}

// Fetch all testimonials
$query = "SELECT * FROM testimonials ORDER BY 
          CASE WHEN status = 'pending' THEN 0 ELSE 1 END,
          created_at DESC";
$result = $conn->query($query);
$testimonials = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css" />
    <style>
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
            font-size: 18px;
            color: #6c757d;
            vertical-align: middle;
        }

        .action-btn:hover i.bi-check-lg {
            color: #198754 !important;
        }

        .action-btn:hover i.bi-x-lg {
            color: #ffc107 !important;
        }

        .action-btn:hover i.bi-trash {
            color: #dc3545 !important;
        }
        
        .custom-border {
            border-left: 7px solid #0d6efd;
        }
        
        .room-table th {
            padding: 8px 7px;
            font-size:1rem ;
        }
        
        .room-table td {
            padding: 12px 10px;
            vertical-align: middle;
            font-size:1rem ;
        }
    </style>
</head>

<body id="body-pd">
    <?php require("nav.php"); ?>
    
    <div>
     

     

        <div class="bg-white p-4 mb- mt-5">
            <h2 class="m-4 h-font ps-4">
                <span class="fw-bold">OUR</span> Testimonials
            </h2>
        </div>
        
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-hover room-table">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Content</th>
                            <th>Rating</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <tr>
                                <td><?= $testimonial['id'] ?></td>
                                <td><?= htmlspecialchars($testimonial['client_name']) ?></td>
                                <td><?= htmlspecialchars(substr($testimonial['content'], 0, 50)) ?>...</td>
                                <td>
                                    <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    <?php endfor; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($testimonial['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $testimonial['status'] === 'approved' ? 'success' : 
                                        ($testimonial['status'] === 'rejected' ? 'danger' : 'warning') 
                                    ?>">
                                        <?= ucfirst($testimonial['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($testimonial['status'] !== 'approved'): ?>
                                        <a href="admin_testimonials.php?action=approve&id=<?= $testimonial['id'] ?>" 
                                           class="action-btn"
                                           title="Approve">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($testimonial['status'] !== 'rejected'): ?>
                                        <a href="admin_testimonials.php?action=reject&id=<?= $testimonial['id'] ?>" 
                                           class="action-btn"
                                           title="Reject">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="admin_testimonials.php?action=delete&id=<?= $testimonial['id'] ?>" 
                                       class="action-btn"
                                       title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this testimonial?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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