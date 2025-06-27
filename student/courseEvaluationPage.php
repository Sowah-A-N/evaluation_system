<?php
session_start();
include '../datacon.php';

$courseId = ($_GET['courseId']); // Ensure the course ID is an integer

// Prepare the SQL statement to prevent SQL injection
$courseDetailsQuery = $conn->prepare("SELECT course_code, name FROM courses WHERE course_code = ?");
$courseDetailsQuery->bind_param("s", $courseId); // "i" denotes the type is integer

// Execute the statement
$courseDetailsQuery->execute();

// Get the result
$courseDetailsResult = $courseDetailsQuery->get_result();

// Retrieve questions and categories
$evaluationQuestionsQuery = "SELECT question_id, question_text, is_required, category FROM evaluation_questions";
$evaluationQuestionsResult = mysqli_query($conn, $evaluationQuestionsQuery);

$questions = [];
if ($evaluationQuestionsResult) {
    while ($row = mysqli_fetch_assoc($evaluationQuestionsResult)) {
        $questions[] = $row;
    }
} else {
    echo "Error: " . mysqli_error($conn); // Debugging statement
}

// Pass questions as JSON for JavaScript use
echo "<script>const questions = " . json_encode($questions, JSON_INVALID_UTF8_IGNORE) . ";</script>";

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Course Evaluation</title>
<link rel="stylesheet" href="assets/css/portal.css">
<script src="assets/plugins/fontawesome/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

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

    #submit-btn {
        margin-top: 30px; /* Move downward */
        padding: 10px 20px;
        background-color: #007bff; /* Bootstrap primary color */
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    #submit-btn:hover {
        background-color: #0056b3; /* Darker shade on hover */
    }

    #submit-btn:active {
        background-color: #003f7f; /* Even darker shade when clicked */
    }

    .category-header {
        font-weight: bold;
        font-size: 1.2rem;
        margin-top: 20px;
        margin-bottom: 10px;
        color: #333;
    }

    .question-div {
        margin-bottom: 15px;
    }
    /* General question div styling */
    .question-div {
        margin-bottom: 20px;
    }

    /* General question div styling */
    .question-div {
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
    }

    /* Slider container styling */
    /* Slider container styling */
    .slider-container {
        position: relative;
        width: 60%; /* Reduced the width of the slider container */
        margin: 10px auto; /* Center the slider container */
    }

    /* Slider styling */
    .rating-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 6px; /* Smaller height */
        border-radius: 3px;
        background: #ddd;
        outline: none;
        cursor: pointer;
        transition: background 0.3s;
        background: linear-gradient(to right, #007bff 0%, #007bff 20%, #ddd 20%, #ddd 40%, #007bff 40%, #007bff 60%, #ddd 60%, #ddd 80%, #007bff 80%, #007bff 100%);
    }

    /* Change slider color on hover */
    .rating-slider:hover {
        background: linear-gradient(to right, #0056b3 0%, #0056b3 20%, #aaa 20%, #aaa 40%, #0056b3 40%, #0056b3 60%, #aaa 60%, #aaa 80%, #0056b3 80%, #0056b3 100%);
    }

    .rating-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 15px;
        height: 15px;
        background: #007bff;
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.3s;
    }

    .rating-slider::-webkit-slider-thumb:hover {
        background: #0056b3;
    }

    .rating-slider::-moz-range-thumb {
        width: 15px;
        height: 15px;
        background: #007bff;
        border-radius: 50%;
        cursor: pointer;
    }

    /* Tick container styling */
    .tick-container {
        display: flex;
        justify-content: space-between;
        margin-top: 5px;
        font-size: 12px;
        color: #333;
    }

    /* Styling individual ticks */
    .tick {
        position: relative;
        text-align: center;
        width: 1px; /* Align ticks properly */
        height: 10px;
        background: transparent; /* No visible line */
        font-weight: bold;
    }

    /* Tick line styling */
    .tick:before {
        content: '';
        position: absolute;
        top: -10px;
        left: -0.5px;
        width: 2px;
        height: 10px;
        background: #333; /* Tick line */
    }

    /* Display the rating value below the slider */
    .rating-value {
        display: block;
        text-align: right;
        font-size: 14px;
        font-weight: bold;
        margin-top: 5px;
    }

</style>
<style>
.rating-container {
    display: inline-block;
    margin-left: 10px;
}

.rating-stars {
    display: inline-block;
    font-size: 24px;
    cursor: pointer;
}

