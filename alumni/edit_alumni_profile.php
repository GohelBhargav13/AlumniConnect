<?php
require '../utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['Enroll_no_alumni'])) {
    header('Location: ../login.php');
    exit();
}
$user_data_fetched = [];
$loggedIn_user = $_SESSION['Enroll_no_alumni'];

try {
    if (!isset($_GET['edit'])) {
        $_SESSION['message'] = ['success' => false, 'err_message' => 'Alumni not found'];
        exit();
    }

    $alumni_id_edit = (int) $_GET['edit'];
    $fetch_user_data = "SELECT * FROM alumnimaster WHERE alumni_id = ?";
    $fetch_user_stmt = $conn->prepare($fetch_user_data);
    $fetch_user_stmt->bind_param('i', $alumni_id_edit);
    $fetch_user_stmt->execute();
    $user_data = $fetch_user_stmt->get_result();
    if ($user_data->num_rows === 1) {
        $user_data_fetched = $user_data->fetch_assoc();
    }
    $fetch_user_stmt->close();
} catch (Exception $th) {
    echo "<script>alert('Data not found of the user: " . htmlspecialchars($th->getMessage()) . "')</script>"; // Use getMessage()
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_profile_btn'])) {

    $alumni_name = $_POST['name'] ?? '';
    $alumni_email = $_POST['email'] ?? '';
    $alumni_phoneNo = $_POST['phone_no'] ?? 0;
    $alumni_dep = $_POST['department'] ?? '';
    $alumni_add_year = $_POST['admission_year'] ?? 0;
    $alumni_pass_year = $_POST['passout_year'] ?? 0;
    $alumni_college = $_POST['college'] ?? '';
    $alumni_city = $_POST['city'] ?? '';
    $alumni_github_link = $_POST['github'] ?? '';
    $alumni_linkedIn_link = $_POST['linkedin'] ?? '';
    $alumni_bio = $_POST['bio'] ?? '';

    if (!empty($alumni_name) || !empty($alumni_email) || !empty($alumni_phoneNo) || !empty($alumni_dep) || !empty($alumni_add_year) || !empty($alumni_pass_year) || !empty($alumni_college) || !empty($alumni_city)) { // Use !empty for better validation
        try {
            $sql_for_update = "UPDATE alumnimaster SET alumni_name=?, alumni_email=?, alumni_phone_no=?, alumni_add_year=?, alumni_pass_year=?, alumni_bio=?, alumni_githublink=?, alumni_linkedIn=?, alumni_city=?, alumni_department=?, alumni_college=? WHERE alumni_id=?";
            $sql_for_stmt = $conn->prepare($sql_for_update);
            $sql_for_stmt->bind_param(
                "ssiiissssssi",
                $alumni_name,
                $alumni_email,
                $alumni_phoneNo,
                $alumni_add_year,
                $alumni_pass_year,
                $alumni_bio,
                $alumni_github_link,
                $alumni_linkedIn_link,
                $alumni_city,
                $alumni_dep,
                $alumni_college,
                $alumni_id_edit
            );
            $sql_for_stmt->execute();

            if ($sql_for_stmt->affected_rows === 1) {
                $_SESSION['message'] = ["success" => true, "mess" => htmlspecialchars($alumni_name) . " Details Updated"];
            } else {
                $_SESSION['message'] = ["success" => false, "mess" => "Error in updating data or no changes made."]; // More descriptive message
            }
            // Redirect after successful update or no changes to prevent form resubmission
            header("Location: ./edit_alumni_profile.php?edit=" . $alumni_id_edit);
            exit();
        } catch (Exception $th) {
            $_SESSION['message'] = ["success" => false, "mess" => "Database error: " . htmlspecialchars($th->getMessage())];
            header("Location: ./edit_alumni_profile.php?edit=" . $alumni_id_edit);
            exit();
        }
    } else {
        $_SESSION['message'] = ["success" => false, "mess" => "Please fill all required fields"];
        header("Location: ./edit_alumni_profile.php?edit=" . $alumni_id_edit); // Redirect back to the form with message
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Edit Profile | AlumniConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="../style/edit_alumni_profile.css"> -->
    <!-- <link rel="stylesheet" href="../style/edit_alumni_profile.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* Basic Reset & Body Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            box-sizing: border-box;
            display: flex;
            /* Make body a flex container to house the dashboard */
            min-height: 100vh;
            /* Ensure body takes full viewport height */
        }

        /* ------------------------------------------- */
        /* New Sidebar & Layout Styles */
        /* ------------------------------------------- */

        .dashboard-container {
            display: flex;
            /* Main container for sidebar and content */
            width: 100%;
            min-height: 100vh;
        }

        .content-area {
            flex: 1;
            /* Takes up remaining space */
            padding: 40px;
            /* Padding around the content */
            background-color: #f0f2f5;
            /* Background for the main content area */
            overflow-y: auto;
            /* Enable scrolling for content if it overflows */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Center content horizontally within the content area */
            /* Add min-height to ensure it acts as a scrollable container */
            min-height: 100vh;
            /* Ensures it takes full height to allow scrolling within */
        }

        /* ------------------------------------------- */
        /* Existing Form Styles (Adjusted for new layout) */
        /* ------------------------------------------- */

        .dashboard-title {
            font-size: 2em;
            color: #2c3e50;
            margin-bottom: 10px;
            text-align: center;
            width: 100%;
            /* Ensure title takes full width to center effectively */
        }

        .profile-card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            /* Limits the width of the form container */
            width: 100%;
            /* Ensures it fills max-width available */
            box-sizing: border-box;
            margin-bottom: 30px;
            /* --- Key changes for scrollbar and sizing --- */
            max-height: calc(100vh - 120px);
            /* Adjust this value as needed. It subtracts space for header/footer/margins. */
            overflow-y: auto;
            /* Adds vertical scrollbar if content exceeds max-height */
            /* --- End key changes --- */
        }

        .profile-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 0.95em;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="number"],
        .form-group input[type="url"],
        .form-group select,
        .form-group textarea {
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

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            background-color: #3498db;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: block;
            width: auto;
            margin: 20px auto 0 auto;
        }

        .btn-submit:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Message styling */
        #message {
            border: 1px solid;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
            display: block;
            transition: all 0.3s ease-in-out;
            text-align: center;
        }

        #message.success {
            background-color: #28a745;
            /* Darker green */
            border-color: #218838;
            color: white;
        }

        #message.error {
            background-color: #dc3545;
            /* Darker red */
            border-color: #c82333;
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {

            /* Adjust breakpoint for sidebar visibility */
            .sidebar {
                width: 200px;
                /* Slightly smaller sidebar on medium screens */
                padding: 15px 0;
            }

            .sidebar-header h2 {
                font-size: 1.6em;
            }

            .sidebar-menu a {
                padding: 10px 15px;
                font-size: 0.95em;
            }

            .content-area {
                padding: 30px 20px;
            }

            .dashboard-title {
                font-size: 1.8em;
                margin-bottom: 15px;
            }

            .profile-card {
                max-height: calc(100vh - 100px);
                /* Adjust max-height for medium screens */
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
                /* Stack sidebar and content vertically on small screens */
            }

            .sidebar {
                width: 100%;
                /* Sidebar takes full width */
                height: auto;
                /* Height adjusts to content */
                position: relative;
                /* No longer sticky */
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                padding: 15px 0;
                text-align: center;
            }

            .sidebar-header {
                margin-bottom: 15px;
            }

            .sidebar-menu {
                display: flex;
                /* Arrange menu items horizontally */
                flex-wrap: wrap;
                /* Allow items to wrap */
                justify-content: center;
                /* Center menu items */
                padding: 0 10px;
            }

            .sidebar-menu li {
                margin: 5px 10px;
                /* Add some space between horizontal items */
            }

            .sidebar-menu a {
                padding: 8px 12px;
                font-size: 0.9em;
                border-left: none;
                /* Remove left border */
                border-bottom: 3px solid transparent;
                /* Use bottom border for active indicator */
            }

            .sidebar-menu a:hover,
            .sidebar-menu a.active {
                border-left-color: transparent;
                /* Ensure no left border */
                border-bottom-color: #3498db;
                /* Apply blue to bottom border */
                background-color: #34495e;
            }

            .content-area {
                padding: 20px 15px;
                min-height: auto;
                /* Remove fixed min-height on small screens as sidebar is not fixed */
            }



            .profile-card {
                padding: 20px;
                max-width: 100%;
                max-height: calc(100vh - 80px);
                /* Adjust max-height for small screens */
            }

            .btn-submit {
                width: 100%;
            }
        }

        @media (max-height: 700px) and (min-width: 769px) {

            /* Adjust for shorter screens (laptops, some tablets) but not mobile */
            .sidebar {
                padding: 10px 0;
            }

            .sidebar-header {
                margin-bottom: 20px;
            }

            .sidebar-menu a {
                padding: 10px 20px;
            }

            .content-area {
                padding: 30px;
            }

            .dashboard-title {
                font-size: 1.8em;
                margin-bottom: 20px;
            }

            .profile-card {
                padding: 25px;
                max-height: calc(100vh - 100px);
                /* Adjust max-height for shorter screens */
            }

            .form-group label {
                font-size: 0.9em;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 8px 10px;
                font-size: 0.9em;
            }

            .form-group textarea {
                min-height: 60px;
            }

            .btn-submit {
                padding: 10px 20px;
                font-size: 0.95em;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <?php include './sidebar.php' ?>

        <div class="content-area">
            <h1 class="dashboard-title">Edit Your Profile</h1>
            <div class="profile-card">
                <form class="profile-form" action="edit_alumni_profile.php?edit=<?= htmlspecialchars($alumni_id_edit) ?>" method="POST">
                    <?php if (isset($_SESSION['message'])): ?>
                        <?php
                        $messageClass = $_SESSION['message']['success'] ? 'success' : 'error';
                        $messageText = $_SESSION['message']['mess'] ?? "";
                        ?>
                        <p id="message" class="<?= htmlspecialchars($messageClass) ?>" style="
                            color: white;
                            border: 1px solid;
                            background-color: <?= $_SESSION['message']['success'] ? '#28a745' : '#dc3545' ?>;
                            padding: 8px 10px;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                            margin-bottom: 10px;
                            display: block;
                            transition: all 0.3s ease-in-out;
                            text-align: center;
                        ">
                            <?= htmlspecialchars($messageText) ?>
                        </p>

                        <script>
                            const messageElement = document.getElementById('message');
                            setTimeout(() => {
                                if (messageElement) {
                                    messageElement.style.display = 'none';
                                }
                            }, 2 * 1000);
                        </script>
                    <?php endif; ?>
                    <?php unset($_SESSION['message']); // Clear message after display 
                    ?>

                    <div class="form-group">
                        <label for="Enrollment">Enrollment No</label>
                        <input type="text" id="Enrollment" name="Enrollment" value="<?= htmlspecialchars($user_data_fetched['Enrollment_No'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_data_fetched['alumni_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data_fetched['alumni_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone_no">Phone Number</label>
                        <input type="tel" id="phone_no" name="phone_no" value="<?= htmlspecialchars($user_data_fetched['alumni_phone_no'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select name="department" id="department">
                            <option value="Computer Engineering" <?= (isset($user_data_fetched['alumni_department']) && $user_data_fetched['alumni_department'] == "Computer Engineering") ? "selected" : "" ?>>Computer Engineering</option>
                            <option value="Information Technology" <?= (isset($user_data_fetched['alumni_department']) && $user_data_fetched['alumni_department'] == "Information Technology") ? "selected" : "" ?>>Information Technology</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admission_year">Admission Year</label>
                        <input type="number" id="admission_year" name="admission_year" min="1980" max="2099" value="<?= htmlspecialchars($user_data_fetched['alumni_add_year'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="passout_year">Passout Year</label>
                        <input type="number" id="passout_year" name="passout_year" min="1980" max="2099" value="<?= htmlspecialchars($user_data_fetched['alumni_pass_year'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="college">College Name</label>
                        <select name="college" id="college">
                            <option value="GEC MODASA" <?= (isset($user_data_fetched['alumni_college']) && $user_data_fetched['alumni_college'] == "GEC MODASA") ? "selected" : "" ?>>GEC MODASA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?= htmlspecialchars($user_data_fetched['alumni_city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="github">GitHub Link</label>
                        <input type="url" id="github" name="github" value="<?= htmlspecialchars($user_data_fetched['alumni_githublink'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="linkedin">LinkedIn Link</label>
                        <input type="url" id="linkedin" name="linkedin" value="<?= htmlspecialchars($user_data_fetched['alumni_linkedIn'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Write a short bio..."><?= htmlspecialchars($user_data_fetched['alumni_bio'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn-submit" name="save_profile_btn">Save Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>