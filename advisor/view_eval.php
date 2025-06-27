<?php

    session_start();
    $pageTitle = "View Evaluations";
    $emptyPlaceholder = "Loading...";

    include './assets/partials/header.inc.php';

    include '../conn.inc.php';

    // var_dump($_SESSION);
    // die();

    $advisorYear = $_SESSION['level_id'];
    $activeSemester = $_SESSION['semester_id'];

    $className = isset($_GET['class_name']) ? $_GET['class_name'] : "";
    var_dump($className);
    //die();

    $classCourseFetchStmt = $conn -> prepare("SELECT * FROM `courses` 
                                                WHERE semester_id = :active_semester 
                                                AND level_id = :advisor_level");

    $classCourseFetchStmt -> execute([':active_semester' => $activeSemester,
                                        'advisor_level' => $advisorYear]);

    $classCourseFetch = $classCourseFetchStmt -> fetchAll(PDO::FETCH_ASSOC);

    // print_r($classCourseFetch);
    // die();


?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<?php
    if($className){        

        $fetchClassEvaluationStmt = $conn -> prepare("SELECT * FROM evaluations;");
        $fetchClassEvaluationStmt -> execute([]);
        $fetchClassEvaluation = $fetchClassEvaluationStmt -> fetchAll(PDO::FETCH_ASSOC);

    }

    // if($sth){

    // }

    //    $Stmt = $conn -> prepare("");
    //    $Stmt -> execute([]);
    //    $sth = $Stmt -> fetch(PDO::FETCH_ASSOC);

?>

<main>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title text-center">Select a course</h1>
                        <select name="class_name" id="class_name" class="form-select">
                            <option value="">--Select a Course--</option>
                            <?php foreach ($classCourseFetch as $classCourse) : ?>
                                <option id="<?php echo $classCourse['course_code']?>" value="<?php echo $classCourse['name']; ?>">
                                    <?php echo $classCourse['course_code'] . " - " . $classCourse['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="tab-content" id="orders-table-tab-content">
        <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
            <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">
                    <div class="table-responsive">
                        <table class="table app-table-hover mb-0 text-left" id="evaluationsTable" >
                            <thead>
                                <tr>
                                    <td class="cell">S/N</td>
                                    <td class="cell">Student ID</td>
                                    <td class="cell">Course Code</td>
                                    <td class="cell">Date Taken</td>
                                                                  
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    const courseDropdown = document.getElementById("class_name");

    if (courseDropdown) {
        courseDropdown.addEventListener('change', getEvaluations);
    } 

    

    function getEvaluations() {
        const selectedOption = courseDropdown.options[courseDropdown.selectedIndex];
        const selectedValue = selectedOption.value;
        const selectedId = selectedOption.id;

        console.log("Dropdown changed to value:", selectedValue);
        console.log("Selected option ID:", selectedId);

         // Send to PHP via GET or POST (using fetch with POST here)
        fetch("./api/get_evaluations.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "class_code=" + encodeURIComponent(selectedId)
        })
        .then(response => response.json()) // Expect JSON response
        .then(data => {
            console.log("Data from PHP:", data);

            // Get the tbody element
            const tbody = document.querySelector('#evaluationsTable tbody');

            // Clear existing table rows
            tbody.innerHTML = '';

            // Loop through each item in the data array
            data.forEach((item, index) => {
                const row = document.createElement('tr');

                // Add S/N column
                const snCell = document.createElement('td');
                snCell.textContent = index + 1; // Serial numbers start from 1
                row.appendChild(snCell);

                // Add other data columns (adjust keys as needed)
                const studIdCell = document.createElement('td');
                studIdCell.textContent = item.student_id;
                row.appendChild(studIdCell);

                const courseCell = document.createElement('td');
                courseCell.textContent = item.course_id;
                row.appendChild(courseCell);

                const dateCell = document.createElement('td');
                dateCell.textContent = item.evaluation_date;
                row.appendChild(dateCell);

                tbody.appendChild(row);
            });
        })

        .catch(error => {
            console.error("Error:", error);
        });
    }                                

</script>

<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>