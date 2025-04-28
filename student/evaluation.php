<?php
session_start();
    
    include '../datacon.php';

    $_SESSION['role_name'] = "student";
    $student_id = $_SESSION['unique_id'] ?? "";
      
    //echo '<script>alert('. $_SESSION['unique_id'] .')</script>';
    $studentInfoQuery = "SELECT user_details.*, department.dep_name, department.t_id 
                         FROM user_details 
                         LEFT JOIN department ON user_details.department = department.dep_name 
                         WHERE user_details.unique_id = '{$student_id}'";
    // Assuming you have a connection to the database in $conn
    $studentInfoResult = $conn->query($studentInfoQuery);
    //$department = $_SESSION['department'];

   
?>
<!DOCTYPE html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Evaluation</title>
    <link rel="stylesheet" href="assets/css/portal.css">
    <script src="assets/plugins/fontawesome/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>


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
</head>
<body>
<!--Styles and theme to be added later -->

    <!-- Card showing student info -->
    <?php include 'sidebar.php'; ?>
    <div class="app-content">
        <div class="card-body">
            <?php
                if ($studentInfoResult->num_rows > 0) {
                // Fetch the data
                while ($row = $studentInfoResult->fetch_assoc()) {
                    // Process each row
                    //echo "Name: " . $row['full_name'] . "<br>";
                    echo "Email: " . $row['email'] . "<br>";
                    echo "Unique ID: " . ($row['unique_id'] ?? "N/A") . "<br>";
                    echo "Department: " . $row['dep_name'] . "<br>";

                     print_r($_SESSION);
                    // var_dump($row);
                }
            } else {
                echo "No results found.";
            }   
            ?>
             <button onclick = "openAvailableEvaluationsModal()">Take Evaluation</button>
        </div>
     </div>

   

    <!--Available Evaluations Modal -->
    <div class="modal fade" id="availableEvaluationsModal" tabindex="-1" aria-labelledby="availableEvaluationsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="availableEvaluationsModalLabel">Available Evaluations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select id="courseSelect" class="form-select">
                        <option value="">Select a course</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Evaluate</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>

<script>
    

    async function openAvailableEvaluationsModal() {
        const modal = new bootstrap.Modal(document.getElementById('availableEvaluationsModal'));
        modal.show();

        try {
            const response = await fetch('showAvailableEvaluations.inc.php'); // Ensure correct path
            const courses = await response.json();

            const selectElement = document.getElementById('courseSelect');
            selectElement.innerHTML = '<option value="">Select a course</option>'; // Reset options

            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.course_code;
                option.textContent = course.name;
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching courses:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('.btn-success').addEventListener('click', () => {
            const selectedCourseId = document.getElementById('courseSelect').value;

            if (!selectedCourseId) {
                alert('Please select a course to evaluate.');
                return;
            }

            const studentId = <?php echo json_encode($student_id); ?>;
            console.log("Student ID : ", studentId);
            console.log("Course ID : ", selectedCourseId);
            recordEvaluation(studentId, selectedCourseId);
        });
    });

    //var studentId = <?php echo $student_id ?>;
    //var courseId = selectedCourseId;

    async function recordEvaluation(studentId, courseId) {
    try {
        const response = await fetch('recordEvaluation.inc.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ student_id: studentId, course_id: courseId })
        });

        const result = await response.json(); // Parse JSON response
        console.log("Response:", result); // Log full response for debugging

        if (result.success) {
            console.log("Evaluation recorded successfully:", result.message);
            // Optionally redirect or show a success message
             window.location.href = `courseEvaluationPage.php?courseId=${courseId}`;
        } else {
            console.error("Failed to record evaluation:", result.message);
        }
    } catch (error) {
        console.error("Error recording evaluation:", error);
    }
}

</script>


</html>