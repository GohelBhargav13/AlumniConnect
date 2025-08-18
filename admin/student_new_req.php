<?php
require "../utills/db_conn.php";
//fetch the student data for the new request

$select_new_req = $conn->query("SELECT student_name,Enrollment_no,student_add_year,student_pass_year,ID_Card,student_department FROM studentmaster WHERE req_status = 'pending'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student New Requests</title>
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
                left: -9999px;
            }

            .request-table tr {
                border: 1px solid #30363d;
                margin-bottom: 1rem;
                border-radius: 8px;
            }

            .request-table td {
                border: none;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }

            .request-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 45%;
                padding-left: 15px;
                font-weight: bold;
                text-align: left;
                color: #8b949e;
            }
        }

        /* Modal specific styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
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
                <table class="request-table" style="width: 100%; border-collapse: collapse; margin-top: 1.5rem;">
                    <thead>
                        <tr>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: left; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Student Name</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: left; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Enrollment</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: left; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Department</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: left; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Admission Year</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: left; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Passout Year</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: left; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">I-Card</th>
                            <th style="background-color: #30363d; padding: 1rem 0.75rem; text-align: left; font-weight: 600; border-bottom: 2px solid #555; white-space: nowrap;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                        <a href="javascript:void(0);" onclick="showICard('../uploads/idcards/<?= htmlspecialchars($row['ID_Card']) ?>')" style="color: #4CAF50; text-decoration: none; font-weight: 500;">View I-Card</a>
                                    </td>
                                    <td data-label="Status" style="padding: 1rem 0.75rem; vertical-align: middle;">
                                        <div class="button-container" style="display: flex; gap: 0.5rem;">
                                            <form action="#" method="post">
                                                <button class="action-btn accept-btn" style="padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; white-space: nowrap; background-color: #4CAF50; color: #fff;" name="act_btn">Accept</button>
                                                <button class="action-btn reject-btn" style="padding: 0.5rem 1rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; white-space: nowrap; background-color: #f44336; color: #fff;" name="rej_btn">Reject</button>
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