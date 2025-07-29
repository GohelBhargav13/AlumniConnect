<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../utills/db_conn.php';
require '../vendor/autoload.php'; // ✅ Moved to the top

if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 

if(!isset($_SESSION['Enroll_no_alumni'])){
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_post'])) {

    if (!isset($_SESSION['alumni_id'])) {
        $_SESSION['message'] = ["success" => false, "final_msg" => "Alumni session is not set"];
        header("Location: alumni_create_post.php");
        exit(); // ✅ Added exit after redirect
    }

    $alumni_id = (int) $_SESSION['alumni_id'];

    $post_title = $_POST['title'] ?? '';
    $post_desc = $_POST['description'] ?? '';
    $post_location = $_POST['location'] ?? '';
    $post_roadmap = $_POST['roadmap'] ?? '';
    $post_skills = $_POST['required_skills'] ?? '';
    $post_ref_link = $_POST['ref_link'] ?? '';
    $post_job_type = $_POST['typeofjob'] ?? '';

    if (empty($post_title) || empty($post_desc) || empty($post_location) || empty($post_roadmap) || empty($post_skills) || empty($post_ref_link) || empty($post_job_type)) {
        $_SESSION['message'] = ["success" => false, "final_msg" => "All fields are required"];
        header("Location: alumni_create_post.php");
        exit(); // ✅ Must stop further execution
    }

    // Insert post
    $post_create_query = "INSERT INTO postmaster (post_title,post_desc,post_location,post_ref_link,post_req_skill,post_ded_roadmap,created_by,post_job_type) VALUES(?,?,?,?,?,?,?,?)";
    $post_stmt = $conn->prepare($post_create_query);
    $post_stmt->bind_param("ssssssis", $post_title, $post_desc, $post_location, $post_ref_link, $post_skills, $post_roadmap, $alumni_id, $post_job_type);

    if ($post_stmt->execute()) {
        $post_id = $post_stmt->insert_id;

        // ✅ Fetch post + alumni details
        $fetch_post_details = "SELECT p.post_title, p.post_desc, a.alumni_name, a.alumni_email 
                               FROM postmaster p 
                               JOIN alumnimaster a ON a.alumni_id = p.created_by 
                               WHERE p.post_id = ?";
        $stmt = $conn->prepare($fetch_post_details);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $final_post = $result->fetch_assoc();

        // ✅ Fetch all students
        $select_all_students = "SELECT student_name, student_email FROM studentmaster";
        $students_result = $conn->query($select_all_students);

        // ✅ Send email to each student
        while ($student = $students_result->fetch_assoc()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'gohelbhargav401@gmail.com';
                $mail->Password = 'aqknaoglmxclkvct'; // ⚠️ App password - secure in .env if possible
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom($final_post['alumni_email'], 'AlumniConnect');
                $mail->addAddress($student['student_email'], $student['student_name']);

                $mail->isHTML(true);
                $mail->Subject = "New Post from {$final_post['alumni_name']} on AlumniConnect";
               $mail->Body = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; color: #333; border: 1px solid #ddd; border-radius: 8px;'>
                        <p style='font-size: 18px; color: #2c3e50;'>Dear <strong>{$student['student_name']}</strong>,</p>

                        <p style='font-size: 16px; color: #16a085; font-weight: bold;'>🎉 New Alumni Post Alert!</p>

                        <div style='margin-top: 10px;'>
                            <p><strong style='color: #2980b9;'>Title:</strong> {$final_post['post_title']}</p>
                            <p><strong style='color: #2980b9;'>Description:</strong> {$final_post['post_desc']}</p>
                            <p><strong style='color: #2980b9;'>Posted by:</strong> {$final_post['alumni_name']}</p>
                            <p><strong style='color: #2980b9;'>Posted on:</strong> Click Here to show Post -> </p>
                            <a href='http://localhost/SE_Project/AlumniConnect/student/student_view_post.php' target='_blank'>http://localhost/SE_Project/AlumniConnect/student/student_view_post.php</a>
                        </div>

                        <hr style='margin: 20px 0;'>

                        <p style='font-size: 14px; color: #7f8c8d;'>This is an automated notification from AlumniConnect platform.</p>

                        <p style='font-size: 14px; margin-top: 10px;'>Best Regards,<br>
                        <strong style='color: #2c3e50;'>AlumniConnect Team</strong></p>
                    </div>
                ";


                $mail->send();

            } catch (Exception $e) {
                error_log("Email failed for {$student['student_email']}: " . $mail->ErrorInfo);
                // You may optionally skip or collect failed emails
            }
        }

        $_SESSION['message'] = ["success" => true, "final_msg" => "Post created and emails sent successfully"];
        header("Location: alumni_create_post.php");
        exit();

    } else {
        $_SESSION['message'] = ["success" => false, "final_msg" => "Error in creating post"];
        header("Location: alumni_create_post.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Post Form | AlumniConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* Basic Reset & Body Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            display: flex; /* Keep flex for the overall layout */
            justify-content: flex-start; /* Align content to start to allow sidebar to sit left */
            align-items: flex-start; /* Align content to start to keep sidebar at top */
            min-height: 100vh;
            color: #333;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            width: 100%; /* Take full width of body */
            max-width: none; /* No max-width on the container itself */
            background-color: #f0f2f5; /* Background for the overall area */
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
            min-height: 100vh;
        }

        .logo {
            color: white;
            text-align: left;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 0;
            padding-left: 20px;
            box-sizing: border-box;
        }

        .nav-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-links li {
            margin: 0;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #dcdcdc;
            font-size: 16px;
            padding: 10px 20px;
            transition: background-color 0.3s, color 0.3s;
        }

        .nav-links a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 18px;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background-color: #1d2b3a;
            color: #ffffff;
            border-left: 4px solid #4fa3ff;
            padding-left: 16px;
        }

        /* Main Content & Form Styling adjustments */
        .main-content {
            flex: 1; /* Allows main content to take remaining space */
            display: flex; /* Use flexbox to center its child (the form) */
            justify-content: center; /* Center form horizontally */
            align-items: center; /* Center form vertically */
            padding: 20px; /* Padding around the centered form */
            box-sizing: border-box;
            background-color: #f0f2f5; /* Match body background */
            overflow-y: auto; /* Allow scrolling if content is too long */
        }

        form {
            background-color: #ffffff; /* White background for the form itself */
            padding: 30px; /* Padding inside the form */
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); /* Shadow for the form */
            max-width: 500px; /* **This makes the form smaller** */
            width: 100%; /* Ensures it takes 100% of its max-width */
            box-sizing: border-box;
        }

        .form-header {
            margin-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
        }

        .form-header h2 {
            font-size: 1.8em;
            color: #2c3e50;
            margin: 0;
            font-weight: 600;
        }

        .form-fields {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .field-row {
            display: flex;
            flex-direction: column;
        }

        .field-row label {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 5px;
            font-weight: 400;
        }

        .field-row input[type="text"],
        .field-row textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.95em;
            font-family: 'Poppins', sans-serif;
            color: #333;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .field-row input[type="text"]:focus,
        .field-row textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .field-row textarea {
            resize: vertical;
            min-height: 80px;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            align-self: flex-start;
            margin-top: 15px;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                flex-direction: column; /* Stack sidebar and main content vertically */
            }

            .sidebar {
                height: auto;
                width: 100%;
                padding: 16px 0;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                flex-direction: row;
                justify-content: space-around;
                align-items: center;
                flex-wrap: wrap;
            }

            .sidebar .logo {
                display: none;
            }

            .sidebar .nav-links {
                display: flex;
                flex-direction: row;
                width: 100%;
                justify-content: space-around;
                padding: 0 10px;
                margin-top: 0;
            }

            .sidebar .nav-links li {
                flex: 1;
                text-align: center;
            }

            .sidebar .nav-links a {
                flex-direction: column;
                padding: 8px 5px;
                font-size: 12px;
                border-left: none;
                border-bottom: 2px solid transparent;
                border-radius: 5px;
            }
            .sidebar .nav-links a i {
                margin-right: 0;
                margin-bottom: 5px;
                font-size: 16px;
            }

            .sidebar .nav-links a:hover,
            .sidebar .nav-links a.active {
                background-color: #1d2b3a;
                border-left: none;
                border-bottom-color: #4fa3ff;
                padding-left: 5px;
            }

            .main-content {
                padding: 15px; /* Adjust padding for smaller screens */
            }

            form {
                max-width: 100%; /* Form takes full width on small screens */
                padding: 20px; /* Smaller padding for form on mobile */
            }

            .form-header h2 {
                font-size: 1.6em;
            }

            .form-fields {
                gap: 12px;
            }

            .field-row label {
                font-size: 0.85em;
            }

            .field-row input[type="text"],
            .field-row textarea {
                padding: 8px 10px;
                font-size: 0.9em;
            }

            .field-row textarea {
                min-height: 70px;
            }

            button[type="submit"] {
                padding: 10px 15px;
                font-size: 0.95em;
                width: 100%; /* Full width button on mobile */
                align-self: center; /* Center button on mobile */
            }
        }

        /* Adjustments for shorter screens (laptops, some tablets) only for wider layouts */
        @media (max-height: 700px) and (min-width: 769px) {
            .main-content {
                padding: 15px; /* Less padding to fit on shorter screens */
            }
            form {
                max-width: 450px; /* Make form even smaller on shorter wider screens */
                padding: 25px; /* Adjust form padding */
            }
            .field-row textarea {
                min-height: 60px;
            }
        }
    </style>
