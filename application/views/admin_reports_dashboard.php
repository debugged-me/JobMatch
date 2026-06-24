    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?= htmlspecialchars($page_title ?? 'JobMatch', ENT_QUOTES, 'UTF-8') ?></title>

        <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.0.7') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/universal.css') ?>">
        <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

        <style>
            :root {
                --blue-900: #c1272d;
                --blue-700: #d63031;
                --blue-600: #e74c3c;
                --blue-500: #e74c3c;
                --gold-700: #1b5e9f;
                --gold-600: #2980b9;
                --silver-600: #a7afba;
                --silver-500: #c0c6d0;
                --silver-300: #d9dee7;
                --silver-200: #e7ebf2;
                --silver-100: #f6f8fc;
                --radius: 12px;
                --pad-panel: 12px;
                --fs-body: 13px;
                --fs-kpi: 18px;
                --fs-kpi-label: 12px;
                --shadow-1: 0 6px 16px rgba(2, 6, 23, .08);
            }

            html,
            body {
                height: 100%
            }

            body {
                font-family: "Karla", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
                font-size: var(--fs-body);
                background: linear-gradient(180deg, var(--silver-100), #eef2f7 60%, #e9edf3 100%);
                color: #0f172a;
            }

            .content-wrapper {
                padding-top: .6rem
            }

            .app {
                max-width: 1100px;
                margin: 0 auto;
                padding: 0 12px
            }

            .eyebrow {
                font-size: 12px;
                color: #64748b;
                font-weight: 600;
                letter-spacing: .2px;
                margin: 4px 0 8px
            }

            /* Panels / cards */
            .panel {
                background: #fff;
                border: 1px solid var(--silver-300);
                border-radius: var(--radius);
                box-shadow: var(--shadow-1);
                padding: var(--pad-panel);
                margin-bottom: 14px
            }

            .panel--wide {
                grid-column: 1/-1
            }

            .panel-head {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 8px
            }

            .panel-head i {
                font-size: 18px;
                color: var(--silver-600)
            }

            .panel-head h6 {
                margin: 0;
                font-size: 13px;
                font-weight: 800;
                color: var(--blue-900)
            }

            .empty {
                color: #6b7280;
                border: 1px dashed var(--silver-300);
                border-radius: 10px;
                padding: 10px;
                text-align: center;
                background: linear-gradient(180deg, #fff, #fbfcff)
            }

            .badge-soft {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: .25rem .5rem;
                border-radius: 9999px;
                border: 1px solid var(--silver-300);
                background: #fff;
                font-weight: 700;
                font-size: 12px
            }

            /* KPI tiles */
            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
                margin: 10px 0 6px
            }

            @media (max-width:992px) {
                .kpi-grid {
                    grid-template-columns: repeat(2, 1fr)
                }
            }

            @media (max-width:520px) {
                .kpi-grid {
                    grid-template-columns: 1fr
                }
            }

            .kpi .label {
                font-size: var(--fs-kpi-label);
                color: #6b7280
            }

            .kpi .value {
                font-size: var(--fs-kpi);
                font-weight: 800;
                line-height: 1.1
            }

            .kpi .icon {
                width: 36px;
                height: 36px;
                border-radius: 9px;
                display: flex;
                align-items: center;
                justify-content: center
            }

            /* Tables */
            .table-r th,
            .table-r td {
                padding-top: 12px;
                padding-bottom: 12px;
                vertical-align: middle
            }

            .pill {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 999px;
                font-size: 12px
            }

            .pill-ok {
                background: rgba(193, 39, 45, .12);
                color: #c1272d;
                border: 1px solid rgba(193, 39, 45, .32)
            }

            .pill-warn {
                background: rgba(251, 191, 36, .18);
                color: #92400e;
                border: 1px solid rgba(251, 191, 36, .4)
            }

            /* Responsive table (mobile cards) */
            @media (max-width: 768px) {
                .table-responsive {
                    overflow-x: visible
                }

                .table-r {
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0 8px
                }

                .table-r thead {
                    display: none
                }

                .table-r tbody tr {
                    display: block;
                    padding: 10px;
                    border: 1px solid var(--silver-300);
                    border-radius: 12px;
                    background: #fff;
                    box-shadow: var(--shadow-1);
                }

                .table-r tbody tr+tr {
                    margin-top: 8px
                }

                .table-r td {
                    display: grid;
                    grid-template-columns: 110px 1fr;
                    gap: 8px;
                    padding: 6px 0 !important;
                    border: 0 !important;
                    font-size: 12.5px;
                }

                .table-r td::before {
                    content: attr(data-label);
                    font-weight: 700;
                    color: #334155
                }
            }
        </style>
        <style>
            @media print {

                /* Hide app chrome */
                .container-scroller>.container-fluid.page-body-wrapper>.main-panel>.content-wrapper .app>*,
                .container-scroller>.container-fluid.page-body-wrapper>*:not(.main-panel) {
                    display: none !important;
                }

                .container-scroller,
                .page-body-wrapper,
                .main-panel,
                .content-wrapper,
                .app {
                    display: block !important;
                    padding: 0 !important;
                    margin: 0 !important;
                }

                body {
                    background: #fff !important;
                }

                /* Show only print area */
                .print-area {
                    display: block !important;
                }
            }

            @media screen {
                .print-area {
                    display: none;
                }
            }

            .print-title {
                font-weight: 800;
                font-size: 16px;
                color: #c1272d;
                margin-bottom: 8px
            }

            .print-sub {
                color: #6b7280;
                font-size: 12px;
                margin-bottom: 10px
            }

            .print-box {
                border: 1px solid #d9dee7;
                border-radius: 10px;
                padding: 12px;
                margin-bottom: 12px
            }

            .print-box h6 {
                margin: 0 0 8px;
                font-size: 13px;
                font-weight: 800;
                color: #c1272d
            }

            .print-table {
                width: 100%;
                border-collapse: collapse;
            }

            .print-table th,
            .print-table td {
                border: 1px solid #e5e7eb;
                padding: 8px;
                font-size: 12.5px;
                vertical-align: top
            }

            .print-small {
                font-size: 12px;
                color: #6b7280
            }
        </style>

    </head>

    <body>
        <?php $this->load->view('partials_translate_banner'); ?>

        <div class="container-scroller">
            <?php $this->load->view('includes_nav'); ?>
            <div class="container-fluid page-body-wrapper">
                <?php $this->load->view('includes_nav_top'); ?>
                <div class="main-panel">
                    <div class="content-wrapper pb-0">
                        <div class="app">

                            <div class="eyebrow"><?= htmlspecialchars($page_title ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></div>

                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger" role="alert"><?= $this->session->flashdata('error'); ?></div>
                            <?php endif; ?>
                            <?php if ($this->session->flashdata('success')): ?>
                                <div class="alert alert-success" role="alert"><?= $this->session->flashdata('success'); ?></div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-end mb-2">
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-light" href="<?= current_url() ?>?print=1" target="_blank" rel="noopener">
                                        <i class="mdi mdi-printer"></i> Print
                                    </a>
                                </div>
                            </div>

                            <!-- KPIs -->
                            <div class="kpi-grid">
                                <div class="panel kpi">
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="icon" style="background:rgba(193,39,45,.12)"><i class="mdi mdi-briefcase-outline" style="font-size:18px;color:#c1272d"></i></div>
                                        <div>
                                            <div class="label">Total Jobs</div>
                                            <div class="value"><?= number_format((int)($total_jobs ?? 0)) ?></div>
                                        </div>
                                    </div>
                                    <div class="text-muted" style="font-size:12px;margin-top:4px">All posts</div>
                                </div>

                                <div class="panel kpi">
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="icon" style="background:rgba(251,191,36,.18)"><i class="mdi mdi-account-multiple-check" style="font-size:18px;color:#b45309"></i></div>
                                        <div>
                                            <div class="label">Jobs With Applicants</div>
                                            <div class="value"><?= number_format((int)($jobs_with_apps ?? 0)) ?></div>
                                        </div>
                                    </div>
                                    <div class="text-muted" style="font-size:12px;margin-top:4px">Distinct jobs with â‰¥1 application</div>
                                </div>

                                <div class="panel kpi">
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="icon" style="background:rgba(245,158,11,.12)"><i class="mdi mdi-briefcase" style="font-size:18px;color:#f59e0b"></i></div>
                                        <div>
                                            <div class="label">Total Client Projects</div>
                                            <div class="value"><?= number_format((int)($total_client_projects ?? 0)) ?></div>
                                        </div>
                                    </div>
                                    <div class="text-muted" style="font-size:12px;margin-top:4px">All client-created projects</div>
                                </div>

                                <div class="panel kpi">
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="icon" style="background:rgba(99,102,241,.12)"><i class="mdi mdi-account-star" style="font-size:18px;color:#6366f1"></i></div>
                                        <div>
                                            <div class="label">Projects With Applicants</div>
                                            <div class="value"><?= number_format((int)($projects_with_apps ?? 0)) ?></div>
                                        </div>
                                    </div>
                                    <div class="text-muted" style="font-size:12px;margin-top:4px">Distinct projects with â‰¥1 application</div>
                                </div>
                            </div>

                            <!-- All Jobs (incl zero applicants) -->
                            <section class="panel panel--wide">
                                <div class="panel-head"><i class="mdi mdi-briefcase-outline"></i>
                                    <h6>All Jobs (including zero applicants)</h6>
                                </div>
                                <div class="panel-body">
                                    <?php if (empty($jobs_all)): ?>
                                        <div class="empty">No jobs found.</div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-r" style="width:100%">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Title</th>
                                                        <th>Type</th>
                                                        <th>Status</th>
                                                        <th>Created</th>
                                                        <th>Applicants</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($jobs_all as $r): ?>
                                                        <tr>
                                                            <td data-label="ID"><?= (int)$r['id'] ?></td>
                                                            <td data-label="Title" class="fw-medium"><?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES) ?></td>
                                                            <td data-label="Type"><?= htmlspecialchars($r['post_type'] ?? '', ENT_QUOTES) ?></td>
                                                            <td data-label="Status">
                                                                <?php $ok = strtolower((string)$r['status']) === 'open'; ?>
                                                                <span class="pill <?= $ok ? 'pill-ok' : 'pill-warn' ?>"><?= htmlspecialchars($r['status'] ?? '', ENT_QUOTES) ?></span>
                                                            </td>
                                                            <td data-label="Created" class="text-muted"><?= htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES) ?></td>
                                                            <td data-label="Applicants"><?= (int)$r['applicant_count'] ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </section>

                            <!-- Jobs with Applicants only -->
                            <section class="panel panel--wide">
                                <div class="panel-head"><i class="mdi mdi-account-multiple-check"></i>
                                    <h6>Jobs That Received Applicants</h6>
                                </div>
                                <div class="panel-body">
                                    <?php if (empty($jobs_applied)): ?>
                                        <div class="empty">No jobs with applicants yet.</div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-r" style="width:100%">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Title</th>
                                                        <th>Type</th>
                                                        <th>Status</th>
                                                        <th>Applicants</th>
                                                        <th>First Applied</th>
                                                        <th>Last Applied</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($jobs_applied as $r): ?>
                                                        <tr>
                                                            <td data-label="ID"><?= (int)$r['id'] ?></td>
                                                            <td data-label="Title" class="fw-medium"><?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES) ?></td>
                                                            <td data-label="Type"><?= htmlspecialchars($r['post_type'] ?? '', ENT_QUOTES) ?></td>
                                                            <td data-label="Status">
                                                                <?php $ok = strtolower((string)$r['status']) === 'open'; ?>
                                                                <span class="pill <?= $ok ? 'pill-ok' : 'pill-warn' ?>"><?= htmlspecialchars($r['status'] ?? '', ENT_QUOTES) ?></span>
                                                            </td>
                                                            <td data-label="Applicants"><?= (int)$r['applicant_count'] ?></td>
                                                            <td data-label="First Applied" class="text-muted"><?= htmlspecialchars($r['first_applied_at'] ?? '', ENT_QUOTES) ?></td>
                                                            <td data-label="Last Applied" class="text-muted"><?= htmlspecialchars($r['last_applied_at'] ?? '', ENT_QUOTES) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </section>

                            <!-- Employer / Client Projects Summary -->
                            <section class="panel panel--wide">
                                <div class="panel-head"><i class="mdi mdi-account-tie-outline"></i>
                                    <h6>Employers / Clients - Projects Summary</h6>
                                </div>
                                <div class="panel-body">
                                    <?php if (empty($clients_sum)): ?>
                                        <div class="empty">No client projects found.</div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-r" style="width:100%">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Client</th>
                                                        <th>Total Projects</th>
                                                        <th>With Applicants</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($clients_sum as $r): ?>
                                                        <?php
                                                        $cid   = (int)$r['clientID'];
                                                        $label = $client_labels[$cid] ?? ('Client #' . $cid);
                                                        ?>
                                                        <tr>
                                                            <td data-label="Client" class="fw-medium"><?= htmlspecialchars($label, ENT_QUOTES) ?> <span class="text-muted">(ID <?= $cid ?>)</span></td>
                                                            <td data-label="Total Projects"><?= (int)$r['total_projects'] ?></td>
                                                            <td data-label="With Applicants"><?= (int)$r['projects_with_apps'] ?></td>
                                                            <td data-label="Action">
                                                                <a class="badge-soft" href="<?= site_url('admin/reports/client/' . $cid) ?>">
                                                                    <i class="mdi mdi-eye"></i> View Projects
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </section>
                            <!-- PRINT-ONLY AREA -->
                            <div class="print-area">
                                <div class="print-title">Admin Report - Jobs & Projects</div>
                                <div class="print-sub">
                                    Generated: <?= date('M d, Y h:i A') ?> • Timezone: Asia/Manila
                                </div>

                                <!-- KPIs (concise) -->
                                <div class="print-box">
                                    <h6>Summary</h6>
                                    <table class="print-table">
                                        <tr>
                                            <th>Total Jobs</th>
                                            <th>Jobs With Applicants</th>
                                            <th>Total Client Projects</th>
                                            <th>Projects With Applicants</th>
                                        </tr>
                                        <tr>
                                            <td><?= (int)($total_jobs ?? 0) ?></td>
                                            <td><?= (int)($jobs_with_apps ?? 0) ?></td>
                                            <td><?= (int)($total_client_projects ?? 0) ?></td>
                                            <td><?= (int)($projects_with_apps ?? 0) ?></td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Jobs with applicants (detailed) -->
                                <div class="print-box">
                                    <h6>Jobs That Received Applicants (with names)</h6>
                                    <?php if (empty($jobs_applied)): ?>
                                        <div class="print-small">No jobs with applicants yet.</div>
                                    <?php else: ?>
                                        <table class="print-table">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px">ID</th>
                                                    <th>Job Title</th>
                                                    <th style="width:80px">Applicants</th>
                                                    <th>Applicant Names</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($jobs_applied as $r):
                                                    $jid  = (int)$r['id'];
                                                    $names = $printMode ? ($jobApplicants[$jid]['names'] ?? []) : [];
                                                ?>
                                                    <tr>
                                                        <td><?= $jid ?></td>
                                                        <td><?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES) ?></td>
                                                        <td><?= (int)$r['applicant_count'] ?></td>
                                                        <td>
                                                            <?php if (!empty($names)): ?>
                                                                <?= htmlspecialchars(implode('; ', $names), ENT_QUOTES) ?>
                                                            <?php else: ?>
                                                                <span class="print-small">—</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <div class="print-small" style="margin-top:6px">Names appear when available in the Users table.</div>
                                    <?php endif; ?>
                                </div>

                                <!-- Clients summary (link to per-client detail pages if needed) -->
                                <div class="print-box">
                                    <h6>Employers / Clients — Projects Summary</h6>
                                    <?php if (empty($clients_sum)): ?>
                                        <div class="print-small">No client projects found.</div>
                                    <?php else: ?>
                                        <table class="print-table">
                                            <thead>
                                                <tr>
                                                    <th>Client</th>
                                                    <th style="width:120px">Total Projects</th>
                                                    <th style="width:140px">With Applicants</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($clients_sum as $r):
                                                    $cid   = (int)$r['clientID'];
                                                    $label = $client_labels[$cid] ?? ('Client #' . $cid);
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($label, ENT_QUOTES) ?> (ID <?= $cid ?>)</td>
                                                        <td><?= (int)$r['total_projects'] ?></td>
                                                        <td><?= (int)$r['projects_with_apps'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <div class="print-small" style="margin-top:6px">For per-client applicant names, print from each client’s page.</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php $this->load->view('includes_footer'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $i18nJs = base_url('assets/js/i18n.js?v=' . (is_file(FCPATH . 'assets/js/i18n.js') ? filemtime(FCPATH . 'assets/js/i18n.js') : time()));
        $scanJs = base_url('assets/js/i18n.autoscan.js?v=' . (is_file(FCPATH . 'assets/js/i18n.autoscan.js') ? filemtime(FCPATH . 'assets/js/i18n.autoscan.js') : time()));
        ?>
        <script src="<?= $i18nJs ?>"></script>
        <script src="<?= $scanJs ?>"></script>
        <script>
            document.addEventListener('DOMContentLoaded', async () => {
                const saved = localStorage.getItem('lang_pref') || 'en';
                await I18N.init({
                    defaultLang: saved
                });
                I18NAutoScan.init();
            });
        </script>

        <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
        <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
        <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
        <script src="<?= base_url('assets/js/misc.js') ?>"></script>
    </body>

    </html>