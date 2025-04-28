<?php
    session_start();

    $pageTitle = "Reports";
    include '../datacon.php';
    include "./assets/partials/header.php";

    print_r($_SESSION);

    $student_id = $_SESSION['unique_id'];
    
    $takenEvalautionStmt = "SELECT c.name, c.department, e.* 
                            FROM evaluations e
                            INNER JOIN courses c ON e.course_id = c.course_code
                            WHERE student_id = '{$student_id}'";

    // Execute the query
    $result = mysqli_query($conn, $takenEvalautionStmt);

    // Check for errors in the execution
    if (!$result) {
        die("Database query failed: " . mysqli_error($conn));
    }

    // Fetch results
    $evaluations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $evaluations[] = $row;
    }

    $pendingEvaluationStmt = "SELECT c.course_code, c.name, c.department
                                FROM courses c
                                LEFT JOIN evaluations e ON c.course_code = e.course_id AND e.student_id = ?
                                WHERE e.course_id IS NULL;";

    //Prepare the query statement
    if ($stmt = $conn->prepare($pendingEvaluationStmt)) {
        //Bind the student_id parameter
        $stmt->bind_param("s", $student_id); // assuming student_id is an integer

        //Execute the query
        $stmt->execute();

        //Get the result set
        $pendingEvaluationResult = $stmt->get_result();

       
    } else {
        // Handle query preparation error
        echo "Error preparing statement: " . $conn->error;
    }

    //var_dump($evaluations);     

?>

<style>
    table {
        width: 70%;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
</style>

<body class="app">
    <?php include "./assets/partials/sidebar.html" ?>
    <div class="container">
     <div>
        <h2>Completed Evaluations</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Course Department</th>
                    <th>Date of Evaluation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($evaluations as $evaluation): ?>
                    <tr>
                        <td><?php echo $evaluation['course_id']; ?></td>
                        <td><?php echo $evaluation['name']; ?></td>
                        <td><?php echo $evaluation['department']; ?></td>
                        <td><?php echo date('d-M-Y', strtotime($evaluation['evaluation_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
     </div>  

     <div>
        <h2>Pending Evaluations</h2>
        <table border="1">      
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($pendingEvaluationResult->num_rows > 0) {
                        while ($row = $pendingEvaluationResult->fetch_assoc()) {
                            //print_r($row);
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                            echo "<td><button onclick='evaluateCourse(".$row['course_code'].")'>Evaluate</button></td>"; 
                            echo "</tr>";
                        }
                    } else {
                        echo "No pending evaluations found.";
                    }
                ?>
            </tbody>
        </table>
     </div>
    </div>

    <script>
        function evaluateCourse(course_code){
            alert(`Evaluating ${course_code}....`);
        }

        function evaluateCourse(){
            alert('Evaluating course...');
        }
    </script>
</body>
<?php include_once "./assets/partials/footer.html"  ?>
