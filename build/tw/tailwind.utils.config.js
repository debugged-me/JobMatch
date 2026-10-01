/** Utilities-only Tailwind build (no preflight) for pages that also load Bootstrap. */
module.exports = {
  content: [
    '../../application/views/users_list.php',
    '../../application/views/dashboard_peso.php',
    '../../application/views/school_admin_bulk.php',
    '../../application/views/school_admin_workers.php',
    '../../application/views/school_admin_form.php',
  ],
  corePlugins: { preflight: false },
  theme: { extend: {} },
  plugins: [],
};
