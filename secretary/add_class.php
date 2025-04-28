<?php

    session_start();
    $pageTitle = "Add Class";
    $emptyPlaceholder = "Loading...";

    include './assets/partials/header.inc.php';
    include '../datacon.php';

    // Add HOD
if (isset($_POST['add_class'])) {
    $class = mysqli_real_escape_string($conn, $_POST['class_name']);
    $program = mysqli_real_escape_string($conn, $_POST['programme']);
    $close_year = mysqli_real_escape_string($conn, $_POST['year_of_completion']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $level = mysqli_real_escape_string($conn, $_POST['lev_id']);    

    if (empty($class) || empty($program) || empty($close_year) || empty($department) || empty($level)) {
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
                // Get the ID of the newly inserted class
                echo "<script>alert('Class added successfully ');</script>";

            }
        }
    }
}


?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<!-- Scripts for cards -->
<?php 
   // $stmt = $conn -> prepare("SELECT `department` FROM user_details WHERE ")
    $userDept = $_SESSION['department'] ?? "N/A";


?>

<main>
<div class="main-panel">
        <div class="app-content">
            <h2 class="text-dark font-weight-bold mb-4">Add a Class</h2>

            <!-- Button to Add  -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">Add Class</button>


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

            <!-- Add Class Modal -->
            <div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-hidden="true">
            
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add a Class</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                       
                        <form method="POST">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="class_name" required>
                            </div><br />
                            <div class="form-group">
                                <label>Programme</label>
                                <select class="form-control" name="programme" required>
                                <option hidden value="">Select Programme</option>
                                    <?php
                                    $sql = "SELECT * FROM programme ";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['t_id']}'>{$row['prog_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div><br />
                            <div class="form-group">
                                <label>Year of Completion</label>
                                <select class="form-control" name="year_of_completion" required>
                                    <?php
                                    $currentYear = date('Y');
                                    for ($i = $currentYear - 4; $i <= $currentYear + 4; $i++) {
                                        echo "<option value='{$i}'>{$i}</option>";
                                    }
                                    ?>
                                </select>
                            </div><br />
                            <div class="form-group">
                                <label>Current Level</label>
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
                                <button type="submit" class="btn btn-success" name="add_class">Submit</button>
                            </div>
                        </form>
                    </div>
                            </div>
            </div>
</main>


<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>