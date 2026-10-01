<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
/**
 * Shared <head> partial — the single place for global meta, fonts,
 * vendor CSS, the theme stylesheet, design tokens and favicon.
 *
 * Usage from a view (replaces the hand-copied head block):
 *   <head><?php $this->load->view('partials/head', [
 *       'page_title' => 'Dashboard',
 *       'css'        => ['assets/css/dashboard-worker.css?v=4.0.0'],
 *   ]); ?></head>
 *
 * Extra page-specific CSS files go in `css`; extra meta/links in
 * `extra_head` (raw HTML, escaped by the caller). The translate banner,
 * nav and footer stay in includes_nav/includes_nav_top/includes_footer.
 *
 * @var string $page_title
 * @var array  $css        list of hrefs relative to base_url
 * @var string $extra_head optional raw HTML appended to <head>
 */
$page_title = $page_title ?? 'JobMatch DavOr';
$css        = is_array($css ?? null) ? $css : [];
?>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="referrer" content="strict-origin-when-cross-origin" />
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?> — JobMatch DavOr</title>

  <link rel="stylesheet" href="<?= base_url('assets/fonts/karla/karla.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/vertical-light/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css?v=20260625b') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/tokens.css?v=1') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/universal.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css?v=1.0.0') ?>">
<?php foreach ($css as $href): ?>
  <link rel="stylesheet" href="<?= base_url($href) ?>">
<?php endforeach; ?>

  <link rel="shortcut icon" href="<?= base_url('assets/images/logo.png') ?>" />
<?= $extra_head ?? '' ?>
