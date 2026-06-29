<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php $page_title = $page_title ?? 'TESDA Training Posts'; ?>
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=5.1.1') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />

  <style>
    :root{
      --brand-blue:#c1272d;
      --brand-blue-dark:#9b1f24;
      --brand-blue-deep:#7d1a1e;
      --bg:#f1f3f6;
      --panel:#ffffff;
      --text:#172033;
      --muted:#7a869f;
      --line:#dfe6f5;
      --line-soft:#edf2fb;
      --green:#0e9f6e;
      --green-bg:#e8f7f0;
      --red:#dc3c3c;
      --red-bg:#fdeeee;
      --sky:#3366cc;
      --sky-bg:#eef3ff;
      --btn-shadow:0 12px 24px rgba(31,77,184,.18);
      --card-shadow:0 8px 24px rgba(15,30,60,.06);
      --radius-xl:20px;
      --radius-lg:16px;
      --radius-md:12px;
      --radius-sm:10px;
    }

    *{
      box-sizing:border-box;
    }

    html,body{
      height:100%;
    }

    body{
      font-family:"Poppins",ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      background:var(--bg);
      color:var(--text);
    }

    body.tesda-trainings-page .container-scroller{
      min-height:100vh;
    }

    body.tesda-trainings-page .main-panel{
      min-height:100vh;
      display:flex;
      flex-direction:column;
      background:var(--bg);
    }

    body.tesda-trainings-page .content-wrapper{
      background:var(--bg);
      padding:16px 18px 10px !important;
      flex:1 0 auto;
    }

    body.tesda-trainings-page .footer{
      margin-top:auto;
    }

    .wrap{
      width:100%;
      padding:8px 8px 24px;
    }

    .page-head{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:18px;
      margin-bottom:18px;
      flex-wrap:wrap;
    }

    .page-head-left{
      min-width:280px;
      flex:1 1 auto;
    }

    .page-title{
      margin:0;
      font-family:"Sora",sans-serif;
      font-size:2rem;
      line-height:1.15;
      font-weight:800;
      letter-spacing:-.03em;
      color:var(--brand-blue-deep);
    }

    .page-sub{
      margin:8px 0 0;
      color:var(--muted);
      font-size:.94rem;
      line-height:1.55;
      max-width:760px;
    }

    .page-head-actions{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      justify-content:flex-end;
    }

    .xbtn{
      appearance:none;
      border:none;
      outline:none;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:.55rem;
      min-height:46px;
      border-radius:14px;
      padding:.72rem 1.15rem;
      font-size:.9rem;
      font-weight:700;
      line-height:1;
      cursor:pointer;
      transition:all .2s ease;
      white-space:nowrap;
    }

    .xbtn i{
      font-size:1rem;
      line-height:1;
    }

    .xbtn-primary{
      color:#fff;
      background:linear-gradient(135deg,var(--brand-blue),var(--brand-blue-dark));
      box-shadow:var(--btn-shadow);
    }

    .xbtn-primary:hover{
      color:#fff;
      text-decoration:none;
      transform:translateY(-1px);
      box-shadow:0 16px 28px rgba(31,77,184,.22);
    }

    .xbtn-outline{
      background:#fff;
      color:#1f2a44;
      border:1.5px solid #d5def1;
      box-shadow:0 6px 16px rgba(15,30,60,.03);
    }

    .xbtn-outline:hover{
      color:var(--brand-blue);
      border-color:#b8c7ea;
      text-decoration:none;
      background:#fbfcff;
      transform:translateY(-1px);
    }

    .xbtn-danger{
      background:#fff;
      color:#a12a2a;
      border:1.5px solid #f3c7c7;
    }

    .xbtn-danger:hover{
      background:#fff4f4;
      color:#8e1f1f;
      text-decoration:none;
    }

    .stats-grid{
      display:grid;
      grid-template-columns:repeat(3,minmax(0,1fr));
      gap:14px;
      margin-bottom:18px;
    }

    .stat-card{
      background:var(--panel);
      border:1px solid var(--line);
      border-radius:22px;
      box-shadow:var(--card-shadow);
      padding:18px 18px;
      display:flex;
      align-items:center;
      gap:14px;
      min-height:98px;
    }

    .stat-icon{
      width:52px;
      height:52px;
      flex:0 0 52px;
      border-radius:16px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      font-size:1.35rem;
    }

    .stat-card.stat-open .stat-icon{
      background:var(--green-bg);
      color:var(--green);
    }

    .stat-card.stat-closed .stat-icon{
      background:var(--red-bg);
      color:var(--red);
    }

    .stat-card.stat-public .stat-icon{
      background:var(--sky-bg);
      color:var(--sky);
    }

    .stat-meta{
      min-width:0;
    }

    .stat-label{
      margin:0 0 4px;
      font-size:.79rem;
      font-weight:800;
      color:#6b7892;
      text-transform:uppercase;
      letter-spacing:.08em;
      line-height:1.2;
    }

    .stat-value{
      margin:0;
      font-family:"Sora",sans-serif;
      font-size:2rem;
      line-height:1;
      font-weight:800;
      color:#111b34;
      letter-spacing:-.04em;
    }

    .main-card{
      background:var(--panel);
      border:1px solid var(--line);
      border-radius:22px;
      box-shadow:var(--card-shadow);
      overflow:hidden;
    }

    .main-card + .main-card{
      margin-top:16px;
    }

    .main-card-head{
      padding:16px 18px;
      border-bottom:1px solid var(--line-soft);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      flex-wrap:wrap;
    }

    .main-card-head h2{
      margin:0;
      font-size:1.28rem;
      font-weight:800;
      color:var(--brand-blue-deep);
      line-height:1.2;
    }

    .main-card-body{
      padding:16px 18px 18px;
    }

    .search-form{
      width:100%;
      max-width:470px;
      display:flex;
      align-items:center;
      gap:10px;
      margin-left:auto;
    }

    .search-input-wrap{
      position:relative;
      flex:1 1 auto;
    }

    .search-input-wrap input{
      width:100%;
      height:48px;
      border-radius:16px;
      border:1.5px solid #d6dff2;
      background:#fbfcff;
      outline:none;
      padding:0 16px;
      font-size:.96rem;
      color:#22304d;
      transition:all .2s ease;
    }

    .search-input-wrap input:focus{
      background:#fff;
      border-color:#9fb6eb;
      box-shadow:0 0 0 4px rgba(31,77,184,.09);
    }

    .search-form .xbtn{
      min-width:118px;
      height:48px;
      border-radius:16px;
    }

    .alert{
      border-radius:14px;
      margin:0 0 14px;
      padding:.9rem 1rem;
      font-size:.9rem;
      line-height:1.45;
    }

    .form-grid{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:14px;
    }

    .field{
      display:flex;
      flex-direction:column;
      gap:6px;
    }

    .field-wide{
      grid-column:1 / -1;
    }

    .field label{
      margin:0;
      font-size:.78rem;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.08em;
      color:#70809e;
    }

    .field input,
    .field select,
    .field textarea{
      width:100%;
      border:1.5px solid #d6dff2;
      background:#fbfcff;
      color:#1a2742;
      border-radius:14px;
      padding:.78rem .92rem;
      outline:none;
      font:inherit;
      transition:all .2s ease;
    }

    .field input:focus,
    .field select:focus,
    .field textarea:focus{
      background:#fff;
      border-color:#9fb6eb;
      box-shadow:0 0 0 4px rgba(31,77,184,.09);
    }

    .field textarea{
      min-height:120px;
      resize:vertical;
      line-height:1.6;
    }

    .img-preview{
      width:100%;
      max-width:280px;
      aspect-ratio:16/9;
      border:1px solid var(--line);
      background:#f7faff;
      border-radius:16px;
      overflow:hidden;
      box-shadow:var(--card-shadow);
    }

    .img-preview img{
      width:100%;
      height:100%;
      display:block;
      object-fit:cover;
    }

    .form-actions{
      margin-top:16px;
      display:flex;
      justify-content:flex-end;
      gap:10px;
      flex-wrap:wrap;
    }

    .table-wrap{
      width:100%;
      overflow-x:auto;
      border:1px solid var(--line-soft);
      border-radius:16px;
      background:#fff;
    }

    table.posts-table{
      width:100%;
      min-width:1200px;
      border-collapse:separate;
      border-spacing:0;
      font-size:.9rem;
      color:#1d2741;
    }

    table.posts-table thead th{
      padding:14px 14px;
      background:#f6f9ff;
      color:#70809e;
      text-transform:uppercase;
      letter-spacing:.08em;
      font-size:.72rem;
      font-weight:800;
      border-bottom:1px solid var(--line);
      white-space:nowrap;
      text-align:left;
      vertical-align:middle;
    }

    table.posts-table thead th:first-child{
      border-top-left-radius:16px;
    }

    table.posts-table thead th:last-child{
      border-top-right-radius:16px;
    }

    table.posts-table tbody td{
      padding:16px 14px;
      border-bottom:1px solid #edf2fb;
      vertical-align:top;
      background:#fff;
    }

    table.posts-table tbody tr:hover td{
      background:#fbfdff;
    }

    table.posts-table tbody tr:last-child td{
      border-bottom:none;
    }

    .training-cell{
      min-width:0;
    }

    .training-title{
      margin:0;
      font-size:1.02rem;
      line-height:1.35;
      font-weight:800;
      color:#18233a;
      word-break:break-word;
    }

    .training-desc{
      margin-top:6px;
      color:#75829b;
      font-size:.86rem;
      line-height:1.55;
      max-width:470px;
      word-break:break-word;
      display:-webkit-box;
      -webkit-line-clamp:4;
      -webkit-box-orient:vertical;
      overflow:hidden;
    }

    .see-more-btn{
      display:inline-block;
      margin-top:6px;
      padding:0;
      border:none;
      background:transparent;
      color:var(--brand-blue);
      font-size:.82rem;
      font-weight:700;
      text-decoration:underline;
      cursor:pointer;
    }

    .location-text,
    .created-text{
      color:#2d3851;
      line-height:1.55;
      white-space:normal;
      word-break:break-word;
    }

    .location-text{
      max-width:210px;
    }

    .created-text{
      min-width:130px;
    }

    .table-link{
      color:var(--brand-blue);
      font-weight:700;
      text-decoration:none;
      word-break:break-all;
    }

    .table-link:hover{
      color:var(--brand-blue-dark);
      text-decoration:underline;
    }

    .muted-dash{
      color:#97a2b8;
    }

    .chip{
      display:inline-flex;
      align-items:center;
      gap:.42rem;
      min-height:34px;
      border-radius:999px;
      padding:.35rem .8rem;
      font-size:.78rem;
      font-weight:800;
      line-height:1;
      border:1px solid transparent;
      white-space:nowrap;
    }

    .chip i{
      font-size:.9rem;
      line-height:1;
    }

    .chip-open{
      color:var(--green);
      background:var(--green-bg);
      border-color:#cdeedf;
    }

    .chip-closed{
      color:var(--red);
      background:var(--red-bg);
      border-color:#f5d0d0;
    }

    .chip-public{
      color:var(--brand-blue);
      background:var(--sky-bg);
      border-color:#cfdbff;
    }

    .actions-cell{
      text-align:right;
    }

    .action-group{
      display:flex;
      justify-content:flex-end;
      align-items:center;
      gap:8px;
      flex-wrap:nowrap;
    }

    .action-group .xbtn{
      min-height:40px;
      padding:.62rem .9rem;
      border-radius:13px;
      font-size:.82rem;
    }

    .dt-footer{
      display:grid;
      grid-template-columns:1fr auto auto;
      align-items:center;
      gap:14px;
      padding-top:14px;
      margin-top:14px;
      border-top:1px dashed #d9e2f4;
    }

    .dt-info{
      font-size:.88rem;
      color:#74829c;
    }

    .dt-info strong{
      color:#1b2640;
      font-weight:800;
    }

    .dt-pagination{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:6px;
      flex-wrap:wrap;
    }

    .pg-btn{
      appearance:none;
      border:1.5px solid #d6dff2;
      background:#fff;
      color:#23314d;
      min-width:38px;
      height:38px;
      border-radius:12px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      font-size:.85rem;
      font-weight:800;
      line-height:1;
      padding:0 .65rem;
      cursor:pointer;
      transition:all .2s ease;
    }

    .pg-btn:hover{
      background:#f7faff;
      border-color:#bccced;
    }

    .pg-btn.active{
      color:#fff;
      border-color:var(--brand-blue);
      background:linear-gradient(135deg,var(--brand-blue),var(--brand-blue-dark));
      box-shadow:0 10px 22px rgba(31,77,184,.2);
    }

    .pg-btn:disabled{
      opacity:.45;
      cursor:not-allowed;
    }

    .pg-ellipsis{
      color:#98a4ba;
      font-weight:800;
      padding:0 2px;
    }

    .per-page-wrap{
      display:inline-flex;
      align-items:center;
      gap:8px;
      font-size:.86rem;
      color:#72809a;
      white-space:nowrap;
    }

    .per-page-wrap select{
      height:40px;
      min-width:80px;
      border-radius:12px;
      border:1.5px solid #d6dff2;
      background:#fff;
      color:#1f2a44;
      font-size:.84rem;
      font-weight:700;
      padding:0 .7rem;
      outline:none;
    }

    .empty{
      padding:40px 20px;
      text-align:center;
      background:#fbfdff;
      color:#76829b;
      border:1px dashed #cfdcf5;
      border-radius:16px;
      font-size:.95rem;
      font-weight:500;
    }

    #training-modal,
    #training-desc-modal{
      position:fixed;
      inset:0;
      z-index:1040;
      display:none;
      align-items:center;
      justify-content:center;
      padding:18px;
      background:rgba(11,20,40,.55);
      backdrop-filter:blur(3px);
    }

    .tm-card,
    .tdm-card{
      width:min(840px,100%);
      max-height:min(92vh,900px);
      overflow:auto;
      background:#fff;
      border-radius:22px;
      border:1px solid #dce5f7;
      box-shadow:0 20px 60px rgba(2,12,34,.28);
    }

    .tdm-card{
      width:min(660px,100%);
    }

    .tm-head,
    .tdm-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:18px 18px 14px;
      border-bottom:1px solid var(--line-soft);
    }

    .tm-title,
    .tdm-title{
      margin:0;
      font-family:"Sora",sans-serif;
      font-size:1.2rem;
      line-height:1.3;
      font-weight:800;
      color:var(--brand-blue-deep);
      letter-spacing:-.02em;
    }

    .tm-close,
    .tdm-close{
      appearance:none;
      border:1.5px solid #d7e0f4;
      background:#fff;
      color:#42506b;
      width:40px;
      height:40px;
      border-radius:12px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      cursor:pointer;
      transition:all .2s ease;
      flex:0 0 auto;
    }

    .tm-close:hover,
    .tdm-close:hover{
      background:#f8fbff;
      border-color:#c1d0ee;
    }

    .tm-card form{
      padding:16px 18px 18px;
    }

    .tdm-body{
      margin:0;
      padding:16px 18px 20px;
      color:#65748f;
      font-size:.92rem;
      line-height:1.75;
      white-space:pre-wrap;
      word-break:break-word;
    }

    @media (max-width:1200px){
      .stats-grid{
        grid-template-columns:1fr;
      }
    }

    @media (max-width:992px){
      body.tesda-trainings-page .content-wrapper{
        padding:14px 12px 10px !important;
      }

      .wrap{
        padding:4px 2px 20px;
      }

      .page-title{
        font-size:1.65rem;
      }

      .form-grid{
        grid-template-columns:1fr;
      }

      .dt-footer{
        grid-template-columns:1fr;
        justify-items:center;
        text-align:center;
      }

      .search-form{
        max-width:100%;
        margin-left:0;
      }

      .main-card-head{
        align-items:flex-start;
      }
    }

    @media (max-width:767.98px){
      .page-head{
        align-items:stretch;
      }

      .page-head-actions{
        width:100%;
        justify-content:stretch;
      }

      .page-head-actions .xbtn{
        flex:1 1 auto;
      }

      .search-form{
        flex-direction:column;
        align-items:stretch;
      }

      .search-form .xbtn{
        width:100%;
      }

      .tm-card,
      .tdm-card{
        border-radius:18px;
      }

      .action-group{
        flex-wrap:wrap;
        justify-content:flex-end;
      }
    }
  </style>
