<?php
require '../utills/db_conn.php';
include("./alumni_favicon.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Make sure someone is actually logged in before fetching their posts.
if (!isset($_SESSION['alumni_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($conn)) {
    die("Database connection is not established");
}

$alumni_id = $_SESSION['alumni_id'];

// the post types
$post_types = ["General", "Achivement", "New Job", "Internship", "Higher Studies", "Startup"];
$post_status = ["pending", "accepted", "rejected"];

// fetch the year from the table
$post_years = [];
$year_sql = "SELECT DISTINCT YEAR(created_at) AS post_year
             FROM community_posts
             ORDER BY post_year DESC";
$exec_query = mysqli_query($conn, $year_sql);
while ($row = mysqli_fetch_assoc($exec_query)) {
    $post_years[] = $row['post_year'];
}

// for the delete the following post
if (isset($_GET['post_id'])) {
    // get the id from the url
    $post_id = (int) $_GET["post_id"];

    $sql = "DELETE FROM community_posts
            WHERE post_id = ? AND alumni_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $alumni_id);

    if ($stmt->execute()) {
        $message = "post deleted successfully";
        header("Location: alumni_post_view.php?success=$message");
        exit();
    }
    $error = "some error while deleting a post";
    header("Location: alumni_post_view.php?error=$error");
    exit();
}

// filter of the posts
$post_category = $_GET["category"] ?? 'all';
$year = $_GET["year"] ?? 'all';

if ($post_category !== 'all' && !empty($post_category) && $year !== 'all' && !empty($year)) {
    $find_query = "SELECT * FROM community_posts cp
                    JOIN alumni_student_master sm ON cp.alumni_id = sm.alumni_id
                    WHERE cp.alumni_id = ? AND cp.post_type = ? AND YEAR(cp.created_at) = ? ORDER BY cp.created_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("iss", $alumni_id, $post_category, $year);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} elseif (($post_category == 'all' || empty($post_category)) && $year !== 'all' && !empty($year)) {
    $find_query = "SELECT * FROM community_posts cp
                    JOIN alumni_student_master sm ON cp.alumni_id = sm.alumni_id
                    WHERE cp.alumni_id = ? AND YEAR(cp.created_at) = ? ORDER BY cp.created_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("is", $alumni_id, $year);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} elseif ($post_category !== 'all' && !empty($post_category) && ($year == 'all' || empty($year))) {
    $find_query = "SELECT * FROM community_posts cp
                    JOIN alumni_student_master sm ON cp.alumni_id = sm.alumni_id
                    WHERE cp.alumni_id = ? AND cp.post_type = ? ORDER BY cp.created_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("is", $alumni_id, $post_category);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} else {
    $find_query = "SELECT * FROM community_posts cp
                    JOIN alumni_student_master sm ON cp.alumni_id = sm.alumni_id
                    WHERE cp.alumni_id = ? ORDER BY cp.created_at DESC
                    ";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("s", $alumni_id);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
}
$myPosts = $data_res->fetch_all(MYSQLI_ASSOC);

// declare the css for the status
$STATUS_STYLE = [
    "pending" => "bg-info text-dark",
    "accepted" => "bg-success",
    "rejected" => "bg-danger",
    "disabled" => "bg-danger text-white"
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Community Posts</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f8fc;
            color: #2b2f31;
        }

        /* ===========================
                Layout
        =========================== */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .content-area {
            flex: 1;
            padding: 40px;
            background-color: #f4f8fc;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        /* A wrapper to keep the cards and header from stretching too wide */
        .content-wrapper {
            width: 100%;
            max-width: 1050px;
            /* Adjust this to match exact visual width you need */
        }

        /* ===========================
                Heading
        =========================== */
        .page-title {
            font-size: 2rem;
            color: #2E75B6;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: #667079;
            font-size: 15px;
            margin-bottom: 30px;
        }

        /* ===========================
                Filter Section
        =========================== */
        .filter-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 25px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid #e1e8ed;
            margin-bottom: 35px;
            width: 100%;
        }

        .filter-container form {
            display: flex;
            gap: 20px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 220px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: #667079;
            font-weight: 500;
            font-size: 14px;
        }

        .filter-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d6e2ef;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            background: #ffffff;
            color: #2b2f31;
            transition: 0.3s;
        }

        .filter-group select:focus {
            border-color: #2E75B6;
            box-shadow: 0 0 0 2px rgba(46, 117, 182, 0.15);
        }

        .filter-container button {
            background: #2E75B6;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 12px 35px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: 0.3s;
            height: 45px;
        }

        .filter-container button:hover {
            background: #1F5A94;
        }

        /* ===========================
                Cards Grid
        =========================== */
        .post-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            width: 100%;
        }

        /* ===========================
                Card Style
        =========================== */
        .post-card {
            background: #ffffff;
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 25px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        /* ===========================
                Card Header
        =========================== */
        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .badge {
            background: #80b7f2;
            color: #2E75B6;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .post-date {
            color: #8894a0;
            font-size: 13px;
            font-weight: 500;
        }

        /* ===========================
                Card Content
        =========================== */
        .post-card h2 {
            font-size: 20px;
            margin-bottom: 12px;
            color: #2b2f31;
            font-weight: 600;
            line-height: 1.3;
        }

        .post-card p {
            color: #667079;
            line-height: 1.6;
            font-size: 14px;
            margin-bottom: 25px;
            display: -webkit-box;
            line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ===========================
                Card Actions
        =========================== */
        .post-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: auto;
        }

        .edit-btn,
        .delete-btn {
            text-decoration: none;
            padding: 8px 22px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s;
            display: inline-block;
            text-align: center;
        }

        .edit-btn {
            background: #2E75B6;
            color: #ffffff;
            border: 1px solid #2E75B6;
        }

        .edit-btn:hover {
            background: #1F5A94;
            border-color: #1F5A94;
        }

        .delete-btn {
            background: #ffffff;
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .delete-btn:hover {
            background: #dc3545;
            color: #ffffff;
        }

        /* ===========================
                Responsive Design
        =========================== */
        @media (max-width: 992px) {
            .post-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .content-area {
                padding: 20px;
                align-items: center;
            }

            .filter-container form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                width: 100%;
            }

            .filter-container button {
                width: 100%;
            }

            .post-footer {
                flex-direction: row;
            }
        }
    </style>
</head>

<body>

    <div class="dashboard-container">

        <!-- Sidebar Direct Include (Matches your working code) -->
        <?php include "sidebar.php"; ?>

        <!-- Content Area -->
        <div class="content-area">
            <?php if (isset($_GET["success"]) || isset($_GET["error"])): ?>
                <p id="message" style="color: <?php echo isset($_GET["success"]) ? '#0a7d3e' : '#d92d20'; ?>;"><?php echo isset($_GET["success"]) ? htmlspecialchars($_GET["success"]) : htmlspecialchars($_GET["error"] ?? ""); ?></p>
            <?php endif; ?>
            <!-- Script for the remove the message from the display -->
            <script>
                const message = document.getElementById("message");
                setTimeout(() => {
                    message.style.display = "none";
                }, 2000)
            </script>
            <!-- Added Wrapper for constrained, correct alignment -->
            <div class="content-wrapper">

                <h1 class="page-title">My Community Posts</h1>

                <p class="page-subtitle">
                    View, edit and manage your community posts.
                </p>

                <!-- Filter Section -->
                <div class="filter-container">
                    <form method="GET">

                        <!-- Category -->
                        <div class="filter-group">
                            <label>Category</label>
                            <select name="category">
                                <option value="all">All Categories</option>
                                <?php foreach ($post_types as $ptype): ?>
                                    <option value="<?php echo $ptype ?>"><?= htmlspecialchars($ptype) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Year -->
                        <div class="filter-group">
                            <label>Year</label>
                            <select name="year">
                                <option value="all">All Years</option>
                                <?php foreach ($post_years as $year): ?>
                                    <option value="<?= htmlspecialchars($year) ?>"><?= htmlspecialchars($year); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- status -->
                        <div class="filter-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="all">All status</option>
                                <?php
                                foreach ($post_status as $status): ?>
                                    <option value="<?php echo $status ?>"><?= htmlspecialchars($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit">Filter</button>
                    </form>
                </div>

                <!-- Cards Container -->
                <div class="post-container">

                    <!-- Card 1 -->
                    <?php if (mysqli_num_rows($data_res) > 0):  ?>
                        <?php foreach ($myPosts as $pdetails): ?>
                            <?php if ($pdetails["status"] == 'disabled'): ?>
                                <div class="post-card">
                                    <div class="post-header">
                                        <div style="display: flex; gap: 10px    ;">
                                            <span class="badge"><?= htmlspecialchars($pdetails["post_type"]) ?></span>
                                            <span class="badge <?= $STATUS_STYLE[$pdetails["status"]] ?>">
                                                <?= strtoupper(htmlspecialchars($pdetails["status"])) ?>
                                            </span>
                                        </div>
                                        <span class="post-date"><?= htmlspecialchars($pdetails["created_at"]) ?></span>
                                    </div>

                                    <h2><?= htmlspecialchars($pdetails["post_title"]) ?></h2>

                                    <p>
                                        <?= htmlspecialchars($pdetails["post_content"]) ?>
                                    </p>
                                    <div style="display: flex; gap: 10px;">
                                        <p class="badge bg-light text-dark">NOTE :- </p>
                                        <P class="badge bg-danger text-white">Due to inconvenience this post is disable by admin</P>
                                    </div>
                                    <form action="alumni_post_view.php" method="get">
                                        <div class="post-footer">
                                            <a href="alumni_post_view.php?post_id=<?= htmlspecialchars($pdetails["post_id"]) ?>" class="delete-btn" onclick="return confirm('Delete this post?')">Delete</a>
                                        </div>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="post-card">
                                    <div class="post-header">
                                        <div style="display: flex; gap: 10px    ;">
                                            <span class="badge"><?= htmlspecialchars($pdetails["post_type"]) ?></span>
                                            <span class="badge <?= $STATUS_STYLE[$pdetails["status"]] ?>"><?= strtoupper(htmlspecialchars($pdetails["status"])) ?></span>
                                        </div>
                                        <span class="post-date"><?= htmlspecialchars($pdetails["created_at"]) ?></span>
                                    </div>

                                    <h2><?= htmlspecialchars($pdetails["post_title"]) ?></h2>

                                    <p>
                                        <?= htmlspecialchars($pdetails["post_content"]) ?>
                                    </p>

                                    <form action="alumni_post_view.php" method="get">
                                        <div class="post-footer">
                                            <?php if (isset($pdetails["status"]) && $pdetails["status"] == "pending"): ?>
                                                <a href="alumni_community_post.php?edit=<?= htmlspecialchars($pdetails["post_id"]) ?>&current_page=<?= htmlspecialchars("edit") ?>" class="edit-btn">Edit</a>
                                            <?php endif; ?>
                                            <a href="alumni_post_view.php?post_id=<?= htmlspecialchars($pdetails["post_id"]) ?>" class="delete-btn" onclick="return confirm('Delete this post?')">Delete</a>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No posts</p>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>