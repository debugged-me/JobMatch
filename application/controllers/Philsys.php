<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Philsys — national ID (PhilID / ePhilID) QR verification endpoints.
 *
 *   POST philsys/preview  — decode-only lookup used on the signup page.
 *                           Offline signature check; nothing is stored.
 *   POST philsys/attach   — attach a scanned ID to the logged-in account.
 *                           Runs signature + online card-status checks.
 *   POST philsys/review   — staff confirm/reject a pending scan.
 */
class Philsys extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('philsys_qr');
        $this->load->model('User_model', 'users');
    }

    /**
     * Decode + offline-verify a QR payload for the signup form.
     * No login required (guest scans their own card); nothing is persisted.
     */
    public function preview()
    {
        $this->require_post_json();

        $raw = (string)$this->input->post('qr', false); // raw — must not be XSS-filtered
        $r   = $this->philsys_qr->analyze($raw);

        if ($r['ok'] && !empty($r['everify'])) {
            $ev = $this->philsys_qr->everify_check((string)$r['qr_value']);
            if ($ev['pending_consent'] || !$ev['ok']) {
                return $this->json_out([
                    'ok' => false,
                    'msg' => $ev['error'] ?: 'eVerify lookup failed.',
                ]);
            }
            if ($ev['profile']) {
                $r['subject']   = $this->philsys_qr->everify_subject($ev['profile']);
                $r['full_name'] = trim(implode(' ', array_filter([
                    $r['subject']['fName'], $r['subject']['mName'],
                    $r['subject']['lName'], $r['subject']['Suffix'],
                ])));
                $r['full_name'] = preg_replace('/\s+/', ' ', $r['full_name']);
                $r['dob']       = $r['subject']['DOB'] !== ''
                    ? date('Y-m-d', strtotime($r['subject']['DOB'])) : null;
                $pcn = preg_replace('/\D/', '', $r['subject']['PCN']);
                if ($pcn !== '') $r['pcn_hash'] = hash('sha256', $pcn);
            }
            $r['card_type'] = $ev['card_type'];
            $r['sig_valid'] = true;  // registry lookup is authoritative
        }

        if (!$r['ok']) {
            return $this->json_out([
                'ok' => false,
                'msg' => 'This QR code is not a PhilID/ePhilID. ' . ($r['error'] ?? ''),
            ]);
        }

        $s = $r['subject'];
        return $this->json_out([
            'ok'        => true,
            'card_type' => $r['card_type'],
            'sig_valid' => $r['sig_valid'],
            'subject'   => [
                'fName'  => (string)($s['fName'] ?? ''),
                'lName'  => (string)($s['lName'] ?? ''),
                'mName'  => (string)($s['mName'] ?? ''),
                'suffix' => (string)($s['Suffix'] ?? ''),
                'sex'    => (string)($s['sex'] ?? ''),
                'dob'    => $r['dob'],
                'pob'    => (string)($s['POB'] ?? ''),
                // Raw PCN is never sent back or stored — masked only.
                'pcn_masked' => $this->mask_pcn((string)($s['PCN'] ?? '')),
            ],
        ]);
    }

    /**
     * Verify and attach a scanned national ID to the current account.
     * Full check: offline signature (v1) + PSA online card status.
     */
    public function attach()
    {
        $this->require_post_json();
        $this->require_login_json();

        $uid = $this->current_user_id();
        $raw = (string)$this->input->post('qr', false);
        $r   = $this->philsys_qr->analyze($raw);

        if ($r['ok'] && !empty($r['everify'])) {
            $ev = $this->philsys_qr->everify_check((string)$r['qr_value']);
            if ($ev['pending_consent'] || !$ev['ok']) {
                return $this->json_out([
                    'ok' => false,
                    'msg' => $ev['error'] ?: 'eVerify lookup failed.',
                ]);
            }
            if ($ev['profile']) {
                $r['subject']   = $this->philsys_qr->everify_subject($ev['profile']);
                $r['full_name'] = trim(implode(' ', array_filter([
                    $r['subject']['fName'], $r['subject']['mName'],
                    $r['subject']['lName'], $r['subject']['Suffix'],
                ])));
                $r['full_name'] = preg_replace('/\s+/', ' ', $r['full_name']);
                $r['dob']       = $r['subject']['DOB'] !== ''
                    ? date('Y-m-d', strtotime($r['subject']['DOB'])) : null;
                $pcn = preg_replace('/\D/', '', $r['subject']['PCN']);
                if ($pcn !== '') $r['pcn_hash'] = hash('sha256', $pcn);
            }
            $r['card_type'] = $ev['card_type'];
            $r['sig_valid'] = true;  // registry lookup is authoritative
        }

        if (!$r['ok']) {
            $this->users->record_philsys($uid, ['status' => 'failed']);
            return $this->json_out([
                'ok' => false, 'status' => 'failed',
                'msg' => 'Not a valid PhilID/ePhilID QR. ' . ($r['error'] ?? ''),
            ]);
        }

        // Online card-status check (PSA PhilSys Check endpoint). null = offline.
        // eVerify payloads already hit the registry — treat as confirmed.
        $r['online'] = !empty($r['everify']) ? true : null;
        if ($r['normalized'] && ($r['sig_valid'] !== false)) {
            $r['online'] = $this->philsys_qr->verify_online($r['normalized']);
        }

        // One national ID → one account.
        if ($r['pcn_hash']) {
            $owner = $this->users->philsys_pcn_owner($r['pcn_hash']);
            if ($owner !== null && $owner !== $uid) {
                return $this->json_out([
                    'ok' => false, 'status' => 'failed',
                    'msg' => 'This National ID is already linked to another account.',
                ], 409);
            }
        }

        $me = $this->users->get_by_id($uid);
        // A verified account cannot silently swap to a different ID — only a
        // rescan of the same card may refresh the record.
        if (($me->philsys_status ?? '') === 'verified'
            && $r['pcn_hash'] && $me->philsys_pcn_hash !== $r['pcn_hash']) {
            return $this->json_out([
                'ok' => false, 'status' => 'verified',
                'msg' => 'This account is already verified with a different National ID. Contact PESO staff to change it.',
            ], 409);
        }
        $accountName = trim(($me->first_name ?? '') . ' ' . ($me->last_name ?? ''));
        $nameMatch = $this->philsys_qr->name_matches($accountName, (string)$r['full_name']);

        $r['status'] = $this->philsys_qr->status_for($r, $nameMatch);
        $this->users->record_philsys($uid, $r);

        $msg = [
            'verified' => 'National ID verified.',
            'pending'  => 'ID accepted — pending staff confirmation.',
            'failed'   => 'ID could not be verified.',
        ][$r['status']];

        return $this->json_out([
            'ok'      => $r['status'] !== 'failed',
            'status'  => $r['status'],
            'msg'     => $msg,
            'card'    => $r['card_type'],
            'sig'     => $r['sig_valid'],
            'online'  => $r['online'],
            'match'   => $nameMatch,
            'name'    => $r['full_name'],
            'dob'     => $r['dob'],
            'subject' => [
                'sex' => (string)($r['subject']['sex'] ?? ''),
            ],
        ]);
    }

    /** Staff confirm/reject a user's pending scan. */
    public function review()
    {
        $this->require_post_json();
        $this->require_role_json(['admin', 'peso', 'tesda admin', 'school admin']);

        $uid    = (int)$this->input->post('user_id');
        $action = (string)$this->input->post('action', true);
        if ($uid <= 0 || !in_array($action, ['verify', 'reject'], true)) {
            return $this->json_out(['ok' => false, 'msg' => 'Bad request'], 422);
        }

        $ok = $this->users->review_philsys($uid, $action, $this->current_user_id());
        return $this->json_out([
            'ok' => $ok,
            'msg' => $ok
                ? ($action === 'verify' ? 'Marked verified.' : 'Marked failed.')
                : 'Update failed.',
            'status' => $ok ? ($action === 'verify' ? 'verified' : 'failed') : null,
        ]);
    }

    private function mask_pcn(string $pcn): string
    {
        $d = preg_replace('/\D/', '', $pcn);
        if (strlen($d) < 4) return '••••';
        return '••••-••••-••••-' . substr($d, -4);
    }
}
