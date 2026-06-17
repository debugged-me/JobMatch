<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=overlays-content">
  <title>Public Employment Service Office — Davao Oriental</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css'); ?>">
  <?php $lpCssVer = is_file(FCPATH . 'assets/css/landing-peso.css') ? filemtime(FCPATH . 'assets/css/landing-peso.css') : time(); ?>
  <link rel="stylesheet" href="<?= base_url('assets/css/landing-peso.css?v=' . $lpCssVer); ?>">
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
      'summary' => 'Keep the province-wide workforce and employers aligned every day.',
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

  <!-- Top utility strip -->
  <div class="lp-topbar">
    <div class="lp-container">
      <span class="lp-topbar-left">
        <i class="mdi mdi-shield-check-outline"></i> Official portal of PESO &mdash; Davao Oriental
      </span>
      <span class="lp-topbar-right">
        <a href="<?= site_url('hotlines'); ?>"><i class="mdi mdi-phone-in-talk-outline"></i> Hotlines</a>
        <a href="<?= site_url('complaints'); ?>"><i class="mdi mdi-message-alert-outline"></i> File a concern</a>
      </span>
    </div>
  </div>

  <!-- Navbar -->
  <header class="lp-nav" id="home">
    <div class="lp-container">
      <a href="<?= site_url(); ?>" class="lp-brand" aria-label="PESO Davao Oriental Home">
        <img src="<?= base_url('assets/images/logo.png'); ?>" alt="PESO Davao Oriental logo">
        <span class="lp-brand-text">
          <strong>Public Employment Service Office</strong>
          <span>Davao Oriental</span>
        </span>
      </a>

      <nav class="lp-navlinks" aria-label="Primary navigation">
        <a href="#jobs-latest">Jobs</a>
        <a href="#services">Services</a>
        <a href="#role-toolkit">Toolkits</a>
        <a href="#how-it-works">How it works</a>
        <a href="#partners">Partners</a>
      </nav>

      <div class="lp-nav-actions">
        <?php if ($isLoggedIn): ?>
          <a class="lp-btn lp-btn--ghost lp-btn--sm" href="<?= site_url('auth/logout'); ?>">Logout</a>
          <a class="lp-btn lp-btn--primary lp-btn--sm" href="<?= site_url($dashboardRoute); ?>">Dashboard</a>
        <?php else: ?>
          <a class="lp-btn lp-btn--ghost lp-btn--sm" href="<?= site_url('auth/login'); ?>">Log in</a>
          <a class="lp-btn lp-btn--primary lp-btn--sm" href="<?= site_url('auth/signup'); ?>">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="lp-hero">
    <div class="lp-container">
      <div class="lp-hero-copy">
        <?php if ($isLoggedIn): ?>
          <span class="lp-hero-eyebrow"><i class="mdi mdi-account-circle-outline"></i> Welcome back<?= $firstNameSafe !== '' ? ', ' . $firstNameSafe : ''; ?></span>
          <h1 class="lp-hero-title">Your <em>PESO</em> workspace,<br>ready when you are.</h1>
          <p class="lp-hero-text">
            Pick up where you left off &mdash; manage applications, job orders, and announcements from the Davao Oriental Public Employment Service Office.
          </p>
          <div class="lp-hero-cta">
            <a class="lp-btn lp-btn--light" href="<?= site_url($dashboardRoute); ?>">Go to dashboard <i class="mdi mdi-arrow-right"></i></a>
            <a class="lp-btn lp-btn--clear" href="#jobs-latest">View latest jobs</a>
          </div>
        <?php else: ?>
          <span class="lp-hero-eyebrow"><i class="mdi mdi-map-marker-outline"></i> Serving the whole of Davao Oriental</span>
          <h1 class="lp-hero-title">Find work. Hire talent.<br>All through <em>one</em> public office.</h1>
          <p class="lp-hero-text">
            The Public Employment Service Office connects skilled workers, employers, and schools across Davao Oriental &mdash; with verified job orders, trainings, and real-time assistance.
          </p>
          <div class="lp-hero-cta">
            <a class="lp-btn lp-btn--light" href="<?= site_url('auth/signup'); ?>">Create free account <i class="mdi mdi-arrow-right"></i></a>
            <a class="lp-btn lp-btn--clear" href="<?= site_url('auth/login'); ?>">Log in</a>
          </div>
        <?php endif; ?>

        <ul class="lp-hero-points">
          <li><i class="mdi mdi-check-circle"></i> Daily updates direct from Davao Oriental PESO</li>
          <li><i class="mdi mdi-check-circle"></i> TESDA-certified trainings and local job fairs</li>
          <li><i class="mdi mdi-check-circle"></i> Support for workers, employers, and schools</li>
        </ul>
      </div>

      <aside class="lp-statboard" aria-label="Platform snapshot">
        <div class="lp-statboard-head">
          <strong>Live snapshot</strong>
          <span>Updated daily</span>
        </div>
        <div class="lp-statgrid">
          <div class="lp-stat">
            <b><?= $statWorkers > 0 ? $statWorkersFmt . '+' : '--'; ?></b>
            <small>Registered workers</small>
          </div>
          <div class="lp-stat">
            <b><?= $statEmployers > 0 ? $statEmployersFmt . '+' : '--'; ?></b>
            <small>Verified employers</small>
          </div>
          <div class="lp-stat">
            <b><?= $statJobs > 0 ? $statJobsFmt : '--'; ?></b>
            <small>Open job orders</small>
          </div>
          <div class="lp-stat">
            <b><?= $statHotlines > 0 ? $statHotlinesFmt : '--'; ?></b>
            <small>Active hotlines</small>
          </div>
        </div>
      </aside>
    </div>
  </section>

  <main>
    <!-- Live insights -->
    <section class="lp-section" id="insights">
      <div class="lp-container">
        <div class="lp-head">
          <span class="lp-eyebrow">Live snapshot</span>
          <h2 class="lp-title">PESO opportunities right now</h2>
          <p class="lp-sub">Numbers refresh as soon as provincial job orders go public.</p>
        </div>

        <div class="lp-insights-grid">
          <article class="lp-insight">
            <span class="lp-insight-label">Open job orders</span>
            <div class="lp-insight-value" id="insight-total-jobs">--</div>
            <span class="lp-insight-note" id="insight-total-jobs-note">Waiting for updates...</span>
          </article>

          <article class="lp-insight">
            <span class="lp-insight-label">Active locations</span>
            <div class="lp-insight-value" id="insight-total-locations">--</div>
            <span class="lp-insight-note" id="insight-location-top">--</span>
          </article>

          <article class="lp-insight">
            <span class="lp-insight-label">Salary transparency</span>
            <div class="lp-insight-value" id="insight-pay-count">--</div>
            <span class="lp-insight-note" id="insight-pay-percent">--</span>
          </article>

          <article class="lp-insight">
            <span class="lp-insight-label">Newest posting</span>
            <div class="lp-insight-value" id="insight-last-updated">--</div>
            <span class="lp-insight-note" id="insight-last-updated-note">--</span>
          </article>
        </div>
      </div>
    </section>

    <!-- Latest jobs -->
    <section class="lp-section lp-section--tint" id="jobs-latest">
      <div class="lp-container">
        <div class="lp-jobs-shell">
          <div class="lp-jobs-head">
            <div class="lp-head" style="margin-bottom:0;">
              <span class="lp-eyebrow">Now hiring</span>
              <h2 class="lp-title">Latest job vacancies</h2>
              <p class="lp-sub">Fresh postings straight from the Davao Oriental Public Employment Service Office.</p>
            </div>
            <div class="lp-jobs-actions">
              <?php if ($isLoggedIn): ?>
                <a class="lp-btn lp-btn--outline lp-btn--sm" href="<?= site_url($dashboardRoute); ?>">Manage saved jobs</a>
              <?php else: ?>
                <a class="lp-btn lp-btn--outline lp-btn--sm" href="<?= site_url('auth/signup'); ?>">Save these jobs</a>
                <a class="lp-btn lp-btn--ghost lp-btn--sm" href="<?= site_url('auth/login'); ?>">Login</a>
              <?php endif; ?>
            </div>
          </div>

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

    <!-- Services / features -->
    <section class="lp-section" id="services">
      <div class="lp-container">
        <div class="lp-head lp-head--center">
          <span class="lp-eyebrow">What you get</span>
          <h2 class="lp-title">Everything you need to land the role</h2>
          <p class="lp-sub">Whether you are a worker, employer, or school partner, PESO Davao Oriental keeps everyone in sync.</p>
        </div>

        <div class="lp-feature-grid">
          <article class="lp-feature">
            <span class="lp-feature-icon"><i class="mdi mdi-briefcase-check"></i></span>
            <h3>Guided applications</h3>
            <p>Track requirements, upload documents, and monitor interview slots in one place.</p>
          </article>

          <article class="lp-feature">
            <span class="lp-feature-icon"><i class="mdi mdi-school"></i></span>
            <h3>TESDA-aligned trainings</h3>
            <p>Discover accredited programs and reserve slots for upskilling or reskilling.</p>
          </article>

          <article class="lp-feature">
            <span class="lp-feature-icon"><i class="mdi mdi-account-group"></i></span>
            <h3>Community support</h3>
            <p>Connect with local employers, PESO officers, and fellow workers for timely updates.</p>
          </article>

          <article class="lp-feature">
            <span class="lp-feature-icon"><i class="mdi mdi-chart-line"></i></span>
            <h3>Insights for employers</h3>
            <p>Manage postings, respond to applicants, and review hiring analytics from your dashboard.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- Role toolkits -->
    <section class="lp-section lp-section--tint" id="role-toolkit">
      <div class="lp-container">
        <div class="lp-head">
          <span class="lp-eyebrow">By account type</span>
          <h2 class="lp-title">Toolkits for every account</h2>
          <p class="lp-sub">Switch tabs to discover the workflows available to you inside PESO Davao Oriental.</p>
        </div>

        <div class="toolkit-shell" data-default-role="<?= htmlspecialchars($toolkitDefault, ENT_QUOTES, 'UTF-8'); ?>">
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

    <!-- How it works -->
    <section class="lp-section" id="how-it-works">
      <div class="lp-container">
        <div class="lp-head">
          <span class="lp-eyebrow">Simple steps</span>
          <h2 class="lp-title">How your PESO journey works</h2>
          <p class="lp-sub">From profile setup to hiring, follow these steps to make the most of the platform.</p>
        </div>

        <div class="lp-steps">
          <div class="lp-step">
            <h3>Create your profile</h3>
            <p>Complete your details so PESO officers can match you with jobs or trainings faster.</p>
          </div>
          <div class="lp-step">
            <h3>Browse active vacancies</h3>
            <p>Filter openings, save the ones you like, and get notified for urgent hiring.</p>
          </div>
          <div class="lp-step">
            <h3>Connect and get hired</h3>
            <p>Attend orientations, submit documents, and coordinate with employers in your dashboard.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Partners -->
    <section class="lp-section lp-section--tint" id="partners">
      <div class="lp-container">
        <div class="lp-head lp-head--center">
          <span class="lp-eyebrow">Trusted by</span>
          <h2 class="lp-title">Local partners backing your success</h2>
          <p class="lp-sub">We work closely with government offices, schools, and private employers to provide trusted opportunities.</p>
        </div>

        <div class="lp-partner-grid">
          <div class="lp-partner">
            <span class="lp-partner-badge">Government</span>
            <h3>Davao Oriental LGUs</h3>
            <p>Provincial and municipal offices offering frontline, administrative, and community roles.</p>
          </div>
          <div class="lp-partner">
            <span class="lp-partner-badge">Training</span>
            <h3>TESDA Davao Oriental</h3>
            <p>Gain new certifications and connect with employers looking for skilled talent.</p>
          </div>
          <div class="lp-partner">
            <span class="lp-partner-badge">Education</span>
            <h3>Schools &amp; Universities</h3>
            <p>Onboard students for internships, on-the-job training, and industry partnerships.</p>
          </div>
          <div class="lp-partner">
            <span class="lp-partner-badge">Industry</span>
            <h3>Regional Employers</h3>
            <p>Tourism, construction, and service companies with consistent hiring needs.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="lp-section" id="get-started">
      <div class="lp-container">
        <div class="lp-cta">
          <?php if ($isLoggedIn): ?>
            <h2>Ready to continue your hiring journey?</h2>
            <p>Review candidate profiles, update job posts, and respond to applications in seconds.</p>
            <div class="lp-cta-actions">
              <a class="lp-btn lp-btn--light" href="<?= site_url($dashboardRoute); ?>">Return to your dashboard</a>
            </div>
          <?php else: ?>
            <h2>Start your next opportunity with PESO Davao Oriental</h2>
            <p>Sign up for free to manage applications, follow job fairs, and receive tailored alerts from Davao Oriental PESO.</p>
            <div class="lp-cta-actions">
              <a class="lp-btn lp-btn--light" href="<?= site_url('auth/signup'); ?>">Create free account</a>
              <a class="lp-btn lp-btn--clear" href="<?= site_url('auth/login'); ?>">Log in</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="lp-footer">
    <div class="lp-container">
      <div class="lp-foot-top">
        <div class="lp-foot-brand">
          <img src="<?= base_url('assets/images/logo.png'); ?>" alt="PESO Davao Oriental"
            onerror="this.onerror=null;this.src='<?= base_url('assets/images/logo.png'); ?>';">
          <p>The Public Employment Service Office of Davao Oriental connects skilled workers and employers with verified opportunities, trainings, and assistance.</p>
        </div>

        <div class="lp-foot-col">
          <h4>Discover jobs</h4>
          <a href="#jobs-latest">Latest vacancies</a>
          <a href="#services">Worker services</a>
          <a href="<?= site_url('hotlines'); ?>">Job fairs &amp; caravans</a>
        </div>

        <div class="lp-foot-col">
          <h4>For workers</h4>
          <a href="<?= site_url('auth/signup'); ?>">Create account</a>
          <a href="<?= site_url('auth/login'); ?>">Sign in</a>
          <a href="<?= site_url('profile'); ?>">Update profile</a>
        </div>

        <div class="lp-foot-col">
          <h4>Support</h4>
          <span>support@jobmatch.ph</span>
          <a href="<?= site_url('visibility'); ?>">Privacy notice</a>
          <a href="<?= site_url('complaints'); ?>">Submit a complaint</a>
        </div>
      </div>

      <div class="lp-foot-bottom">
        <span>&copy; <?= date('Y'); ?> Public Employment Service Office &mdash; Davao Oriental</span>
        <span><a href="#" data-twx-open="about" role="button">Terms of use</a> &nbsp;|&nbsp; <a href="<?= site_url('hotlines'); ?>">Contact us</a></span>
      </div>
    </div>
  </footer>

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