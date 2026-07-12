<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../utills/db_conn.php';
include "./alumni_favicon.php";

if (!isset($conn)) {
    die("Database connection is not established");
}

// fetch from the two tables
$sql = "SELECT e.event_id, e.event_name, e.event_desc, e.event_venue,
               e.event_date, e.event_time, e.event_additional_links,
               e.created_at, a.admin_name AS admin_name
        FROM event_master e
        JOIN adminmaster a ON e.created_by = a.admin_id ";
$result = mysqli_query($conn, $sql);

$today = date('Y-m-d');
$latestEvent = null;
$pastEvents = [];
$years = [];

$filterYear = $_GET['fyear'] ?? null;
while ($row = mysqli_fetch_assoc($result)) {

    // Collect distinct years for the filter dropdown
    $eventYear = date('Y', strtotime($row['event_date']));
    if (!in_array($eventYear, $years)) {
        $years[] = $eventYear;
    }

    // logic of latest events show on dashboard
    if ($row['event_date'] >= $today && $latestEvent === null) {
        $latestEvent = $row;
    } elseif ($row['event_date'] < $today) {
        
        // simple filter logic for the years
        if (empty($filterYear) || $eventYear == $filterYear || $filterYear == "all") {
            $pastEvents[] = $row;
        }
    }
}

// sort for the latest event is first than after another
if (isset($pastEvents) && count($pastEvents) > 2) {
    $pastEvents = array_reverse($pastEvents);
}

// Sort years descending for the dropdown (most recent year first)
rsort($years);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | GEC Modasa Alumni Portal</title>
    <style>
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

        /* ---- Top banner: Latest Event ---- */
        .latest-banner {
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy-dark) 100%);
            color: #fff;
            padding: 50px 20px 60px;
        }

        .latest-banner .label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12.5px;
            color: #cfe3f5;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .latest-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 26px 28px;
        }

        .latest-card h2 {
            color: #fff;
            font-size: 1.6rem;
            margin-bottom: 10px;
        }

        .latest-card p.desc {
            color: #cfd9dc;
            font-size: 0.98rem;
            margin-bottom: 16px;
            max-width: 700px;
        }

        .latest-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            font-size: 0.92rem;
            color: #d7e0e2;
        }

        .latest-meta span strong {
            color: #fff;
            display: block;
            font-size: 0.78rem;
            color: #cfe3f5;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .latest-card a.link-btn {
            display: inline-block;
            margin-top: 18px;
            background: var(--teal);
            color: #fff;
            text-decoration: none;
            padding: 9px 20px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .latest-card a.link-btn:hover {
            background: var(--teal-dark);
        }

        .no-upcoming {
            color: #cfd9dc;
            font-size: 0.95rem;
            padding: 10px 0;
        }

        /* ---- Past Events section ---- */
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

        .past-header form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        select#yearFilter {
            padding: 9px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
            color: var(--text);
            font-size: 0.9rem;
            font-family: inherit;
        }

        .past-header select {
            padding: 9px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
            color: var(--text);
            font-size: 0.9rem;
            font-family: inherit;
        }

        .past-header button[type="submit"] {
            padding: 9px 18px;
            border: none;
            border-radius: 6px;
            background: var(--teal);
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .past-header button[type="submit"]:hover {
            background: var(--teal-dark);
        }

        .event-card {
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

        .event-card .info h3 {
            font-size: 1.05rem;
            margin-bottom: 6px;
        }

        .event-card .info p.desc {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .event-card .info .meta {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .event-card .info .meta span {
            margin-right: 16px;
        }

        .event-card .posted-by {
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

        /* ---------- Responsive ---------- */
        @media (max-width: 760px) {
            .latest-banner {
                padding: 36px 16px 44px;
            }

            .latest-card {
                padding: 20px 20px;
            }

            .latest-card h2 {
                font-size: 1.3rem;
            }

            .past-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .past-header form {
                width: 100%;
            }

            .past-header select {
                flex: 1;
            }

            .event-card {
                flex-direction: column;
                gap: 8px;
            }

            .event-card .posted-by {
                align-self: flex-start;
            }
        }
    </style>
</head>

<body>
    <?php include "./navbar.php"; ?>
    <!-- ===== Latest Event Banner ===== -->
    <section class="latest-banner">
        <div class="container">
            <div class="label">Latest Event Information</div>

            <?php if ($latestEvent): ?>
                <div class="latest-card">
                    <h2><?= htmlspecialchars($latestEvent['event_name']) ?></h2>

                    <?php if (!empty($latestEvent['event_desc'])): ?>
                        <p class="desc"><?= htmlspecialchars($latestEvent['event_desc']) ?></p>
                    <?php endif; ?>

                    <div class="latest-meta">
                        <span><strong>Date</strong><?= date('d M Y', strtotime($latestEvent['event_date'])) ?></span>

                        <?php if (!empty($latestEvent['event_time'])): ?>
                            <span><strong>Time</strong><?= date('h:i A', strtotime($latestEvent['event_time'])) ?></span>
                        <?php endif; ?>

                        <span><strong>Venue</strong><?= htmlspecialchars($latestEvent['event_venue']) ?></span>
                        <span><strong>Posted By</strong><?= htmlspecialchars($latestEvent['admin_name']) ?></span>
                    </div>

                    <?php if (!empty($latestEvent['event_additional_links'])): ?>
                        <a href="<?= htmlspecialchars($latestEvent['event_additional_links']) ?>" class="link-btn" target="_blank" rel="noopener">View Details / Register</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="no-upcoming">No upcoming events at the moment. Check back soon.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== Past Events Section ===== -->
    <section class="past-section">
        <div class="container">
            <div class="past-header">
                <h2>Past Events</h2>

                <!-- Year filter dropdown — logic to be wired up separately -->
                <form method="GET">
                    <select name="fyear">
                        <option value="all">-- Show All --</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= htmlspecialchars($year); ?>">
                                <?= htmlspecialchars($year); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">
                        Filter
                    </button>
                </form>
            </div>

            <?php if (isset($pastEvents) && count($pastEvents) > 0): ?>
                <?php foreach ($pastEvents as $event): ?>
                    <div class="event-card" data-year="<?= date('Y', strtotime($event['event_date'])) ?>">
                        <div class="info">
                            <h3><?= htmlspecialchars($event['event_name']) ?></h3>

                            <?php if (!empty($event['event_desc'])): ?>
                                <p class="desc"><?= htmlspecialchars($event['event_desc']) ?></p>
                            <?php endif; ?>

                            <div class="meta">
                                <span>&#128197; <?= date('d M Y', strtotime($event['event_date'])) ?></span>
                                <span>&#128205; <?= htmlspecialchars($event['event_venue']) ?></span>
                            </div>
                        </div>
                        <div class="posted-by">Posted by <?= htmlspecialchars($event['admin_name']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">No past events to show yet.</div>
            <?php endif; ?>
        </div>
    </section>

    <!-- footer section -->
    <?php include "./footer.php" ?>
</body>

</html>