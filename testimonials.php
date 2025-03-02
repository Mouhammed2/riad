<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection with proper error handling
try {
    require $_SERVER['DOCUMENT_ROOT'] . '/riad/db_conection.php';
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Only allow logged-in users to submit testimonials
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['login']) && $_SESSION['login'] === true) {
    try {
        // Verify all required fields are present
        if (empty($_POST['content']) || empty($_POST['rating'])) {
            throw new Exception("All fields are required");
        }

        $client_name = $_SESSION['name']; // Use logged in user's name
        $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
        $rating = (int)$_POST['rating'];
        
        // Validate rating
        if ($rating < 1 || $rating > 5) {
            throw new Exception("Rating must be between 1 and 5");
        }
        
        // Debug output (remove in production)
        error_log("Attempting to insert testimonial: $client_name, $content, $rating, {$_SESSION['user_id']}");
        
        $stmt = $conn->prepare("INSERT INTO testimonials (client_name, content, rating, status, user_id) VALUES (?, ?, ?, 'pending', ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssii", $client_name, $content, $rating, $_SESSION['user_id']);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $insert_id = $stmt->insert_id;
        error_log("Testimonial inserted with ID: $insert_id");
        
        $_SESSION['success'] = "Thank you! Your testimonial has been submitted and is awaiting approval.";
        $stmt->close();
        
        header("Location: testimonials.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        error_log("Testimonial submission error: " . $e->getMessage());
        header("Location: testimonials.php");
        exit;
    }
}

// Fetch only approved testimonials for display
try {
    $query = "SELECT * FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC";
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    $approved_testimonials = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading testimonials: " . $e->getMessage();
    $approved_testimonials = [];
    error_log("Testimonial fetch error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Testimonials - Riad El Behja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <style>
        .testimonial-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: 100%;
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .rating i {
            color: #ffc107;
            font-size: 1.2rem;
        }
        .testimonial-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            background: linear-gradient(180deg, #dba135, #f2ca7e) !important;
  color: #f8f9fa !important;
        }
        .testimonial-btn:hover {
            transform: scale(1.1);
        }
        .testimonial-modal .modal-dialog {
            max-width: 600px;
        }
        .testimonial-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
        body {
            padding-top: 80px; /* Adjust for fixed header */
        }
        .alert {
            max-width: 800px;
            margin: 0 auto 20px;
        }
        .empty-testimonials {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/riad/includ/header.php'); ?>

    <div class="container py-5">
        <h1 class="text-center mb-5">What Our Guests Say</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Testimonials Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if (!empty($approved_testimonials)): ?>
                <?php foreach ($approved_testimonials as $testimonial): ?>
                    <div class="col">
                        <div class="testimonial-card" data-bs-toggle="modal" data-bs-target="#testimonialModal" 
                             data-name="<?= htmlspecialchars($testimonial['client_name']) ?>"
                             data-date="<?= date('F j, Y', strtotime($testimonial['created_at'])) ?>"
                             data-content="<?= htmlspecialchars($testimonial['content']) ?>"
                             data-rating="<?= $testimonial['rating'] ?>">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="bi bi-person-circle fs-1"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0"><?= htmlspecialchars($testimonial['client_name']) ?></h5>
                                    <small class="text-muted"><?= date('F j, Y', strtotime($testimonial['created_at'])) ?></small>
                                </div>
                            </div>
                            <p class="mb-3"><?= htmlspecialchars(substr($testimonial['content'], 0, 100)) ?><?= strlen($testimonial['content']) > 100 ? '...' : '' ?></p>
                            <div class="rating">
                                <?= str_repeat('<i class="bi bi-star-fill"></i>', $testimonial['rating']) ?>
                                <?= str_repeat('<i class="bi bi-star"></i>', 5 - $testimonial['rating']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-testimonials">
                        <i class="bi bi-chat-square-text fs-1 mb-3"></i>
                        <h3>No testimonials yet</h3>
                        <p>Be the first to share your experience!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Only show add button if user is logged in -->
        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
            <button class="btn testimonial-btn" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                <i class="bi bi-plus-lg"></i>
            </button>
        <?php else: ?>
            <div class="text-center mt-5">
                <p>Want to share your experience? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a> to submit a testimonial.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add Testimonial Modal (only shown to logged in users) -->
    <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
    <div class="modal fade testimonial-modal" id="addTestimonialModal" tabindex="-1" aria-labelledby="addTestimonialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  text-white" style="background: linear-gradient(180deg, #dba135, #f2ca7e) !important;
  color: #f8f9fa !important;">
                    <h5 class="modal-title" id="addTestimonialModalLabel">Share Your Experience</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="testimonialForm" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Posting as:</label>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($_SESSION['picture'])): ?>
                                    <img src="<?= htmlspecialchars($_SESSION['picture']) ?>" class="testimonial-img me-3" alt="Profile">
                                <?php else: ?>
                                    <i class="bi bi-person-circle fs-1 me-3"></i>
                                <?php endif; ?>
                                <h5><?= htmlspecialchars($_SESSION['name']) ?></h5>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Your Experience *</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required minlength="20" maxlength="500"></textarea>
                            <small class="text-muted">Minimum 20 characters, maximum 500 characters</small>
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Your Rating *</label>
                            <select class="form-select" id="rating" name="rating" required>
                                <option value="">Select rating</option>
                                <option value="5">Excellent (5 Stars)</option>
                                <option value="4">Very Good (4 Stars)</option>
                                <option value="3">Good (3 Stars)</option>
                                <option value="2">Fair (2 Stars)</option>
                                <option value="1">Poor (1 Star)</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="testimonialForm" class="btn " style="background: linear-gradient(180deg, #dba135, #f2ca7e) !important;
  color: #f8f9fa !important;">Submit Testimonial</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Testimonial Detail Modal -->
    <div class="modal fade testimonial-modal" id="testimonialModal" tabindex="-1" aria-labelledby="testimonialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testimonialModalLabel">Guest Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <i class="bi bi-person-circle fs-1"></i>
                        </div>
                        <div>
                            <h4 id="modalClientName" class="mb-0"></h4>
                            <small class="text-muted" id="modalTestimonialDate"></small>
                        </div>
                    </div>
                    <p class="lead" id="modalTestimonialContent"></p>
                    <div class="rating mb-4" id="modalTestimonialRating"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script>
        // Initialize testimonial detail modal
        const testimonialModal = document.getElementById('testimonialModal');
        if (testimonialModal) {
            testimonialModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                document.getElementById('modalClientName').textContent = button.getAttribute('data-name');
                document.getElementById('modalTestimonialDate').textContent = button.getAttribute('data-date');
                document.getElementById('modalTestimonialContent').textContent = button.getAttribute('data-content');
                
                // Set rating stars
                const rating = parseInt(button.getAttribute('data-rating'));
                const ratingContainer = document.getElementById('modalTestimonialRating');
                ratingContainer.innerHTML = '';
                
                for (let i = 0; i < 5; i++) {
                    const star = document.createElement('i');
                    star.className = i < rating ? 'bi bi-star-fill' : 'bi bi-star';
                    ratingContainer.appendChild(star);
                }
            });
        }

        // Clear form when add modal is closed
        const addTestimonialModal = document.getElementById('addTestimonialModal');
        if (addTestimonialModal) {
            addTestimonialModal.addEventListener('hidden.bs.modal', () => {
                document.getElementById('testimonialForm').reset();
            });
        }

        // Form validation
        const testimonialForm = document.getElementById('testimonialForm');
        if (testimonialForm) {
            testimonialForm.addEventListener('submit', function(e) {
                const content = this.content.value.trim();
                const rating = this.rating.value;
                
                if (content.length < 20) {
                    e.preventDefault();
                    alert('Please write at least 20 characters about your experience.');
                    return false;
                }
                
                if (!rating) {
                    e.preventDefault();
                    alert('Please select a rating.');
                    return false;
                }
                
                return true;
            });
        }
    </script>
</body>
</html>