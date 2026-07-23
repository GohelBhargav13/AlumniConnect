<?php
include "../utills/db_conn.php";
include "./alumni_favicon.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// the post types
$post_types  = ["General", "Achivement", "New Job", "Internship", "Higher Studies", "Startup"];


// Check alumni login
if (!isset($_SESSION['alumni_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($conn)) {
    die("Connection is not established");
}

// get the current page is edit page or the update page
$update_community_post = null;
if (isset($_GET["current_page"]) && isset($_GET["edit"])) {
    $post_id = $_GET["edit"] ?? 0;
    $current_page = $_GET["current_page"] ?? "";

    // fetch the update event details
    $fetch_query = "SELECT * FROM community_posts WHERE post_id = ?";
    $fetch_stmt = $conn->prepare($fetch_query);
    $fetch_stmt->bind_param("i", $post_id);
    $fetch_stmt->execute();
    $result = $fetch_stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ./alumni_community_post.php?error=" . urlencode("post not found."));
        exit();
    }

    $update_community_post = $result->fetch_assoc();
}

// Handle form submission
if (isset($_POST['create_post_btn'])) {

    // Get form data
    $alumni_id = $_SESSION['alumni_id'];
    $post_type = trim($_POST['post_type']);
    $post_title = trim($_POST['post_title']);
    $post_content = trim($_POST['post_content']);

    // Default status
    $status = "pending";

    // Validation
    if (empty($post_type) || empty($post_title) || empty($post_content)) {

        header("Location: alumni_community_post.php?error=Please fill all required fields.");
        exit();
    }

    if (isset($post_id) && isset($current_page) && $current_page == "edit") {
        $update_query = "UPDATE community_posts 
                          SET post_title = ?, post_content = ?, post_type = ?
                          WHERE post_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssi", $post_title, $post_content, $post_type, $post_id);

        if ($update_stmt->execute()) {
            header("Location: ./alumni_community_post.php?success=" . urlencode("post updated successfully."));
            exit();
        } else {
            header("Location: ./alumni_community_post.php?edit=" . urlencode($post_id) . "&error=" . urlencode("Failed to update post."));
            exit();
        }
    } else {
        $sql = "INSERT INTO community_posts
            (alumni_id, post_type, post_title, post_content, status)
            VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "issss",
            $alumni_id,
            $post_type,
            $post_title,
            $post_content,
            $status
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: alumni_community_post.php?success=Post published successfully.");
            exit();
        } else {

            header("Location: alumni_community_post.php?error=Unable to publish post.");
            exit();
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            color: #2b2f31;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .content-area {
            flex: 1;
            padding: 40px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dashboard-title {
            font-size: 2rem;
            color: #2E75B6;
            margin-bottom: 30px;
            text-align: center;
        }

        .profile-card {
            width: 100%;
            max-width: 650px;
            background: #f4f8fc;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .community-info {
            background: #EAF4FF;
            border-left: 5px solid #2E75B6;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .community-info h3 {
            color: #2E75B6;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .community-info p {
            color: #555;
            line-height: 1.6;
            font-size: 14px;
        }

        .profile-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #667079;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #d6e2ef;
            border-radius: 6px;
            font-size: 15px;
            font-family: inherit;
            background: #fff;
            transition: 0.3s;
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
            min-height: 180px;
        }

        .btn-submit {
            width: 100%;
            background: #2E75B6;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: .3s;
        }

        .btn-submit:hover {
            background: #1F5A94;
        }

        #message {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        #message.success {
            background: #0a7d3e;
            color: white;
        }

        #message.error {
            background: #d92d20;
            color: white;
        }

        @media (max-width: 768px) {

            .dashboard-container {
                flex-direction: column;
            }

            .content-area {
                padding: 20px;
            }

            .dashboard-title {
                font-size: 1.7rem;
            }

            .profile-card {
                padding: 20px;
            }

            .community-info h3 {
                font-size: 16px;
            }

            .btn-submit {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">

        <?php include "sidebar.php"; ?>

        <div class="content-area">

            <h1 class="dashboard-title">
                Share with Alumni Community
            </h1>

            <div class="profile-card">
                <!-- display the message -->
                <?php if (isset($_GET["success"]) || isset($_GET["error"])): ?>
                    <p id="message"
                        class="<?php echo isset($_GET['success']) ? 'success' : (isset($_GET['error']) ? 'error' : ''); ?>">

                        <?php
                        echo isset($_GET['success'])
                            ? htmlspecialchars($_GET['success'])
                            : htmlspecialchars($_GET['error'] ?? "");
                        ?>
                    </p>
                <?php endif; ?>
                <script>
                    const messageElement = document.getElementById('message');
                    setTimeout(() => {
                        if (messageElement) {
                            messageElement.style.display = 'none';
                        }
                    }, 2 * 1000);
                </script>
                <!-- event edit page -->
                <?php if (isset($current_page) && $current_page == "edit"): ?>
                    <form method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="post_title">
                                Post Title
                            </label>
                            <input
                                type="text"
                                id="post_title"
                                name="post_title"
                                placeholder="Example: Excited to Join Infosys"
                                value="<?= htmlspecialchars($update_community_post["post_title"]) ?>"
                                required>

                        </div>
                        <div class="form-group">
                            <label for="post_content">
                                Post Content
                            </label>
                            <textarea
                                id="post_content"
                                name="post_content"
                                rows="8"
                                placeholder="Share your achievement, ask a question, or write something for the alumni community..."
                                required>
                                <?= trim(htmlspecialchars($update_community_post["post_content"])); ?>
                            </textarea>

                        </div>
                        <div class="form-group">
                            <label for="post_type">Post Type</label>

                            <select
                                name="post_type"
                                id="post_type"
                                required>
                                <option value="<?= htmlspecialchars($update_community_post["post_type"]) ?>"><?= htmlspecialchars($update_community_post["post_type"]) ?></option>
                                <?php foreach ($post_types as $ptype): ?>
                                    <option value="<?php echo $ptype ?>"><?= htmlspecialchars($ptype) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button
                            type="submit"
                            name="create_post_btn"
                            class="btn-submit">

                            Publish Post

                        </button>

                    </form>
                <?php else: ?>
                    <form method="POST" class="profile-form">


                        <div class="form-group">

                            <label for="post_title">
                                Post Title
                            </label>

                            <input
                                type="text"
                                id="post_title"
                                name="post_title"
                                placeholder="Example: Excited to Join Infosys"
                                required>

                        </div>

                        <div class="form-group">

                            <label for="post_content">
                                Post Content
                            </label>

                            <textarea
                                id="post_content"
                                name="post_content"
                                rows="8"
                                placeholder="Share your achievement, ask a question, or write something for the alumni community..."
                                required></textarea>

                        </div>
                        <div class="form-group">
                            <label for="post_type">Post Type</label>

                            <select
                                name="post_type"
                                id="post_type"
                                required>

                                <option value="">Select Post Type</option>
                                <?php foreach ($post_types as $ptype): ?>
                                    <option value="<?php echo $ptype ?>"><?= htmlspecialchars($ptype) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button
                            type="submit"
                            name="create_post_btn"
                            class="btn-submit">

                            Publish Post

                        </button>

                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>