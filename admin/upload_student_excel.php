<?php
include "../utills/db_conn.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    die("Database connection not established.");
}

if (!isset($_SESSION['admin_id'])) {
    die("Access denied. Please login as admin.");
}

// Correct absolute path to the composer autoloader from the /admin/ folder
require __DIR__ . '/../vendor/autoload.php';

// Explicitly import the specific Xlsx Reader class instead of IOFactory
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$message = $_GET['message'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {

    $file = $_FILES['excel_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("File upload failed. Please try again.");
    }

    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($fileExt !== 'xlsx') {
        die("Invalid file type. Please upload a .xlsx file only.");
    }

    try {
        // Instantiate the Xlsx reader class directly (No IOFactory)
        $reader = new Xlsx();

        // Read only data values, ignoring cell styling to maximize server performance
        $reader->setReadDataOnly(true);

        // Load the temporary uploaded file
        $spreadsheet = $reader->load($file['tmp_name']); // php store the temporary file in the $_FILES array, so we can use it directly
        $sheet = $spreadsheet->getActiveSheet(); // get the active sperad sheet from the uploaded file
        $rows = $sheet->toArray(); // convert the sheet data to an array for easier processing like ['Rahul', 'rahul@example.com', '12345', '2020', 'CSE']

    } catch (\Exception $e) {
        die("Error loading Excel file: " . $e->getMessage());
    }

    $sql = "INSERT INTO alumni_student_master
                    (alumni_name,email, enrollment_no, passout_year, branch)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    alumni_name = VALUES(alumni_name),
                    email = VALUES(email),
                    enrollment_no = VALUES(enrollment_no),
                    passout_year = VALUES(passout_year),
                    branch = VALUES(branch)";

    $stmt = mysqli_prepare($conn, $sql);

    // "sssis" maps variables to column types: s=string, i=integer
    $alumniName = $email = $enrollmentNo = $passoutYear = $branch = "";
    mysqli_stmt_bind_param($stmt, "sssis", $alumniName, $email, $enrollmentNo, $passoutYear, $branch);

    $insertedCount = 0;
    $skippedCount = 0;

    // start with $i = 1 to skip the header row (index 0)
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];

        $alumniName   = trim($row[0] ?? '');
        $email        = trim($row[1] ?? '');
        $enrollmentNo = trim($row[2] ?? '');
        $passoutYear  = trim($row[3] ?? '');
        $branch       = trim($row[4] ?? '');

        if (empty($alumniName) || empty($email) || empty($enrollmentNo)) {
            $errors[] = "Row " . ($i + 1) . ": missing required field — skipped.";
            $skippedCount++;
            continue;
        }

        // email validation using PHP's built-in filter
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row " . ($i + 1) . ": invalid email — skipped.";
            $skippedCount++;
            continue;
        }

        // Execute using the updated iteration values
        if (mysqli_stmt_execute($stmt)) {
            $insertedCount++;
        } else {
            $errors[] = "Row " . ($i + 1) . ": DB error — " . mysqli_stmt_error($stmt);
            $skippedCount++;
        }
    }

    mysqli_stmt_close($stmt);
    $message = "$insertedCount inserted/updated, $skippedCount skipped. "
        . "(" . $insertedCount . " separate queries sent to the database)";
    header("Location: ./upload_student_excel.php?success=" . urlencode($message));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload Passout Student List (mysqli version)</title>
</head>

<body style="margin: 5px; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: white;">
    <!-- Main container for the entire page -->
    <div style="display: flex; height: 100vh; overflow: hidden; border: 1px solid #30363d; border-radius: 10px; margin: 20px;">

        <!-- Sidebar Navigation -->
        <?php include "./sidebar.php" ?>
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117;">

            <h2 style="text-align:center; color:#f0f6fc; margin-bottom:15px;">
                Upload Passout Student Excel Sheet
            </h2>

            <p style="text-align:center; color:lightcyan; margin-bottom:25px;">
                Expected columns in order:
                <strong>Name, Email, Enrollment No, Passout Year, Branch</strong>
            </p>

            <?php if (isset($_GET["success"]) || isset($_GET["error"])): ?>

                <p style="
                    padding:12px;
                    border-radius:6px;
                    text-align:center;
                    font-weight:bold;
                    margin-bottom:20px;
                    color:<?= isset($_GET['success']) ? '#3fb950' : '#ff7b72'; ?>;
                    background:<?= isset($_GET['success']) ? 'rgba(35,134,54,0.15)' : 'rgba(248,81,73,0.10)'; ?>;
                    border:1px solid <?= isset($_GET['success']) ? '#238636' : '#f85149'; ?>;
                ">
                    <?= htmlspecialchars($_GET['success'] ?? $_GET['error']); ?>
                </p>

            <?php endif; ?>

            <?php if (!empty($errors)): ?>

                <div style="
                    margin-bottom:20px;
                    padding:15px;
                    background:rgba(248,81,73,0.10);
                    border:1px solid #f85149;
                    border-radius:8px;
                    max-height:220px;
                    overflow-y:auto;
                ">

                    <h3 style="margin-top:0; color:#ff7b72;">
                        Processing Logs / Issues
                    </h3>

                    <ul style="padding-left:20px; margin:0; color:#ff7b72;">

                        <?php foreach ($errors as $error): ?>

                            <li style="margin-bottom:8px;">
                                <?= htmlspecialchars($error); ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">

                <div style="margin-bottom:20px;">

                    <label for="excel_file" style="display:block; margin-bottom:8px; color:#f0f6fc; font-weight:bold;">
                        Select Excel File (.xlsx)
                    </label>

                    <input
                        type="file"
                        name="excel_file"
                        id="excel_file"
                        accept=".xlsx"
                        required
                        style="
                            width:100%;
                            padding:10px;
                            background:#0d1117;
                            color:white;
                            border:1px solid #30363d;
                            border-radius:6px;
                            box-sizing:border-box;
                        ">

                </div>

                <button
                    type="submit"
                    style="
                        width:100%;
                        padding:12px;
                        background:#238636;
                        color:white;
                        border:none;
                        border-radius:6px;
                        font-size:16px;
                        font-weight:bold;
                        cursor:pointer;
                    ">
                    Upload & Process
                </button>

            </form>

        </div>
    </div>
</body>

</html>