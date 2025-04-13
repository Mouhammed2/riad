<?php
require '../db_conection.php';

if (isset($_GET['delete_user'])) {
    $user_id = (int)$_GET['delete_user'];

    try {

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $_SESSION['success'] = "User deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
    }

    header("Location: user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />

    <style>
        .user-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
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

        .action-btn:hover i.bx-trash {
            color: #dc3545 !important;
        }
    </style>
</head>

<body id="body-pd">
    <?php
    require("nav.php");

    ?>
    <div>


        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="bg-white p-4 mb- mt-5">
            <h2 class="m-4 h-font ps-4 custom-border">

                <span class="fw-bold">OUR</span> Users
            </h2>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-hover room-table">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Zip Code</th>
                            <th>DOB</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM users";
                        $result = $conn->query($query);

                        while ($user = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td>
                                    <img src="/riad/<?= htmlspecialchars($user['picture']) ?>"
                                        class="user-img"
                                        alt="User Photo"
                                        onerror="this.src='/riad/images/default-user.jpg'">
                                </td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td><?= htmlspecialchars($user['addrese']) ?></td>
                                <td><?= htmlspecialchars($user['zip_code']) ?></td>
                                <td><?= htmlspecialchars($user['dob']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['role'] == 'admin' ? 'primary' : 'secondary' ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Edit Button -->
                                    <a href="#"
                                        class="action-btn"
                                        title="Edit User"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        data-id="<?= $user['id'] ?>"
                                        data-name="<?= htmlspecialchars($user['name']) ?>"
                                        data-email="<?= htmlspecialchars($user['email']) ?>"
                                        data-phone="<?= htmlspecialchars($user['phone']) ?>"
                                        data-addrese="<?= htmlspecialchars($user['addrese']) ?>"
                                        data-zip_code="<?= htmlspecialchars($user['zip_code']) ?>"
                                        data-dob="<?= htmlspecialchars($user['dob']) ?>"
                                        data-role="<?= htmlspecialchars($user['role']) ?>">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <a href="user.php?delete_user=<?= $user['id'] ?>"
                                        class="action-btn"
                                        title="Delete User"
                                        onclick="return confirm('Are you sure you want to delete this user?');">
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

    <!-- Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="crud_user/update_user.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editPhone" name="phone">
                        </div>

                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="editAddress" name="addrese">
                        </div>

                        <div class="mb-3">
                            <label for="editZipCode" class="form-label">Zip Code</label>
                            <input type="text" class="form-control" id="editZipCode" name="zip_code">
                        </div>

                        <div class="mb-3">
                            <label for="editDob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="editDob" name="dob">
                        </div>

                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <select class="form-select" id="editRole" name="role">
                                <option value="client">Client</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editPicture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="editPicture" name="picture" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer ">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn" style="  background: linear-gradient(180deg, #dba135, #f2ca7e) !important;
            color: #f8f9fa !important;">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle edit modal population
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editUserModal');

            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    // Button that triggered the modal
                    const button = event.relatedTarget;

                    // Extract info from data-* attributes
                    const userId = button.getAttribute('data-id');
                    const userName = button.getAttribute('data-name');
                    const userEmail = button.getAttribute('data-email');
                    const userPhone = button.getAttribute('data-phone');
                    const userAddress = button.getAttribute('data-addrese');
                    const userZipCode = button.getAttribute('data-zip_code');
                    const userDob = button.getAttribute('data-dob');
                    const userRole = button.getAttribute('data-role');

                    // Update the modal's content
                    const modalTitle = editModal.querySelector('.modal-title');
                    const modalBodyInputId = editModal.querySelector('input[name="user_id"]');
                    const modalBodyInputName = editModal.querySelector('#editName');
                    const modalBodyInputEmail = editModal.querySelector('#editEmail');
                    const modalBodyInputPhone = editModal.querySelector('#editPhone');
                    const modalBodyInputAddress = editModal.querySelector('#editAddress');
                    const modalBodyInputZipCode = editModal.querySelector('#editZipCode');
                    const modalBodyInputDob = editModal.querySelector('#editDob');
                    const modalBodySelectRole = editModal.querySelector('#editRole');

                    modalTitle.textContent = 'Edit User: ' + userName;
                    modalBodyInputId.value = userId;
                    modalBodyInputName.value = userName;
                    modalBodyInputEmail.value = userEmail;
                    modalBodyInputPhone.value = userPhone;
                    modalBodyInputAddress.value = userAddress;
                    modalBodyInputZipCode.value = userZipCode;
                    modalBodyInputDob.value = userDob;

                    // Set the selected role
                    Array.from(modalBodySelectRole.options).forEach(option => {
                        option.selected = option.value === userRole;
                    });
                });
            }
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