<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../login/");
    die();
}

include '../datacon.php'; // Database connection
require '../vendor/autoload.php'; // For PHPMailer, adjust path if necessary

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Add HOD
if (isset($_POST['add_secretary'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $department_id = $_POST['department'];
    $password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);



    

    if (empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($department_id)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        $checkQuery = "SELECT * FROM user_details WHERE email = '$email' OR username = '$username'";
        $result = $conn->query($checkQuery);
         
        
        if ($result->num_rows > 0) {
            echo "<script>alert('Email or Username already exists.');</script>";
        } else {
            $insertQuery = "INSERT INTO user_details (f_name, l_name, email, username, password, role_id, department)
                            VALUES ('$first_name', '$last_name', '$email', '$username', '$hashed_password', 3, '$department_id')";
            if ($conn->query($insertQuery) === TRUE) {
                // Get the ID of the newly inserted HOD
                

                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'isabdulaisaiku@gmail.com'; // Replace with your email
                    $mail->Password = 'twkurtspdegwanpu';   // Replace with your app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('isabdulaisaiku@gmail.com', 'RMU Course Evaluation System');
                    $mail->addAddress($email, $first_name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your Account Details For the Course Evaluation System';
                    $mail->Body = " 
                    Dear $first_name,<br><br>
                        <h3>Welcome to the Course Evaluation System</h3>
                         Your account has been created successfully. Below are your login details:<br>
                        <p>Username: $username</p>
                        <p>Password: $password</p>
                        <p>Please log in and update your password as soon as possible.</p>";

                    $mail->send();
                    echo "<script>alert('Department Secretary added successfully and email sent.');</script>";
                } catch (Exception $e) {
                    echo "<script>alert('Department Secretary added but email could not be sent.');</script>";
                }
            } else {
                echo "<script>alert('Error: {$conn->error}');</script>";
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
    <title>Add a Secretary</title>
    <link rel="stylesheet" href="assets/css/portal.css">
    <script src="assets/plugins/fontawesome/js/all.min.js"></script>
    
   <!-- Bootstrap CSS -->

   <style>
    .app {
      display: flex;
      flex-direction: row;
      height: 100vh;
    }

    .app-sidebar {
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      overflow-y: auto;
    }

    .app-content {
      margin-left: 260px;
      padding: 20px;
      flex-grow: 1;
    }

    .modal-backdrop {
      background-color: rgba(0, 0, 0, 0.5); /* Reduce brightness dimming effect */
    }
  </style>
  
    </style>
</head>
<body>
<div class="container-scroller">
    <?php include 'sidebar.php'; ?>
    <div class="main-panel">
        <div class="app-content">
            <h2 class="text-dark font-weight-bold mb-4">Add a Secretary</h2>

            <!-- Button to Add  -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addHodModal">Add Secretary</button>


            <!-- Table -->
            <div class="mt-4">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php
$department = $_SESSION['department']; // Get the department from session

// Corrected SQL query: removed the extra 'WHERE' and combined conditions with 'AND'
$sql = "SELECT * FROM user_details WHERE user_details.role_id = 3 AND user_details.department = ?";

$stmt = $conn->prepare($sql); // Prepare the statement

// Bind the department parameter to the query
$stmt->bind_param("s", $department);

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();
$count = 1;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$count}</td>
                <td>{$row['f_name']}</td>
                <td>{$row['l_name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['username']}</td>
              </tr>";
        $count++;
    }
} else {
    echo "<tr><td colspan='5'>No Secretary found for your department.</td></tr>";
}
?>

                    </tbody>
                </table>
            </div>

            <!-- Add HOD Modal -->
            <div class="modal fade" id="addHodModal" tabindex="-1" role="dialog" aria-hidden="true">
            
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Secretary</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                       
                        <div class="modal-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                                
                                <div class="form-group">
    <label>Department</label>
    

    <input type="text" class="form-control" name="department" value="<?php echo  $_SESSION['department'] ?>" readonly>
</div>

                                <div>
                                <button type="submit" class="btn btn-success" name="add_secretary">Submit</button>
                                </div>
                            </form>
                        </div>
                            </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
