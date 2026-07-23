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
                    WHERE cp.alumni_id = ? AND cp.post_type = ? AND YEAR(cp.created_at) = ? AND cp.status = 'accepted' ORDER BY cp.created_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("iss", $alumni_id, $post_category, $year);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} elseif (($post_category == 'all' || empty($post_category)) && $year !== 'all' && !empty($year)) {
    $find_query = "SELECT * FROM community_posts cp
                    JOIN alumni_student_master sm ON cp.alumni_id = sm.alumni_id
                    WHERE cp.alumni_id = ? AND YEAR(cp.created_at) = ? AND cp.status = 'accepted' ORDER BY cp.created_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("is", $alumni_id, $year);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} elseif ($post_category !== 'all' && !empty($post_category) && ($year == 'all' || empty($year))) {
    $find_query = "SELECT * FROM community_posts cp
                    JOIN alumni_student_master sm ON cp.alumni_id = sm.alumni_id
                    WHERE cp.alumni_id = ? AND cp.post_type = ? AND cp.status = 'accepted' ORDER BY cp.created_at DESC";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("is", $alumni_id, $post_category);
    $find_stmt->execute();
    $data_res = $find_stmt->get_result();
} else {
    $find_query = "SELECT * FROM community_posts cp
                    JOIN alumni_student_master sm ON cp.alumni_id = sm.alumni_id
                    WHERE cp.alumni_id = ? AND cp.status = 'accepted' ORDER BY cp.created_at DESC
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
    "rejected" => "bg-danger"
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Batch | GEC Modasa Alumni Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --navy: #2E75B6;
            --navy-dark: #1F5A94;
            --teal: #2E75B6;
            --teal-dark: #1F5A94;
            --bg: #ffffff;
            --card-bg: #ffffff;
            --text: #2b2f31;
            --muted: #667079;
            --border: #e0e3df;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        h1,
        h2,
        h3 {
            font-family: Georgia, "Times New Roman", serif;
            color: var(--navy);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ---- Top banner ---- */
        .page-banner {
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy-dark) 100%);
            color: #fff;
            padding: 46px 20px 40px;
        }

        .page-banner .label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12.5px;
            color: #cfe3f5;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .page-banner h1 {
            color: #fff;
            font-size: 1.9rem;
            margin-bottom: 8px;
        }

        .page-banner p {
            color: #cfd9dc;
            font-size: 0.95rem;
            max-width: 560px;
        }

        /* ---- Filter bar ---- */
        .filter-bar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 18px 20px;
        }

        .filter-bar form {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-bar label {
            font-size: 0.85rem;
            color: var(--muted);
            margin-right: 6px;
        }

        .filter-bar select {
            padding: 8px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--bg);
            color: var(--text);
            font-size: 0.9rem;
            font-family: inherit;
        }

        .filter-bar button[type="submit"] {
            padding: 9px 18px;
            border: none;
            border-radius: 6px;
            background: #2E75B6;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .filter-bar button[type="submit"]:hover {
            background: #1F5A94;
        }

        .filter-bar .result-count {
            margin-left: auto;
            font-size: 0.85rem;
            color: var(--muted);
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
            color: white;
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

        /* ---- Cards grid ---- */
        .directory-section {
            padding: 40px 20px 70px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .student-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 22px;
        }

        .avatar-circle {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--teal);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Georgia, serif;
            font-weight: 700;
            font-size: 17px;
            margin-bottom: 14px;
        }

        .student-card h3 {
            font-size: 1.05rem;
            margin-bottom: 4px;
        }

        .batch-branch {
            font-size: 0.85rem;
            color: var(--teal-dark);
            font-weight: 600;
            margin-bottom: 12px;
        }

        .student-card .detail-row {
            font-size: 0.87rem;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .student-card .detail-row strong {
            color: var(--text);
        }

        .student-card a.linkedin-link {
            display: inline-block;
            margin-top: 10px;
            font-size: 0.85rem;
            color: var(--teal-dark);
            font-weight: 600;
        }

        .student-card a.linkedin-link:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            color: var(--muted);
            padding: 60px 0;
            font-size: 0.95rem;
        }

        @media (max-width: 860px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 560px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }

            .filter-bar .result-count {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Add the navbar -->
    <?php include "./navbar.php" ?>

    <!-- ===== Page Banner ===== -->
    <section class="page-banner">
        <div class="container">
            <div class="label">Community</div>
            <h1>Find Your Batchmates Posts</h1>
            <p>Search fellow alumni posts by post category or year and reconnect with familiar faces from GEC Modasa.</p>
        </div>
    </section>

    <!-- ===== Filter Bar ===== -->
    <section class="filter-bar">
        <div class="container">
            <form method="GET" action="">
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

                <button type="submit">Filter</button>
                <div class="result-count"><?= count($myPosts) ?> post found</div>
            </form>
        </div>
    </section>

    <!-- ===== Directory Cards ===== -->
    <section class="directory-section">
        <div class="container">

            <!-- Card 1 -->
            <?php if (mysqli_num_rows($data_res) > 0):  ?>
                <?php foreach ($myPosts as $pdetails): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <div style="display: flex; gap: 10px;">
                                <span class="badge"><?= htmlspecialchars($pdetails["post_type"]) ?></span>
                            </div>
                            <span class="post-date"><?= htmlspecialchars($pdetails["created_at"]) ?></span>
                        </div>

                        <h2><?= htmlspecialchars($pdetails["post_title"]) ?></h2>

                        <p>
                            <?= htmlspecialchars($pdetails["post_content"]) ?>
                        </p>
                        <div class="post-footer">
                            <a class="delete-btn"><?= htmlspecialchars($pdetails["alumni_name"]) ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts</p>
            <?php
            endif;
            ?>

        </div>
    </section>
    <!-- Add Footer in file -->
    <?php include "./footer.php" ?>
</body>

</html>