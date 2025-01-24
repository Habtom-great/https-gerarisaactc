<?php
// Include required files
require_once 'db.php';
require_once 'header_common.php';

try {
    // Database connection settings
    $host = "localhost";
    $db = "accounting_course";
    $user = "root";
    $pass = "";
    $charset = "utf8mb4";

    // Establish PDO connection
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $conn = new PDO($dsn, $user, $pass, $options);

    // Process POST request
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Sanitize and retrieve form data
        $username = trim($_POST['username']);
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $address = trim($_POST['address']);
        $phone = trim($_POST['phone']);
        $education_level = trim($_POST['education_level']);
        $age = trim($_POST['age']);
        $gender = trim($_POST['gender']);
        $nationality = trim($_POST['nationality']);
        $country_residence = trim($_POST['country_residence']);
        $role = trim($_POST['role']);

        // Handle profile image upload
        $profile_image_path = null;
        if (!empty($_FILES["profile_image"]["tmp_name"])) {
            $upload_dir = "uploads/";
            $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
            $target_file = $upload_dir . $file_name;
            $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate image
            if (!getimagesize($_FILES["profile_image"]["tmp_name"])) {
                throw new Exception("File is not a valid image.");
            }
            if (!in_array($image_type, ["jpg", "jpeg", "png", "gif"])) {
                throw new Exception("Only JPG, JPEG, PNG, and GIF files are allowed.");
            }
            if ($_FILES["profile_image"]["size"] > 500000) {
                throw new Exception("Image size exceeds 500KB.");
            }

            // Move uploaded file
            if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                throw new Exception("Failed to upload profile image.");
            }

            $profile_image_path = $target_file;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Check for duplicate username or email (case insensitive)
        $check_query = "SELECT id FROM users WHERE LOWER(email) = LOWER(:email) OR LOWER(username) = LOWER(:username)";
        $stmt = $conn->prepare($check_query);
        $stmt->execute([':email' => $email, ':username' => $username]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("Username or email already exists.");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Determine the target table based on the role
        $role_tables = ['admin', 'staff', 'tutor', 'students'];
        if (!in_array(strtolower($role), $role_tables)) {
            throw new Exception("Invalid role specified.");
        }
        $table_name = strtolower($role);

        // Insert user data into the target table
        $insert_query = "
            INSERT INTO $table_name 
            (username, first_name, last_name, email, password, address, telephone, education_level, age, gender, nationality, country_residence, role, profile_image) 
            VALUES 
            (:username, :first_name, :last_name, :email, :password, :address, :telephone, :education_level, :age, :gender, :nationality, :country_residence, :role, :profile_image)
        ";
        $stmt = $conn->prepare($insert_query);
        $stmt->execute([
            ':username' => $username,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':address' => $address,
            ':telephone' => $phone,
            ':education_level' => $education_level,
            ':age' => $age,
            ':gender' => $gender,
            ':nationality' => $nationality,
            ':country_residence' => $country_residence,
            ':role' => $role,
            ':profile_image' => $profile_image_path,
        ]);

        echo "Registration successful!";
    }
} catch (PDOException $e) {
    echo "Database Error: " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
} finally {
    // Close the PDO connection
    $conn = null;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (isset($registration_success)): ?>
            <div class="alert alert-success"><?php echo $registration_success; ?></div>
        <?php elseif (isset($registration_error)): ?>
            <div class="alert alert-danger"><?php echo $registration_error; ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" required></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        
                        <option value="admin">Admin</option>
                        <option value="tutor">tutor</option>
                        <option value="staff">Staff</option>
                        <option value="Student">Student</option>
                        <option value="other">Other</option>
                      

                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" class="form-control" id="profile_image" name="profile_image" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="education_level">Education Level</label>
                    <select class="form-control" id="education_level" name="education_level" required>
                        <option value="High School">High School</option>
                        <option value="Certificate">Certificate</option>
                        <option value="Some college level">Some College Level</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="PhD">PhD</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="age">Age</label>
                    <input type="number" class="form-control" id="age" name="age" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="gender">Gender</label>
                    <select class="form-control" id="sex" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                   
                </select>
                </div>
            </div>
             <!-- Nationality and Residence -->
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="nationality">Nationality</label>
                    <select class="form-control" id="nationality" name="nationality" required>
                        <option value="">Select your nationality</option> 
                        <?php
                        $countries = [
                            "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria",
                            "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan",
                            "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia",
                            "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)", "Costa Rica",
                            "Croatia", "Cuba", "Cyprus", "Czechia (Czech Republic)", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt",
                            "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini (fmr. Swaziland)", "Ethiopia", "Fiji", "Finland", "France", "Gabon",
                            "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana",
                            "Haiti", "Holy See", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland",
                            "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan",
                            "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar",
                            "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia",
                            "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (formerly Burma)", "Namibia", "Nauru", "Nepal",
                            "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan",
                            "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar",
                            "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia",
                            "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa",
                            "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Tajikistan",
                            "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu",
                            "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam",
                            "Yemen", "Zambia", "Zimbabwe"
                        ];
                        
                        foreach ($countries as $country): ?>
                            <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="country_residence">Country of Residence</label>
                    <select class="form-control" id="country_residence" name="country_residence" required>
                        <option value="">Select your country of residence</option>
                        <?php
                        foreach ($countries as $country): ?>
                            <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
       
            <div class="form-row">
            <div class="col-md-3 text-center">
            <button type="submit" class="btn btn-primary btn-block" name="register">Register</button>
        </form>
        <div class="form-row">
        <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
        </div>
        </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
include('footer.php');
?>

kkkkk
<?php
include('db.php');
include('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $education_level = isset($_POST['education_level']) ? trim($_POST['education_level']) : '';
    $age = isset($_POST['age']) ? trim($_POST['age']) : '';
    $sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
    $nationality = isset($_POST['nationality']) ? trim($_POST['nationality']) : '';
    $country_residence = isset($_POST['country_residence']) ? trim($_POST['country_residence']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : 'user'; // Default to 'user' if not set

    // Handle image upload
    $profile_image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_ext, $allowed_exts)) {
            $profile_image = 'uploads/' . uniqid() . '.' . $image_ext;
            if (!move_uploaded_file($image_tmp, $profile_image)) {
                $registration_error = "Failed to upload the image.";
            }
        } else {
            $registration_error = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = "Invalid email format.";
    } else {
        $check_sql = "SELECT id FROM users WHERE email = ? OR username = ?";
        $stmt = $pdo->prepare($check_sql);

        $stmt->execute([$email, $username]);
        if ($stmt->rowCount() > 0) {
            $registration_error = "Username or Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert_sql = "INSERT INTO users (username, first_name, last_name, email, password, address, telephone, education_level, age, gender, nationality, country_residence, role, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($insert_sql);

            $stmt->execute([$username, $first_name, $last_name, $email, $hashed_password, $address, $phone, $education_level, $age, $sex, $nationality, $country_residence, $role, $profile_image]);

            if ($stmt->rowCount() > 0) {
                $registration_success = "Registration successful.";
            } else {
                $registration_error = "Registration failed. Please try again.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-theme.min.css"> <!-- Ensure this path is correct -->
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (isset($registration_success)): ?>
            <div class="alert alert-success"><?php echo $registration_success; ?></div>
        <?php elseif (isset($registration_error)): ?>
            <div class="alert alert-danger"><?php echo $registration_error; ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" required></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                          <option value="admin">Admin</option>
                          <option value="staff">Staff</option>
                          <option value="tutors">Tutor</option>
                        <option value="Students">Student</option>
                        <option value="other">Others</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="education_level">Education Level</label>
                    <select class="form-control" id="education_level" name="education_level" required>
                        <option value="High School">High School</option>
                        <option value="Associate Degree">Associate Degree</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="Doctorate">Doctorate</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="age">Age</label>
                    <input type="number" class="form-control" id="age" name="age" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="sex">Sex</label>
                    <select class="form-control" id="sex" name="sex" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nationality">Nationality</label>
                    <select class="form-control" id="nationality" name="nationality" required>
                        <option value="">Select your nationality</option>
                        <?php
                        $countries = [
                            "United States", "Canada", "United Kingdom", "Australia", "India", "Germany", "France", "Italy", "Spain", "Brazil",
                            "Mexico", "China", "Japan", "Russia", "South Africa", "Nigeria", "Eritrea", "Ethiopia", "Egypt", "Kenya", "Argentina", "Colombia"
                        ];
                        foreach ($countries as $country): ?>
                            <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="country_residence">Country of Residence</label>
                    <select class="form-control" id="country_residence" name="country_residence" required>
                        <option value="">Select your country of residence</option>
                        <?php
                        foreach ($countries as $country): ?>
                            <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="image">Profile Image</label>
                    <input type="file" class="form-control-file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block" name="register">Register</button>
        </form>
        <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
include('footer.php');
?>