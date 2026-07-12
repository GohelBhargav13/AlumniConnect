<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$alumni_id = $_SESSION["alumni_id"] ?? null;
include "./alumni_favicon.php"; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GEC Modasa Alumni Portal</title>
  <link rel="icon" type="image/png" href="../assets/gec_favicon.png">
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
  font-weight: 700;
}

a {
  text-decoration: none;
  color: inherit;
}

ul {
  list-style: none;
}

img {
  max-width: 100%;
  display: block;
}

.container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 20px;
}

a:focus-visible,
button:focus-visible {
  outline: 3px solid var(--teal);
  outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  * {
    transition: none !important;
  }
}

/* ---------- Header / Navbar ---------- */
header {
  background: #ffffff;
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  z-index: 50;
}

.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 20px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
}

.brand-badge {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--navy);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: Georgia, serif;
  font-weight: 700;
  font-size: 15px;
  flex-shrink: 0;
}

.brand-text strong {
  display: block;
  font-size: 16px;
  color: var(--navy);
}

.brand-text span {
  display: block;
  font-size: 12px;
  color: var(--muted);
}

nav.main-nav {
  display: flex;
  align-items: center;
  gap: 26px;
}

nav.main-nav a {
  font-size: 14.5px;
  color: var(--text);
  padding: 6px 2px;
  border-bottom: 2px solid transparent;
  transition: border-color 0.15s ease, color 0.15s ease;
}

nav.main-nav a:hover {
  color: var(--teal-dark);
  border-bottom-color: var(--teal);
}

