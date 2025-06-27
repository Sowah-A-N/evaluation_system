<?php

    include "../conn.inc.php";

    header('Content-Type: application/json'); // return JSON

    if (isset($_POST['class_code'])) {
        $class_code = $_POST['class_code'];

        try{

            $fetchEvaluationsStmt =  $conn -> prepare("SELECT * FROM `evaluations` 
                                                        WHERE course_id = :course_id;");
            $fetchEvaluationsStmt -> execute(['course_id' => $class_code]);
            $fetchEvaluationsResult = $fetchEvaluationsStmt -> fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($fetchEvaluationsResult);

        }
        catch (PDOException $e){

            echo "Database connection error : " . $e -> getMessage();

        }

    } else {

        echo json_encode(["error" => "No class name received"]);

    }