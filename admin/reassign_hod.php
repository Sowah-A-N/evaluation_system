<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../login/");
    die();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../datacon.php'; // Database connection
require '../vendor/autoload.php'; // For PHPMailer, adjust path if necessary

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['reassign_hod'])) {
    // Fetch form data
    $departmentId = $_POST['department_id'];
    $newFirstName = mysqli_real_escape_string($conn, $_POST['new_first_name']);
    $newLastName = mysqli_real_escape_string($conn, $_POST['new_last_name']);
    $newEmail = mysqli_real_escape_string($conn, $_POST['new_email']);
    $newUsername = mysqli_real_escape_string($conn, $_POST['new_username']);
    $currentHodId = $_POST['current_hod_id']; // Hidden field for current HOD ID

    // Generate a new password
    $newPassword = bin2hex(random_bytes(4)); // Generate a strong 4-byte random password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the current HOD's details in the `user_details` table
    $sql = "UPDATE user_details 
            SET f_name = ?, l_name = ?, email = ?, username = ?, password = ? 
            WHERE user_details = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $newFirstName, $newLastName, $newEmail, $newUsername, $hashedPassword, $currentHodId);

    if ($stmt->execute()) {
        // Update the department to reassign the new HOD
        $updateDepartment = "UPDATE department SET hod_id = ? WHERE t_id = ?";
        $updateStmt = $conn->prepare($updateDepartment);
        $updateStmt->bind_param("ii", $currentHodId, $departmentId);
        $updateStmt->execute();

        // Send the new username and password via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'isabdulaisaiku@gmail.com'; // Use environment variables for security
            $mail->Password = 'twkurtspdegwanpu';    // Use environment variables
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('isabdulaisaiku@gmail.com', 'University Admin');
            $mail->addAddress($newEmail, $newFirstName);

            $mail->isHTML(true);
            $mail->Subject = 'Your Updated HOD Login Credentials';
            $mail->Body = "
            Hello $newFirstName $newLastName,<br><br>
            Your HOD account details have been updated.<br>
            <p>Username: $newUsername</p>
            <p>Password: $newPassword</p>
            <p>Please log in and update your password at your earliest convenience.</p>
            <br>Best regards,<br>University Admin";

            $mail->send();
            echo "<script>alert('HOD reassigned successfully and email sent.');</script>";
        } catch (Exception $e) {
            echo "<script>alert('HOD reassigned but email could not be sent.');</script>";
        }

        // Redirect to manage HODs page with success message
        echo "<script>alert('HOD reassigned but email could not be sent.');</script>";
        exit();
    } else {
        echo "<script>alert('Error: {$stmt->error}');</script>";
    }
}
?>
