<?php

    session_start();
    $pageTitle = "Dashboard - Secretary";
    $emptyPlaceholder = "Loading...";

    include './assets/partials/header.inc.php';

    include '../conn.inc.php';


?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<!-- Scripts for cards -->
<?php 
   // $stmt = $conn -> prepare("SELECT `department` FROM user_details WHERE ")
    $userDept = $_SESSION['department'] ?? "N/A";

    $studentCountStmt = $conn -> prepare("SELECT COUNT(*) AS total_students_in_dept 
                                        FROM `user_details` 
                                        WHERE `department` = :userDept 
                                        AND `unique_id` IS NOT NULL;");

    $studentCountStmt -> execute([':userDept' => $userDept]);
    $studentCountResult = $studentCountStmt->fetch(PDO::FETCH_ASSOC);

    $advisorCountStmt = $conn -> prepare("SELECT COUNT(*) AS total_advisors_in_dept 
                                        FROM `user_details` 
                                        WHERE `department` = :userDept 
                                        AND `role_id` = 4;");

    $advisorCountStmt -> execute([':userDept' => $userDept]);
    $advisorCountResult = $advisorCountStmt->fetch(PDO::FETCH_ASSOC);

    $classCountStmt = $conn -> prepare("SELECT COUNT(*) AS total_classes_in_dept 
                                        FROM `classes` 
                                        WHERE `department` = :userDept  ;");

    $classCountStmt -> execute([':userDept' => $userDept]);
    $classCountResult = $classCountStmt->fetch(PDO::FETCH_ASSOC);


?> 

    <main>
        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                    <div class="app-card-body p-3 p-lg-4">
                        <h4 class="stats-type mb-1">Department</h4>
                        <div class="stats-figure"><?= $userDept ?></div>
                        
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
            </div><!--//col-->

            <div class="col-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                    <div class="app-card-body p-3 p-lg-4">No. Of Classes</h4>
                        <div class="stats-figure"><?= $classCountResult['total_classes_in_dept'] ?></div>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
            </div><!--//col-->

            <div class="col-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                    <div class="app-card-body p-3 p-lg-4">
                        <h4 class="stats-type mb-1">No. of Advisors</h4>
                        <div class="stats-figure"><?= $advisorCountResult['total_advisors_in_dept'] ?></div>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
            </div><!--//col-->

            <div class="col-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                    <div class="app-card-body p-3 p-lg-4">
                        <h4 class="stats-type mb-1">No. of Students</h4>
                        <div class="stats-figure"><?= $studentCountResult['total_students_in_dept'] ?></div>
                    </div><!--//app-card-body-->
                    <a class="app-card-link-mask" href="#"></a>
                </div><!--//app-card-->
            </div><!--//col-->
        </div>


    </main>

<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>