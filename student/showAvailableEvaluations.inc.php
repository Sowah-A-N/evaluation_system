<?php   
    session_start();
    include_once '../datacon.php';
    $department = $_SESSION['department']; // Assuming department_id is stored in session

    // Fetch courses for the department
    $sql = "SELECT course_code, name FROM courses WHERE department = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    $stmt->close();
    $conn->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($courses);