<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

// Set default paths
$baseDir = __DIR__; // Current directory
$defaultImage = 'images/default-profile.jpg';
$profileImage = $defaultImage;

// Handle profile picture
if (!empty($_SESSION['picture'])) {
    $picturePath = trim($_SESSION['picture']);
    
    // Remove any duplicate slashes
    $picturePath = preg_replace('~/+~', '/', $picturePath);
    
    // Check possible locations
    $locationsToCheck = [
        $picturePath, // Absolute path as stored
        ltrim($picturePath, '/'), // Relative to root
        'admin_panel/'.$picturePath, // Admin subdirectory
        'images/'.basename($picturePath), // Just filename in images folder
        '../'.$picturePath // Parent directory
    ];
    
    // Add unique paths only
    $locationsToCheck = array_unique($locationsToCheck);
    
    foreach ($locationsToCheck as $location) {
        $fullPath = $baseDir.'/'.ltrim($location, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            $profileImage = $location;
            break;
        }
    }
}

// Debug output (enable by removing style display:none)
$debugInfo = sprintf(
    "Session Picture: %s\nUsing Image: %s\nFile Exists: %s\nChecked Paths: %s",
    $_SESSION['picture'] ?? 'Not set',
    $profileImage,
    file_exists($profileImage) ? 'Yes' : 'No',
    implode(', ', $locationsToCheck ?? [])
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        .debug-info {
            background: #f8f9fa;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
            display: none; /* Change to block to debug */
        }
    </style>
</head>
<body>
    <header class="header" id="header">
        <div class="header_toggle">
            <i class="bx bx-menu" id="header-toggle"></i>
        </div>
        <div class="dor">
              <h6 class="nam-pd"> </h6>
            <div class="d-flex gap-2 align-items-end">
                <div class="header_img">
                    <div class="debug-info"><?= htmlspecialchars($debugInfo) ?></div>
                    <img src="<?= htmlspecialchars($profileImage) ?>" 
                         alt="Profile Picture"
                         class="rounded-circle"
                         width="30"
                         height="30"
                         onerror="this.onerror=null;this.src='<?= htmlspecialchars($defaultImage) ?>'">
                </div>
                <h6 class="nam-user"><?= htmlspecialchars($_SESSION['name'] ?? 'Guest') ?></h6>
            </div>
        </div>
    </header>

    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div class="mb-4">
                <a href="">
                    <img class="ms-3" src="logo.png" alt="logo" width="200px">
                </a>

                <div class="nav_list mt-5">
                    <a href="index.php" class="nav_link">
                        <i class="bx bx-grid-alt nav_icon"></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="user.php" class="nav_link">
                        <i class="bx bx-user nav_icon"></i>
                        <span class="nav_name">Users</span>
                    </a>
                    <a href="booking.php" class="nav_link">
                        <i class="bx bxs-calendar nav_icon"></i>
                        <span class="nav_name">Bookings</span>
                    </a>
                    <a href="room.php" class="nav_link">
                        <i class="bx bx-bed nav_icon"></i>
                        <span class="nav_name">Rooms</span>
                    </a>
                    <a href="events.php" class="nav_link">
                        <i class="bx bx-news nav_icon"></i>
                        <span class="nav_name" id="events">Events</span>
                    </a>
                    <a href="admin_testimonials.php" class="nav_link">
                        <i class="bx bxs-star-half nav_icon"></i>
                        <span class="nav_name">Testimonials</span>
                    </a>
                </div>
            </div>
            <div class="mb-4">
                <a href="#" class="last-nav">
                    <i class="bx bxs-cog nav_icon"></i>
                    <span class="nav_name">Settings</span>
                </a>
                <a href="logout.php" class="last-nav">
                    <i class="bx bx-log-out-circle nav_icon"></i>
                    <span class="nav_name">Sign Out</span>
                </a>
            </div>
        </nav>
    </div>
</body>
</html>