<?php

    session_start();
    $pageTitle = "View Students";
    $emptyPlaceholder = "Loading...";

    include './assets/partials/header.inc.php';

    include '../conn.inc.php';

    

?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<?php

       $fetchStudentStmt = $conn -> prepare("SELECT unique_id, class 
                                                    FROM user_details
                                                    WHERE level_id = :advisor_level;");


       $advisorYear = $_SESSION['level_id'];

       $fetchStudentStmt -> execute(['advisor_level' => $advisorYear]);
       $fetchStudentsResult = $fetchStudentStmt -> fetchAll(PDO::FETCH_ASSOC);

    //    print_r($_SESSION);  
    //    print_r($fetchStudentsResult);
    //    die();

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
                                    <th class="cell">Unique ID</th>
                                    <th class="cell">Evaluations Completed</th>
                                    <th class="cell">Evaluations Pending</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($fetchStudentsResult): ?>
                                    <?php foreach ($fetchStudentsResult as $row){ ?>
                                        <tr>
                                            <td class="cell"><?php echo $row['class']?></td>
                                            <td class="cell"><?php echo $row['unique_id']?></td>
                                            <td class="cell"><span>17 Oct</span><span class="note">2:16 PM</span></td>
                                            <td class="cell"><span class="badge bg-success">Paid</span></td>
                                        </tr>
                                    <?php } ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>



</main>

<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>