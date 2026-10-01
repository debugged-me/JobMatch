/** Full Tailwind build (with preflight) for standalone pages without Bootstrap. */
module.exports = {
  content: [
    '../../application/views/client_edit.php',
    '../../application/views/profile_edit.php',
  ],
  theme: { extend: {} },
  plugins: [],
};
