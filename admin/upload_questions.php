<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../login/");
    die();
}

include '../datacon.php'; // Database connection
require '../vendor/autoload.php'; // For PHPMailer, adjust path if necessary

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Add a single question
if (isset($_POST['add_question'])) {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    if (empty($question) || empty($category)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        $insertQuery = "INSERT INTO evaluation_questions (question_text, category) VALUES ('$question', '$category')";
        if ($conn->query($insertQuery) === TRUE) {
            echo "<script>alert('Question Uploaded Successfully.');</script>";
        } else {
            echo "<script>alert('Error: {$conn->error}');</script>";
        }
    }
}

// Update a question
if (isset($_POST['edit_question'])) {
    $question_id = mysqli_real_escape_string($conn, $_POST['question_id']);
    $updated_question = mysqli_real_escape_string($conn, $_POST['updated_question']);
    $updated_category = mysqli_real_escape_string($conn, $_POST['updated_category']);
    $updateQuery = "UPDATE evaluation_questions SET question_text = '$updated_question', category = '$updated_category' WHERE question_id = $question_id";
    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>alert('Question Updated Successfully.');</script>";
    } else {
        echo "<script>alert('Error: {$conn->error}');</script>";
    }
}

// Remove a question
if (isset($_POST['remove_question'])) {
    $question_id = mysqli_real_escape_string($conn, $_POST['question_id']);
    // Move question to the archive table
    $archiveQuery = "INSERT INTO questions_archive (question_text, category, archived_at)
                     SELECT question_text, category, NOW() FROM evaluation_questions WHERE id = $question_id";
    if ($conn->query($archiveQuery) === TRUE) {
        // Delete the question from the original table
        $deleteQuery = "DELETE FROM evaluation_questions WHERE id = $question_id";
        $conn->query($deleteQuery);
        echo "<script>alert('Question Removed Successfully.');</script>";
    } else {
        echo "<script>alert('Error: {$conn->error}');</script>";
    }
}
?>


<?php
require '../vendor/autoload.php';

if (isset($_POST['upload'])) {
    $file = $_FILES['excelFile']['tmp_name'];
    $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file);
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    foreach ($rows as $row) {
        // Assuming the first row contains headers, skip it
        if ($row === $rows[0]) {
            continue;
        }

        $question = mysqli_real_escape_string($conn, $row[0]);
        $category = mysqli_real_escape_string($conn, $row[1]);
        

    

        

        // Insert the user into the database
        $insert_query = "INSERT INTO evaluation_questions (question_text, category)
                            VALUES ('$question', '$category')";
        $conn->query($insert_query);

        
    }

    echo '<script type="text/javascript">alert("Questions uploaded successfully.");</script>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Questions</title>
    <link rel="stylesheet" href="assets/css/portal.css">
    <script src="assets/plugins/fontawesome/js/all.min.js"></script>
    
   <!-- Bootstrap CSS -->

   <style>
    .app {
      display: flex;
      flex-direction: row;
      height: 100vh;
    }

    .app-sidebar {
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      overflow-y: auto;
    }

    .app-content {
      margin-left: 260px;
      padding: 20px;
      flex-grow: 1;
    }

    .modal-backdrop {
      background-color: rgba(0, 0, 0, 0.5); /* Reduce brightness dimming effect */
    }
  </style>
  
    </style>