</head>
<body>
     <?php include './sidebar.php' ?>
    <div class="container"> 
        <div class="main-content">
            <form action="./alumni_create_post.php" method="POST">
                <div class="form-header">
                    <h2>Alumni Post</h2>
                </div>
                <?php if(isset($_SESSION['message'])){ ?>
                    <p id="message" style=" color: white;
                            border: 1px solid;
                            background-color: <?= $_SESSION['message']['success'] ? '#28a745' : '#dc3545' ?>;
                            padding: 8px 10px;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                            margin-bottom: 10px;
                            display: block;
                            transition: all 0.3s ease-in-out;
                            text-align: center;"><?= htmlspecialchars($_SESSION['message']['final_msg'] ?? '') ?></p>
                    <?php } ?>
                    <script>
                        const message = document.getElementById("message");
                        setTimeout(() => {
                           message.style.display = 'none';
                           message.style.backgroundColor = '';
                        },2 * 1000)
                    </script>
                    <?php unset($_SESSION['message']) ?>
                <div class="form-fields">
                    <div class="field-row">
                        <label for="post-title">Title</label>
                        <input type="text" id="post-title" name="title" placeholder="e.g., Software Engineer Opening" required>
                    </div>

                    <div class="field-row">
                        <label for="post-skills">Skills</label>
                        <input type="text" id="post-skills" name="required_skills" placeholder="e.g., Python, UI/UX" required>
                    </div>
                    <div class="field-row">
                        <label for="post-location">Location</label>
                        <input type="text" id="post-location" name="location" placeholder="e.g., San Francisco, Remote" required>
                    </div>
                      <div class="field-row">
                        <label for="post-location">Reference Link</label>
                        <input type="text" id="post-location" name="ref_link" placeholder="e.g., San Francisco, Remote" required>
                    </div>
                    <div class="field-row">
                        <label for="post-desc">Description</label>
                        <textarea id="post-desc" name="description" placeholder="Detailed description." rows="4" required></textarea>
                    </div>
                     <div class="field-row">
                        <label for="post-desc">Type</label>
                       <select name="typeofjob" id="typeofjob">
                            <option value="">SELECT TYPE</option>
                            <option value="Internship">Internship</option>
                            <option value="Job">Job</option>
                            <option value="Part-time">Part-time</option>
                       </select>
                    </div>
                    
                    <div class="field-row">
                        <label for="post-roadmap">Roadmap (Optional)</label>
                        <textarea id="post-roadmap" name="roadmap" placeholder="Outline steps or a plan." rows="2"></textarea>
                    </div>
                    <button type="submit" name="create_post">Create Post</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>