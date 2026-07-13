<?php
require './utills/db_conn.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['click_btn'])) {

    $email = filter_var($_POST["email"] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"] ?? '';

    // Prepared Statement
    $stmt = $conn->prepare("SELECT alumni_id, enrollment_No, password_hash FROM alumni_student_master WHERE email = ? AND is_registered = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {

            // Regenerate ID to prevent Session Fixation
            session_regenerate_id(true);
            $_SESSION['alumni_id'] = $user['alumni_id'];
            $_SESSION['Enroll_no_alumni'] = $user['enrollment_No'];
            $_SESSION['email'] = $email;
            $_SESSION["alumni_passout_year"] = $user["passout_year"];
            header("Location: ./alumni/landing.php");
            exit();
        }
    }

    header("Location: ./login.php?error=" . urlencode("Invalid email or password."));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="icon" href="./assets/gec_favicon.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --navy: #2E75B6;
            --navy-dark: #255E92;
            --teal: #2E75B6;
            --teal-dark: #255E92;
            --text-color: #2b2f31;
            --light-gray: #f6f7f5;
            --border-color: #e0e3df;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            overflow: hidden;
            padding: 50px 20px;
        }

        .main {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 600px;
            height: 95vh;
            border-radius: 20px;
            overflow: hidden;
        }

        .background-section {
            flex: 1.2;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
            width: 50px;
            height: 500px;
            margin-left: 30px;
            margin-top: 40px;
        }

        .login-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 20px;
            box-sizing: border-box;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .login-card {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
            text-align: center;
            z-index: 1;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo span {
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--navy);
        }

        h2 {
            font-size: 1.6rem;
            color: var(--text-color);
            margin-bottom: 25px;
            font-weight: 500;
        }

        .form-group {
            text-align: left;
            position: relative;
            padding: 8px 8px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: calc(100% - 20px);
            padding: 12px 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            color: var(--text-color);
            outline: none;
            transition: border-color 0.2s;
            background-color: #fff;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--teal);
        }

        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg fill='%23666' height='16' viewBox='0 0 24 24' width='16' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            cursor: pointer;
        }

        .password-group .password-toggle {
            position: absolute;
            right: 10px;
            top: 60%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 8px;
            accent-color: var(--teal);
        }

        .forgot-password {
            color: var(--teal-dark);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background-color: var(--teal);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-bottom: 20px;
        }

        .btn-primary:hover {
            background-color: var(--teal-dark);
        }

        .social-login-separator {
            position: relative;
            margin: 25px 0;
            text-align: center;
            color: #999;
            font-size: 0.9rem;
        }

        .social-login-separator span {
            background-color: #fff;
            padding: 0 10px;
            position: relative;
            z-index: 1;
        }

        .social-login-separator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px solid var(--border-color);
            z-index: 0;
        }

        .btn-google {
            width: 100%;
            padding: 12px;
            background-color: #fff;
            color: #666;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .btn-google img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .btn-google:hover {
            background-color: var(--light-gray);
            border-color: #c0c0c0;
        }

        .signup-link {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #666;
        }

        .signup-link a {
            color: var(--teal-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
                height: auto;
                max-width: 500px;
            }

            .background-section {
                height: 200px;
                border-radius: 20px 20px 0 0;
            }

            .login-section {
                padding: 20px;
                border-radius: 0 0 20px 20px;
            }

            .login-card {
                padding: 30px;
                box-shadow: none;
            }
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 20px;
            }

            h2 {
                font-size: 1.4rem;
            }

            .form-group input,
            .form-group select {
                padding: 10px 8px;
            }

            .btn-primary,
            .btn-google {
                padding: 10px;
                font-size: 1rem;
            }

            .freebie-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="main">
        <div class="container">
            <div class="login-section">
                <div class="login-card">
                    <div class="logo">
                        <span>AlumniConnect</span>
                    </div>
                    <h2>Nice to see you again</h2>

                    <form action="./login.php" method="POST">
                        <div class="form-group">
                            <?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
                                <p id="message" class="msg" style="
                                        color: <?= isset($_GET['error']) ? 'red' : 'green' ?>;
                                        background-color: #fdecea;
                                        border: 1px solid #f5c6cb;
                                        padding: 8px 10px;
                                        border-radius: 6px;
                                        font-size: 14px;
                                        font-weight: 500;
                                        margin-bottom: 10px;
                                        display: block;
                                        transition: all 0.3s ease-in-out;
                                    ">
                                    <?= isset($_GET['error']) ? htmlspecialchars($_GET['error']) : (isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '') ?>
                                </p>
                            <?php endif; ?>
                            <script>
                                setTimeout(() => {
                                    let mess = document.getElementById('message');
                                    if (mess) mess.style.display = 'none';
                                }, 2 * 1000);
                            </script>
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter Email" maxlength="50">
                        </div>
                        <div class="form-group password-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter password" minlength="8">
                            <p id="strength_password"></p>
                        </div>

                        <div class="options">
                            <div class="remember-me">
                                <input type="checkbox" id="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="./alumni/alumni_forgot_password.php" class="forgot-password">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn-primary" id="click_btn" name="click_btn" style="margin-bottom: 13px;">Sign In</button>
                        <p style="text-align: center;">Do you want to <a href="./index.php">Go Back</a>?</p>
                    </form>

                    <div class="signup-link">
                        <span>Don't have an account? <a href="./alumni/alumni_register.php">Sign up now</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const message = document.getElementById("message");
        const email = document.getElementById("email");
        const password = document.getElementById("password");

        document.getElementById("click_btn").addEventListener("click", (e) => {

            function messageDisplay(text) {
                message.innerText = `${text} *`;
                message.style.color = "red";
                message.style.backgroundColor = "#fdecea";
                message.style.border = "1px solid #f5c6cb";
                message.style.padding = "8px 10px";
                message.style.borderradius = "6px";
                message.style.fontsize = "14px";
                message.style.fontweight = "500";
                message.style.marginbottom = "10px";
                message.style.display = "block";
                message.style.transition = "all 0.3s ease-in-out";

                setTimeout(() => {
                    message.style.display = 'none';
                    message.innerText = '';
                }, 2 * 1000);
            }

            let email_value = email.value;
            let password_value = password.value;

            if (!email_value || !password_value) {
                e.preventDefault();
                messageDisplay("All fields are required");
                return;
            }

            if (email_value.length > 50) {
                e.preventDefault();
                messageDisplay("Email must be at most 50 characters");
                return;
            }

        })
    </script>
</body>

</html>