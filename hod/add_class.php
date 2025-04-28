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
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $program = mysqli_real_escape_string($conn, $_POST['program_id']);
    $close_year = mysqli_real_escape_string($conn, $_POST['year']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $level = mysqli_real_escape_string($conn, $_POST['lev_id']);



    

    if (empty( $class ) || empty($program) || empty($close_year) || empty($department) || empty($level)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        $checkQuery = "SELECT * FROM classes WHERE class_name = '$class'";
        $result = $conn->query($checkQuery);
         
        
        if ($result->num_rows > 0) {
            echo "<script>alert('Class Name already exists.');</script>";
        } else {
            $insertQuery = "INSERT INTO classes (class_name, programme, year_of_completion, department, level_id)
                            VALUES (' $class', '$program', '$close_year', '$department', '$level')";
            if ($conn->query($insertQuery) === TRUE) {
                // Get the ID of the newly inserted HOD
                echo "<script>alert('Class added successfully .');</script>";

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
    <title>Add a Class</title>
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
            <h2 class="text-dark font-weight-bold mb-4">Add a Class</h2>

            <!-- Button to Add  -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addHodModal">Add Class</button>


            <!-- Table -->
            <div class="mt-4">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Class Name</th>
                        <th>Programme</th>
                        <th>Year of Completion</th>
                        <th>Current Level</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Get the department from the session
                        $department = $_SESSION['department'];

                        // Prepare the SQL query with a WHERE clause for the department
                        $sql = "SELECT c.t_id, c.year_of_completion, c.department, c.class_name,
                                    p.prog_name AS programme, 
                                    l.level_name AS level
                                FROM classes c
                                JOIN programme p ON c.programme = p.t_id
                                JOIN level l ON c.level_id = l.t_id
                                WHERE c.department = ?";

                        $stmt = $conn->prepare($sql); // Prepare the statement

                        // Bind the department parameter to the query
                        $stmt->bind_param("s", $department);

                        // Execute the statement
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $count = 1;

                        // Check if the query returned any rows
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$count}</td>
                                        <td>{$row['class_name']}</td>
                                        <td>{$row['programme']}</td>
                                        <td>{$row['year_of_completion']}</td>
                                        <td>{$row['level']}</td>
                                    </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No classes found for your department.</td></tr>";
                        }

                        // Close the statement
                        $stmt->close();
                        ?>


                    </tbody>
                </table>
            </div>

            <!-- Add HOD Modal -->
            <div class="modal fade" id="addHodModal" tabindex="-1" role="dialog" aria-hidden="true">
            
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add a Class</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                       
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
                                <label>Level</label>
                                <select class="form-control" name="lev_id" required>
                                <option hidden value="">Select Level</option>
                                    <?php
                                    $sql = "SELECT * FROM level ";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['t_id']}'>{$row['level_name']}</option>";
                                    }
                                    ?>
                                </select>
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
   
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
