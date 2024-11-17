<?php
   

   if (session_status() === PHP_SESSION_NONE) {
       session_start();
   }
   
   
    ?>
  <!-- Nav bar  -->
  <nav
    class="navbar navbar-expand-lg navbar-light bg-light fixed-top transparent-navbar">
    <div class="container-fluid container d-flex justify-content-center">
      <a class="navbar-brand" href="index.php">
        <img src="image/logo.png" alt="logo" style="width: 200px" /></a>
      <button
      class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div
        class="collapse navbar-collapse justify-content-center"
        id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto text-center">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="room.php">Rooms</a>
          </li>
          <li class="nav-item">
            <a
              class="nav-link "
              href="#events"
              id="navbarDropdown"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false">
              Events
            </a>
           
          </li>
          <li class="nav-item">
            <a
              class="nav-link"
              href="#"
              id="navbarDropdown"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false">
              Culture & Traditions
            </a>
           
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Contact</a>
          </li>
        </ul>
        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-dark shadow-non dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
            <img src="<?php echo htmlspecialchars($_SESSION['picture']); ?>" alt="Profile" class="rounded-circle" width="30" height="30">
            <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><a class="dropdown-item " href="testimonials.php">testimonials</a></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            
        
           
        </ul>
    </div>
<?php else: ?>
    <!-- Show login/register buttons when user is not logged in -->
   <div class="d-flex flex-column flex-md-row gap-2">
          <button
            class="btn btn-outline-dark shadow-none me-lg-2 me-3 bi-person-circle"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#linkmodal">
            Login
          </button>
          <button
            class="btn btn-outline-dark shadow-none me-lg-2 me-3 bi-person-circle"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#linkmodal2">
            Registration
          </button>
        </div>
    <?php endif; ?>
   
       
      </div>
    </div>
  </nav>
  <!-- Modal login -->
  <form action="login.php" method="post">
    <div
      class="modal fade"
      id="linkmodal"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
      tabindex="-1"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5 me-2" id="staticBackdropLabel">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="30"
                height="30"
                fill="currentColor"
                class="bi bi-person-circle"
                viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                <path
                  fill-rule="evenodd"
                  d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
              </svg>
              User Login
            </h1>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input
                  name="email"
                  type="email"
                  class="form-control"
                  id="exampleInputEmail1"
                  aria-describedby="emailHelp"
                  placeholder="Enter email" />
              </div>
            </div>
            <div class="mb-3">
              <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input
                  name="password"
                  type="password"
                  class="form-control"
                  id="exampleInputPassword1"
                  placeholder="Password" />
              </div>
            </div>
            <button type="submit" class="btn btn-dark shadow-none">
              Login
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Modal registration -->
  <form action="register.php" method="post" enctype="multipart/form-data">
    <div
      class="modal fade"
      id="linkmodal2"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
      tabindex="-1"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5 me-2" id="staticBackdropLabel">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="30"
                height="30"
                fill="currentColor"
                class="bi bi-person-lines-fill"
                viewBox="0 0 16 16">
                <path
                  d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z" />
              </svg>
              User Registration
            </h1>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <span
              class="badge rounded-pill bg-light text-dark mb-3 text-wrap lh-base">
              Note: Your details must match your ID ( passport, or driving
              license) that will be required during check-in.
            </span>
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-6 ps-0 mb-3">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input
                      name="email"
                      type="email"
                      class="form-control"
                      id="exampleInputEmail1"
                      aria-describedby="emailHelp"
                      placeholder="Enter email" />
                  </div>
                </div>

                <div class="col-md-6 p-0 mb-3">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input
                      name="name"
                      type="text"
                      class="form-control"
                      id="exampleInputEmail1"
                      aria-describedby="emailHelp"
                      placeholder="Enter your name" />
                  </div>
                </div>
                <div class="col-md-6 ps-0 mb-3">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Phone number</label>
                    <input
                      name="phone"
                      type="number"
                      class="form-control"
                      id="exampleInputEmail1"
                      aria-describedby="emailHelp"
                      placeholder="Enter Phone number" />
                  </div>
                </div>

                <div class="col-md-6 p-0 mb-3">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Picture</label>
                    <input
                      name="picture"
                      type="file"
                      class="form-control"
                      id="exampleInputEmail1"
                      aria-describedby="emailHelp"
                      placeholder="Enter your Picture" />
                  </div>
                </div>

                <div class="col-md-12 p-0 mb-3">
                  <label for="exampleFormControlTextarea1" class="form-label">Address</label>
                  <textarea
                    name="address"
                    class="form-control"
                    id="exampleFormControlTextarea1"
                    rows="1"></textarea>
                </div>
                <div class="col-md-6 ps-0 mb-3">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Zip code</label>
                    <input
                      name="zip_code"
                      type="number"
                      class="form-control"
                      id="Zip code"
                      aria-describedby="Zip code"
                      placeholder="Enter Zip code" />
                  </div>
                </div>

                <div class="col-md-6 p-0 mb-3">
                  <div class="form-group">
                    <label for="Date of birth">Date of birth</label>
                    <input
                      name="dob"
                      type="date"
                      class="form-control"
                      id="Date of birth"
                      aria-describedby="Date of birth"
                      placeholder="Enter your Date of birth" />
                  </div>
                </div>

                <div class="col-md-6 ps-0 mb-3">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Password</label>
                    <input
                      name="password"
                      type="text"
                      class="form-control"
                      id="Password"
                      aria-describedby="Password"
                      placeholder="Enter your Password" />
                  </div>
                </div>

                <div class="col-md-6 p-0 mb-3">
                  <div class="form-group">
                    <label for="Date of birth">Comfirm Password</label>
                    <input

                      type="text"
                      class="form-control"
                      id="Comfirm Password"
                      aria-describedby="Comfirm Password"
                      placeholder="Enter your Comfirm Password" />
                  </div>
                </div>
              </div>
            </div>
            <div class="text-center my-1">
              <button type="submit" class="btn btn-dark shadow-none">
                Register
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>