.star {
    color: #ccc;
    transition: color 0.2s ease;
}

.star.active {
    color: #ffd700;
}

.rating-value {
    display: inline-block;
    margin-left: 10px;
    font-size: 18px;
}
</style>

</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="app-content">
    <div class="container">
        <?php
            if ($courseDetailsResult->num_rows > 0) {
                $courseDetails = $courseDetailsResult->fetch_assoc();
                echo "Course Code: " . htmlspecialchars($courseDetails['course_code']) . "<br /><br />";
                echo "Course Name: " . htmlspecialchars($courseDetails['name']) . "<br>";
                echo "<hr />";
            } else {
                echo "No course found with the given ID.";
            }
        ?>
        <div id="questions-container"></div>
        <div style="text-align: center;">
            <button id="submit-btn">Submit</button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("questions-container");

    if (!questions || !Array.isArray(questions)) {
        console.error('Questions data is not properly initialized.');
        return;
    }

    // Group questions by categories
    const groupedQuestions = questions.reduce((groups, question) => {
        const category = question.category || "Uncategorized";
        if (!groups[category]) {
            groups[category] = [];
        }
        groups[category].push(question);
        return groups;
    }, {});

    let questionCounter = 1; // Initialize the counter for continuous numbering

    // Render questions by category
    Object.keys(groupedQuestions).forEach(category => {
        // Add category header
        const categoryHeader = document.createElement("div");
        categoryHeader.textContent = category;
        categoryHeader.classList.add("category-header");
        container.appendChild(categoryHeader);

        groupedQuestions[category].forEach((question) => {
            const questionDiv = document.createElement("div");
            questionDiv.classList.add("question-div");

            // Add question number and text
            const questionText = document.createElement("p");
            questionText.textContent = `${questionCounter}. ${question.question_text}`;
            questionDiv.appendChild(questionText);

            // Create a container for the star ratings
            const ratingContainer = document.createElement("div");
            ratingContainer.classList.add("rating-container");

            // Create the star ratings
            const ratingStars = document.createElement("div");
            ratingStars.classList.add("rating-stars");

            for (let i = 1; i <= 5; i++) {
                const star = document.createElement("span");
                star.classList.add("star");
                star.textContent = "\u2605"; // Unicode character for a star
                ratingStars.appendChild(star);
            }

            ratingContainer.appendChild(ratingStars);

            // Add event listeners to handle mouse clicks and hover effects
            ratingStars.addEventListener("click", (e) => {
                if (e.target.classList.contains("star")) {
                    const rating = Array.prototype.indexOf.call(ratingStars.children, e.target) + 1;
                    updateRating(ratingStars, rating);
                }
            });

            // ratingStars.addEventListener("mouseover", (e) => {
            //     if (e.target.classList.contains("star")) {
            //         const rating = Array.prototype.indexOf.call(ratingStars.children, e.target) + 1;
            //         updateRating(ratingStars, rating, true);
            //     }
            // });

            // ratingStars.addEventListener("click", () => {
            //     updateRating(ratingStars, rating, true);
            // });

            // Initialize the rating to 3
            updateRating(ratingStars, 3);

            questionDiv.appendChild(ratingContainer);

            container.appendChild(questionDiv);

            // Increment the question number for the next question
            questionCounter++;
        });
    });
});

// Helper function to update the rating
function updateRating(ratingStars, rating, isHover = false) {
    const stars = ratingStars.children;

    for (let i = 0; i < stars.length; i++) {
        if (i < rating) {
            stars[i].classList.add("active");
        } else {
            stars[i].classList.remove("active");
        }
    }

    if (!isHover) {
        // Update the rating value
        const ratingValue = ratingStars.parentNode.querySelector(".rating-value");
        if (ratingValue) {
            ratingValue.textContent = rating;
        }
    }
}
// Handle submit button click
document.getElementById("submit-btn").addEventListener("click", () => {
    const responses = questions.map(question => {
        const input = document.querySelector(`input[name='answer_${question.question_id}']`);
        return {
            question_id: question.question_id,
            answer: input ? input.value : ""
        };
    });

    console.log("Submitting responses...");
    console.log("Responses => : ", responses);
    // Send responses to the server
    
    fetch('submitEvaluationResponses.inc.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ responses })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Thank you for your responses!");
        } else {
            alert("There was an error. Please try again.");
        }
    })
    .catch(error => console.error("Error:", error));
    
    console.log("After submission...");
    
});
</script>
</body>
</html>
