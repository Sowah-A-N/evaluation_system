<?php
session_start();
include '../datacon.php';

// Set response headers to return JSON
header('Content-Type: application/json');

// Retrieve JSON data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);

$student_id = $data['student_id'] ?? null; // Correctly access 'student_id'
$course_id = $data['course_id'] ?? null;  // Correctly access 'course_id'

//echo json_encode(["info", "message" => $student_id, " | ", $course_id]);
// Check if student_id and course_id are provided
if ($student_id == "" || $course_id == "") {
    echo json_encode(["success" => false, "message" => "Student ID and Course ID are required."]);
    exit;
}

try {
    // Prepare the SQL statement
    $sql = "INSERT INTO evaluations (student_id, course_id) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE evaluation_date = CURRENT_TIMESTAMP";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_id, $course_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Evaluation recorded successfully."]);
        $_SESSION['evaluation_id'] = mysqli_insert_id($conn);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
} finally {
    $conn->close();
}
