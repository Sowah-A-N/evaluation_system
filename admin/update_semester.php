<?php
session_start();
include '../datacon.php';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the selected semester ID from the POST request
    $selectedSemesterId = $_POST['semester_id'];

    // Ensure the selected semester ID is not empty or invalid
    if (!empty($selectedSemesterId) && is_numeric($selectedSemesterId)) {
        // Begin a transaction to ensure data consistency
        mysqli_begin_transaction($conn);

        try {
            // First, set all semesters to inactive (is_active = 0)
            $updateInactiveQuery = "UPDATE active_semester SET is_active = 0;";
            if (!mysqli_query($conn, $updateInactiveQuery)) {
                throw new Exception("Error setting semesters to inactive.");
            }

            // Now, set the selected semester as active (is_active = 1)
            $setActiveQuery = "UPDATE active_semester SET is_active = 1 WHERE semester_id = '$selectedSemesterId';";
            if (!mysqli_query($conn, $setActiveQuery)) {
                throw new Exception("Error activating the selected semester.");
            }

            // Commit the transaction if both queries are successful
            mysqli_commit($conn);

            // Provide a success message
            echo "Active semester updated successfully!";
        } catch (Exception $e) {
            // If an error occurs, roll back the transaction
            mysqli_rollback($conn);
            

            // Display the error message
            echo "Error updating active semester: " . $e->getMessage();
        }
    } else {
        echo "Invalid semester selected.";
    }
}

// Don't forget to close the database connection
mysqli_close($conn);
?>
