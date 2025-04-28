<?php
    session_start();
    include '../datacon.php';

    // The query to fetch the active academic year & Semester
    $academicYearQuery = "SELECT * FROM academic_year WHERE is_active = 1;";

    // Execute the query
    $academicYearResult = mysqli_query($conn, $academicYearQuery);

    // Check if the query returned a result
    if ($academicYearResult && mysqli_num_rows($academicYearResult) > 0) {
        // Fetch the data for the active academic year
        $academicYear = mysqli_fetch_assoc($academicYearResult);
        
        // You can now use the fetched academic year data
        $academicYearId = $academicYear['academic_year_id'];
        $academicYearLabel = $academicYear['year_label'];
       
        // Example: Display the academic year
        echo "Active Academic Year: " . $academicYearLabel. "<br />";
    } else {
        echo "No active academic year found.". "<br />";
    }
   
    // The query to fetch all semesters
    $allSemestersQuery = "SELECT * FROM active_semester;";

    // Execute the query to fetch all semesters
    $allSemestersResult = mysqli_query($conn, $allSemestersQuery);

    // The query to fetch the active semester
    $activeSemesterQuery = "SELECT * FROM active_semester WHERE is_active = 1;";
    $activeSemesterResult = mysqli_query($conn, $activeSemesterQuery);

    if ($activeSemesterResult && mysqli_num_rows($activeSemesterResult) > 0) {
        // Fetch the data for the active semester
        $activeSemester = mysqli_fetch_assoc($activeSemesterResult);
        $activeSemesterId = $activeSemester['semester_id'];
        $activeSemesterName = $activeSemester['semester_name'];

        // Display the active semester
        echo "Active Semester: " . $activeSemesterName . "<br />";
    } else {
        echo "No active semester found." . "<br />";
    }
 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Year</title>
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
 <?php include 'sidebar.php'; ?>
 <div class="app">
 <div class="app-content">
    <form action="update_semester.php" method="POST">
        <label for="semester">Select Semester:</label>
        <select name="semester_id" id="semester">
            <?php
            // Loop through all semesters and display them as options
            if ($allSemestersResult && mysqli_num_rows($allSemestersResult) > 0) {
                while ($semester = mysqli_fetch_assoc($allSemestersResult)) {
                    $semesterId = $semester['semester_id'];
                    $semesterName = $semester['semester_name'];

                    // Mark the active semester as selected
                    $selected = ($semesterId == $activeSemesterId) ? 'selected' : '';
                    echo "<option value='$semesterId' $selected>$semesterName</option>";
                }
            } else {
                echo "<option value='' disabled>No semesters available</option>";
            }
            ?>
        </select>

        <button type="submit">Update Active Semester</button>
    </form>
        </div>
    </div>
    <?php
    // Don't forget to close the database connection
    mysqli_close($conn);
    ?>
    </div>
 </body>
 </html>