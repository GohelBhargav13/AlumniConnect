<?php
require "../utills/db_conn.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: ./admin_login.php");
    exit();
}

//fetch the student data for the new request
$select_new_req = $conn->query("SELECT student_id,student_name,Enrollment_no,student_add_year,student_pass_year,ID_Card,student_department FROM studentmaster WHERE req_status = 'pending'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['act_btn'])) {
        $student_id = $_POST['student_id'] ?? " ";

        //UPDATE THE STATUS FROM "pending" TO "accepted"
        $update_status = "UPDATE studentmaster SET req_status = 'accepted' WHERE student_id = ?";
        $update_status_stmt = $conn->prepare($update_status);
        $update_status_stmt->bind_param("i", $student_id);

        if ($update_status_stmt->execute() && $update_status_stmt->affected_rows === 1) {
            $_SESSION['message'] = ["success" => true, "final_msg" => "Request Accepted", "final_color" => "green"];
        } else {
            $_SESSION['message'] = ["success" => false, "final_msg" => "Internal Error to Accepting request", "final_color" => "red"];
        }
    }
    if (isset($_POST['rej_btn'])) {
        $student_id = $_POST['student_id'] ?? " ";

        //fetch data 
        $get_file = "SELECT ID_Card FROM studentmaster WHERE student_id = ?";
        $get_file_stmt = $conn->prepare($get_file);
        $get_file_stmt->bind_param("i", $student_id);
        $get_file_stmt->execute();
        $get_file_result = $get_file_stmt->get_result();

        if ($get_file_result && $get_file_result->num_rows === 1) {
            $file_row = $get_file_result->fetch_assoc();
            $id_card_file = $file_row['ID_Card'];


            $file_path = "../uploads/idcards/student/" . $id_card_file;

            //  Delete the student record
            $update_status = "DELETE FROM studentmaster WHERE student_id = ?";
            $update_status_stmt = $conn->prepare($update_status);
            $update_status_stmt->bind_param("i", $student_id);

            if ($update_status_stmt->execute() && $update_status_stmt->affected_rows === 1) {
                //  If DB delete success, also remove file
                if (file_exists($file_path)) {
                    unlink($file_path); // delete file
                }
                $_SESSION['message'] = [
                    "success" => true,
                    "final_msg" => "Request Rejected & ID Card Deleted",
                    "final_color" => "red"
                ];
            } else {
                $_SESSION['message'] = [
                    "success" => false,
                    "final_msg" => "Internal Error while rejecting request",
                    "final_color" => "red"
                ];
            }
        } else {
            $_SESSION['message'] = [
                "success" => false,
                "final_msg" => "Student record not found",
                "final_color" => "red"
            ];
        }
    }


    $update_status_stmt->close();
    header("Location:student_new_req.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student New Requests | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Responsive design rules and modal styles that cannot be moved inline */
        @media (max-width: 992px) {
            body {
                display: block;
            }

            .dashboard-container {
                flex-direction: column;
                height: auto;
            }

            .sidebar {
                width: 100%;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
                padding: 1rem;
                border-right: none;
                border-bottom: 1px solid #30363d;
            }

            .main-content {
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {

            .request-table,
            .request-table thead,
            .request-table tbody,
            .request-table th,
            .request-table td,
            .request-table tr {
                display: block;
            }

            .request-table thead tr {
                position: absolute;
                top: -9999px;
                center: -9999px;
            }

            .request-table tr {
                border: 1px solid #30363d;
                margin-bottom: 1rem;
                border-radius: 8px;
            }

            .request-table td {
                border: none;
                position: relative;
                padding-center: 50%;
                text-align: right;
            }

            .request-table td::before {
                content: attr(data-label);
                position: absolute;
                center: 0;
                width: 45%;
                padding-center: 15px;
                font-weight: bold;
                text-align: center;
                color: #8b949e;
            }
        }

        /* Modal specific styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1000;
            center: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            position: relative;
            background-color: #161b22;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #30363d;
            max-width: 80%;
            max-height: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .modal-content img {
            max-width: 100%;
            max-height: 100%;
            height: auto;
            display: block;
            border-radius: 4px;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 25px;
            color: #aaa;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: #f0f0f0;
            text-decoration: none;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: #c9d1d9; display: flex; height: 100vh;">
    <div class="dashboard-container" style="display: flex; width: 100%; height: 100%;">
        <!-- PHP include for sidebar -->
        <?php include "./sidebar.php" ?>

        <!-- Main Content -->
        <main class="main-content" style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117; overflow-y: auto;">
            <header class="header" style="text-align: center; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">
                <h1 style="margin: 0; font-weight: 600; color: #f0f0f0; font-size: 24px;">New Student Requests</h1>
            </header>
            <div class="request-card" style="background-color: #161b22; border-radius: 8px; padding: 20px; border: 1px solid #30363d; margin: 0 20px;">
                <?php if (isset($_SESSION['message'])): ?>
                    <?php
                    $msgText = $_SESSION['message']['final_msg'] ?? '';
                    $msgColor = $_SESSION['message']['final_color'] ?? 'white';
                    ?>
                    <p id="message" class="msg" style="text-align: center; font-weight: bold; color: <?= htmlspecialchars($msgColor) ?>;">
                        <?= htmlspecialchars($msgText) ?>
                    </p>
                    <script>
                        const mess = document.getElementById('message');
                        setTimeout(() => {
                            mess.style.display = 'none';
                        }, 1500);
                    </script>
                    <?php unset($_SESSION['message']) ?>
                <?php endif ?>


                <table class="request-table" style="width: 100%; border-collapse: collapse; margin-top: 1.5rem;">
                    <thead>
                        <tr>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: center; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Student Name</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: center; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Enrollment</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: center; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Department</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: center; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Admission Year</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: center; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Passout Year</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: center; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">I-Card</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: center; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Status</th>
                        </tr>
                    </thead>
                    <tbody style="text-align: center;">
                        <!-- Example Student Row -->
                        <?php
                        if ($select_new_req):
                            while ($row = $select_new_req->fetch_assoc()):

                        ?>
                                <tr style="border-bottom: 1px solid #30363d;">
                                    <td data-label="Student Name" style="padding: 1rem 0.75rem; vertical-align: middle;"><?= htmlspecialchars($row['student_name']) ?></td>
                                    <td data-label="Enrollment" style="padding: 1rem 0.75rem; vertical-align: middle;"><?= htmlspecialchars($row['Enrollment_no']) ?></td>
                                    <td data-label="Department" style="padding: 1rem 0.75rem; vertical-align: middle;"><?= htmlspecialchars($row['student_department']) ?></td>
                                    <td data-label="Admission Year" style="padding: 1rem 0.75rem; vertical-align: middle;"><?= htmlspecialchars($row['student_add_year']) ?></td>
                                    <td data-label="Passout Year" style="padding: 1rem 0.75rem; vertical-align: middle;"><?= htmlspecialchars($row['student_pass_year']) ?></td>
                                    <td data-label="I-Card" style="padding: 1rem 0.75rem; vertical-align: middle;">
                                        <a href="javascript:void(0);" onclick="showICard('../uploads/idcards/student/<?= htmlspecialchars($row['ID_Card']) ?>')" style="color: #4CAF50; text-decoration: none; font-weight: 500;">I-Card</a>
                                    </td>
                                    <td data-label="Status" style="padding: 1rem 0.75rem; vertical-align: middle;">
                                        <div class="button-container" style="display: flex; gap: 1rem;">
                                            <form action="./student_new_req.php" method="post">
                                                <input type="hidden" name="student_id" value='<?= htmlspecialchars($row['student_id']) ?>'>
                                                <button class="action-btn accept-btn" type="submit" style="margin-bottom: 4px; padding: 0.5rem 0.8rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; white-space: nowrap; background-color: #4CAF50; color: #fff;" name="act_btn">Accept</button>
                                                <button class="action-btn reject-btn" type="submit" style="padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; white-space: nowrap; background-color: #f44336; color: #fff;" name="rej_btn">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            endwhile;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- The Modal HTML -->
    <div id="iCardModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeICard()">&times;</span>
            <img id="iCardImage" src="" alt="Student ID Card">
        </div>
    </div>
    <script>
        const modal = document.getElementById("iCardModal");
        const modalImage = document.getElementById("iCardImage");

        function showICard(imageURL) {
            modalImage.src = imageURL;
            modalImage.style.height = "550px"
            modalImage.style.width = "50%"
            modal.style.display = "flex";
        }

        function closeICard() {
            modal.style.display = "none";
        }

        // Close the modal when the user clicks anywhere outside of the image
        window.onclick = function(event) {
            if (event.target == modal) {
                closeICard();
            }
        };
    </script>
</body>

</html>