.auth-links {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn {
  display: inline-block;
  padding: 8px 18px;
  border-radius: 5px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  border: 1.5px solid transparent;
  transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}

.btn-outline {
  border-color: var(--navy);
  color: var(--navy);
  background: transparent;
}

.btn-outline:hover {
  background: var(--navy);
  color: #fff;
}

.btn-solid {
  background: var(--teal);
  color: #fff;
}

.btn-solid:hover {
  background: var(--teal-dark);
}

.menu-toggle {
  display: none;
  background: none;
  border: 1px solid var(--border);
  border-radius: 5px;
  padding: 7px 10px;
  font-size: 18px;
  cursor: pointer;
  color: var(--navy);
}

/* ---------- Hero ---------- */
.hero {
  background: linear-gradient(160deg, var(--navy) 0%, var(--navy-dark) 100%);
  color: #fff;
  padding: 70px 20px 90px;
  text-align: center;
}

.hero h1 {
  color: #fff;
  font-size: 2.3rem;
  max-width: 700px;
  margin: 0 auto 16px;
}

.hero p {
  color: #cfd9dc;
  max-width: 560px;
  margin: 0 auto 30px;
  font-size: 1.02rem;
}

.hero-actions {
  display: flex;
  justify-content: center;
  gap: 14px;
  flex-wrap: wrap;
}

.btn-lg {
  padding: 12px 26px;
  font-size: 15px;
}

.btn-white {
  background: #fff;
  color: var(--navy);
}

.btn-white:hover {
  background: #e7ece9;
}

.btn-ghost {
  border: 1.5px solid rgba(255, 255, 255, 0.6);
  color: #fff;
  background: transparent;
}

.btn-ghost:hover {
  background: rgba(255, 255, 255, 0.12);
}

/* ---------- Steps ---------- */
.steps-section {
  padding: 64px 20px;
}

.section-heading {
  text-align: center;
  max-width: 560px;
  margin: 0 auto 44px;
}

.section-heading h2 {
  font-size: 1.7rem;
  margin-bottom: 10px;
}

.section-heading p {
  color: var(--muted);
  font-size: 0.98rem;
}

.steps {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.step {
  text-align: center;
  padding: 0 10px;
}

.step-number {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: var(--teal);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  margin: 0 auto 16px;
  font-family: Georgia, serif;
}

.step h3 {
  font-size: 1.05rem;
  margin-bottom: 8px;
}

.step p {
  color: var(--muted);
  font-size: 0.92rem;
}

/* ---------- Quick access cards ---------- */
.quick-access {
  background: #fff;
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  padding: 64px 20px;
}

.cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 26px 22px;
  transition: box-shadow 0.15s ease, transform 0.15s ease;
}

.card:hover {
  box-shadow: 0 6px 18px rgba(27, 58, 75, 0.09);
  transform: translateY(-2px);
}

.card-icon {
  width: 38px;
  height: 38px;
  border-radius: 6px;
  background: #e8f1ee;
  color: var(--teal-dark);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  margin-bottom: 14px;
}

.card h3 {
  font-size: 1.05rem;
  margin-bottom: 8px;
}

.card p {
  color: var(--muted);
  font-size: 0.9rem;
  margin-bottom: 14px;
}

.card a {
  color: var(--teal-dark);
  font-size: 0.88rem;
  font-weight: 600;
}

.card a:hover {
  text-decoration: underline;
}

/* ---------- About strip ---------- */
.about-strip {
  padding: 60px 20px;
  text-align: center;
}

.about-strip .container {
  max-width: 720px;
}

.about-strip h2 {
  font-size: 1.5rem;
  margin-bottom: 14px;
}

.about-strip p {
  color: var(--muted);
  font-size: 0.98rem;
}

/* ---------- Footer ---------- */
footer {
  background: var(--navy-dark);
  color: #cfd9dc;
  padding: 46px 20px 22px;
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr 1fr;
  gap: 30px;
  margin-bottom: 30px;
}

.footer-grid h4 {
  color: #fff;
  font-size: 0.95rem;
  margin-bottom: 12px;
  font-family: Georgia, serif;
}

.footer-grid ul li {
  margin-bottom: 8px;
}

.footer-grid ul li a {
  font-size: 0.88rem;
  color: #b9c4c8;
}

.footer-grid ul li a:hover {
  color: #fff;
}

.footer-about p {
  font-size: 0.88rem;
  color: #b9c4c8;
  max-width: 320px;
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  padding-top: 18px;
  text-align: center;
  font-size: 0.82rem;
  color: #93a0a5;
}

/* ---------- Responsive ---------- */
@media (max-width: 860px) {

  .steps,
  .cards {
    grid-template-columns: 1fr;
  }

  .footer-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 760px) {
  nav.main-nav {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border-bottom: 1px solid var(--border);
    flex-direction: column;
    align-items: flex-start;
    gap: 0;
    padding: 6px 20px 14px;
  }

  nav.main-nav.open {
    display: flex;
  }

  nav.main-nav a {
    width: 100%;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
  }

  .auth-links {
    display: none;
  }

  .auth-links.open {
    display: flex;
    position: absolute;
    top: calc(100% + 190px);
    left: 20px;
    right: 20px;
  }

  .menu-toggle {
    display: inline-block;
  }

  .hero h1 {
    font-size: 1.7rem;
  }
}
  </style>
</head>

<body>
 	<?php include("./navbar.php"); ?>
  <section class="hero" id="home">
    <h1>Stay connected with GEC Modasa, long after graduation.</h1>
    <p>One simple place for alumni and the college to stay in touch — find batchmates, share opportunities, and keep up with campus events.</p>
  </section>

  <section class="steps-section">
    <div class="container">
      <div class="section-heading">
        <h2>How the portal works</h2>
        <p>A straightforward process, built for alumni who studied at GEC Modasa.</p>
      </div>
      <div class="steps">
        <div class="step">
          <div class="step-number">1</div>
          <h3>Register with your college email</h3>
          <p>If your details were already shared by the college office, registering only takes a minute.</p>
        </div>
        <div class="step">
          <div class="step-number">2</div>
          <h3>Complete your profile</h3>
          <p>Add your current company, city, and LinkedIn so batchmates and juniors can find you.</p>
        </div>
        <div class="step">
          <div class="step-number">3</div>
          <h3>Stay in the loop</h3>
          <p>Browse the alumni directory, post opportunities, and check college events anytime.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="quick-access">
    <div class="container">
      <div class="section-heading">
        <h2>What you can do here</h2>
        <p>A few simple tools — nothing more than the college actually needs.</p>
      </div>
      <div class="cards">

        <div class="card" id="directory">
          <div class="card-icon">&#9906;</div>
          <h3>Alumni Directory</h3>
          <p>Search fellow alumni by batch, branch, or city, and reconnect with old classmates.</p>
          <a href="./show_batches.php">Browse directory &rarr;</a>
        </div>

        <div class="card" id="opportunities">
          <div class="card-icon">&#128188;</div>
          <h3>Job &amp; Internship Board</h3>
          <p>Alumni share openings at their companies. Students can view every listing — no login needed.</p>
          <a href="#opportunities">View opportunities &rarr;</a>
        </div>

        <div class="card" id="events">
          <div class="card-icon">&#128197;</div>
          <h3>Events &amp; Announcements</h3>
          <p>Reunions, guest lectures, and official notices from the college, posted by the admin office.</p>
          <a href="./show_events.php">See what's coming up &rarr;</a>
        </div>

      </div>
    </div>
  </section>

  <section class="about-strip" id="announcements">
    <div class="container">
      <h2>Built for GEC Modasa, kept simple on purpose</h2>
      <p>This portal exists for one reason: to keep alumni connected with the college and with each other. No clutter, no unnecessary features — just a directory, a place to share opportunities, and a way to stay informed about what's happening on campus.</p>
    </div>
  </section>

  <!-- Footer section added in file  -->
  <?php include "./footer.php" ?>

  <script src="./scripts/script.js"></script>
</body>

</html>