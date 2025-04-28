<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../login/");
    die();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../datacon.php'; // Database connection
require '../vendor/autoload.php'; // For PHPMailer, adjust path if necessary

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Add HOD
if (isset($_POST['add_hod'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $department_id = $_POST['department_id'];
    $password = bin2hex(random_bytes(4)); // Stronger password generation
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = $conn->prepare("SELECT dep_name FROM department WHERE t_id = ?");
    $sql->bind_param("i", $department_id);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    $department_db = $row['dep_name'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($department_id)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        $checkQuery = "SELECT * FROM user_details WHERE email = '$email' OR username = '$username'";
        $result = $conn->query($checkQuery);

        if ($result->num_rows > 0) {
            echo "<script>alert('Email or Username already exists.');</script>";
        } else {
            $insertQuery = "INSERT INTO user_details (f_name, l_name, email, username, password, role_id, department)
                            VALUES ('$first_name', '$last_name', '$email', '$username', '$hashed_password', 2, '$department_db')";
            if ($conn->query($insertQuery) === TRUE) {
                // Get the ID of the newly inserted HOD
                $hod_id = $conn->insert_id;

                // Assign the HOD to the department
                $updateQuery = "UPDATE department SET hod_id = ? WHERE t_id = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ii", $hod_id, $department_id);
                $stmt->execute();

                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'isabdulaisaiku@gmail.com'; // Use an environment variable for security
                    $mail->Password = 'twkurtspdegwanpu';   // Use an environment variable
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
                    echo "<script>alert('HOD added successfully and email sent.');</script>";
                } catch (Exception $e) {
                    echo "<script>alert('HOD added but email could not be sent.');</script>";
                }
            } else {
                echo "<script>alert('Error: {$conn->error}');</script>";
            }
        }
    }
}





