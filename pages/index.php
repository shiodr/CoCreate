<?php
require_once __DIR__ . '/../includes/session.php';
$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';
$pageTitle = 'Build With Better Teammates';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="hero">
  <div class="hero-copy">
    <p class="eyebrow">Teamwork for creative builders</p>
    <h1>CoCreate</h1>
    <p class="hero-text">Turn unfinished ideas into real projects. Find teammates by skill, show what you are building, and manage every collaboration request in one focused workspace.</p>
    <div class="hero-actions">
      <a class="btn btn-primary" href="<?= is_logged_in() ? 'dashboard.php' : 'register.php' ?>">Get Started</a>
      <a class="btn btn-secondary" href="login.php">Login</a>
      <a class="btn btn-ghost" href="register.php">Register</a>
    </div>
    <div class="hero-metrics" aria-label="CoCreate highlights">
      <div><strong>Skill-first</strong><span>Match by what people can build</span></div>
      <div><strong>Project-ready</strong><span>Share context, needs, and status</span></div>
      <div><strong>Request flow</strong><span>Keep join requests organized</span></div>
    </div>
  </div>
  <div class="hero-panel" aria-label="Featured collaboration preview">
    <div class="hero-panel-top">
      <span>Live project board</span>
      <strong>3 open</strong>
    </div>
    <div class="mini-card strong">
      <span class="mini-kicker">Web App</span>
      <strong>Portfolio Builder</strong>
      <p>Needs PHP, CSS, UI design</p>
    </div>
    <div class="mini-card">
      <span class="mini-kicker">Community Tool</span>
      <strong>Campus Event Finder</strong>
      <p>Search filters, MySQL, UX research</p>
    </div>
    <div class="mini-card accent">
      <span class="mini-kicker">Creative Media</span>
      <strong>Indie Music Video</strong>
      <p>Writing, editing, art direction</p>
    </div>
    <div class="hero-collab-row">
      <span class="avatar-dot">M</span>
      <span class="avatar-dot">L</span>
      <span class="avatar-dot">A</span>
      <p>Creators are already forming teams</p>
    </div>
  </div>
</section>

<section class="section feature-section">
  <div class="section-head">
    <div>
      <p class="eyebrow">How it works</p>
      <h2>Create, discover, collaborate</h2>
    </div>
  </div>
  <div class="cards-grid">
    <article class="card feature-card">
      <span class="feature-badge">01</span>
      <h3>Post a Project</h3>
      <p>Describe your idea, category, status, and the skills you need from future teammates.</p>
    </article>
    <article class="card feature-card">
      <span class="feature-badge">02</span>
      <h3>Find Teammates</h3>
      <p>Browse projects by keyword, status, or required skill so the right people can connect faster.</p>
    </article>
    <article class="card feature-card">
      <span class="feature-badge">03</span>
      <h3>Manage Requests</h3>
      <p>Accept or reject applicants from your project dashboard and keep every request organized.</p>
    </article>
  </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
