<?php $hideFooterBar = !empty($hide_footer_bar); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/footer-modal.css') ?>">

<?php if (!$hideFooterBar): ?>
  <footer class="footer twx-footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
      <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
        Copyright &copy; <?= date('Y') ?>
        <a href="#" id="twx-about-link" class="twx-about-link" data-twx-open="about" aria-haspopup="dialog" aria-controls="twx-about-modal">
          <span class="twx-brand-blue">JobMatch</span><span class="twx-brand-red"> DavOr</span>
        </a>
        All rights reserved.
      </span>
    </div>
  </footer>
<?php endif; ?>

<div id="twx-about-modal" class="twx-modal" aria-hidden="true">
  <div class="twx-modal__overlay" data-twx-close></div>
  <div class="twx-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="twx-about-title" aria-describedby="twx-about-desc" tabindex="-1">
    <button class="twx-modal__close" type="button" aria-label="Close" data-twx-close>
      <i class="mdi mdi-close"></i>
    </button>

    <header class="twx-modal__header">
      <div class="twx-brand-pill">
        <i class="mdi mdi-briefcase-account-outline"></i> JobMatch DavOr
      </div>
      <h3 id="twx-about-title">Where every task finds its expert.</h3>
      <p class="twx-sub" id="twx-about-desc">
        JobMatch DavOr connects people who need help with skilled workers who can deliver &mdash; fast, reliable, and transparent.
      </p>
    </header>

    <section class="twx-features">
      <div class="twx-feature">
        <i class="mdi mdi-magnify"></i>
        <div>
          <div class="twx-feature-title">Search &amp; hire</div>
          <div class="twx-feature-desc">Find workers by skill, view profiles &amp; portfolios, and start a job.</div>
        </div>
      </div>
      <div class="twx-feature">
        <i class="mdi mdi-message-text-outline"></i>
        <div>
          <div class="twx-feature-title">Message in-app</div>
          <div class="twx-feature-desc">Align on scope, confirm, complete, and review &mdash; all in one place.</div>
        </div>
      </div>
      <div class="twx-feature">
        <i class="mdi mdi-image-multiple-outline"></i>
        <div>
          <div class="twx-feature-title">Showcase work</div>
          <div class="twx-feature-desc">Add portfolio items (images/PDFs) and build trust with reviews.</div>
        </div>
      </div>
      <div class="twx-feature">
        <i class="mdi mdi-shield-account-outline"></i>
        <div>
          <div class="twx-feature-title">Quality &amp; safety</div>
          <div class="twx-feature-desc">Profiles, categories, and approvals help keep standards high.</div>
        </div>
      </div>
    </section>

    <footer class="twx-modal__footer">
      <span class="twx-note"><i class="mdi mdi-palette-swatch"></i> Brand: blue - gold - red</span>
      <button class="twx-btn" type="button" data-twx-close>Close</button>
    </footer>
  </div>
</div>

<?php if (!empty($this->session->userdata('logged_in'))): ?>
  <div id="jmFooterConfig"
    data-logged-in="1"
    data-csrf-name="<?= $this->security->get_csrf_token_name(); ?>"
    data-csrf-hash="<?= $this->security->get_csrf_hash(); ?>"
    data-presence-ping-url="<?= site_url('messages/api_presence_ping') ?>"
    data-presence-beacon-url="<?= site_url('messages/presence_beacon') ?>"></div>
<?php else: ?>
  <div id="jmFooterConfig" data-logged-in="0"></div>
<?php endif; ?>

<script src="<?= base_url('assets/js/jm-ui.js') ?>"></script>
<script src="<?= base_url('assets/js/csrf.js') ?>"></script>
<script src="<?= base_url('assets/js/footer.js') ?>"></script>
