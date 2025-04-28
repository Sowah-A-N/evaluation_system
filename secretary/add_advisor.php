<?php

    session_start();
    $pageTitle = "Add Advisor";
    $emptyPlaceholder = "Loading...";

    include './assets/partials/header.inc.php';
    include '../datacon.php';
    require '../vendor/autoload.php'; // For PHPMailer, adjust path if necessary

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<!-- Scripts for cards -->
<?php 
   // $stmt = $conn -> prepare("SELECT `department` FROM user_details WHERE ")
    $userDept = $_SESSION['department'] ?? "N/A";

    // Add HOD
    if (isset($_POST['add_advisor'])) {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
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
        $password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);



        

        if (empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($department_name) || empty($level)) {
            echo "<script>alert('Please fill in all fields.');</script>";
        } else {
            $checkQuery = "SELECT * FROM user_details WHERE email = '$email' OR username = '$username'";
            $result = $conn->query($checkQuery);
            
            
            if ($result->num_rows > 0) {
                echo "<script>alert('Email or Username already exists.');</script>";
            } else {
                $insertQuery = "INSERT INTO user_details (f_name, l_name, email, username, password, role_id, department)
                                VALUES ('$first_name', '$last_name', '$email', '$username', '$hashed_password', 4, '$department_name')";
                if ($conn->query($insertQuery) === TRUE) {
                    $advisor_id = $conn->insert_id;

                    $insertLevelQuery = "INSERT INTO advisor_levels ( department_id, level_id, advisor_id) 
                    VALUES ('$department_id', '$level', '$advisor_id')";
    if ($conn->query($insertLevelQuery) === TRUE) {

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
    }

?>

<div class="main-panel">
        <div class="app-content">
            <h2 class="text-dark font-weight-bold mb-4">Add an Advisor</h2>

            <!-- Button to Add  -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addAdvisorModal">Add an Advisor</button>


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
                        <th>Assigned To</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $department = $_SESSION['department']; // Get the department from session

                        // Corrected SQL query: removed the extra 'WHERE' and combined conditions with 'AND'
                        $sql = "SELECT ud.user_details, ud.f_name, ud.l_name, ud.email, ud.username, d.dep_name, l.level_name
                                FROM user_details ud 
                                JOIN advisor_levels al 
                                ON ud.user_details = al.advisor_id
                                JOIN department d ON al.department_id = d.t_id 
                                JOIN level l ON al.level_id = l.t_id 
                                WHERE ud.role_id = 4 AND ud.department = ?" ;

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
                                        <td>{$row['level_name']}</td>
                                    </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No Advisor found for your department.</td></tr>";
                        }
                        ?>

                    </tbody>
                </table>
            </div>

            <!-- Add HOD Modal -->
            <div class="modal fade" id="addAdvisorModal" tabindex="-1" role="dialog" aria-hidden="true">            
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add An Advisor</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                       
                        <div class="modal-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="first_name" required>
                                </div><br />
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="last_name" required>
                                </div><br />
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div><br />
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div><br />
                                
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
                                </div><br />
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="text" class="form-control" name="department" value="<?php echo  $_SESSION['department'] ?>" readonly>
                                </div><br />

                                <div>
                                <button type="submit" class="btn btn-success" name="add_advisor">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>


<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>