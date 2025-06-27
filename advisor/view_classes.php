<?php

    session_start();
    $pageTitle = "View Classes";
    $emptyPlaceholder = "Loading...";

    include './assets/partials/header.inc.php';

    $advisorDept = $_SESSION['department'] ?? "";
    $advisorYear = $_SESSION['level_id'] ?? "";

    include '../conn.inc.php';

?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<?php

        $advisorClassesStmt = $conn -> prepare("SELECT c.t_id, c.class_name, c.department, c.year_of_completion, p.prog_name 
                                                FROM `classes` c 
                                                JOIN `programme` p ON c.t_id = p.t_id
                                                WHERE c.department = :advisor_dept
                                                AND c.level_id = :advisor_level;");

        $advisorClassesStmt -> execute([':advisor_dept' => $advisorDept,
                                        ':advisor_level' => $advisorYear]);

        $advisorClasses = $advisorClassesStmt -> fetchAll(PDO::FETCH_ASSOC);

        // print_r($advisorClasses)
?>

<main>
    <div class="tab-content" id="orders-table-tab-content">
        <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
            <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">
                    <div class="table-responsive">
                        <table class="table app-table-hover mb-0 text-left">
                            <thead>
                                <tr>
                                    <th class="cell">Class</th>
                                    <th class="cell">Department</th> 
                                    <th class="cell">Programme</th>
                                    <th class="cell">Year of Completion</th>
                                    <th class="cell">Action</th>                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($advisorClasses): ?>
                                    <?php foreach ($advisorClasses as $row){ ?>
                                        <tr>
                                            <td class="cell"><?php echo $row['class_name']?></td>
                                            <td class="cell"><?php echo $row['department']?></td>
                                            <td class="cell"><?php echo $row['prog_name']?></td>
                                            <td class="cell"><?php echo $row['year_of_completion'] ?></td>
                                            <td class="cell">
                                    <!---View evaluations using 'class_name'-->
                                                <button type="button" class="btn btn-outline-info" onclick="viewClassEval('<?=$row['class_name'] ?>')">
                                                    View Evaluations
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No results available for viewing</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
   function viewClassEval(className) {
    const class_name = encodeURIComponent(className);
    console.log(class_name);

    // Optional: You don't actually need XHR if you're redirecting
    // But if you want to validate before redirecting, keep it
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "view_eval.php?class_name=" + class_name, true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log("Success");
            // Redirect with class_name included
            window.location = "view_eval.php?class_name=" + class_name;
        } else {
            console.error("Error:", xhr.statusText);
        }
    };

    xhr.send();
}
</script>

<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>