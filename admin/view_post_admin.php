<?php 
    include '../utills/db_conn.php';

// Fetch all posts
$fetch_all_post = "SELECT p.*, am.alumni_name,am.alumni_email,am.alumni_id 
    FROM postmaster as p 
    JOIN alumnimaster as am ON am.alumni_id = p.created_by 
    ORDER BY post_created_at DESC";
$result = $conn->query($fetch_all_post);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniConnect | Admin</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', sans-serif; background-color: #0d1117; color: white;">

    <!-- Main container for the entire page -->
    <div style="display: flex; height: 100vh; overflow: hidden; border: 1px solid #30363d; border-radius: 10px; margin: 20px;">

      <?php include "./sidebar.php" ?>

        <!-- Main Content Area -->
        <div style="flex-grow: 1; padding: 20px; box-sizing: border-box; background-color: #0d1117;">
            <h1 style="text-align: center; font-size: 24px; margin-bottom: 40px; border-bottom: 1px solid #30363d; padding-bottom: 20px;">All Posts</h1>
            
            <!-- Posts container -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php 
                    if($result):
                        while($row = $result->fetch_assoc()):
                ?>
                <!-- Post Item 1 -->
                <div style="background-color: #161b22; padding: 20px; border-radius: 8px; border: 1px solid #30363d;">
                    <h2 style="font-size: 20px; margin-top: 0; margin-bottom: 10px;"><?= htmlspecialchars($row['post_title']) ?></h2>
                    <p style="font-size: 16px; margin: 5px;"><?= htmlspecialchars($row['post_desc']) ?></p>
                    <p style="font-size: 16px; margin: 5px;"><b>Location</b> : <?= htmlspecialchars($row['post_location']) ?></p>
                    <p style="font-size: 16px; margin: 5px;"><b>Required Skills</b> : <?= htmlspecialchars($row['post_req_skill']) ?></p>
                    <p style="font-size: 16px; margin: 5px;"><b>Type </b> : <?= htmlspecialchars($row['post_job_type']) ?></p>
                    <p style="font-size: 18px; margin: 5px;"><b>Posted By</b> : <?= htmlspecialchars($row['alumni_name']) ?></p>
                    <p style="font-size: 16px; margin: 5px;"><b>Ref Link </b> : <a href="<?= htmlspecialchars($row['post_ref_link']) ?>" style="text-decoration: none; color: white;" > <?= htmlspecialchars($row['post_ref_link']) ?></a></p>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
