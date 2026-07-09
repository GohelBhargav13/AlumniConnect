<?php
require_once '../utills/db_conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    die("Database connection not established.");
}

// set the dynamic year for the passout year input field
$startYear = 2000;
$currentYear = (int) date('Y');

$passoutYears = range($startYear, $currentYear);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_click_btn'])) {

    $alumni_email = trim($_POST['email'] ?? '');
    $alumni_passoutYear = (int) $_POST['passOutYear'] ?? 0;
    $alumni_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirmPassword'] ?? '';
    $is_registered = 1; // Set to 1 for registered alumni

    // before insert check for the some conditions on password
    $isNumeric = preg_match('/[0-9]/', $alumni_password);
    $isUpperCase = preg_match('/[A-Z]/', $alumni_password);
    $isSpecialChar = preg_match('/[^a-zA-z0-9]/', $alumni_password);
    $isLength = strlen($alumni_password) >= 8;

    // check for passwords
    if (!$isLength) {
        header("Location: ./alumni_register.php?error=" . urlencode("password must be 8 character long"));
        exit();
    }

    // check for the number, uppercase, specialsymbol
    if (!$isNumeric || !$isUpperCase || !$isSpecialChar) {
        header("Location: ./alumni_register.php?error=" . urlencode("password contains uppecase, numbers and special characters"));
        exit();
    }

    try {
        // check if enrollment already exists
        $exist_user = "SELECT * FROM alumni_student_master WHERE is_registered = ? AND email = ? AND passout_year = ?";
        $exist_user_stmt = $conn->prepare($exist_user);
        $exist_user_stmt->bind_param('isi', $is_registered, $alumni_email, $alumni_passoutYear);
        $exist_user_stmt->execute();
        $exist_user_res = $exist_user_stmt->get_result();

        if ($exist_user_res->num_rows > 0) {
            $message = "You are already registered. Please log in.";
            header('Location: alumni_register.php?info=' . urlencode($message));
            exit();
        } else {
            // password check
            if ($alumni_password !== $confirm_password) {
                $message = "Passwords do not match.";
                header('Location: alumni_register.php?error=' . urlencode($message));
                exit();
            }

            // hash password
            $hashed_password = password_hash($alumni_password, PASSWORD_DEFAULT);

            // update the is_registered flag in alumni_student_master and password what if user is not found in alumni_student_master table
            $user_exists = "SELECT * FROM alumni_student_master WHERE email = ? AND passout_year = ?";
            $user_exists_stmt = $conn->prepare($user_exists);
            $user_exists_stmt->bind_param('si', $alumni_email, $alumni_passoutYear);
            $user_exists_stmt->execute();
            $user_exists_res = $user_exists_stmt->get_result();

            if ($user_exists_res->num_rows === 0) {
                $message = "User not found.";
                header('Location: alumni_register.php?error=' . urlencode($message));
                exit();
            } else {
                $register_new_alumni = "UPDATE alumni_student_master SET is_registered = ?, password_hash = ? WHERE email = ? AND passout_year = ?";
                $register_new_stmt = $conn->prepare($register_new_alumni);
                $register_new_stmt->bind_param(
                    'issi',
                    $is_registered,
                    $hashed_password,
                    $alumni_email,
                    $alumni_passoutYear
                );
                if ($register_new_stmt->execute()) {
                    $message = "Registration successful. You can now log in.";
                    header("Location: ../login.php?success=" . urlencode($message));
                    exit();
                } else {
                    $message = "Registration failed. Please try again.";
                    header("Location: alumni_register.php?error=" . urlencode($message));
                    exit();
                }
            }
        }
    } catch (Exception $e) {
        $message = "There is some issue in registration";
        header("Location: alumni_register.php?error=" . urlencode($message));
        exit();
    } finally {
        $exist_user_stmt->close();
        $register_new_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Register | AlumniConnect</title>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS for the whole page -->
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap");

        :root {
            --green: #007bff;
            --light-green: #e8f8f2;
            --text-dark: #333;
            --text-muted: #777;
            --border: #ccc;
            --input-bg: #fff;
        }

        body {
            font-family: "Poppins", sans-serif;
            background-color: white;
            margin: 0;
            padding: 50px 20px;
        }

        .main {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 600px;
            justify-content: center;
            background-color: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
            color: var(--green);
            font-size: 1.6rem;
            font-weight: 600;
        }

        h2 {
            text-align: center;
            color: var(--text-dark);
            font-size: 1.2rem;
            background-color: var(--light-green);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 30px;
            font-weight: 500;
        }

        form .form-group {
            margin-bottom: 18px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        form input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            background-color: var(--input-bg);
            transition: border 0.3s ease;
        }

        form input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 2px rgba(0, 184, 107, 0.15);
        }

        .half-width-group {
            display: flex;
            gap: 35px;
        }

        .half-width-field {
            flex: 1;
        }

        .btn-primary {
            background-color: var(--green);
            color: #fff;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background-color: #00995a;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .login-link a {
            color: var(--green);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .half-width-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="main">
        <div class="container" style="padding: 20px;">
            <h2>Alumni Registration</h2>
            <?php if (isset($_GET['error']) || isset($_GET['success']) || isset($_GET['info'])): ?>
                <p id="msg" class="msg" style="color: <?= (isset($_GET['error']) ? 'red' : (isset($_GET['success']) ? 'green' : 'blue')) ?>"><?= (isset($_GET['error']) ? htmlspecialchars($_GET['error']) : (isset($_GET['success']) ? htmlspecialchars($_GET['success']) : htmlspecialchars($_GET["info"]))) ?></p>
            <?php endif ?>
            <script>
                const msg = document.getElementById("msg");
                setTimeout(() => {
                    msg.style.display = "none";
                }, 2000)
            </script>
            <form action="alumni_register.php" method="POST" id="registerForm">
                <div class="form-group" style="width: 95%;">
                    <label for="email">Email.</label>
                    <input type="email" name="email" id="email" style="width: 100%; padding: 12px 16px; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: black" required>
                </div>
                <div class="form-group" style="width: 95%;">
                    <label for="passOutYear">Pass Out Year</label>
                    <select name="passOutYear" id="passOutYear" style="width: 100%; padding: 12px 16px; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: black;" required>
                        <?php foreach ($passoutYears as $year): ?>
                            <option value="<?= $year  ?>"><?= $year ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="width: 95%;">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" style="width: 100%; padding: 12px 16px; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: black" required>
                </div>
                <div class="form-group" style="width: 95%;">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" style="width: 100%; padding: 12px 16px; border: 1px solid #4B5563; border-radius: 8px; outline: none; transition: all 0.2s ease-in-out; color: black;" required>
                </div>

                <button class="btn-primary" type="submit" name="register_click_btn">Register Now</button>
                <p style="text-align: center; margin-top: 10px;">Do you want to <a href="../index.php">Go Back</a>?</p>
            </form>

            <div class="login-link">
                Already have an account? <a href="../login.php">Sign in here</a>
            </div>
        </div>
    </div>
    <script src="./script.js"></script>
</body>

</html>