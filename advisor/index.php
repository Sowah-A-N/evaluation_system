<?php

    session_start();
    $pageTitle = "Dashboard - Advisor";
    $emptyPlaceholder = "Loading...";

    include './assets/partials/header.inc.php';

    include '../conn.inc.php';

    // print_r($_SESSION['user_id']);
    // die();
    $advisorId = $_SESSION['user_id'];
    $advisorDept = $_SESSION['department'];
    // print_r($_SESSION);
    // die();

?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<?php 

    $advisorLevelStmt = $conn -> prepare("SELECT al.level_id, al.advisor_id, l.level_name AS \"Level\"
                                            FROM `advisor_levels` al
                                            INNER JOIN `level` l
                                            ON l.t_id = al.level_id
                                            WHERE al.advisor_id = :advisor_id;");

   $advisorLevelStmt -> execute(['advisor_id' => $advisorId]);
   $advisorLevel = $advisorLevelStmt -> fetch(PDO::FETCH_ASSOC);

   $_SESSION['level_id'] = $advisorLevel['level_id']; 
   $advisorYear = $advisorLevel['level_id'];

   $AdvisorClassCountStmt = $conn -> prepare("SELECT COUNT(*) AS 'num_of_classes'
                                                FROM `classes` 
                                                WHERE level_id = :advisor_level; ");
   $AdvisorClassCountStmt -> execute([':advisor_level' => $advisorYear]);
   $advisorClassCount = $AdvisorClassCountStmt -> fetch(PDO::FETCH_ASSOC);

   $advisorStudentCountStmt = $conn -> prepare("SELECT COUNT(*) AS num_of_students
                                                FROM user_details ud
                                                JOIN classes c ON ud.level_id = c.level_id
                                                WHERE c.level_id = :advisor_level;");

   $advisorStudentCountStmt -> execute([':advisor_level' => $advisorYear]);
   $advisorStudentCount = $advisorStudentCountStmt -> fetch(PDO::FETCH_ASSOC);

//    $Stmt = $conn -> prepare("");
//    $Stmt -> execute([]);
//    $sth = $Stmt -> fetch(PDO::FETCH_ASSOC);


?>

<main>
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Department</h4>
                    <div class="stats-figure"><?= $advisorDept ?? $emptyPlaceholder ?></div>
                    <div class="stats-type"><?= $advisorLevel['Level'] ?? $emptyPlaceholder ?></div>
                    
                </div><!--//app-card-body-->
                <a class="app-card-link-mask" href="#"></a>
            </div><!--//app-card-->
        </div><!--//col-->

        <div class="col-6 col-lg-3">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">No. Of Classes</h4>
                    <div class="stats-figure"><?= $advisorClassCount['num_of_classes'] ?? $emptyPlaceholder ?></div>
                </div><!--//app-card-body-->
                <a class="app-card-link-mask" href="#"></a>
            </div><!--//app-card-->
        </div><!--//col-->

        <div class="col-6 col-lg-3">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Evaluations Taken</h4>
                    <div class="stats-figure"><?= $advisorCountResult['total_advisors_in_dept'] ?? $emptyPlaceholder ?></div>
                </div><!--//app-card-body-->
                <a class="app-card-link-mask" href="#"></a>
            </div><!--//app-card-->
        </div><!--//col-->

        <div class="col-6 col-lg-3">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">No. of Students</h4>
                    <div class="stats-figure"><?= $advisorStudentCount['num_of_students'] ?? $emptyPlaceholder ?></div>
                </div><!--//app-card-body-->
                <a class="app-card-link-mask" href="#"></a>
            </div><!--//app-card-->
        </div><!--//col-->
    </div>
</main>


<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>