</head>
<body>
<div class="container-scroller">
    <?php include 'sidebar.php'; ?>
    <div class="main-panel">
        <div class="app-content">
            <h2 class="text-dark font-weight-bold mb-4">Evaluation Questionaire</h2>

            <!-- Button to Add HOD -->
            <button class="btn btn-primary" data-toggle="modal" data-target="#addHodModal">Add a Question</button>

            <button class="btn btn-success" type="button" id="uploadExcelButton" aria-haspopup="true" aria-expanded="false" data-toggle="modal" data-target="#uploadExcelModal"> Upload Excel File</button>
            <!-- Table -->
            <div class="mt-4">
            <div class="tab-content tab-transparent-content overflow-auto">
    <table class="table">
        <thead>
        <tr>
            <th>S/N</th>
            <th>Question</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM evaluation_questions ORDER BY category";

        $result = $conn->query($sql);
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$count}</td>
                    <td>{$row['question_text']}</td>
                    <td>{$row['category']}</td>
                    <td>
                        <button class='btn btn-warning' data-toggle='modal' data-target='#editModal{$row['question_id']}'>Edit</button>
                        <button class='btn btn-danger' data-toggle='modal' data-target='#removeModal{$row['question_id']}'>Remove</button>
                    </td>
                  </tr>";

            // Edit Modal
            echo "
            <div class='modal fade' id='editModal{$row['question_id']}' tabindex='-1' role='dialog' aria-hidden='true'>
                <div class='modal-dialog' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title'>Edit Question</h5>
                            <button type='button' class='close' data-dismiss='modal'>&times;</button>
                        </div>
                        <div class='modal-body'>
                            <form method='POST'>
                                <input type='hidden' name='question_id' value='{$row['question_id']}'>
                                <div class='form-group'>
                                    <label>Question</label>
                                    <input type='text' class='form-control' name='updated_question' value='{$row['question_text']}' required>
                                </div>
                                <div class='form-group'>
                                    <label for='updated_category'>Category</label>
                                    <select class='form-control' name='updated_category' required>
                                        <option hidden value='{$row['category']}'>{$row['category']}</option>
                                        <option value='Questions'>Questions</option>
                                        <option value='Assessment'>Assessment</option>
                                        <option value='Teaching and learning environment'>Teaching and Learning Environment</option>
                                        <option value='Washroom & Surroundings'>Washroom and Surroundings</option>
                                        <option value='Customer Service'>Customer Service</option>
                                        <option value='Registry'>Registry</option>
                                        <option value='Accounts'>Accounts</option>
                                        <option value='Library'>Library</option>
                                        <option value='Administration'>Administration</option>
                                        <option value='Sickbay'>Sickbay</option>
                                    </select>
                                </div>
                                <button type='submit' class='btn btn-success' name='edit_question'>Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>";

            // Remove Modal
            echo "
            <div class='modal fade' id='removeModal{$row['question_id']}' tabindex='-1' role='dialog' aria-hidden='true'>
                <div class='modal-dialog' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title'>Confirm Removal</h5>
                            <button type='button' class='close' data-dismiss='modal'>&times;</button>
                        </div>
                        <div class='modal-body'>
                            <p>Are you sure you want to remove this question?</p>
                            <form method='POST'>
                                <input type='hidden' name='question_id' value='{$row['question_id']}'>
                                <button type='submit' class='btn btn-danger' name='remove_question'>Yes, Remove</button>
                                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>";
            $count++;
        }
        ?>
        </tbody>
    </table>
</div>

            </div>
            <!-- Add HOD Modal -->
            <div class="modal fade" id="addHodModal" tabindex="-1" role="dialog" aria-hidden="true">
            <!-- <div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-hidden="true"> -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add a Question</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        
                        <div class="modal-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Question</label>
                                    <input type="text" class="form-control" name="question" id="question" required>
                                </div>

                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control" name="category" id="category" required>
                                    <option hidden value="">Select Category</option>
                                        <option value="Questions">Questions</option>
                                        <option value="Assessment">Assessment</option>
                                        <option value="Teaching_and_learning_environment">Teaching and Learning Environment</option>
                                        <option value="Washroom_and_surroundings">Washroom and Surroundings</option>
                                        <option value="Customer_service">Customer Service</option>
                                        <option value="Registry">Registry</option>
                                        <option value="Accounts">Accounts</option>
                                        <option value="Library">Library</option>
                                        <option value="Administration">Administration</option>
                                        <option value="Sickbay">Sickbay</option>
                                    </select>
                                </div>

                               
                                <div>
                                <button type="submit" class="btn btn-success" name="add_question">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

              <!-- Upload Excel Modal -->
              <div class="modal fade" id="uploadExcelModal" tabindex="-1" role="dialog" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="uploadExcelModalLabel">Upload Questions</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                  <div class="form-group">
                                    <label for="excelFile">Choose Excel File:</label>
                                    <input type="file" class="form-control" name="excelFile" id="excelFile" accept=".xls, .xlsx" required>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" class="btn btn-success" name="upload">Upload</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
