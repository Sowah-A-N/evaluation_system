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
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $semester = mysqli_real_escape_string($conn, $_POST['sem']);
    $course = mysqli_real_escape_string($conn, $_POST['name']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $level = mysqli_real_escape_string($conn, $_POST['lev_id']);



    

    if (empty( $code ) || empty( $semester) || empty($course) || empty( $department) || empty($level)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
       
        $checkQuery = "SELECT * FROM courses WHERE course_code = '$code' OR name = '$course'";
        $result = $conn->query($checkQuery);
         
        
        if ($result->num_rows > 0) {
            echo "<script>alert('Course Code or Name already exists.');</script>";
        } else {
            $insertQuery = "INSERT INTO courses (course_code, name, semester_id, department, level_id)
                            VALUES (' $code', '$course', ' $semester', '$department', '$level')";
            if ($conn->query($insertQuery) === TRUE) {
                // Get the ID of the newly inserted HOD
                echo "<script>alert('Course Details added successfully .');</script>";

            }
        }
    }
}
?>

<?php
require '../vendor/autoload.php';

if (isset($_POST['upload'])) {
    $file = $_FILES['excelFile']['tmp_name'];
    $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file);
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    foreach ($rows as $row) {
        // Assuming the first row contains headers, skip it
        if ($row === $rows[0]) {
            continue;
        }

        $codes = mysqli_real_escape_string($conn, $row[0]);
        $courses = mysqli_real_escape_string($conn, $row[1]);
        $semesters = mysqli_real_escape_string($conn, $row[2]);
        $departments = mysqli_real_escape_string($conn, $row[3]);
        $levels = mysqli_real_escape_string($conn, $row[4]);

        $checkEmailQuery = "SELECT COUNT(*) as count FROM courses WHERE course_code = '$codes'";
        $result = $conn->query($checkEmailQuery);
        $row = $result->fetch_assoc();

        if (isset($row['count']) && $row['count'] > 0) {
            // Skip this row if email already exists
            continue;
        }

        // Insert the user into the database
        $insertQuery = "INSERT INTO courses (course_code, name, semester_id, department, level_id)
                            VALUES (' $codes', '$courses', ' $semesters', '$departments', '$levels')";


        if ($conn->query($insertQuery) === TRUE) {
                
                    echo "<script>alert('Courses Uploaded Sucessfully.');</script>";
             } else {
                echo "<script>alert('Error: {$conn->error}');</script>";
            }

        
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Advisor</title>
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
            <h2 class="text-dark font-weight-bold mb-4">Add a Course</h2>

            <!-- Button to Add  -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addHodModal">Add a Course</button>
            <!-- <button class="btn btn-success" type="button" id="uploadExcelButton" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#uploadExcelModal"> Upload Excel File</button> -->

            <!-- Table -->
            <div class="mt-4">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Level</th>
                        <th>Semester</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Get the department from the session
                        $department = $_SESSION['department'];

                        // Prepare the SQL query with a WHERE clause for the department
                        $sql = "SELECT c.id, c.name, c.department, c.course_code,
                                    a.semester_name AS active_semester, 
                                    l.level_name AS level
                                FROM courses c
                                JOIN active_semester a ON c.semester_id = a.semester_id
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
                                        <td>{$row['course_code']}</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['level']}</td>
                                        <td>{$row['active_semester']}</td>
                                    </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No courses found for your department.</td></tr>";
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
                            <h5 class="modal-title">Add a Course</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                       
                        <div class="modal-body">
                            <form method="POST">

                            <div class="form-group">
                                    <label>Course Code</label>
                                    <input type="text" class="form-control" name="code" required>
                                </div>

                                <div class="form-group">
                                    <label>Course Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>


                                <div class="form-group">
                                    <label>Semester</label>
                                    <select class="form-control" name="sem" required>
                                    <option hidden value="">Select Programme</option>
                                        <?php
                                        $sql = "SELECT * FROM active_semester ";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['semester_id']}'>{$row['semester_name']}</option>";
                                        }
                                        ?>
                                    </select>
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


            <div class="modal fade" id="uploadExcelModal" tabindex="-1" role="dialog" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="uploadExcelModalLabel">Upload Questions</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                  <div class="form-group">
                                    <label for="excelFile">Choose Excel File:</label>
                                    <input type="file" class="form-control" name="excelFile" id="excelFile" accept=".xls, .xlsx" required>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-success" name="upload">Upload</button>
                                </form>
                              </div>
                            </div>
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
