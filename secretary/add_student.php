<?php

    session_start();
    $pageTitle = "Add Student";
    $emptyPlaceholder = "Loading...";

    //include '../conn.inc.php';
    include '../datacon.php';

    require '../vendor/autoload.php'; // For PHPMailer, adjust path if necessary

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    include './assets/partials/header.inc.php';


?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<!-- Scripts for cards -->
<?php 
   // $stmt = $conn -> prepare("SELECT `department` FROM user_details WHERE ")
    $userDept = $_SESSION['department'] ?? "N/A";

    // Add student
    if (isset($_POST['add_student'])) {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $class_name = mysqli_real_escape_string($conn, $_POST['class_id']);
        $level = mysqli_real_escape_string($conn, $_POST['lev_id']);
        $department_name = mysqli_real_escape_string($conn, $_POST['department']);
        
        // Retrieve department_id based on department_name
        $departmentQuery = "SELECT t_id FROM department WHERE dep_name = '$department_name' LIMIT 1";
        $departmentResult = $conn->query($departmentQuery);
        
        if ($departmentResult->num_rows > 0) {
            $department_row = $departmentResult->fetch_assoc();
            $department_id = $department_row['t_id']; // Store department_id in variable
        } else {
            echo "<script>alert('Department not found.');</script>";
            exit; // Stop further processing if department is not found
        }

        $ClassQuery = "SELECT class_name FROM classes WHERE t_id= '$class_name' LIMIT 1";
        $ClassResult = $conn->query($ClassQuery);
        
        if ($ClassResult->num_rows > 0) {
            $class_row = $ClassResult->fetch_assoc();
            $class = $class_row['class_name']; // Store department_id in variable
        } else {
            echo "<script>alert('Class not found.');</script>";
            exit; // Stop further processing if department is not found
        }

        $password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $unique_id = 'RMU' . substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 9);


        

        if (empty($first_name) || empty($last_name) || empty($email) || empty($department_name) || empty($level) || empty($class_name)) {
            echo "<script>alert('Please fill in all fields.');</script>";
        } else {
            $checkQuery = "SELECT * FROM user_details WHERE email = '$email' OR unique_id = '$unique_id'";
            $result = $conn->query($checkQuery);
            
            
            if ($result->num_rows > 0) {
                echo "<script>alert('Email or Unique ID already exists.');</script>";
            } else {
                $insertQuery = "INSERT INTO user_details (f_name, l_name, email,  password, role_id, department, unique_id, class, level_id)
                                VALUES ('$first_name', '$last_name', '$email',  '$hashed_password', 5, '$department_name', '$unique_id', '$class', '$level')";
                
    if ($conn->query($insertQuery) === TRUE) {

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
                            <p>Username: $unique_id</p>
                            <p>Password: $password</p>
                            <p>Please log in and update your password as soon as possible.</p>";

                        $mail->send();
                        echo "<script>alert('Student added successfully  and email sent.');</script>";
                    } catch (Exception $e) {
                        echo "<script>alert('Student added but email could not be sent.');</script>";
                    }
                } else {
                    echo "<script>alert('Error: {$conn->error}');</script>";
                }
            }
        }
    }

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
    
            $f_name = mysqli_real_escape_string($conn, $row[0]);
            $l_name = mysqli_real_escape_string($conn, $row[1]);
            $email = mysqli_real_escape_string($conn, $row[2]);
            $class = mysqli_real_escape_string($conn, $row[3]);
            $level = mysqli_real_escape_string($conn, $row[4]);
            $department = mysqli_real_escape_string($conn, $row[5]);
    
            // Generate a random password
            $password = bin2hex(random_bytes(4)); // Generates an 8-character random password
    
    
            $unique = 'RMU' . substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 9);
            // Hash the generated password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
            // Check if email already exists
            $checkEmailQuery = "SELECT COUNT(*) as count FROM user_details WHERE email = '$email' OR unique_id='$unique' ";
            $result = $conn->query($checkEmailQuery);
            $row = $result->fetch_assoc();
    
            if (isset($row['count']) && $row['count'] > 0) {
                // Skip this row if email already exists
                continue;
            }
    
            // Insert the user into the database
            $insertQuery = "INSERT INTO user_details (f_name, l_name, email,  password, role_id, department, unique_id, class, level_id)
                             VALUES ('$f_name', '$l_name', '$email',  '$hashedPassword', 5, '$department', '$unique', '$class', '$level')";
            $conn->query($insertQuery);
    
            // Send the login details via email
            sendEmail($f_name, $email, $unique, $password);
        }
    
        echo '<script type="text/javascript">alert("Users uploaded and emails sent successfully.");</script>';
    }

    function sendEmail($f_name, $email, $unique, $password) {
        $mail = new PHPMailer(true);
    
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'isabdulaisaiku@gmail.com'; // Your email
            $mail->Password = 'twkurtspdegwanpu'; // Your Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            //Recipients
            $mail->setFrom('isabdulaisaiku@gmail.com', 'RMU Course Evaluation System');
            $mail->addAddress($email, $f_name);
    
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Account Details For RMU Course Evaluation System ';
            $mail->Body    = "
                Dear $f_name,<br><br>
                Your account has been created successfully. Below are your login details:<br>
                <strong>Username:</strong> $unique<br>
                <strong>Password:</strong> $password<br><br>
                Please log in and change your password.<br><br>
                Regards,<br>RMU Course Evaluation System
            ";
    
            $mail->send();
        } catch (Exception $e) { 
            echo  '<script type="text/javascript">alert("Message could not be sent. Mailer Error: {$mail->ErrorInfo}"); </script>';
        }
    }

?>


<main>
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">			    
            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Add / View Students</h1>
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <div class="table-search-form row gx-1 align-items-center">
                                    <div class="col-auto">
                                        <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addStudentModal">
                                            Add a Student
                                        </button>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-outline-success btn-lg" id="uploadExcelButton" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#uploadExcelModal">
                                            Upload Excel File
                                        </button>
                                    </div> 
                                </div>                                                                       
                            </div>
                        </div>
                    </div>                        
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal-->
    <!-- Modal Structure -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add A Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lev_id">Level</label>
                                    <select class="form-control" id="lev_id" name="lev_id" required>
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
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class_id">Class</label>
                                    <select class="form-control" id="class_id" name="class_id" required>
                                        <option hidden value="">Select Class</option>
                                        <?php
                                        $sql = "SELECT * FROM classes ";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['t_id']}'>{$row['class_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" class="form-control" id="department" name="department" value="<?php echo  $_SESSION['department'] ?>" readonly>
                                </div>
                            </div>
                        </div><br><br>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success" name="add_student">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Upload Excel File-->
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



    <div class="mt-4">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Class</th>
                <th>Level</th>
            </tr>
            </thead>
            <tbody>
            <?php
                $department = $_SESSION['department']; // Get the department from session

                // Corrected SQL query: removed the extra 'WHERE' and combined conditions with 'AND'
                $sql = "SELECT ud.user_details, ud.f_name, ud.l_name, ud.email, ud.class,  ud.department, l.level_name
                         FROM user_details ud
                         JOIN level l
                         ON ud.level_id = l.t_id 
                         WHERE ud.role_id = 5 AND ud.department = ?" ;

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
                                <td>{$row['class']}</td>
                                <td>{$row['level_name']}</td>
                            </tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No Student found for your department.</td></tr>";
                }
            ?>

            </tbody>
        </table>
    </div>
</main>


<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>