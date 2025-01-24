<?php
session_start();
include('db.php');
include('header.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($username) || empty($password) || empty($role)) {
        $login_error = "All fields are required.";
    } else {
        $sql = "SELECT * FROM users WHERE username = :username AND role = :role";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($password, $result['password'])) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['role'] = $result['role'];

            if ($_SESSION['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: courses.php");
            }
            exit;
        } else {
            $login_error = "Invalid username or password.";
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recover'])) {
    $email = $_POST['recover_email'] ?? '';

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Here you should implement the password recovery logic (e.g., sending an email with a reset link)
        $recovery_message = "Password recovery instructions have been sent to your email.";
    } else {
        $recovery_error = "No user found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 450px;
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-link {
            color: #007bff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="auth.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="Student/User">Student/User</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
            <a href="#" class="btn btn-link" data-toggle="modal" data-target="#recoverModal">Forgot Password?</a>
        </form>
    </div>

    <!-- Password Recovery Modal -->
    <div class="modal fade" id="recoverModal" tabindex="-1" aria-labelledby="recoverModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recoverModalLabel">Password Recovery</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="auth.php">
                        <div class="form-group">
                            <label for="recover_email">Email:</label>
                            <input type="email" id="recover_email" name="recover_email" class="form-control" required>
                        </div>
                        <?php if (isset($recovery_error)): ?>
                            <div class="alert alert-danger"><?php echo $recovery_error; ?></div>
                        <?php elseif (isset($recovery_message)): ?>
                            <div class="alert alert-success"><?php echo $recovery_message; ?></div>
                        <?php endif; ?>
                        <button type="submit" name="recover" class="btn btn-primary">Recover Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="home_style.css">
    <nav class="navbar">
      <div class="navbar-container container">
        <input type="checkbox" name="" id="">
        <div class="hamburger-lines"></div>
        <ul class="menu-items">
          <li>
            <a href="#home">Home</a>
          </li>
          <li>
            <a href="#about">About</a>
          </li>
          <li>
            <a href="#food">Category</a>
          </li>
          <li>
            <a href="#food-menu">Menu</a>
          </li>
          <li>
            <a href="#testimonials">Testimonial</a>
          </li>
          <li>
            <a href="#contact">Contact</a>
          </li>
        </ul>
        <h1 class="logo">ROHOBOT</h1>
      </div>
    </nav>
    <section class="showcase-area" id="showcase">
      <div class="showcase-container">
        <h1 class="main-title" id="home">Eat Right Food-</h1>
        <p><br>
        Eat Healthy, it is good for your health.</p><a href="#food-menu" class="btn btn-primary">Menu</a>
      </div>
    </section>
    <section id="about">
      <div class="about-wrapper container">
        <div class="about-text">
          <p class="small">About Us</p>
          <h2>We've been making healthy food for 8 years</h2>
          <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Esse ab eos omnis, nobis dignissimos perferendis et officia architecto, fugiat possimus eaque qui ullam excepturi suscipit aliquid optio, maiores praesentium soluta alias asperiores saepe commodi consequatur? Perferendis est placeat facere aspernatur!</p>
        </div>
        <div class="about-img"><img src="images/home_images/about-photo.jpg" alt="food"></div>
      </div>
    </section>
    <section id="food">
      <h2>Types of food</h2>
      <div class="food-container container">
        <div class="food-type fruite">
          <div class="img-container">
            <img src="images/home_images/food1.jpg" alt="error">
            <div class="img-content">
              <h3>fruits</h3><a href="https://en.wikipedia.org/wiki/Fruit" class="btn btn-primary" target="blank">learn more</a>
            </div>
          </div>
        </div>
        <div class="food-type vegetable">
          <div class="img-container">
            <img src="images/home_images/food2.jpg" alt="error">
            <div class="img-content">
              <h3>vegetable</h3><a href="https://en.wikipedia.org/wiki/Vegetable" class="btn btn-primary" target="blank">learn more</a>
            </div>
          </div>
        </div>
        <div class="food-type grin">
          <div class="img-container">
            <img src="images/home_images/food3.jpg" alt="error">
            <div class="img-content">
              <h3>grain</h3><a href="https://en.wikipedia.org/wiki/Grain" class="btn btn-primary" target="blank">learn more</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="food-menu">
      <h2 class="food-menu-heading">Food Menu</h2>
      <div class="food-menu-container container">
        <div class="food-menu-item">
          <div class="food-img"><img src="images/home_images/food-menu1.jpg" alt="food-menu1"></div>
          <div class="food-description">
            <h2 class="food-titile">Food Menu Item 1</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, quae.</p>
            <p class="food-price">Price: --- ; ---</p>
          </div>
        </div>
        <div class="food-menu-item">
          <div class="food-img"><img src="images/home_images/food-menu2.jpg" alt="error"></div>
          <div class="food-description">
            <h2 class="food-titile">Food Menu Item 2</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, quae.</p>
            <p class="food-price">Price:--- ; ---</p>
          </div>
        </div>
        <div class="food-menu-item">
          <div class="food-img"><img src="images/home_images/food-menu3.jpg" alt="food-menu3"></div>
          <div class="food-description">
            <h2 class="food-titile">Food Menu Item 3</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, quae.</p>
            <p class="food-price">Price: --- ; ---</p>
          </div>
        </div>
        <div class="food-menu-item">
          <div class="food-img"><img src="images/home_images/food-menu4.jpg" alt="food-menu4"></div>
          <div class="food-description">
            <h2 class="food-titile">Food Menu Item 4</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, quae.</p>
            <p class="food-price">Price: --- ; ---</p>
          </div>
        </div>
        <div class="food-menu-item">
          <div class="food-img"><img src="images/home_images/food-menu5.jpg" alt="food-menu5"></div>
          <div class="food-description">
            <h2 class="food-titile">Food Menu Item 5</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, quae.</p>
            <p class="food-price">Price: ₹ 250</p>
          </div>
        </div>
        <div class="food-menu-item">
          <div class="food-img"><img src="images/home_images/food-menu6.jpg" alt="food-menu6"></div>
          <div class="food-description">
            <h2 class="food-titile">Food Menu Item 6</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, quae.</p>
            <p class="food-price">Price: --- ; ---</p>
          </div>
        </div>
      </div>
    </section>
    <section id="testimonials">
      <h2 class="testimonial-title">What Our Customers Say</h2>
      <div class="testimonial-container container">
        <div class="testimonial-box">
          <div class="customer-detail">
            <div class="customer-photo">
              <img src="images/home_images/Hab-5.JPG" alt="Hab-5">
              <p class="customer-name">Andrew The great</p>
            </div>
          </div>
          <div class="star-rating"></div>
          <p class="testimonial-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Impedit voluptas cupiditate aspernatur odit doloribus non.</p>
        </div>
        <div class="testimonial-box">
          <div class="customer-detail">
            <div class="customer-photo">
              <img src="images/home_images/Hab-6.JPG" alt="Hab-6">
              <p class="customer-name">Habtom Ar</p>
            </div>
          </div>
          <div class="star-rating"></div>
          <p class="testimonial-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Impedit voluptas cupiditate aspernatur odit doloribus non.</p>
        </div>
        <div class="testimonial-box">
          <div class="customer-detail">
            <div class="customer-photo">
              <img src="images/home_images/Hab-2.JPG" alt="Hab-2">
              <p class="customer-name">Jack Mickiel</p>
            </div>
          </div>
          <div class="star-rating"></div>
          <p class="testimonial-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Impedit voluptas cupiditate aspernatur odit doloribus non.</p>
        </div>
      </div>
    </section>
    <section id="contact">
      <div class="contact-container container">
        <div class="contact-img"><img src="images/home_images/female-photo1.jpg" alt="female-photo1"></div>
        <div class="form-container">
          <h2>Contact Us</h2><input type="text" placeholder="Your Name"> <input type="email" placeholder="E-Mail"> 
          <textarea cols="30" rows="6" placeholder="Type Your Message"></textarea> <a href="#" class="btn btn-primary">Submit</a>
        </div>
      </div>
    </section>
    <footer id="footer">
      <div class="customer-photo">
        <h2><img src="images/home_images/Hab-6.JPG" alt="Hab-6">Web Developer Habtom Araya-ACCA © all rights reserved</h2>
      </div>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
    <script src="app.js"></script>
  </div>
</body>
</html>