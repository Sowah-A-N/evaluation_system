<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role_name'])) {
    header("Location: /evaluation/index.php");
    exit();
}
include '../datacon.php';
// Get the logged-in user's details
$userName = htmlspecialchars($_SESSION['username']); // Name of the user
$userRole = htmlspecialchars($_SESSION['role_name']); // Role of the user
$userImage = "assets/images/default-profile.png"; // Placeholder profile image

// You can optionally fetch and replace $userImage if your database stores user profile pictures


// Initialize variables
$hodCount = $departmentCount = $academicYearCount = $activeSemesterCount = 0;

// Fetch number of HODs and their associated departments
$hodQuery = "SELECT 
                u.user_details, 
                CONCAT(u.f_name, ' ', u.l_name) AS hod_name, 
                d.dep_name 
             FROM user_details u
             JOIN department d ON u.user_details = d.hod_id
             WHERE u.role_id = 2"; // Assuming '2' is the role_id for HOD

$hodResult = $conn->query($hodQuery);

if ($hodResult->num_rows > 0) {
    while ($row = $hodResult->fetch_assoc()) {
        $hodName = $row['hod_name'];
        $departmentName = $row['dep_name'];
        // echo "HOD: $hodName - Department: $departmentName<br>";
    }
} else {
    echo "No HODs found.";
}

// Fetch number of departments
$departmentQuery = "SELECT COUNT(*) AS department_count FROM department";
$departmentResult = $conn->query($departmentQuery);
if ($departmentResult->num_rows > 0) {
    $row = $departmentResult->fetch_assoc();
    $departmentCount = $row['department_count'];
}

// Query to fetch the semester name for the active semester
$activeSemesterQuery = "SELECT semester_name FROM active_semester WHERE is_active = 1";
$activeSemesterResult = mysqli_query($conn, $activeSemesterQuery);

// Check if the query was successful and returned a result
if ($activeSemesterResult && mysqli_num_rows($activeSemesterResult) > 0) {
    $row = mysqli_fetch_assoc($activeSemesterResult);
    $activeYear = $row['semester_name']; // Get the actual semester name
} else {
    $activeYear = "No active semester found"; // Fallback value
}

// Output the result (for debugging or further use)
// echo "Active Semester: " . $activeSemester;



// Fetch number of active semesters
$activeSemesterQuery = "SELECT year_label FROM academic_year WHERE is_active = 1";
$activeSemesterResult = mysqli_query($conn, $activeSemesterQuery);

// Check if the query was successful and returned a result
if ($activeSemesterResult && mysqli_num_rows($activeSemesterResult) > 0) {
    $row = mysqli_fetch_assoc($activeSemesterResult);
    $activeSemester = $row['year_label']; // Get the actual active year_label
} else {
    $activeSemester = "No active year found"; // Fallback value
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="assets/css/portal.css">
    <script src="assets/plugins/fontawesome/js/all.min.js"></script>

    <style>
        /* Ensure the sidebar and content align properly */
.app {
    display: flex;
    flex-direction: row;
    height: 100vh; /* Full viewport height */
}

/* Sidebar styling */
.app-sidebar {
    width: 250px; /* Fixed width for the sidebar */
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    overflow-y: auto;
}

/* Content styling */
.app-content {
    margin-left: 250px; /* Match the sidebar width */
    padding: 20px;
    flex-grow: 1;
}

/* Adjust the size of the logo or user profile image */
.user-profile img {
    width: 40px; /* Adjust width */
    height: 40px; /* Adjust height */
    border-radius: 50%; /* Make it circular */
    object-fit: cover; /* Ensure the image doesn't stretch */
}

/* Styling for user details at the top left */
.user-details {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    background-color: #f8f9fa; /* Light background */
    border-bottom: 1px solid #e0e0e0; /* Optional bottom border */
}

    </style>
</head>
<body class="app">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>
    <div class="user-details">
    <!-- <img src="<?= htmlspecialchars($_SESSION['user_image']) ?>" alt="Profile"> -->
    <div>
        <p><strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
        <p><?= htmlspecialchars($_SESSION['role_name']) ?></p>
    </div>
</div>
    <!-- Main Content -->
    <div class="app-content">
        <div class="container">
            <h1 class="mt-4">Student Dashboard</h1>
            <div class="row">
                <!-- HOD Count -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Student of :</h5>
                            <p class="card-text"><?=  $_SESSION['department'] ?></p>
                        </div>
                    </div>
                </div>
                <!-- Departments Count -->
                <!-- <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Number of Departments</h5>
                            <p class="card-text"><?= $departmentCount ?></p>
                        </div>
                    </div>
                </div> -->
                <!-- Academic Years Count -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title"> Current Academic Year</h5>
                            <p class="card-text"><?= $activeSemester ?></p>
                        </div>
                    </div>
                </div>
                <!-- Active Semesters Count -->
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Active Semester</h5>
                            <p class="card-text"><?= $activeYear ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

