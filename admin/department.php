<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location:../login/");
  die();
}

include '../datacon.php'; // Ensure this file correctly connects to your database

// Add Department Manually
if (isset($_POST['add'])) {
  // Convert input to uppercase
  $department_name = strtoupper(mysqli_real_escape_string($conn, $_POST['dep_name']));
  $code = strtoupper(mysqli_real_escape_string($conn, $_POST['dep_code']));

  // Check for empty fields
  if (empty($department_name) || empty($code)) {
    echo "<script>alert('Please fill in all fields.');</script>";
  } else {
    // Check for duplicates
    $checkQuery = "SELECT * FROM department WHERE UPPER(dep_name) = '$department_name' OR UPPER(dep_code) = '$code'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
      echo "<script>alert('Department Name or Code already exists.');</script>";
    } else {
      // Insert into the database
      $insertQuery = "INSERT INTO department (dep_name, dep_code) VALUES ('$department_name', '$code')";
      if ($conn->query($insertQuery) === TRUE) {
        echo "<script>alert('Department Details added successfully.');</script>";
      } else {
        echo "<script>alert('Error: {$conn->error}');</script>";
      }
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department</title>
    <link rel="stylesheet" href="assets/css/portal.css">
    <script src="assets/plugins/fontawesome/js/all.min.js"></script>


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
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>
  <div class="app-content">
    <h2 class="text-dark font-weight-bold mb-4">Departments</h2>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addDepartmentModal">Add Department</button>

    <div class="mt-4">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Department Name</th>
            <th>Department code</th>

          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM department";
          $result = mysqli_query($conn, $sql);
          $count = 1;
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$count}</td>
                     <td>{$row['dep_name']}</td>
                    <td>{$row['dep_code']}</td>
                  </tr>";
            $count++;
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Department</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="POST">
              <div class="form-group">
                <label>Department Name</label>
                <input type="text" class="form-control" name="dep_name" required>
              </div>

              <div class="form-group">
                <label>Department Code</label>
                <input type="text" class="form-control" name="dep_code" required>
              </div>
              <button type="submit" class="btn btn-primary" name="add">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Corrected Bootstrap 4 JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

