<?php
require '../utills/db_conn.php';
include("./alumni_favicon.php");

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['Enroll_no_alumni'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($conn)) {
    die("Database connection not established.");
}

$alumni_id_edit = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if (!$alumni_id_edit) {
    $_SESSION['message'] = ['success' => false, 'mess' => 'Invalid Request'];
    header('Location: ./edit_alumni_profile.php?edit=' . $alumni_id_edit); // Redirect to a safe page
    exit();
}

// 1. Fetch User Data
$user_data_fetched = [];
$stmt = $conn->prepare("SELECT am.*, ap.* FROM alumni_student_master AS am 
                        LEFT JOIN alumni_profile AS ap ON am.alumni_id = ap.alumni_id 
                        WHERE am.alumni_id = ? AND (am.email = ? OR am.enrollment_no = ?)");
$stmt->bind_param("iss", $alumni_id_edit, $_SESSION['alumni_email'], $_SESSION['Enroll_no_alumni']);
$stmt->execute();
$user_data_fetched = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user_data_fetched) {
    $_SESSION['message'] = ['success' => false, 'mess' => 'Unauthorized or Alumni not found'];
    header('Location: ./edit_alumni_profile.php?edit=' . $alumni_id_edit);
    exit();
}


// calculate the addmission year feature
$start_year = 2000;
$passout_year = $user_data_fetched["passout_year"];
$addmission_year = $passout_year - 3;
$addmission_year_range = range($start_year, $addmission_year);

// 2. Handle POST Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_profile_btn'])) {

    // Sanitize Inputs
    $data = [
        'phone' => filter_input(INPUT_POST, 'phone_no', FILTER_SANITIZE_SPECIAL_CHARS),
        'batch' => filter_input(INPUT_POST, 'admission_year', FILTER_SANITIZE_NUMBER_INT),
        'city'  => filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS),
        'github' => filter_input(INPUT_POST, 'github', FILTER_VALIDATE_URL),
        'linked' => filter_input(INPUT_POST, 'linkedin', FILTER_VALIDATE_URL),
        'comp'  => filter_input(INPUT_POST, 'company', FILTER_SANITIZE_SPECIAL_CHARS),
        'desig' => filter_input(INPUT_POST, 'designation', FILTER_SANITIZE_SPECIAL_CHARS),
        'addr'  => filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS)
    ];


    // Atomic Update using UPSERT (MySQL specific)
    $upsert_sql = "INSERT INTO alumni_profile 
                   (alumni_id, alumni_phone_no, alumni_address, alumni_batch, alumni_company, alumni_designation, alumni_city, alumni_github_link, alumni_linkedin_link) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                   ON DUPLICATE KEY UPDATE 
                   alumni_phone_no=VALUES(alumni_phone_no), alumni_address=VALUES(alumni_address), 
                   alumni_batch=VALUES(alumni_batch), alumni_company=VALUES(alumni_company), 
                   alumni_designation=VALUES(alumni_designation), alumni_city=VALUES(alumni_city), 
                   alumni_github_link=VALUES(alumni_github_link), alumni_linkedin_link=VALUES(alumni_linkedin_link)";

    $stmt = $conn->prepare($upsert_sql);
    $stmt->bind_param("issssssss", $alumni_id_edit, $data['phone'], $data['addr'], $data['batch'], $data['comp'], $data['desig'], $data['city'], $data['github'], $data['linked']);

    if ($stmt->execute()) {
        $_SESSION['message'] = ["success" => true, "mess" => "Profile updated successfully."];
    } else {
        error_log("Database Update Error: " . $stmt->error); // Log error server-side
        $_SESSION['message'] = ["success" => false, "mess" => "An error occurred. Please try again."];
    }

    header("Location: ./edit_alumni_profile.php?edit=" . $alumni_id_edit);
    exit();
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
            background-color: #ffffff;
            color: #2b2f31;
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
            background-color: #ffffff;
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
            color: #2E75B6;
            margin-bottom: 10px;
            text-align: center;
            width: 100%;
            /* Ensure title takes full width to center effectively */
        }

        .profile-card {
            background-color: #f4f8fc;
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
            color: #667079;
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
            border: 1px solid #d6e2ef;
            border-radius: 6px;
            font-size: 0.95em;
            font-family: 'Poppins', sans-serif;
            color: #2b2f31;
            background-color: #ffffff;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2E75B6;
            box-shadow: 0 0 0 2px rgba(46, 117, 182, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            background-color: #2E75B6;
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
            background-color: #1F5A94;
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
            background-color: #0a7d3e;
            border-color: #0a7d3e;
            color: white;
        }

        #message.error {
            background-color: #d92d20;
            border-color: #d92d20;
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
                border-bottom-color: #2E75B6;
                /* Apply blue to bottom border */
                background-color: #1F5A94;
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
                        <p id="message" class="<?= htmlspecialchars($messageClass) ?>">
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
                        <input type="text" id="Enrollment" name="Enrollment" value="<?= htmlspecialchars($user_data_fetched['enrollment_no'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_data_fetched['alumni_name'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data_fetched['email'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="phone_no">Phone Number</label>
                        <input type="tel" id="phone_no" name="phone_no"  pattern="[0-9]{10}" maxlength="10" title="Please enter exactly 10 digits"  							value="<?= htmlspecialchars($user_data_fetched['alumni_phone_no'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select name="department" id="department" disabled>
                            <option value=""><?= htmlspecialchars($user_data_fetched['branch'] ?? '') ?></option>

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admission_year">Admission Year</label>
                        <?php if (!isset($user_data_fetched["alumni_batch"])){ ?>
                        <select name="admission_year" id="admission_year" required>
                                <?php foreach ($addmission_year_range as $year): ?>
                                    <option value="<?= $year  ?>"><?= $year ?></option>
                                <?php endforeach; ?>
                            <?php } else{ ?>
                                <input type="text" value="<?= $user_data_fetched["alumni_batch"] ?>" disabled>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="passout_year">Passout Year</label>
                        <input type="number" id="passout_year" name="passout_year" min="1980" max="2099" value="<?= htmlspecialchars($user_data_fetched['passout_year'] ?? '') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?= htmlspecialchars($user_data_fetched['alumni_city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" cols="5" rows="3"><?= htmlspecialchars($user_data_fetched['alumni_address'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="company">Current Company</label>
                        <input type="text" id="company" name="company" value="<?= htmlspecialchars($user_data_fetched['alumni_company'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="designation">Designation</label>
                        <input type="text" id="designation" name="designation" value="<?= htmlspecialchars($user_data_fetched['alumni_designation'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="github">GitHub Link</label>
                        <input type="url" id="github" name="github" value="<?= htmlspecialchars($user_data_fetched['alumni_github_link'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="linkedin">LinkedIn Link</label>
                        <input type="url" id="linkedin" name="linkedin" value="<?= htmlspecialchars($user_data_fetched['alumni_linkedin_link'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn-submit" name="save_profile_btn">Save Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>