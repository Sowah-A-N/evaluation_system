<?php

    session_start();
    $pageTitle = "";
    $emptyPlaceholder = "Loading...";

    include './assets/partials/header.inc.php';

    include '../conn.inc.php';

?>

<?php include './assets/partials/sidebar.inc.php'; ?>

<?php

    //    $Stmt = $conn -> prepare("");
    //    $Stmt -> execute([]);
    //    $sth = $Stmt -> fetch(PDO::FETCH_ASSOC);

?>

<main></main>

<?php include './assets/partials/footer.inc.php'; ?>
<?php include './assets/partials/scripts.inc.php'; ?>