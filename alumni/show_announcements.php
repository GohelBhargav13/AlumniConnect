<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../utills/db_conn.php';

if (!isset($conn)) {
    die("Database connection is not established");
}

$sql = "SELECT a.anno_id, a.anno_title, a.anno_desc, a.anno_type,
               a.anno_show_until, a.anno_additional_links, a.created_at,
               ad.admin_name AS admin_name
        FROM announcement_master a
        JOIN adminmaster ad ON a.created_by = ad.admin_id
        ORDER BY a.created_at DESC";

$result = mysqli_query($conn, $sql);

$today = date('Y-m-d');
$latestAnnouncements = [];
$pastAnnouncements = [];

$allowedPriorities = ['Normal', 'Important', 'Urgent'];
$filterPriority = null;

if (!empty($_GET['priority']) && in_array($_GET['priority'], $allowedPriorities)) {
    $filterPriority = $_GET['priority'];
}

while ($row = mysqli_fetch_assoc($result)) {
    if (empty($row['anno_show_until']) || $row['anno_show_until'] >= $today) {
        $latestAnnouncements[] = $row;
    } else {
        if ($filterPriority === null || $row['anno_type'] === $filterPriority) {
            $pastAnnouncements[] = $row;
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
    <title>Announcements | GEC Modasa Alumni Portal</title>
    <style>
        :root {
            --navy: #1b3a4b;
            --navy-dark: #122733;
            --teal: #2f7a68;
            --teal-dark: #235d51;
            --bg: #f6f7f5;
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
            font-family: -apple-system, "Segoe UI", Arial, sans-serif;
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ---- Top banner: Latest Announcements ---- */
        .latest-banner {
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy-dark) 100%);
            color: #fff;
            padding: 50px 20px 60px;
        }

        .latest-banner .label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12.5px;
            color: #9fd6c6;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .latest-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 22px 26px;
            margin-bottom: 16px;
        }

        .latest-card:last-child {
            margin-bottom: 0;
        }

        .latest-card h2 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .latest-card p.desc {
            color: #cfd9dc;
            font-size: 0.95rem;
            margin-bottom: 14px;
            max-width: 700px;
        }

        .badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .badge-normal {
            background: rgba(255, 255, 255, 0.15);
            color: #d7e0e2;
        }

        .badge-important {
            background: #f5d97a;
            color: #5a4b0a;
        }

        .badge-urgent {
            background: #e5786b;
            color: #fff;
        }

        .latest-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            font-size: 0.88rem;
            color: #d7e0e2;
        }

        .latest-meta span strong {
            color: #9fd6c6;
            display: block;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .latest-card a.link-btn {
            display: inline-block;
            margin-top: 14px;
            background: var(--teal);
            color: #fff;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 0.87rem;
            font-weight: 600;
        }

        .latest-card a.link-btn:hover {
            background: var(--teal-dark);
        }

        .no-announcements {
            color: #cfd9dc;
            font-size: 0.95rem;
            padding: 10px 0;
        }

        /* ---- Past Announcements section ---- */
        .past-section {
            padding: 50px 20px 70px;
        }

        .past-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 30px;
        }

        .past-header h2 {
            font-size: 1.5rem;
        }

        select#priorityFilter {
            padding: 9px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
            color: var(--text);
            font-size: 0.9rem;
            font-family: inherit;
        }

        .announcement-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 20px 22px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .announcement-card .info h3 {
            font-size: 1.05rem;
            margin-bottom: 6px;
        }

        .announcement-card .info p.desc {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .announcement-card .badge-light {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
        }

        .badge-light.normal {
            background: #eceff0;
            color: var(--muted);
        }

        .badge-light.important {
            background: #fbe9b4;
            color: #7a5d00;
        }

        .badge-light.urgent {
            background: #f6d5cf;
            color: #a3372a;
        }

        .announcement-card .info .meta {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .announcement-card .posted-by {
            font-size: 0.8rem;
            color: var(--muted);
            white-space: nowrap;
            align-self: flex-start;
        }

        .empty-state {
            text-align: center;
            color: var(--muted);
            padding: 40px 0;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>

    <!-- add navbar in the file -->
    <?php include "./navbar.php"  ?>

    <!-- ===== Latest Announcements Banner ===== -->
    <section class="latest-banner">
        <div class="container">
            <div class="label">Latest Announcements</div>

            <?php if (count($latestAnnouncements) > 0): ?>
                <?php foreach ($latestAnnouncements as $anno): ?>
                    <div class="latest-card">
                        <span class="badge badge-<?= htmlspecialchars($anno['anno_type']) ?>"><?= htmlspecialchars($anno['anno_type']) ?></span>
                        <h2><?= htmlspecialchars($anno['anno_title']) ?></h2>

                        <?php if (!empty($anno['anno_desc'])): ?>
                            <p class="desc"><?= htmlspecialchars($anno['anno_desc']) ?></p>
                        <?php endif; ?>

                        <div class="latest-meta">
                            <span><strong>Posted On</strong><?= date('d M Y', strtotime($anno['created_at'])) ?></span>

                            <?php if (!empty($anno['anno_show_until'])): ?>
                                <span><strong>Visible Until</strong><?= date('d M Y', strtotime($anno['anno_show_until'])) ?></span>
                            <?php endif; ?>

                            <span><strong>Posted By</strong><?= htmlspecialchars($anno['admin_name']) ?></span>
                        </div>

                        <?php if (!empty($anno['anno_additional_links'])): ?>
                            <a href="<?= htmlspecialchars($anno['anno_additional_links']) ?>" class="link-btn" target="_blank" rel="noopener">View Attachment</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-announcements">No active announcements at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== Past Announcements Section ===== -->
    <section class="past-section">
        <div class="container">
            <div class="past-header">
                <h2>Past Announcements</h2>

                <!-- Priority filter — submits as a normal GET request, ?priority=... -->
                <form method="GET" action="">
                    <select id="priorityFilter" name="priority">
                        <option value="all">All Priorities</option>
                        <option value="Normal">Normal</option>
                        <option value="Important">Important</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                    <button type="submit">
                        filter
                    </button>
                </form>
            </div>

            <?php if (count($pastAnnouncements) > 0): ?>
                <?php foreach ($pastAnnouncements as $anno): ?>
                    <div class="announcement-card" data-priority="<?= htmlspecialchars($anno['anno_type']) ?>">
                        <div class="info">
                            <span class="badge-light <?= htmlspecialchars($anno['anno_type']) ?>"><?= htmlspecialchars($anno['anno_type']) ?></span>
                            <h3><?= htmlspecialchars($anno['anno_title']) ?></h3>

                            <?php if (!empty($anno['anno_desc'])): ?>
                                <p class="desc"><?= htmlspecialchars($anno['anno_desc']) ?></p>
                            <?php endif; ?>

                            <div class="meta">
                                &#128197; Posted <?= date('d M Y', strtotime($anno['created_at'])) ?>
                                <?php if (!empty($anno['anno_show_until'])): ?>
                                    &nbsp;&middot;&nbsp; Expired <?= date('d M Y', strtotime($anno['anno_show_until'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="posted-by">Posted by <?= htmlspecialchars($anno['admin_name']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">No past announcements to show yet.</div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Add the footer in the file -->
    <?php include "./footer.php" ?>
</body>

</html>