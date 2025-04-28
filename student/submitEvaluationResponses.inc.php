<?php
    session_start();
    include '../datacon.php';

    $evaluation_id = $_SESSION['evaluation_id'];

    // Assuming a MySQL connection is already established
    $data = json_decode(file_get_contents('php://input'), true);


    foreach ($data['responses'] as $response) {
        $question_id = intval($response['question_id']);
        $answer = mysqli_real_escape_string($conn, $response['answer']);
        $query = "INSERT INTO responses (evaluation_id, question_id, response_value) VALUES ($evaluation_id, $question_id, '$answer')";
        mysqli_query($conn, $query);
    }


    echo json_encode(['success' => true]);

    // echo '<script>alert("Responses have been submitted.")</script>';

