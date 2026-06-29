<?php defined('BASEPATH') or exit('No direct script access allowed');

class ClientProfile_model extends CI_Model
{
    protected $table = 'client_profile';
    protected $has_company_position = false;
    protected $column_nullable_cache = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // Optional column support (future-proof)
        $this->has_company_position = $this->db->field_exists('company_position', $this->table);
        $this->load_column_nullability();
    }

    protected function load_column_nullability(): void
    {
        $this->column_nullable_cache = [];

        if (!$this->db->table_exists($this->table)) {
            return;
        }

        $table = $this->db->protect_identifiers($this->table, true);
        $query = $this->db->query("SHOW COLUMNS FROM {$table}");
        if (!$query) {
            return;
        }

        foreach ($query->result_array() as $col) {
            $name = (string)($col['Field'] ?? '');
            if ($name === '') continue;
            $this->column_nullable_cache[$name] = strtoupper((string)($col['Null'] ?? 'YES')) === 'YES';
        }
    }

    protected function column_allows_null(string $column): bool
    {
        if (array_key_exists($column, $this->column_nullable_cache)) {
            return (bool)$this->column_nullable_cache[$column];
        }
        return true;
    }

    /**
     * Sanitize only fields that are PRESENT in payload.
     * Do NOT inject missing keys (prevents overwriting existing DB values on update).
     */
    protected function sanitize_payload(array $data): array
    {
        // Strongly-typed text fields we always persist as strings.
        $stringFields = ['fName', 'mName', 'lName', 'phoneNo', 'companyName', 'city', 'province', 'brgy'];

        foreach ($stringFields as $f) {
            if (array_key_exists($f, $data)) {
                // If null is passed, normalize to empty string for NOT NULL columns
                $data[$f] = ($data[$f] === null) ? '' : (string) $data[$f];
            }
        }

        // Optional text/varchar fields.
        $optionalTextFields = [
            'address',
            'employer',
            'business_name',
            'business_location',
            'avatar',
            'id_image',
            'business_permit',
            'company_position',
        ];

        foreach ($optionalTextFields as $f) {
            if (array_key_exists($f, $data)) {
                $allowsNull = $this->column_allows_null($f);

                if ($data[$f] === null && !$allowsNull) {
                    $data[$f] = '';
                } elseif ($data[$f] === '' && $allowsNull) {
                    $data[$f] = null;
                }

                if ($data[$f] !== null) {
                    $data[$f] = (string) $data[$f];
                }
            }
        }

        // Certificates: store JSON string or NULL
        if (array_key_exists('certificates', $data)) {
            if (is_array($data['certificates'])) {
                $vals = array_values(array_unique(array_filter($data['certificates'], function ($x) {
                    return $x !== null && $x !== '';
                })));
                $data['certificates'] = !empty($vals) ? json_encode($vals) : ($this->column_allows_null('certificates') ? null : '');
            } else {
                $v = trim((string) $data['certificates']);
                if ($v !== '') {
                    $data['certificates'] = $v;
                } else {
                    $data['certificates'] = $this->column_allows_null('certificates') ? null : '';
                }
            }
        }

        // Remove company_position if column doesn't exist
        if (!$this->has_company_position && array_key_exists('company_position', $data)) {
            unset($data['company_position']);
        }

        return $data;
    }

    /**
     * Ensure there is a row for this user.
     * IMPORTANT: This assumes client_profile.clientID == users.id
     */
    public function ensure_row($user_id, array $seed = [])
    {
        $user_id = (int) $user_id;

        $row = $this->db->get_where($this->table, ['clientID' => $user_id])->row();
        if ($row) return $row;

        $now = date('Y-m-d H:i:s');

        // Defaults based on your schema (NOT NULL columns must be non-null)
        $data = [
            'clientID'          => $user_id,
            'fName'             => $seed['fName'] ?? '',
            'mName'             => $seed['mName'] ?? '',
            'lName'             => $seed['lName'] ?? '',
            'phoneNo'           => $seed['phoneNo'] ?? '',
            'companyName'       => $seed['companyName'] ?? '',
            'city'              => $seed['city'] ?? '',
            'province'          => $seed['province'] ?? '',
            'brgy'              => $seed['brgy'] ?? '',
            'employer'          => $seed['employer'] ?? null,
            'business_name'     => $seed['business_name'] ?? null,
            'business_location' => $seed['business_location'] ?? null,
            'address'           => $seed['address'] ?? null,
            'avatar'            => $seed['avatar'] ?? null,
            'id_image'          => $seed['id_image'] ?? null,
            'certificates'      => $seed['certificates'] ?? null,
            'business_permit'   => $seed['business_permit'] ?? null,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];

        if ($this->has_company_position) {
            $data['company_position'] = $seed['company_position'] ?? null;
        }

        // Allow extra seed keys (but sanitize after)
        $data = array_merge($data, $seed);
        $data = $this->sanitize_payload($data);

        $ok = $this->db->insert($this->table, $data);
        if (!$ok) return null;

        return $this->db->get_where($this->table, ['clientID' => $user_id])->row();
    }

    /**
     * Joined profile (users + client_profile)
     */
    public function get($user_id)
    {
        $user_id = (int) $user_id;

        $columns = [
            'u.id as user_id',
            'u.email',
            'u.first_name',
            'u.last_name',
            'u.role',

            'c.clientID',
            'c.fName',
            'c.mName',
            'c.lName',
            'c.phoneNo',
            'c.companyName',
        ];

        if ($this->has_company_position) $columns[] = 'c.company_position';

        $columns = array_merge($columns, [
            'c.city',
            'c.province',
            'c.brgy',
            'c.address',
            'c.employer',
            'c.business_name',
            'c.business_location',
            'c.avatar',
            'c.id_image',
            'c.certificates',
            'c.business_permit',
            'c.created_at',
            'c.updated_at',
        ]);

        $this->db->select(implode(', ', $columns));
        $this->db->from('users u');
        $this->db->join($this->table . ' c', 'c.clientID = u.id', 'left');
        $this->db->where('u.id', $user_id);

        return $this->db->get()->row();
    }

    /**
     * Basic "profile complete" rule:
     * - has first/last name (either in client_profile or users)
     * - has id_image uploaded
     */
    public function is_complete($p)
    {
        if (!$p) return false;

        $hasNames =
            ((trim((string) $p->fName) !== '') || (trim((string) $p->first_name) !== '')) &&
            ((trim((string) $p->lName) !== '') || (trim((string) $p->last_name) !== ''));

        $hasId = !empty($p->id_image);

        return $hasNames && $hasId;
    }

    /**
     * Upsert: update only fields present in $data.
     * Returns: ['ok'=>bool,'changed'=>bool,'row'=>object|null,'error'=>string|null]
     */
    public function upsert($user_id, array $data = [])
    {
        $user_id = (int) $user_id;

        $allowed = [
            'fName',
            'mName',
            'lName',
            'phoneNo',
            'companyName',
            'city',
            'province',
            'brgy',
            'address',
            'employer',
            'business_name',
            'business_location',
            'avatar',
            'id_image',
            'certificates',
            'business_permit',
        ];

        if ($this->has_company_position) $allowed[] = 'company_position';

        $clean = array_intersect_key($data, array_flip($allowed));
        $clean = $this->sanitize_payload($clean);

        $exists = $this->db->select('clientID')
            ->from($this->table)
            ->where('clientID', $user_id)
            ->limit(1)
            ->get()
            ->num_rows() > 0;

        $now = date('Y-m-d H:i:s');

        if ($exists) {
            if (empty($clean)) {
                return ['ok' => true, 'changed' => false, 'row' => $this->get($user_id)];
            }

            $clean['updated_at'] = $now;

            $ok = $this->db->where('clientID', $user_id)->update($this->table, $clean);
            if (!$ok) {
                $err = $this->db->error();
                return ['ok' => false, 'changed' => false, 'row' => null, 'error' => $err['message'] ?? 'DB update failed'];
            }

            return ['ok' => true, 'changed' => ($this->db->affected_rows() > 0), 'row' => $this->get($user_id)];
        }

        // Insert path: MUST create a row with clientID = users.id
        $defaults = [
            'clientID'          => $user_id,
            'fName'             => '',
            'mName'             => '',
            'lName'             => '',
            'phoneNo'           => '',
            'companyName'       => '',
            'city'              => '',
            'province'          => '',
            'brgy'              => '',
            'employer'          => null,
            'business_name'     => null,
            'business_location' => null,
            'address'           => null,
            'avatar'            => null,
            'id_image'          => null,
            'certificates'      => null,
            'business_permit'   => null,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];

        if ($this->has_company_position) $defaults['company_position'] = null;

        $row = array_merge($defaults, $clean);
        $row = $this->sanitize_payload($row);

        $ok = $this->db->insert($this->table, $row);
        if (!$ok) {
            $err = $this->db->error();
            return ['ok' => false, 'changed' => false, 'row' => null, 'error' => $err['message'] ?? 'DB insert failed'];
        }

        return ['ok' => true, 'changed' => true, 'row' => $this->get($user_id)];
    }

    /* ===================================================================
     * NSRP Form 2 — Establishment Registration
     * =================================================================== */

    /** Establishment/contact columns the NSRP Form 2 owns on client_profile. */
    private $nsrp_establishment_fields = [
        // Business identity
        'companyName','business_name','trade_name','acronym','office_type','tin',
        'employer_type','employer_subtype','workforce_size','line_of_business',
        // Address
        'street_village','brgy','city','province','address',
        // Contact details
        'owner_name','contact_person','contact_position','phoneNo','telephone','fax',
        // First/last/middle name of the registering user (kept for parity with client_profile)
        'fName','mName','lName',
    ];

    /** Full joined row (users + client_profile, all columns) for the NSRP form. */
    public function get_full($user_id)
    {
        $this->db->select('u.id as user_id, u.email, u.first_name, u.last_name, u.role, c.*', false);
        $this->db->from('users u');
        $this->db->join($this->table . ' c', 'c.clientID = u.id', 'left');
        $this->db->where('u.id', (int)$user_id);
        return $this->db->get()->row();
    }

    /** Save the establishment/contact portion of NSRP Form 2. */
    public function save_establishment($user_id, array $data): bool
    {
        $user_id = (int)$user_id;
        $this->ensure_row($user_id);

        $clean = array_intersect_key($data, array_flip($this->nsrp_establishment_fields));
        foreach ($clean as $k => $v) {
            if ($v === null) $clean[$k] = '';
        }
        if (empty($clean)) return true;

        $clean['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('clientID', $user_id)->update($this->table, $clean);
        return $this->db->error()['code'] == 0;
    }

    /** PESO-only: set establishment registration status. */
    public function set_nsrp_status($user_id, string $status): bool
    {
        if (!in_array($status, ['draft','submitted','assessed'], true)) return false;
        $this->db->where('clientID', (int)$user_id)
            ->update($this->table, ['nsrp_status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        return $this->db->error()['code'] == 0;
    }

    /* =======================
     * Notifications
     * ======================= */

    public function add_notification($user_id, $actor_id, $type, $title, $body = '', $link = '')
    {
        $data = [
            'user_id'    => (int) $user_id,
            'actor_id'   => (int) $actor_id,
            'type'       => (string) $type,
            'title'      => (string) $title,
            'body'       => (string) $body,
            'link'       => (string) $link,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return (bool) $this->db->insert('tw_notifications', $data);
    }

    public function get_notifications($user_id, $limit = 10, $offset = 0, array $types = null)
    {
        $this->db->select('n.*, u.first_name AS actor_fname, u.last_name AS actor_lname')
            ->from('tw_notifications n')
            ->join('users u', 'u.id = n.actor_id', 'left')
            ->where('n.user_id', (int) $user_id);

        if ($types && count($types)) {
            $this->db->where_in('n.type', $types);
        }

        return $this->db->order_by('n.created_at', 'DESC')
            ->limit((int) $limit, (int) $offset)
            ->get()
            ->result();
    }

    public function unread_count($user_id, array $types = null)
    {
        $this->db->from('tw_notifications')
            ->where('user_id', (int) $user_id)
            ->where('is_read', 0);

        if ($types && count($types)) {
            $this->db->where_in('type', $types);
        }

        return (int) $this->db->count_all_results();
    }

    public function mark_read($id, $user_id)
    {
        $data = ['is_read' => 1];

        if ($this->db->field_exists('read_at', 'tw_notifications')) {
            $data['read_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', (int) $id)
            ->where('user_id', (int) $user_id)
            ->update('tw_notifications', $data);

        return ($this->db->affected_rows() > 0);
    }

    public function mark_read_by_actor($user_id, $actor_id, array $types = [])
    {
        $data = ['is_read' => 1];

        if ($this->db->field_exists('read_at', 'tw_notifications')) {
            $data['read_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('user_id', (int) $user_id)
            ->where('actor_id', (int) $actor_id)
            ->where('is_read', 0);

        if (!empty($types)) {
            $this->db->where_in('type', $types);
        }

        $this->db->update('tw_notifications', $data);

        return (int) $this->db->affected_rows();
    }
}
