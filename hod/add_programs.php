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
    $p_code = mysqli_real_escape_string($conn, $_POST['prog_code']);
    $p_name = mysqli_real_escape_string($conn, $_POST['prog_name']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
  



    

    if (empty($p_code) || empty($p_name) || empty($department)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        $checkQuery = "SELECT * FROM programme WHERE prog_code = '$p_code' OR prog_name = '$p_name'";
        $result = $conn->query($checkQuery);
         
        
        if ($result->num_rows > 0) {
            echo "<script>alert('Program Code or Name already exists.');</script>";
        } else {
            $insertQuery = "INSERT INTO programme (prog_code, prog_name, department)
                            VALUES (' $p_code', '$p_name', '$department')";
            if ($conn->query($insertQuery) === TRUE) {
                // Get the ID of the newly inserted HOD
                echo "<script>alert('Department Programme added successfully .');</script>";

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
    <title>Add Programme</title>
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
            <h2 class="text-dark font-weight-bold mb-4">Add a Programme</h2>

            <!-- Button to Add  -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addHodModal">Add  Programme</button>


            <!-- Table -->
            <div class="mt-4">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Programme Code</th>
                        <th>Programme Name</th>
                        
                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $dep = $_SESSION['department'];
                        // Escape the string value properly
                        $dep = $conn->real_escape_string($dep);

                        $sql = "SELECT * FROM programme WHERE department = '$dep'";
                        $result = $conn->query($sql);

                        $count = 1;
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$count}</td>
                                        <td>{$row['prog_code']}</td>
                                        <td>{$row['prog_name']}</td>
                                    </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='3'>No programmes found for the specified department.</td></tr>";
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
                            <h5 class="modal-title">Add a Programme</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                       
                        <div class="modal-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Programme Code</label>
                                    <input type="text" class="form-control" name="prog_code" required>
                                </div>
                                <div class="form-group">
                                    <label>Programme Name</label>
                                    <input type="text" class="form-control" name="prog_name" required>
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
