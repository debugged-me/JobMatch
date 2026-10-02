<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * SchemaMigrate — auto-applies pending SQL migrations from
 * database/migrations/*.sql once per deploy. Tracks applied files in a
 * `schema_migrations` table so production never needs a manual mysql import.
 *
 * Runs on post_controller_constructor; fails silently (logged) so a bad
 * migration can never take the site down. Migration files MUST be
 * idempotent — they may be retried after a partial failure.
 */
class SchemaMigrate
{
    private const TABLE = 'schema_migrations';
    private const DIR   = 'database/migrations';
    private const LOCK  = 'jm_schema_migrate';

    public function run(): void
    {
        try {
            $CI =& get_instance();
            $CI->load->database();
            $db = $CI->db;

            // MySQL advisory lock — concurrent requests skip; no filesystem
            // dependency (works even when writable/ isn't web-writable).
            $row = $db->query(
                'SELECT GET_LOCK(' . $db->escape(self::LOCK) . ', 0) AS l'
            )->row();
            if (!$row || (int)$row->l !== 1) return;

            try {
                $this->migrate($db);
            } finally {
                $db->query('SELECT RELEASE_LOCK(' . $db->escape(self::LOCK) . ')');
            }
        } catch (Throwable $e) {
            log_message('error', 'SchemaMigrate: ' . $e->getMessage());
        }
    }

    private function migrate($db): void
    {
        $files = glob(FCPATH . self::DIR . '/*.sql');
        if (!$files) return;

        // Tracking table first.
        $db->query(sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` (
                `filename`   VARCHAR(120) NOT NULL PRIMARY KEY,
                `applied_at` DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
            self::TABLE
        ));

        $applied = array_flip(array_column(
            $db->select('filename')->get(self::TABLE)->result_array(),
            'filename'
        ));

        sort($files);
        foreach ($files as $file) {
            $name = basename($file);
            if (isset($applied[$name])) continue;

            $sql = file_get_contents($file);
            if ($sql === false) continue;

            foreach ($this->statements($sql) as $stmt) {
                if ($db->query($stmt) === false) {
                    $err = $db->error();
                    log_message('error', "SchemaMigrate: {$name} failed: "
                        . ($err['message'] ?? 'unknown'));
                    throw new RuntimeException("Migration {$name} failed");
                }
            }

            $db->insert(self::TABLE, [
                'filename'   => $name,
                'applied_at' => date('Y-m-d H:i:s'),
            ]);
            log_message('info', "SchemaMigrate: applied {$name}");
        }
    }

    /**
     * Split a .sql file into executable statements: ';' ends a statement
     * anywhere on a line (migrations use PREPARE/EXECUTE chains), quotes
     * and comment lines are respected.
     */
    private function statements(string $sql): array
    {
        // Strip line comments first.
        $lines = [];
        foreach (preg_split('/\r?\n/', $sql) as $line) {
            $t = trim($line);
            if ($t === '' || strpos($t, '--') === 0 || strpos($t, '/*') === 0) {
                continue;
            }
            $lines[] = $line;
        }
        $sql = implode("\n", $lines);

        $out = [];
        $buf = '';
        $inStr = null; // ', " or `
        $len = strlen($sql);
        for ($i = 0; $i < $len; $i++) {
            $ch = $sql[$i];
            if ($inStr !== null) {
                $buf .= $ch;
                if ($ch === $inStr && ($i === 0 || $sql[$i - 1] !== '\\')) {
                    $inStr = null;
                }
                continue;
            }
            if ($ch === "'" || $ch === '"' || $ch === '`') {
                $inStr = $ch;
                $buf .= $ch;
                continue;
            }
            if ($ch === ';') {
                if (trim($buf) !== '') $out[] = $buf;
                $buf = '';
                continue;
            }
            $buf .= $ch;
        }
        if (trim($buf) !== '') $out[] = $buf;
        return $out;
    }
}