// Edit HOD
if (isset($_POST['edit_hod'])) {
    $hod_id = $_POST['edit_hod_id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['edit_first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['edit_last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['edit_email']);
    $username = mysqli_real_escape_string($conn, $_POST['edit_username']);

    $updateQuery = "UPDATE user_details SET f_name = ?, l_name = ?, email = ?, username = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $username, $hod_id);
    if ($stmt->execute()) {
        echo "<script>alert('HOD details updated successfully.');</script>";
    } else {
        echo "<script>alert('Error updating HOD details: {$stmt->error}');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage HODs</title>
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

    /* Style for readonly fields */
input[readonly] {
    background-color: #f0f0f0;  /* Light gray background */
    border: 1px solid #ddd;  /* Lighter border */
    color: #555;  /* Darker text color for readability */
}

/* Optionally, add some padding or adjust font size for better appearance */
input[readonly] {
    padding: 10px;
    font-size: 14px;
}

  </style>
  
    </style>
</head>
<body>
<div class="container-scroller">
    <?php include 'sidebar.php'; ?>
    <div class="main-panel">
        <div class="app-content">
            <h2 class="text-dark font-weight-bold mb-4">Manage HODs</h2>

            <!-- Button to Add HOD -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addHodModal">Add HOD</button>


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
            <th>Department</th>
            <th>Actions</th> <!-- Added Actions Column -->
        </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT u.*, d.dep_name FROM user_details u
                JOIN department d ON u.user_details = d.hod_id
                WHERE u.role_id = 2";
        $result = $conn->query($sql);
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$count}</td>
                    <td>{$row['f_name']}</td>
                    <td>{$row['l_name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['dep_name']}</td>
                    <td>
                        <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#reassignHodModal' 
    data-id='{$row['user_details']}' 
    data-firstname='{$row['f_name']}' 
    data-lastname='{$row['l_name']}' 
    data-email='{$row['email']}' 
    data-username='{$row['username']}' 
    data-department='{$row['dep_name']}'
    >Reassign</button>

                        <button class='btn btn-info btn-sm' data-toggle='modal' data-target='#editHodModal' data-id='{$row['user_details']}' data-fname='{$row['f_name']}' data-lname='{$row['l_name']}' data-email='{$row['email']}' data-username='{$row['username']}'>Edit</button>
                    </td>
                  </tr>";
            $count++;
        }
        ?>
        </tbody>
    </table>
</div>


            <!-- Add HOD Modal -->
            <div class="modal fade" id="addHodModal" tabindex="-1" role="dialog" aria-hidden="true">
            <!-- <div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-hidden="true"> -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New HOD</h5>
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
                                    <select class="form-control" name="department_id" required>
                                    <option hidden value="">Select Department</option>
                                        <?php
                                        $sql = "SELECT * FROM department WHERE hod_id =0";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['t_id']}'>{$row['dep_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                <button type="submit" class="btn btn-success" name="add_hod">Submit</button>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>

<!-- Reassign HOD Modal -->
<div class="modal fade" id="reassignHodModal" tabindex="-1" role="dialog" aria-labelledby="reassignHodModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reassignHodModalLabel">Reassign HOD</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
$currentHodId = null; // Default value

if (isset($_GET['department_id'])) { 
    $departmentId = $_GET['department_id'];

    $query = "SELECT hod_id FROM department WHERE t_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $departmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentHodId = $row['hod_id'];
    }
}
?>

            <div class="modal-body">
                <form method="POST" action="reassign_hod.php">
                    <!-- Hidden field for Department ID -->
                    <input type="hidden" id="reassign_department_id" name="department_id">
                    <input type="hidden" name="current_hod_id" value="<?php echo isset($currentHodId) ? $currentHodId : ''; ?>">

                     
                    <input type="hidden" name="current_hod_id" id="hod_id">

                    <!-- Read-only Department Field -->
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" class="form-control" id="reassign_department" name="department" readonly>
                    </div>

                    <!-- Current HOD Details (Read-only) -->
                    <div class="form-group">
                        <label>Current HOD First Name</label>
                        <input type="text" class="form-control" id="current_first_name" name="current_first_name" readonly>
                    </div>

                    <div class="form-group">
                        <label>Current HOD Last Name</label>
                        <input type="text" class="form-control" id="current_last_name" name="current_last_name" readonly>
                    </div>

                    <div class="form-group">
                        <label>Current HOD Email</label>
                        <input type="email" class="form-control" id="current_email" name="current_email" readonly>
                    </div>

                    <div class="form-group">
                        <label>Current HOD Username</label>
                        <input type="text" class="form-control" id="current_username" name="current_username" readonly>
                    </div>

                    <!-- New HOD Details (Editable) -->
                    <div class="form-group">
                        <label>New HOD First Name</label>
                        <input type="text" class="form-control" id="new_first_name" name="new_first_name" required>
                    </div>

                    <div class="form-group">
                        <label>New HOD Last Name</label>
                        <input type="text" class="form-control" id="new_last_name" name="new_last_name" required>
                    </div>

                    <div class="form-group">
                        <label>New HOD Email</label>
                        <input type="email" class="form-control" id="new_email" name="new_email" required>
                    </div>

                    <div class="form-group">
                        <label>New HOD Username</label>
                        <input type="text" class="form-control" id="new_username" name="new_username" required>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-success" name="reassign_hod">Reassign HOD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Edit HOD Modal -->
<div class="modal fade" id="editHodModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit HOD Details</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" id="edit_hod_id" name="edit_hod_id">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" id="edit_first_name" name="edit_first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" id="edit_last_name" name="edit_last_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="edit_email" name="edit_email" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" id="edit_username" name="edit_username" required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success" name="edit_hod">Update</button>
                    </div>
                </form>
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

<script>
$(document).on("click", ".reassign-btn", function() {
    let departmentId = $(this).data("department-id");
    $("#reassign_department_id").val(departmentId);
});

$(document).on("click", ".edit-btn", function() {
    let hodId = $(this).data("hod-id");
    let firstName = $(this).data("first-name");
    let lastName = $(this).data("last-name");
    let email = $(this).data("email");
    let username = $(this).data("username");

    $("#edit_hod_id").val(hodId);
    $("#edit_first_name").val(firstName);
    $("#edit_last_name").val(lastName);
    $("#edit_email").val(email);
    $("#edit_username").val(username);
});


$('#reassignHodModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var departmentId = button.data('id'); // Get department ID
    var hodId = button.data('hod'); // Get department ID
    var currentFirstName = button.data('firstname'); // Get current first name
    var currentLastName = button.data('lastname'); // Get current last name
    var currentEmail = button.data('email'); // Get current email
    var currentUsername = button.data('username'); // Get current username
    var currentDepartment = button.data('department'); // Get current department name

    var modal = $(this);
    
    // Set values for the read-only fields in the modal
    modal.find('#reassign_department_id').val(departmentId); // Hidden department ID
    modal.find('#hod_id').val(hodId); // Hidden department ID
    modal.find('#reassign_department').val(currentDepartment); // Department field (read-only)
    modal.find('#current_first_name').val(currentFirstName); // Current HOD first name (readonly)
    modal.find('#current_last_name').val(currentLastName); // Current HOD last name (readonly)
    modal.find('#current_email').val(currentEmail); // Current HOD email (readonly)
    modal.find('#current_username').val(currentUsername); // Current HOD username (readonly)

    // Optionally, clear the new HOD fields (or pre-fill them if needed)
    modal.find('#new_first_name').val('');
    modal.find('#new_last_name').val('');
    modal.find('#new_email').val('');
    modal.find('#new_username').val('');
});


</script>
</body>
</html>
