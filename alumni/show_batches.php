<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../utills/db_conn.php';

if (!isset($conn)) {
    die("Database connection is not established");
}

if (!isset($_SESSION["alumni_id"])) {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT m.alumni_id, m.alumni_name, m.branch, m.passout_year,
               p.alumni_company, p.alumni_designation, p.alumni_city,
               p.alumni_linkedin_link, p.alumni_phone_no, m.email
        FROM alumni_student_master m
        LEFT JOIN alumni_profile p ON p.alumni_id = m.alumni_id
        WHERE m.is_registered = 1
        ORDER BY m.passout_year DESC, m.alumni_name ASC";

$result = mysqli_query($conn, $sql);

// Read filters from the URL, validated before use.
$filterBatch = null;
if (!empty($_GET['batch']) && filter_var($_GET['batch'], FILTER_VALIDATE_INT)) {
    $filterBatch = (int) $_GET['batch'];
}

$allowedBranches = ['CE', 'IT'];
$filterBranch = null;
if (!empty($_GET['branch']) && in_array($_GET['branch'], $allowedBranches)) {
    $filterBranch = $_GET['branch'];
}

$students = [];
$batches = [];

while ($row = mysqli_fetch_assoc($result)) {

    // mapping the student branch
    switch ($row["branch"]) {
        case 'Computer Engineering':
            $row["branch"] = "CE";
            break;
        case 'Information Technology':
            $row["branch"] = "IT";
            break;
        default:
            break;
    }

    if (!in_array($row['passout_year'], $batches)) {
        $batches[] = $row['passout_year'];
    }

    // Apply both filters together — a row is included only if it matches
    // every filter that is currently set.
    $matchesBatch  = ($filterBatch === null)  || ((int) $row['passout_year'] === $filterBatch);
    $matchesBranch = ($filterBranch === null) || ($row['branch'] === $filterBranch);

    if ($matchesBatch && $matchesBranch) {
        $students[] = $row;
    }
}

rsort($batches); // most recent batch first in the dropdown

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Batch | GEC Modasa Alumni Portal</title>
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
            color: #9fd6c6;
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

        .filter-bar .result-count {
            margin-left: auto;
            font-size: 0.85rem;
            color: var(--muted);
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
            <div class="label">Alumni Directory</div>
            <h1>Find Your Batch</h1>
            <p>Search fellow alumni by batch year or department and reconnect with familiar faces from GEC Modasa.</p>
        </div>
    </section>

    <!-- ===== Filter Bar ===== -->
    <section class="filter-bar">
        <div class="container">
            <form method="GET" action="">
                <div>
                    <label for="batch">Batch</label>
                    <select id="batch" name="batch" onchange="this.form.submit()">
                        <option value="">All Batches</option>
                        <?php foreach ($batches as $year): ?>
                            <option value="<?= htmlspecialchars($year) ?>" <?= ($filterBatch === (int) $year) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="branch">Department</label>
                    <select id="branch" name="branch" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        <option value="CE" <?= $filterBranch === 'CE' ? 'selected' : '' ?>>CE</option>
                        <option value="IT" <?= $filterBranch === 'IT' ? 'selected' : '' ?>>IT</option>
                    </select>
                </div>

                <div class="result-count"><?= count($students) ?> alumni found</div>
            </form>
        </div>
    </section>

    <!-- ===== Directory Cards ===== -->
    <section class="directory-section">
        <div class="container">

            <?php if (count($students) > 0): ?>
                <div class="cards-grid">
                    <?php foreach ($students as $s): ?>
                        <div class="student-card">
                            <div class="avatar-circle"><?= strtoupper(substr($s['alumni_name'], 0, 1)) ?></div>

                            <h3><?= htmlspecialchars($s['alumni_name']) ?></h3>
                            <div class="batch-branch">Batch <?= htmlspecialchars($s['passout_year']) ?> &middot; <?= htmlspecialchars($s['branch']) ?></div>

                            <?php if (!empty($s['alumni_company'])): ?>
                                <div class="detail-row"><strong><?= htmlspecialchars($s['alumni_designation'] ?: 'Working at') ?></strong> &middot; <?= htmlspecialchars($s['alumni_company']) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($s['city'])): ?>
                                <div class="detail-row">&#128205; <?= htmlspecialchars($s['city']) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($s["email"])): ?>
                                <div class="detail-row">&#9993; <?= htmlspecialchars($s['email']) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($s['alumni_linkedin_link'])): ?>
                                <a href="<?= htmlspecialchars($s['alumni_linkedin_link']) ?>" class="linkedin-link" target="_blank" rel="noopener">View Linkedin &rarr;</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">No alumni found for the selected filters.</div>
            <?php endif; ?>

        </div>
    </section>
    <!-- Add Footer in file -->
    <?php include "./footer.php" ?>
</body>

</html>