</head>

<body class="tesda-trainings-page" data-api-address="<?= site_url('address/api') ?>">
<div class="container-scroller">
  <?php $this->load->view('includes_nav'); ?>
  <div class="container-fluid page-body-wrapper">
    <?php $this->load->view('includes_nav_top'); ?>

    <div class="main-panel">
      <div class="content-wrapper pb-0">
        <div class="wrap">

          <div class="page-head">
            <div class="page-head-left">
              <h1 class="page-title"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></h1>
              <p class="page-sub">
                Create and manage TESDA training announcements shown on the landing page when public and open.
              </p>
            </div>

            <div class="page-head-actions">
              <button type="button" class="xbtn xbtn-primary" id="btn-open-training-modal">
                <i class="mdi mdi-plus-circle-outline"></i>
                <span>New Training Post</span>
              </button>
              <a class="xbtn xbtn-outline" href="<?= site_url('tesda/trainings') ?>">
                <i class="mdi mdi-refresh"></i>
                <span>Refresh</span>
              </a>
            </div>
          </div>

          <section class="stats-grid">
            <article class="stat-card stat-open">
              <div class="stat-icon">
                <i class="mdi mdi-checkbox-marked-circle-outline"></i>
              </div>
              <div class="stat-meta">
                <p class="stat-label">Open Trainings</p>
                <p class="stat-value"><?= number_format((int)($k_open ?? 0)) ?></p>
              </div>
            </article>

            <article class="stat-card stat-closed">
              <div class="stat-icon">
                <i class="mdi mdi-close-circle-outline"></i>
              </div>
              <div class="stat-meta">
                <p class="stat-label">Closed Trainings</p>
                <p class="stat-value"><?= number_format((int)($k_closed ?? 0)) ?></p>
              </div>
            </article>

            <article class="stat-card stat-public">
              <div class="stat-icon">
                <i class="mdi mdi-earth"></i>
              </div>
              <div class="stat-meta">
                <p class="stat-label">Public Trainings</p>
                <p class="stat-value"><?= number_format((int)($k_public ?? 0)) ?></p>
              </div>
            </article>
          </section>

          <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
          <?php endif; ?>

          <?php if ($this->session->flashdata('danger')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('danger'); ?></div>
          <?php endif; ?>

          <?php
            $edit   = isset($edit) && is_array($edit) ? $edit : null;
            $isEdit = !empty($edit);
            $rows   = isset($list) && is_array($list) ? $list : [];
          ?>

          <?php if ($isEdit): ?>
            <section class="main-card">
              <div class="main-card-head">
                <h2>Edit Training Post</h2>
              </div>

              <div class="main-card-body">
                <form method="post" enctype="multipart/form-data" action="<?= site_url('tesda/trainings/update/' . (int)$edit['id']) ?>">
                  <div class="form-grid">
                    <div class="field field-wide">
                      <label for="edit_title">Training Title *</label>
                      <input
                        id="edit_title"
                        type="text"
                        name="title"
                        required
                        maxlength="200"
                        value="<?= htmlspecialchars((string)($edit['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="e.g., Shielded Metal Arc Welding NC II - Scholarship Batch"
                      />
                    </div>

                    <div class="field">
                      <label for="edit_province">Province</label>
                      <select id="edit_province" name="province" data-pre="<?= htmlspecialchars((string)($edit['province'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <option value="">Select Province</option>
                      </select>
                    </div>

                    <div class="field">
                      <label for="edit_city">City / Municipality</label>
                      <select id="edit_city" name="city" data-pre="<?= htmlspecialchars((string)($edit['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" disabled>
                        <option value="">Select City / Municipality</option>
                      </select>
                    </div>

                    <div class="field">
                      <label for="edit_brgy">Barangay</label>
                      <select id="edit_brgy" name="brgy" data-pre="<?= htmlspecialchars((string)($edit['brgy'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" disabled>
                        <option value="">Select Barangay</option>
                      </select>
                    </div>

                    <input
                      id="edit_location_text"
                      type="hidden"
                      name="location_text"
                      value="<?= htmlspecialchars((string)($edit['location_text'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    />
                    <input
                      type="hidden"
                      id="edit_address_id"
                      name="address_id"
                      value="<?= (int)($edit['address_id'] ?? 0) ?>"
                    >

                    <div class="field">
                      <label for="edit_link">Website / Apply Link</label>
                      <input
                        id="edit_link"
                        type="url"
                        name="website_url"
                        value="<?= htmlspecialchars((string)($edit['website_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="https://..."
                      />
                    </div>

                    <div class="field">
                      <label for="edit_training_image">Training Image</label>
                      <input
                        id="edit_training_image"
                        type="file"
                        name="training_image"
                        accept=".jpg,.jpeg,.png,.webp,.gif,image/*"
                      />
                    </div>

                    <?php
                      $editImagePath = trim((string)($edit['image_path'] ?? ''));
                      $editImageUrl  = '';

                      if ($editImagePath !== '') {
                        $editImageUrl = preg_match('#^https?://#i', $editImagePath)
                          ? $editImagePath
                          : base_url(ltrim($editImagePath, '/'));
                      }
                    ?>

                    <?php if ($editImageUrl !== ''): ?>
                      <div class="field">
                        <label>Current Image</label>
                        <div class="img-preview">
                          <img src="<?= htmlspecialchars($editImageUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Training image">
                        </div>
                      </div>
                    <?php endif; ?>

                    <div class="field field-wide">
                      <label for="edit_desc">Description</label>
                      <textarea
                        id="edit_desc"
                        name="description"
                        placeholder="Include qualifications, schedule, requirements, and slot details."
                      ><?= htmlspecialchars((string)($edit['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                  </div>

                  <div class="form-actions">
                    <a class="xbtn xbtn-outline" href="<?= site_url('tesda/trainings') ?>">Cancel Edit</a>
                    <button class="xbtn xbtn-primary" type="submit">
                      <i class="mdi mdi-content-save-outline"></i>
                      <span>Save Changes</span>
                    </button>
                  </div>
                </form>
              </div>
            </section>
          <?php endif; ?>

          <section class="main-card">
            <div class="main-card-head">
              <h2>My Training Posts</h2>

              <form class="search-form" method="get" action="<?= site_url('tesda/trainings') ?>">
                <div class="search-input-wrap">
                  <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars((string)($q ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Search title or location"
                  />
                </div>
                <button class="xbtn xbtn-outline" type="submit">
                  <i class="mdi mdi-magnify"></i>
                  <span>Search</span>
                </button>
              </form>
            </div>

            <div class="main-card-body">
              <?php if (empty($rows)): ?>
                <div class="empty">No training posts yet.</div>
              <?php else: ?>
                <div class="table-wrap">
                  <table class="posts-table" id="training-table">
                    <thead>
                      <tr>
                        <th style="min-width:340px;">Training</th>
                        <th style="min-width:180px;">Location</th>
                        <th style="min-width:120px;">Link</th>
                        <th style="min-width:160px;">Created</th>
                        <th style="min-width:110px;">Status</th>
                        <th style="min-width:110px;">Visibility</th>
                        <th style="min-width:320px; text-align:right;">Actions</th>
                      </tr>
                    </thead>

                    <tbody id="posts-tbody">
                      <?php foreach ($rows as $r): ?>
                        <?php
                          $id         = (int)($r['id'] ?? 0);
                          $title      = trim((string)($r['title'] ?? 'Untitled training'));
                          $desc       = trim((string)($r['description'] ?? ''));
                          $loc        = trim((string)($r['location_text'] ?? ''));
                          $url        = trim((string)($r['website_url'] ?? ''));
                          $status     = strtolower((string)($r['status'] ?? 'open')) === 'open' ? 'open' : 'closed';
                          $createdAt  = trim((string)($r['created_at'] ?? ''));
                          $createdLbl = $createdAt !== '' ? date('M d, Y h:i A', strtotime($createdAt)) : '-';

                          $descLimit = 240;
                          if (function_exists('mb_strlen')) {
                            $descIsLong  = mb_strlen($desc, 'UTF-8') > $descLimit;
                            $descPreview = $descIsLong ? rtrim(mb_substr($desc, 0, $descLimit, 'UTF-8')) . '...' : $desc;
                          } else {
                            $descIsLong  = strlen($desc) > $descLimit;
                            $descPreview = $descIsLong ? rtrim(substr($desc, 0, $descLimit)) . '...' : $desc;
                          }
                        ?>
                        <tr data-id="<?= $id ?>">
                          <td class="training-cell">
                            <p class="training-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></p>

                            <?php if ($desc !== ''): ?>
                              <div class="training-desc"><?= htmlspecialchars($descPreview, ENT_QUOTES, 'UTF-8') ?></div>

                              <?php if ($descIsLong): ?>
                                <button
                                  type="button"
                                  class="see-more-btn desc-more-btn"
                                  data-desc-title="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                                  data-desc-body="<?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>"
                                >See more</button>
                              <?php endif; ?>
                            <?php endif; ?>
                          </td>

                          <td>
                            <div class="location-text"><?= htmlspecialchars($loc !== '' ? $loc : '-', ENT_QUOTES, 'UTF-8') ?></div>
                          </td>

                          <td>
                            <?php if ($url !== ''): ?>
                              <a
                                class="table-link"
                                href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
                                target="_blank"
                                rel="noopener"
                              >Open link</a>
                            <?php else: ?>
                              <span class="muted-dash">-</span>
                            <?php endif; ?>
                          </td>

                          <td>
                            <div class="created-text"><?= htmlspecialchars($createdLbl, ENT_QUOTES, 'UTF-8') ?></div>
                          </td>

                          <td>
                            <span class="chip <?= $status === 'open' ? 'chip-open' : 'chip-closed' ?>">
                              <i class="mdi mdi-checkbox-blank-circle"></i>
                              <?= strtoupper($status) ?>
                            </span>
                          </td>

                          <td>
                            <span class="chip chip-public">
                              <i class="mdi mdi-earth"></i>
                              PUBLIC
                            </span>
                          </td>

                          <td class="actions-cell">
                            <div class="action-group">
                              <a class="xbtn xbtn-outline" href="<?= site_url('tesda/trainings/edit/' . $id) ?>">
                                <i class="mdi mdi-pencil-outline"></i>
                                <span>Edit</span>
                              </a>

                              <a class="xbtn xbtn-outline" href="<?= site_url('tesda/trainings/toggle/' . $id) ?>">
                                <i class="mdi mdi-toggle-switch-outline"></i>
                                <span><?= $status === 'open' ? 'Close' : 'Open' ?></span>
                              </a>

                              <a
                                class="xbtn xbtn-danger"
                                href="<?= site_url('tesda/trainings/delete/' . $id) ?>"
                                onclick="return confirm('Delete this training post?');"
                              >
                                <i class="mdi mdi-delete-outline"></i>
                                <span>Delete</span>
                              </a>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="dt-footer" id="dt-footer">
                  <div class="dt-info" id="dt-info">Showing <strong>-</strong> of <strong>-</strong> posts</div>

                  <div class="dt-pagination" id="dt-pagination"></div>

                  <div class="per-page-wrap">
                    <span>Rows per page</span>
                    <select id="per-page-select">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </section>

        </div>
      </div>

      <?php $this->load->view('includes_footer'); ?>
    </div>
  </div>
</div>

<div id="training-modal" aria-hidden="true">
  <div class="tm-card" role="dialog" aria-modal="true" aria-labelledby="training-modal-title">
    <div class="tm-head">
      <h3 class="tm-title" id="training-modal-title">New Training Post</h3>
      <button type="button" class="tm-close" id="btn-close-training-modal" aria-label="Close">
        <i class="mdi mdi-close"></i>
      </button>
    </div>

    <form method="post" enctype="multipart/form-data" action="<?= site_url('tesda/trainings/store') ?>">
      <div class="form-grid">
        <div class="field field-wide">
          <label for="new_title">Training Title *</label>
          <input
            id="new_title"
            type="text"
            name="title"
            required
            maxlength="200"
            value="<?= htmlspecialchars((string)set_value('title'), ENT_QUOTES, 'UTF-8') ?>"
            placeholder="e.g., Shielded Metal Arc Welding NC II - Scholarship Batch"
          />
        </div>

        <div class="field">
          <label for="new_province">Province</label>
          <select id="new_province" name="province" data-pre="<?= htmlspecialchars((string)set_value('province'), ENT_QUOTES, 'UTF-8') ?>">
            <option value="">Select Province</option>
          </select>
        </div>

        <div class="field">
          <label for="new_city">City / Municipality</label>
          <select id="new_city" name="city" data-pre="<?= htmlspecialchars((string)set_value('city'), ENT_QUOTES, 'UTF-8') ?>" disabled>
            <option value="">Select City / Municipality</option>
          </select>
        </div>

        <div class="field">
          <label for="new_brgy">Barangay</label>
          <select id="new_brgy" name="brgy" data-pre="<?= htmlspecialchars((string)set_value('brgy'), ENT_QUOTES, 'UTF-8') ?>" disabled>
            <option value="">Select Barangay</option>
          </select>
        </div>

        <input
          id="new_location_text"
          type="hidden"
          name="location_text"
          value="<?= htmlspecialchars((string)set_value('location_text'), ENT_QUOTES, 'UTF-8') ?>"
        />
        <input
          type="hidden"
          id="new_address_id"
          name="address_id"
          value="<?= (int)set_value('address_id') ?>"
        >

        <div class="field">
          <label for="new_link">Website / Apply Link</label>
          <input
            id="new_link"
            type="url"
            name="website_url"
            value="<?= htmlspecialchars((string)set_value('website_url'), ENT_QUOTES, 'UTF-8') ?>"
            placeholder="https://..."
          />
        </div>

        <div class="field">
          <label for="new_training_image">Training Image</label>
          <input
            id="new_training_image"
            type="file"
            name="training_image"
            accept=".jpg,.jpeg,.png,.webp,.gif,image/*"
          />
        </div>

        <div class="field field-wide">
          <label for="new_desc">Description</label>
          <textarea
            id="new_desc"
            name="description"
            placeholder="Include qualifications, schedule, requirements, and slot details."
          ><?= htmlspecialchars((string)set_value('description'), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button class="xbtn xbtn-outline" type="button" id="btn-cancel-training-modal">Cancel</button>
        <button class="xbtn xbtn-primary" type="submit">
          <i class="mdi mdi-plus-circle-outline"></i>
          <span>Post Training</span>
        </button>
      </div>
    </form>
  </div>
</div>

<div id="training-desc-modal" aria-hidden="true">
  <div class="tdm-card" role="dialog" aria-modal="true" aria-labelledby="training-desc-title">
    <div class="tdm-head">
      <h3 class="tdm-title" id="training-desc-title">Training Description</h3>
      <button type="button" class="tdm-close" id="btn-close-training-desc" aria-label="Close">
        <i class="mdi mdi-close"></i>
      </button>
    </div>
    <p class="tdm-body" id="training-desc-body"></p>
  </div>
</div>

<script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
<script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
<script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
<script src="<?= base_url('assets/js/misc.js') ?>"></script>

<script>
(function () {
  'use strict';

  var tbody   = document.getElementById('posts-tbody');
  var dtInfo  = document.getElementById('dt-info');
  var dtPager = document.getElementById('dt-pagination');
  var perSel  = document.getElementById('per-page-select');

  var allRows = [];
  var currentPage = 1;
  var perPage = 10;

  function collectRows() {
    if (!tbody) return;
    allRows = Array.prototype.slice.call(tbody.querySelectorAll('tr[data-id]'));
  }

  function mkBtn(label, page, active, disabled, isIcon) {
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'pg-btn' + (active ? ' active' : '');
    b.disabled = disabled;
    b.innerHTML = isIcon ? '<i class="mdi ' + label + '"></i>' : label;

    if (!disabled && !active) {
      b.addEventListener('click', function () {
        currentPage = page;
        renderPage();
      }, { passive: true });
    }
    return b;
  }

  function renderPage() {
    if (!tbody || !dtInfo || !dtPager) return;

    var total = allRows.length;
    var pages = Math.max(1, Math.ceil(total / perPage));
    currentPage = Math.min(currentPage, pages);

    var start = (currentPage - 1) * perPage;
    var end   = Math.min(start + perPage, total);

    requestAnimationFrame(function () {
      for (var i = 0; i < total; i++) {
        allRows[i].style.display = (i >= start && i < end) ? '' : 'none';
      }
    });

    if (total === 0) {
      dtInfo.innerHTML = 'No training posts found';
    } else {
      dtInfo.innerHTML = 'Showing <strong>' + (start + 1) + '-' + end + '</strong> of <strong>' + total + '</strong> posts';
    }

    var frag = document.createDocumentFragment();

    if (pages > 1) {
      frag.appendChild(mkBtn('mdi-chevron-left', currentPage - 1, false, currentPage === 1, true));

      var delta = 1;
      var prev = null;

      for (var p = 1; p <= pages; p++) {
        if (p === 1 || p === pages || (p >= currentPage - delta && p <= currentPage + delta)) {
          if (prev !== null && p - prev > 1) {
            var el = document.createElement('span');
            el.className = 'pg-ellipsis';
            el.textContent = '...';
            frag.appendChild(el);
          }
          frag.appendChild(mkBtn(String(p), p, p === currentPage, false, false));
          prev = p;
        }
      }

      frag.appendChild(mkBtn('mdi-chevron-right', currentPage + 1, false, currentPage === pages, true));
    }

    dtPager.innerHTML = '';
    dtPager.appendChild(frag);
  }

  if (perSel) {
    perSel.addEventListener('change', function () {
      perPage = parseInt(perSel.value, 10) || 10;
      currentPage = 1;
      renderPage();
    }, { passive: true });
  }

  collectRows();
  renderPage();

  function splitLocation(raw) {
    var text = (raw || '').trim();
    var out = { province: '', city: '', brgy: '' };
    if (!text) return out;

    var parts = text.split(',').map(function (s) {
      return s.trim();
    }).filter(Boolean);

    if (parts.length >= 3) {
      out.province = parts[parts.length - 1];
      out.city     = parts[parts.length - 2];
      out.brgy     = parts.slice(0, parts.length - 2).join(', ');
    } else if (parts.length === 2) {
      out.city     = parts[0];
      out.province = parts[1];
    } else if (parts.length === 1) {
      out.city = parts[0];
    }

    return out;
  }

  function initAddressPicker(prefix) {
    var apiBase      = (document.body && document.body.dataset && document.body.dataset.apiAddress) || '<?= site_url('address/api') ?>';
    var provSel      = document.getElementById(prefix + '_province');
    var citySel      = document.getElementById(prefix + '_city');
    var brgySel      = document.getElementById(prefix + '_brgy');
    var locInput     = document.getElementById(prefix + '_location_text');
    var addressInput = document.getElementById(prefix + '_address_id');

    if (!provSel || !citySel || !brgySel || !locInput) return;

    var originalLocation = (locInput.value || '').trim();
    var parsed = splitLocation(originalLocation);

    var pre = {
      province: (provSel.dataset.pre || '').trim() || parsed.province,
      city:     (citySel.dataset.pre || '').trim() || parsed.city,
      brgy:     (brgySel.dataset.pre || '').trim() || parsed.brgy
    };

    function reset(sel, placeholder) {
      sel.innerHTML = '<option value="">' + placeholder + '</option>';
      sel.disabled = true;
    }

    function fill(sel, items, placeholder) {
      sel.innerHTML = '<option value="">' + placeholder + '</option>';
      (items || []).forEach(function (item) {
        var opt = document.createElement('option');
        opt.value = item;
        opt.textContent = item;
        sel.appendChild(opt);
      });
      sel.disabled = false;
    }

    function composeLocation() {
      var parts = [brgySel.value || '', citySel.value || '', provSel.value || ''].filter(Boolean);
      locInput.value = parts.length ? parts.join(', ') : originalLocation;
      if (addressInput) addressInput.value = '';
    }

    function fetchItems(qs) {
      return fetch(apiBase + '?' + qs, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(function (r) { return r.json(); })
      .then(function (json) {
        return (json && json.ok && Array.isArray(json.items)) ? json.items : [];
      });
    }

    function onProvinceChange(isInit) {
      reset(citySel, 'Select City / Municipality');
      reset(brgySel, 'Select Barangay');
      composeLocation();

      var province = provSel.value;
      if (!province) return Promise.resolve();

      return fetchItems('scope=city&province=' + encodeURIComponent(province))
        .then(function (items) {
          fill(citySel, items, 'Select City / Municipality');

          if (isInit && pre.city && items.indexOf(pre.city) !== -1) {
            citySel.value = pre.city;
            return onCityChange(true);
          }

          composeLocation();
        })
        .catch(function () {
          reset(citySel, 'Unavailable');
          reset(brgySel, 'Unavailable');
        });
    }

    function onCityChange(isInit) {
      reset(brgySel, 'Select Barangay');
      composeLocation();

      var province = provSel.value;
      var city = citySel.value;

      if (!province || !city) return Promise.resolve();

      return fetchItems('scope=brgy&province=' + encodeURIComponent(province) + '&city=' + encodeURIComponent(city))
        .then(function (items) {
          fill(brgySel, items, 'Select Barangay');

          if (isInit && pre.brgy && items.indexOf(pre.brgy) !== -1) {
            brgySel.value = pre.brgy;
          }

          composeLocation();
        })
        .catch(function () {
          reset(brgySel, 'Unavailable');
        });
    }

    provSel.addEventListener('change', function () {
      onProvinceChange(false);
    });

    citySel.addEventListener('change', function () {
      onCityChange(false);
    });

    brgySel.addEventListener('change', composeLocation);

    reset(citySel, 'Select City / Municipality');
    reset(brgySel, 'Select Barangay');

    fetchItems('scope=province')
      .then(function (items) {
        fill(provSel, items, 'Select Province');

        if (pre.province && items.indexOf(pre.province) !== -1) {
          provSel.value = pre.province;
          return onProvinceChange(true);
        }

        composeLocation();
      })
      .catch(function () {
        reset(provSel, 'Unavailable');
        reset(citySel, 'Unavailable');
        reset(brgySel, 'Unavailable');
      });
  }

  initAddressPicker('new');
  initAddressPicker('edit');

  var modal     = document.getElementById('training-modal');
  var btnOpen   = document.getElementById('btn-open-training-modal');
  var btnClose  = document.getElementById('btn-close-training-modal');
  var btnCancel = document.getElementById('btn-cancel-training-modal');

  function toggleModal(show) {
    if (!modal) return;

    modal.style.display = show ? 'flex' : 'none';
    modal.setAttribute('aria-hidden', show ? 'false' : 'true');
    document.body.style.overflow = show ? 'hidden' : '';

    if (show) {
      var firstInput = modal.querySelector('input[name="title"]');
      if (firstInput) firstInput.focus();
    }
  }

  if (btnOpen) {
    btnOpen.addEventListener('click', function () {
      toggleModal(true);
    }, { passive: true });
  }

  if (btnClose) {
    btnClose.addEventListener('click', function () {
      toggleModal(false);
    }, { passive: true });
  }

  if (btnCancel) {
    btnCancel.addEventListener('click', function () {
      toggleModal(false);
    }, { passive: true });
  }

  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) {
        toggleModal(false);
      }
    }, { passive: true });
  }

  var descModal = document.getElementById('training-desc-modal');
  var descTitle = document.getElementById('training-desc-title');
  var descBody  = document.getElementById('training-desc-body');
  var descClose = document.getElementById('btn-close-training-desc');

  function toggleDescModal(show, title, body) {
    if (!descModal || !descTitle || !descBody) return;

    descModal.style.display = show ? 'flex' : 'none';
    descModal.setAttribute('aria-hidden', show ? 'false' : 'true');

    if (show) {
      descTitle.textContent = title || 'Training Description';
      descBody.textContent  = body || '';
      document.body.style.overflow = 'hidden';
    } else if (!modal || modal.style.display !== 'flex') {
      document.body.style.overflow = '';
    }
  }

  document.addEventListener('click', function (e) {
    var btn = e.target && e.target.closest ? e.target.closest('.desc-more-btn') : null;
    if (!btn) return;

    e.preventDefault();
    toggleDescModal(
      true,
      btn.getAttribute('data-desc-title') || 'Training Description',
      btn.getAttribute('data-desc-body') || ''
    );
  });

  if (descClose) {
    descClose.addEventListener('click', function () {
      toggleDescModal(false);
    }, { passive: true });
  }

  if (descModal) {
    descModal.addEventListener('click', function (e) {
      if (e.target === descModal) {
        toggleDescModal(false);
      }
    }, { passive: true });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (descModal && descModal.style.display === 'flex') {
        toggleDescModal(false);
      } else if (modal && modal.style.display === 'flex') {
        toggleModal(false);
      }
    }
  });
})();
</script>
</body>
</html>