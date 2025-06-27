<?php
session_start();
include 'datacon.php'; // Include your database connection file

// Get the active semester
$Query = "SELECT semester_id FROM active_semester WHERE is_active = 1";
$stmt = $conn->prepare($Query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
$semester_id = $result->num_rows > 0 ? $result->fetch_assoc()['semester_id'] : null;

// Store semester_id in the session
$_SESSION['semester_id'] = $semester_id;

// Get the active academic year
$query1 = "SELECT year_label FROM academic_year WHERE is_active = 1";
$stmt = $conn->prepare($query1);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->execute();
$result1 = $stmt->get_result();
$year_label = $result1->num_rows > 0 ? $result1->fetch_assoc()['year_label'] : null;

// Store year_label in the session
$_SESSION['year_label'] = $year_label;

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $query = "SELECT roles.role_name, user_details.user_details, user_details.password, user_details.department, user_details.unique_id
              FROM user_details
              JOIN roles ON user_details.role_id = roles.role_id
              WHERE (user_details.email = ? OR user_details.unique_id = ?)
              LIMIT 1";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
         
    // Check if the user exists and the password matches
    if ($user && password_verify($password, $user['password'])) {
        $role = $user['role_name'];
        $department = $user['department'];
        $unique_id = $user['unique_id'];
        // Store role, username, and department in the session
        $_SESSION['user_id'] = $user['user_details'];
        $_SESSION['username'] = $username;
        $_SESSION['role_name'] = $role;
        $_SESSION['department'] = $department;
        $_SESSION['unique_id'] = $unique_id;

        

        // Define the redirect URL based on role
        switch ($role) {
            case 'admin':
                header("Location: /evaluation/admin/dashboard.php");
                break;
            case 'secretary':
                header("Location: /evaluation/secretary/");
                break;
            case 'advisor':
                header("Location: /evaluation/advisor/");
                break;
            case 'hod':
                header("Location: /evaluation/hod/dashboard.php");
                break;
            case 'student':
                header("Location: /evaluation/student/dashboard.php");
                break;
            default:
                echo "<script>alert('Invalid role.');</script>";
                exit();
        }
        exit();
    } else {
        // If login fails, display an error
        $error = "Invalid credentials. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Course Evaluation System Login</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FontAwesome JS -->
    <script defer src="assets/plugins/fontawesome/js/all.min.js"></script>

    <!-- App CSS -->
    <link id="theme-style" rel="stylesheet" href="assets/css/portal.css">
</head> 

<body class="app app-login p-0">    	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				    <div class="app-auth-branding mb-4">
                        <a class="app-logo" href="#">
                            <img class="logo-icon me-2" src="assets/images/app-logo.svg" alt="logo">
                        </a>
                    </div>
					<h2 class="auth-heading text-center mb-5">Log in to RMU 
                        Course Evaluation System</h2>
			        <div class="auth-form-container text-start">
						<form method="POST" action="" class="auth-form login-form">         
							<div class="email mb-3">
								<label for="username">Email or Unique ID</label>
								<input id="username" name="username" type="text" class="form-control" placeholder="Email or Unique ID" required>
							</div>
							<div class="password mb-3">
								<label for="password">Password</label>
								<input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
							</div>
							<div class="text-center">
								<button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Log In</button>
							</div>
						</form>
						
						<?php if (!empty($error)): ?>
						<div class="alert alert-danger text-center mt-3">
							<?= htmlspecialchars($error) ?>
						</div>
						<?php endif; ?>
					</div>	
			    </div>
		    </div>   
	    </div>
	    <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
		    <div class="auth-background-holder"></div>
		    <div class="auth-background-mask"></div>
		    <div class="auth-background-overlay p-3 p-lg-5">
			    <div class="d-flex flex-column align-content-end h-100">
				    <div class="h-100"></div>
				</div>
		    </div>
	    </div>
    </div>
</body>
</html>
