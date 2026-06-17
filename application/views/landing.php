<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=overlays-content">
  <title>Public Employment Service Office Davao Oriental</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/universal.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/landing.css') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png'); ?>" />

</head>

<body>
  <?php
  $isLoggedIn = !empty($is_logged_in);
  $roleValue = isset($role) ? $role : '';
  $roleSlug = is_string($roleValue) ? strtolower($roleValue) : '';
  $dashboardRoute = 'dashboard/user';
  if ($roleSlug === 'admin') {
    $dashboardRoute = 'dashboard/admin';
  } elseif ($roleSlug === 'worker') {
    $dashboardRoute = 'dashboard/worker';
  } elseif ($roleSlug === 'client') {
    $dashboardRoute = 'dashboard/client';
  } elseif ($roleSlug === 'tesda_admin') {
    $dashboardRoute = 'dashboard/tesda';
  }
  $roleLabelText = $roleSlug !== '' ? ucwords(str_replace('_', ' ', $roleSlug)) : 'Guest';
  $roleLabelSafe = htmlspecialchars($roleLabelText, ENT_QUOTES, 'UTF-8');
  $firstNameValue = isset($first_name) ? (string)$first_name : '';
  $firstNameSafe = $firstNameValue !== '' ? htmlspecialchars($firstNameValue, ENT_QUOTES, 'UTF-8') : '';

  $quickStats = is_array($quick_stats ?? null) ? $quick_stats : [];
  $statWorkers = max(0, (int)($quickStats['workers'] ?? 0));
  $statEmployers = max(0, (int)($quickStats['employers'] ?? 0));
  $statJobs = max(0, (int)($quickStats['open_jobs'] ?? 0));
  $statHotlines = max(0, (int)($quickStats['active_hotlines'] ?? 0));
  $statWorkersFmt = number_format($statWorkers);
  $statEmployersFmt = number_format($statEmployers);
  $statJobsFmt = number_format($statJobs);
  $statHotlinesFmt = number_format($statHotlines);

  $toolkitData = [
    'worker' => [
      'label'   => 'Workers',
      'summary' => 'Build your PESO-ready profile and get matched with local employers.',
      'links'   => [
        [
          'title' => 'Complete worker profile',
          'desc'  => 'Add skills, IDs, and certifications so PESO can endorse you faster.',
          'href'  => site_url('profile'),
          'icon'  => 'mdi-account-hard-hat',
        ],
        [
          'title' => 'Follow community feed',
          'desc'  => 'Join announcements from PESO officers and fellow workers.',
          'href'  => site_url('worker/feed'),
          'icon'  => 'mdi-message-bulleted',
        ],
        [
          'title' => 'Search open jobs',
          'desc'  => 'Filter opportunities by skill, barangay, or company.',
          'href'  => site_url('search'),
          'icon'  => 'mdi-magnify',
        ],
      ],
    ],
    'employer' => [
      'label'   => 'Employers',
      'summary' => 'Post openings, screen applicants, and coordinate interviews.',
      'links'   => [
        [
          'title' => 'Post a new job order',
          'desc'  => 'Create detailed job orders and reach verified workers in minutes.',
          'href'  => site_url('projects/create'),
          'icon'  => 'mdi-briefcase-plus-outline',
        ],
        [
          'title' => 'Review applicant pipeline',
          'desc'  => 'Track submissions, shortlist talent, and manage interviews.',
          'href'  => site_url('dashboard/client'),
          'icon'  => 'mdi-view-dashboard-outline',
        ],
        [
          'title' => 'Message PESO support',
          'desc'  => 'Coordinate caravans or job fairs directly with PESO staff.',
          'href'  => site_url('messages/start'),
          'icon'  => 'mdi-forum-outline',
        ],
      ],
    ],
    'school' => [
      'label'   => 'Schools',
      'summary' => 'Manage student immersions and collaborate on placements.',
      'links'   => [
        [
          'title' => 'Manage student placements',
          'desc'  => 'Submit OJT requests and monitor deployment updates.',
          'href'  => site_url('school-admin'),
          'icon'  => 'mdi-school-outline',
        ],
        [
          'title' => 'Upload student rosters',
          'desc'  => 'Send class lists in bulk for rapid TESDA endorsement.',
          'href'  => site_url('school-admin/bulk'),
          'icon'  => 'mdi-upload',
        ],
        [
          'title' => 'Coordinate with employers',
          'desc'  => 'Connect with partner companies for internships and immersion.',
          'href'  => site_url('messages/start'),
          'icon'  => 'mdi-account-group-outline',
        ],
      ],
    ],
    'tesda' => [
      'label'   => 'TESDA',
      'summary' => 'Align training programs with employer demand across Davao Oriental.',
      'links'   => [
        [
          'title' => 'View TESDA dashboard',
          'desc'  => 'Monitor program slots, assessments, and certifications.',
          'href'  => site_url('dashboard/tesda'),
          'icon'  => 'mdi-chart-timeline-variant',
        ],
        [
          'title' => 'Bulk upload trainees',
          'desc'  => 'Sync graduate data with the PESO system in one upload.',
          'href'  => site_url('tesda/workers/upload'),
          'icon'  => 'mdi-file-upload-outline',
        ],
        [
          'title' => 'Share training results',
          'desc'  => 'Update PESO on completers so employers find qualified talent.',
          'href'  => site_url('messages/start'),
          'icon'  => 'mdi-file-document-edit-outline',
        ],
      ],
    ],
    'peso' => [
      'label'   => 'PESO',
      'summary' => 'Keep the city-wide workforce and employers aligned every day.',
      'links'   => [
        [
          'title' => 'Manage PESO dashboard',
          'desc'  => 'Publish job orders, edit details, and control visibility.',
          'href'  => site_url('dashboard/peso'),
          'icon'  => 'mdi-clipboard-text-outline',
        ],
        [
          'title' => 'Bulk upload workers',
          'desc'  => 'Import verified worker lists directly into the database.',
          'href'  => site_url('admin/workers/upload'),
          'icon'  => 'mdi-database-import-outline',
        ],
        [
          'title' => 'Update public hotlines',
          'desc'  => 'Keep jobseekers informed with the latest assistance numbers.',
          'href'  => site_url('hotlines'),
          'icon'  => 'mdi-phone-classic',
        ],
      ],
    ],
  ];

  $toolkitDefault = 'worker';
  if ($roleSlug === 'worker') {
    $toolkitDefault = 'worker';
  } elseif ($roleSlug === 'client') {
    $toolkitDefault = 'employer';
  } elseif (strpos($roleSlug, 'school') !== false) {
    $toolkitDefault = 'school';
  } elseif (strpos($roleSlug, 'tesda') !== false) {
    $toolkitDefault = 'tesda';
  } elseif (strpos($roleSlug, 'peso') !== false || $roleSlug === 'admin') {
    $toolkitDefault = 'peso';
  }
  if (!isset($toolkitData[$toolkitDefault])) {
    $toolkitDefault = 'worker';
  }
  ?>

  <header class="landing-header" id="home">
    <div class="landing-container nav-container">
      <a href="<?= site_url(); ?>" class="brand" aria-label="Public Employment Service Office Home">
        <img src="<?= base_url('assets/images/logo.png'); ?>" alt="Public Employment Service Office logo">
        <div class="brand-copy">
          <span>Public Employment Service Office</span>
          <small>Davao Oriental</small>
        </div>
      </a>

      <nav class="nav-links" aria-label="Primary navigation">
        <a href="#jobs-latest">Jobs</a>
        <a href="#why-choose">Why Public Employment Service Office?</a>
        <a href="#services">Services</a>
        <a href="#partners">Partners</a>
      </nav>

      <div class="nav-actions">
        <?php if ($isLoggedIn): ?>
          <a class="btn btn-ghost btn-sm" href="<?= site_url('auth/logout'); ?>">Logout</a>
          <a class="btn btn-primary btn-sm" href="<?= site_url($dashboardRoute); ?>">Go to dashboard</a>
        <?php else: ?>
          <a class="btn btn-ghost btn-sm" href="<?= site_url('auth/login'); ?>">Log in</a>
          <a class="btn btn-primary btn-sm" href="<?= site_url('auth/signup'); ?>">Register</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="landing-container hero-grid">
      <div class="hero-copy">
        <div class="hero-seal">
          <img src="<?= base_url('assets/images/logo.png'); ?>" alt="Public Employment Service Office seal">
        </div>

        <?php if ($isLoggedIn): ?>
          <span class="hero-eyebrow"><i class="mdi mdi-account-circle-outline"></i> Welcome back<?= $firstNameSafe !== '' ? ', ' . $firstNameSafe : ''; ?></span>
          <h1 class="hero-title">Public Employment Service Office Portal</h1>
          <p class="hero-text">
            Continue managing applications, updates, and PESO announcements in your dashboard.
          </p>
          <div class="hero-cta">
            <a class="btn btn-primary" href="<?= site_url($dashboardRoute); ?>">Go to dashboard</a>
            <a class="btn btn-outline" href="#jobs-latest">View latest jobs</a>
          </div>
        <?php else: ?>
          <span class="hero-eyebrow"><i class="mdi mdi-map-marker-outline"></i> Davao Oriental</span>
          <h1 class="hero-title">Public Employment Service Office Portal</h1>
          <p class="hero-text">
            Sign up for free to manage applications, follow job fairs, and receive tailored alerts from Davao Oriental PESO.
          </p>
          <div class="hero-cta">
            <a class="btn btn-primary" href="<?= site_url('auth/signup'); ?>">Register now</a>
            <a class="btn btn-outline" href="<?= site_url('auth/login'); ?>">Log in</a>
          </div>
        <?php endif; ?>

        <ul class="hero-points">
          <li><i class="mdi mdi-check-circle-outline"></i> Daily updates direct from Davao Oriental PESO</li>
          <li><i class="mdi mdi-check-circle-outline"></i> TESDA-certified trainings and local events</li>
          <li><i class="mdi mdi-check-circle-outline"></i> Support for workers, employers, and schools</li>
        </ul>
      </div>

      <!-- kept for compatibility but hidden -->
      <div class="hero-panel">
        <div class="hero-card"></div>
      </div>
    </div>
  </header>

  <main>
    <section class="landing-jobs" id="jobs-latest">
      <div class="landing-container">
        <div class="jobs-shell">
          <header class="jobs-header">
            <div>
              <h2 class="section-head">Latest job vacancies</h2>
              <p class="section-subhead">Stay up to date with fresh postings from the Davao Oriental Public Employment Service Office.</p>
            </div>
            <div class="jobs-actions">
              <?php if ($isLoggedIn): ?>
                <span class="muted small">Signed in as <?= $roleLabelSafe; ?>.</span>
                <a class="btn btn-outline btn-sm" href="<?= site_url($dashboardRoute); ?>">Manage saved jobs</a>
              <?php else: ?>
                <a class="btn btn-outline btn-sm" href="<?= site_url('auth/signup'); ?>">Save these jobs</a>
                <a class="btn btn-ghost btn-sm" href="<?= site_url('auth/login'); ?>">Login</a>
              <?php endif; ?>
            </div>
          </header>

          <div id="jobs-carousel" class="jobs-carousel" style="display:none;">
            <div class="jobs-track">
              <div class="jobs-inner" id="jobs-inner"></div>
              <div class="carousel-nav">
                <button type="button" class="nav-btn" id="prevD" aria-label="Previous">
                  <i class="mdi mdi-chevron-left"></i>
                </button>
                <button type="button" class="nav-btn" id="nextD" aria-label="Next">
                  <i class="mdi mdi-chevron-right"></i>
                </button>
              </div>
            </div>
            <div class="jobs-dots" id="jobs-dots" aria-label="Carousel pagination"></div>
          </div>
        </div>
      </div>
    </section>

    <section class="landing-insights" id="insights">
      <div class="landing-container">
        <div class="insights-shell">
          <div>
            <span class="insights-eyebrow">Live snapshot</span>
            <h2 class="section-head">PESO opportunities right now</h2>
            <p class="section-subhead">Numbers refresh as soon as city job orders go public.</p>
          </div>

          <div class="insights-grid">
            <article class="insight-card">
              <span class="insight-label">Open job orders</span>
              <div class="insight-value" id="insight-total-jobs">--</div>
              <span class="insight-note" id="insight-total-jobs-note">Waiting for updates...</span>
            </article>

            <article class="insight-card">
              <span class="insight-label">Active locations</span>
              <div class="insight-value" id="insight-total-locations">--</div>
              <span class="insight-note" id="insight-location-top">--</span>
            </article>

            <article class="insight-card">
              <span class="insight-label">Salary transparency</span>
              <div class="insight-value" id="insight-pay-count">--</div>
              <span class="insight-note" id="insight-pay-percent">--</span>
            </article>

            <article class="insight-card">
              <span class="insight-label">Newest posting</span>
              <div class="insight-value" id="insight-last-updated">--</div>
              <span class="insight-note" id="insight-last-updated-note">--</span>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section class="landing-stats" id="why-choose">
      <div class="landing-container">
        <h2 class="section-head">Community impact across Public Employment Service Office</h2>
        <p class="section-subhead">Figures update from the live database so you always know how active the platform is.</p>

        <div class="stats-grid">
          <div class="stat-card">
            <strong><?= $statWorkers > 0 ? $statWorkersFmt . '+' : '--'; ?></strong>
            <span>Registered workers building their careers</span>
          </div>
          <div class="stat-card">
            <strong><?= $statEmployers > 0 ? $statEmployersFmt . '+' : '--'; ?></strong>
            <span>Verified employers and partner organizations</span>
          </div>
          <div class="stat-card">
            <strong><?= $statJobs > 0 ? $statJobsFmt : '--'; ?></strong>
            <span>Public job orders currently open</span>
          </div>
          <div class="stat-card">
            <strong><?= $statHotlines > 0 ? $statHotlinesFmt : '--'; ?></strong>
            <span>Active PESO hotlines ready to assist</span>
          </div>
        </div>
      </div>
    </section>

    <section class="landing-toolkit" id="role-toolkit">
      <div class="landing-container">
        <div class="toolkit-shell" data-default-role="<?= htmlspecialchars($toolkitDefault, ENT_QUOTES, 'UTF-8'); ?>">
          <div>
            <h2 class="section-head">Toolkits for every account type</h2>
            <p class="section-subhead">Switch tabs to discover the workflows available to you inside Public Employment Service Office.</p>
          </div>

          <div class="toolkit-tabs" role="tablist" aria-label="Role toolkits">
            <?php foreach ($toolkitData as $slug => $conf): ?>
              <?php
              $labelSafe = htmlspecialchars($conf['label'], ENT_QUOTES, 'UTF-8');
              $slugSafe = htmlspecialchars($slug, ENT_QUOTES, 'UTF-8');
              $isActive = $slug === $toolkitDefault ? ' active' : '';
              ?>
              <button type="button" class="toolkit-tab<?= $isActive; ?>" data-role="<?= $slugSafe; ?>"><?= $labelSafe; ?></button>
            <?php endforeach; ?>
          </div>

          <div class="toolkit-panels">
            <?php foreach ($toolkitData as $slug => $conf): ?>
              <?php
              $panelActive = $slug === $toolkitDefault ? ' active' : '';
              $slugSafe = htmlspecialchars($slug, ENT_QUOTES, 'UTF-8');
              $summarySafe = htmlspecialchars($conf['summary'], ENT_QUOTES, 'UTF-8');
              ?>
              <div class="toolkit-panel<?= $panelActive; ?>" data-role="<?= $slugSafe; ?>">
                <p class="toolkit-summary"><?= $summarySafe; ?></p>

                <ul class="toolkit-list">
                  <?php foreach ($conf['links'] as $item): ?>
                    <?php
                    $titleSafe = htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
                    $descSafe = htmlspecialchars($item['desc'], ENT_QUOTES, 'UTF-8');
                    $hrefSafe = htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8');
                    $iconSafe = htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <li>
                      <a class="toolkit-link" href="<?= $hrefSafe; ?>">
                        <span class="toolkit-icon"><i class="mdi <?= $iconSafe; ?>"></i></span>
                        <span>
                          <strong><?= $titleSafe; ?></strong>
                          <small><?= $descSafe; ?></small>
                        </span>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="landing-features" id="services">
      <div class="landing-container">
        <h2 class="section-head">Everything you need to land the role</h2>
        <p class="section-subhead">Whether you are a worker, employer, or school partner, Public Employment Service Office keeps everyone in sync.</p>

        <div class="feature-grid">
          <article class="feature-card">
            <span class="feature-icon"><i class="mdi mdi-briefcase-check"></i></span>
            <h3>Guided applications</h3>
            <p>Track requirements, upload documents, and monitor interview slots in one place.</p>
          </article>

          <article class="feature-card">
            <span class="feature-icon"><i class="mdi mdi-school"></i></span>
            <h3>TESDA-aligned trainings</h3>
            <p>Discover accredited programs and reserve slots for upskilling or reskilling.</p>
          </article>

          <article class="feature-card">
            <span class="feature-icon"><i class="mdi mdi-account-group"></i></span>
            <h3>Community support</h3>
            <p>Connect with local employers, PESO officers, and fellow workers for timely updates.</p>
          </article>

          <article class="feature-card">
            <span class="feature-icon"><i class="mdi mdi-chart-line"></i></span>
            <h3>Insights for employers</h3>
            <p>Manage postings, respond to applicants, and review hiring analytics from your dashboard.</p>
          </article>
        </div>
      </div>
    </section>

    <section class="landing-journey" id="how-it-works">
      <div class="landing-container">
        <h2 class="section-head">How your Public Employment Service Office journey works</h2>
        <p class="section-subhead">From profile setup to hiring, follow these simple steps to make the most of the platform.</p>

        <div class="journey-grid">
          <div class="journey-step">
            <span class="step-index">Step 01</span>
            <h3>Create or update your profile</h3>
            <p>Complete your details so PESO officers can match you with jobs or trainings faster.</p>
          </div>

          <div class="journey-step">
            <span class="step-index">Step 02</span>
            <h3>Browse active vacancies</h3>
            <p>Filter openings, save the ones you like, and receive notifications for urgent hiring.</p>
          </div>

          <div class="journey-step">
            <span class="step-index">Step 03</span>
            <h3>Connect and get hired</h3>
            <p>Attend orientations, submit documents, and coordinate with employers right inside your dashboard.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="landing-partners" id="partners">
      <div class="landing-container">
        <h2 class="section-head">Local partners backing your success</h2>
        <p class="section-subhead">We work closely with government offices, schools, and private employers to provide trusted opportunities.</p>

        <div class="partner-grid">
          <div class="partner-card">
            <span class="partner-badge">Government</span>
            <h3>Davao Oriental Government</h3>
            <p>Municipal offices offering roles in frontline services, administration, and community projects.</p>
          </div>

          <div class="partner-card">
            <span class="partner-badge">Training</span>
            <h3>TESDA Davao Oriental</h3>
            <p>Gain new certifications and connect with employers looking for skilled talent.</p>
          </div>

          <div class="partner-card">
            <span class="partner-badge">Education</span>
            <h3>Partner Schools &amp; Universities</h3>
            <p>Onboard students for internships, on-the-job training, and industry partnerships.</p>
          </div>

          <div class="partner-card">
            <span class="partner-badge">Industry</span>
            <h3>Regional Employers</h3>
            <p>Tourism, construction, and service companies with consistent hiring needs.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="landing-community" id="community">
      <div class="landing-container community-grid">
        <article class="community-card">
          <h3>Stay in the loop</h3>
          <p>Receive caravan schedules, job fair announcements, and PESO advisories right away.</p>
          <a class="btn btn-outline" href="<?= site_url('worker/feed'); ?>">Open community feed</a>
        </article>

        <article class="community-card">
          <h3>Support for employers</h3>
          <p>Coordinate interviews, review applicants, and request assistance from PESO specialists.</p>
          <a class="btn btn-outline" href="<?= site_url('dashboard/client'); ?>">Go to employer tools</a>
        </article>

        <article class="community-card">
          <h3>Schools &amp; partners</h3>
          <p>Manage student placements, track requirements, and collaborate with Davao Oriental.</p>
          <a class="btn btn-outline" href="<?= site_url('school-admin'); ?>">Visit school admin</a>
        </article>
      </div>
    </section>

    <section class="landing-cta" id="get-started">
      <div class="landing-container">
        <div class="cta-shell">
          <?php if ($isLoggedIn): ?>
            <h2>Ready to continue your hiring journey?</h2>
            <p>Review candidate profiles, update job posts, and respond to applications in seconds.</p>
            <a class="btn btn-primary" href="<?= site_url($dashboardRoute); ?>">Return to your dashboard</a>
          <?php else: ?>
            <h2>Start your next opportunity with Public Employment Service Office</h2>
            <p>Sign up for free to manage applications, follow job fairs, and receive tailored alerts from Davao Oriental PESO.</p>
            <div class="cta-actions">
              <a class="btn btn-primary" href="<?= site_url('auth/signup'); ?>">Create free account</a>
              <a class="btn btn-outline" href="<?= site_url('auth/login'); ?>">Log in</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <footer class="landing-footer">
    <div class="landing-container">
      <div class="footer-grid">
        <div>
          <div class="footer-heading">Discover jobs</div>
          <div class="footer-links">
            <a href="#jobs-latest">Latest vacancies</a>
            <a href="#services">Worker services</a>
            <a href="#community">Community updates</a>
            <a href="<?= site_url('hotlines'); ?>">Job fairs &amp; caravans</a>
          </div>
        </div>

        <div>
          <div class="footer-heading">For workers</div>
          <div class="footer-links">
            <a href="<?= site_url('auth/signup'); ?>">Create account</a>
            <a href="<?= site_url('auth/login'); ?>">Sign in</a>
            <a href="<?= site_url('profile'); ?>">Update profile</a>
          </div>
        </div>

        <div>
          <div class="footer-heading">For employers</div>
          <div class="footer-links">
            <a href="<?= site_url('users'); ?>">Manage postings</a>
            <a href="<?= site_url('dashboard/client'); ?>">Employer dashboard</a>
            <a href="<?= site_url('admin/reports'); ?>">Reports</a>
          </div>
        </div>

        <div>
          <div class="footer-heading">Support</div>
          <div class="footer-links">
            <span>Email <a href="mailto:support@jobmatch.ph">support@jobmatch.ph</a></span>
            <a href="<?= site_url('visibility'); ?>">Privacy notice</a>
            <a href="<?= site_url('complaints'); ?>">Submit a complaint</a>
          </div>
        </div>
      </div>

      <div class="footer-meta">
        <span>&copy; <?= date('Y'); ?> Public Employment Service Office - Davao Oriental </span>
        <span><a href="#" data-twx-open="about" role="button">Terms of use</a> | <a href="<?= site_url('hotlines'); ?>">Contact us</a></span>
      </div>
    </div>
  </footer>

  <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js'); ?>"></script>
  <script src="<?= base_url('assets/js/off-canvas.js'); ?>"></script>
  <script src="<?= base_url('assets/js/hoverable-collapse.js'); ?>"></script>
  <script src="<?= base_url('assets/js/misc.js'); ?>"></script>
  <script src="<?= base_url('assets/js/settings.js'); ?>"></script>
  <script src="<?= base_url('assets/js/todolist.js'); ?>"></script>

  <script>
    window.fallbackStats = {
      openJobs: <?= (int) $statJobs; ?>
    };
    window.pesoFeedUrl = '<?= site_url('peso/feed'); ?>';
  </script>


  <script src="<?= base_url('assets/js/landing.js'); ?>"></script>

  <?php $this->load->view('includes_footer', ['hide_footer_bar' => true]); ?>
</body>

</html>
