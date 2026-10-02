<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Philsys_qr — decode + verify PhilSys national ID QR payloads.
 *
 * Supports both card formats:
 *   - PhilID (v1, physical card): the QR text is plain JSON
 *     {DateIssued, Issuer, subject{...}, alg:"EDDSA", signature:<base64>}.
 *     The signature is Ed25519 over base64(serialized-payload-minus-signature)
 *     and can be verified offline via libsodium — no PSA round-trip needed.
 *   - ePhilID (v3, digital/printed): 4-char prefix + Base45 → CBOR/COSE
 *     containing the signed demographic claim (incl. embedded photo).
 *     Offline signature check for COSE needs PSA's certificate chain, so
 *     v3 relies on the online check for authenticity.
 *   - eVerify (newer cards): QR is just the PCN digits or a short token —
 *     identity comes from the eVerify registry (app-ws.everify.gov.ph);
 *     eGovPH-type cards additionally need the holder's in-app approval.
 *
 * Optional online check: the same public endpoint the PSA PhilSys Check
 * site uses (verify.philsys.gov.ph/api/verify) confirms the card is still
 * active and not revoked/deactivated. Controlled by env JM_PHILSYS_ONLINE
 * (default on). Failures degrade gracefully to `null` (unknown).
 *
 * Privacy (RA 11055): we never persist the raw PCN — callers should store
 * only hash('sha256', $pcn) from the 'pcn_hash' field of analyze().
 */
class Philsys_qr
{
    /** PSA Ed25519 public key used to sign legacy PhilID QRs (base64). */
    private const PUBLIC_KEY_B64 = 'vD3czlgHEpf2sxGcri6iTm4zeEEA+jfd9tTq9S8zxe8=';

    private const VERIFY_BASE = 'https://verify.philsys.gov.ph/';
    private const VERIFY_API  = 'https://verify.philsys.gov.ph/api/verify';
    private const EVERIFY_BASE = 'https://app-ws.everify.gov.ph';
    private const UA = 'Mozilla/5.0 (Linux; Android 10; Mobile) AppleWebKit/537.36 '
        . '(KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36';
    private const B45_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:';

    /* ---------------------------------------------------------------- *
     *  Public API
     * ---------------------------------------------------------------- */

    /**
     * Decode a scanned QR payload.
     * Returns ['ok'=>bool, 'version'=>1|3|null, 'card_type'=>string|null,
     *          'sig_valid'=>bool|null, 'subject'=>array, 'full_name'=>string,
     *          'dob'=>'Y-m-d'|null, 'pcn_hash'=>string|null,
     *          'normalized'=>array|null, 'error'=>string]
     */
    public function analyze(string $raw): array
    {
        $out = [
            'ok' => false, 'version' => null, 'card_type' => null,
            'sig_valid' => null, 'subject' => [], 'full_name' => '',
            'dob' => null, 'pcn_hash' => null, 'normalized' => null,
            'error' => '',
        ];
        $raw = trim($raw);
        if ($raw === '') {
            $out['error'] = 'Empty QR payload.';
            return $out;
        }

        // v1: payload is JSON. v3 (ePhilID): base45/COSE binary text.
        // eVerify: bare PCN digits or short A-Z0-9 token (newer cards).
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $this->analyze_v1($json, $out);
        }
        if ($this->is_everify_payload($raw)) {
            $out['ok']        = true;
            $out['version']   = 9;
            $out['card_type'] = 'ePhilID';
            $out['sig_valid'] = null;
            $out['everify']   = true;   // identity comes from the eVerify API
            $out['qr_value']  = $raw;
            $pcn = preg_replace('/\D/', '', $raw);
            if (strlen($pcn) >= 12) {
                $out['pcn_hash'] = hash('sha256', $pcn);
            }
            return $out;
        }
        return $this->analyze_v3($raw, $out);
    }

    /**
     * Online card-status check against the public PSA PhilSys Check endpoint.
     * Returns true (active), false (rejected/deactivated/invalid) or
     * null (service unreachable / disabled).
     */
    public function verify_online(array $normalized): ?bool
    {
        $off = getenv('JM_PHILSYS_ONLINE');
        if ($off === false) $off = $_SERVER['JM_PHILSYS_ONLINE'] ?? null;
        if ($off === '0' || !function_exists('curl_init')) {
            return null;
        }

        $jar = tempnam(sys_get_temp_dir(), 'psys');
        if ($jar === false) return null;

        try {
            // 1) Bootstrap session cookie (same flow PhilSys Check uses).
            $ch = curl_init(self::VERIFY_BASE);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_NOBODY         => false,
                CURLOPT_HEADER         => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_TIMEOUT        => 6,
                CURLOPT_COOKIEJAR      => $jar,
                CURLOPT_COOKIEFILE     => $jar,
                CURLOPT_USERAGENT      => self::UA,
                CURLOPT_HTTPHEADER     => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.9',
                ],
            ]);
            $home = curl_exec($ch);
            $homeCode = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);
            if ($home === false || $homeCode !== 200) return null;

            // 2) POST the normalized payload.
            $ch = curl_init(self::VERIFY_API);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_TIMEOUT        => 6,
                CURLOPT_COOKIEFILE     => $jar,
                CURLOPT_COOKIEJAR      => $jar,
                CURLOPT_USERAGENT      => self::UA,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Accept: */*',
                    'Accept-Language: en-US,en;q=0.9',
                    'Referer: ' . self::VERIFY_BASE,
                    'Origin: https://verify.philsys.gov.ph',
                ],
                CURLOPT_POSTFIELDS     => json_encode($normalized, JSON_UNESCAPED_UNICODE),
            ]);
            $resp  = curl_exec($ch);
            $code  = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($resp === false) return null;
            // PhilSys Check: HTTP 200 = card verified/active; anything else
            // (4xx, or a body signalling failure) = rejected.
            return $code === 200;
        } catch (Throwable $e) {
            log_message('error', 'Philsys_qr::verify_online: ' . $e->getMessage());
            return null;
        } finally {
            @unlink($jar);
        }
    }

    /**
     * Decide the persisted status from scan analysis + online result.
     * $nameMatches: whether the ID name matches the account name.
     * Returns 'verified' | 'pending' | 'failed'.
     */
    public function status_for(array $r, ?bool $nameMatches): string
    {
        if (!($r['ok'] ?? false)) return 'failed';
        // Explicitly-invalid signature = tampered/forged card → reject.
        // sig_valid=null means we could not check (no libsodium / COSE) and
        // falls through to 'pending' for staff review rather than failing.
        if (($r['sig_valid'] ?? null) === false) return 'failed';
        if (($r['online'] ?? null) === false) return 'failed';          // revoked/invalid
        // 'verified' only when PSA confirms the card is active AND the ID
        // name plausibly matches the account. A valid offline signature alone
        // can't detect deactivated cards, so it goes to staff review.
        if (($r['online'] ?? null) === true && $nameMatches === true) return 'verified';
        return 'pending';
    }

    /** Loose comparison of an account name vs the name on the ID. */
    public function name_matches(string $accountName, string $idName): ?bool
    {
        $norm = function (string $s): string {
            $s = mb_strtolower(trim($s));
            $s = preg_replace('/[^a-z0-9 ]/i', '', $s);
            return preg_replace('/\s+/', ' ', $s);
        };
        $a = $norm($accountName);
        $b = $norm($idName);
        if ($a === '' || $b === '') return null;
        if ($a === $b) return true;
        // Tolerate middle-name/suffix differences: every token of the shorter
        // name must appear in the longer one.
        $ta = array_filter(explode(' ', $a));
        $tb = array_filter(explode(' ', $b));
        $small = count($ta) <= count($tb) ? $ta : $tb;
        $big   = count($ta) <= count($tb) ? $tb : $ta;
        return count(array_diff($small, $big)) === 0;
    }

    /* ---------------------------------------------------------------- *
     *  v1 — legacy PhilID (JSON + Ed25519)
     * ---------------------------------------------------------------- */

    private function analyze_v1(array $j, array $out): array
    {
        if (!isset($j['subject']) || !is_array($j['subject'])) {
            $out['error'] = 'QR is JSON but not a PhilSys payload.';
            return $out;
        }
        $s = $j['subject'];
        $out['version']   = 1;
        $out['card_type'] = 'PhilID';
        $out['subject']   = $s;
        $out['sig_valid'] = $this->verify_v1_signature($j);
        $out['ok']        = true;

        $this->fill_common($out, [
            'fName' => $s['fName'] ?? '', 'lName' => $s['lName'] ?? '',
            'mName' => $s['mName'] ?? '', 'Suffix' => $s['Suffix'] ?? '',
            'sex' => $s['sex'] ?? '', 'DOB' => $s['DOB'] ?? '',
            'POB' => $s['POB'] ?? '', 'PCN' => $s['PCN'] ?? '',
        ]);
        $out['normalized'] = [
            'd'   => $this->norm_date((string)($j['DateIssued'] ?? '')),
            'i'   => (string)($j['Issuer'] ?? ''),
            'img' => '',
            'sb'  => [
                'BF'  => (string)($s['BF'] ?? ''),
                'DOB' => $this->norm_date((string)($s['DOB'] ?? '')),
                'PCN' => preg_replace('/\D/', '', (string)($s['PCN'] ?? '')),
                'POB' => (string)($s['POB'] ?? ''),
                'fn'  => (string)($s['fName'] ?? ''),
                'ln'  => (string)($s['lName'] ?? ''),
                'mn'  => (string)($s['mName'] ?? ''),
                's'   => (string)($s['sex'] ?? ''),
                'sf'  => (string)($s['Suffix'] ?? ''),
            ],
        ];
        return $out;
    }

    /**
     * Ed25519 check of the v1 QR. Returns true/false when the signature was
     * actually evaluated, or null when it could not be checked (no libsodium,
     * unusable key) — callers must treat null as "unknown", not "invalid".
     */
    private function verify_v1_signature(array $j): ?bool
    {
        $sigB64 = (string)($j['signature'] ?? '');
        if ($sigB64 === '') {
            return false; // v1 always carries a signature — absent = malformed
        }
        if (!function_exists('sodium_crypto_sign_verify_detached')) {
            return null; // server cannot evaluate signatures
        }
        $pubB64 = getenv('JM_PHILSYS_PUBKEY')
            ?: ($_SERVER['JM_PHILSYS_PUBKEY'] ?? null)
            ?: self::PUBLIC_KEY_B64;

        // Rebuild the exact signed document: template minus "signature",
        // with ñ/Ñ replaced by '?' (same quirk the issuer applies), then
        // base64()'d — the bytes of that base64 string are the message.
        $formatted = $this->format_v1($j);
        $clean     = strtr($formatted, ['ñ' => '?', 'Ñ' => '?']);
        $message   = base64_encode($clean);

        $sig = base64_decode($sigB64, true);
        $pk  = base64_decode($pubB64, true);
        if ($sig === false || strlen($sig) !== SODIUM_CRYPTO_SIGN_BYTES) return false;
        if ($pk === false || strlen($pk) !== SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES) return null;

        try {
            return sodium_crypto_sign_verify_detached($sig, $message, $pk);
        } catch (Throwable $e) {
            return false;
        }
    }

    /** Exact serialization the signature covers (field order matters). */
    private function format_v1(array $j): string
    {
        $s = $j['subject'];
        $f = function ($v) {
            return (string)($v ?? '');
        };
        return
            "{\n" .
            '  "DateIssued": "' . $f($j['DateIssued']) . "\",\n" .
            '  "Issuer": "' . $f($j['Issuer']) . "\",\n" .
            '  "subject": {' . "\n" .
            '    "Suffix": "' . $f($s['Suffix']) . "\",\n" .
            '    "lName": "' . $f($s['lName']) . "\",\n" .
            '    "fName": "' . $f($s['fName']) . "\",\n" .
            '    "mName": "' . $f($s['mName']) . "\",\n" .
            '    "sex": "' . $f($s['sex']) . "\",\n" .
            '    "BF": "' . $f($s['BF']) . "\",\n" .
            '    "DOB": "' . $f($s['DOB']) . "\",\n" .
            '    "POB": "' . $f($s['POB']) . "\",\n" .
            '    "PCN": "' . $f($s['PCN']) . "\"\n" .
            "  },\n" .
            '  "alg": "' . $f($j['alg']) . "\"\n" .
            "}";
    }

    /** Mirror of the eVerify candidate heuristic (openverify). */
    private function is_everify_payload(string $raw): bool
    {
        if (preg_match('/^\d{12,20}$/', $raw)) return true;          // bare PCN
        if (strpos($raw, ':') !== false) return false;              // COSE/base45 prefixes contain ':'
        return (bool)preg_match('/^[A-Z0-9]{16,40}$/', $raw);       // short token
    }

    /**
     * eVerify lookup (newer ePhilID / National ID / eGovPH QRs).
     * 1) POST /api/pub/qr/check {value} → qr_type + either the profile
     *    inline or a tracking_number (eGovPH consent flow).
     * 2) GET /api/pub/qr/egov_ph?tracking_number=… → profile appears once
     *      the holder approves the share in the eGovPH app.
     * Returns ['ok'=>bool,'profile'=>array|null,'card_type'=>string,
     *          'pending_consent'=>bool,'error'=>string]
     */
    public function everify_check(string $value): array
    {
        $ret = ['ok' => false, 'profile' => null, 'card_type' => 'ePhilID',
                'pending_consent' => false, 'error' => ''];
        $off = getenv('JM_PHILSYS_ONLINE');
        if ($off === false) $off = $_SERVER['JM_PHILSYS_ONLINE'] ?? null;
        if ($off === '0' || !function_exists('curl_init')) {
            $ret['error'] = 'Online verification disabled.';
            return $ret;
        }

        $raw = $this->http_json('POST', self::EVERIFY_BASE . '/api/pub/qr/check',
            ['value' => trim($value)]);
        if ($raw === null) { $ret['error'] = 'eVerify service unreachable.'; return $ret; }

        $qrType = (string)($raw['meta']['qr_type'] ?? '');
        if ($qrType !== '') $ret['card_type'] = $qrType === 'eGovPH' ? 'ePhilID' : $qrType;

        $payload = $raw['data']['data'] ?? $raw['data'] ?? [];
        if (is_array($payload) && $this->profile_ready($payload)) {
            $ret['ok'] = true;
            $ret['profile'] = $payload;
            return $ret;
        }

        // eGovPH consent flow — poll briefly; verification completes when the
        // holder approves the request inside their eGovPH app.
        $tracking = (string)($payload['tracking_number'] ?? '');
        if ($tracking !== '') {
            for ($i = 0; $i < 2; $i++) {
                if ($i > 0) sleep(4);
                $p = $this->http_json('GET', self::EVERIFY_BASE
                    . '/api/pub/qr/egov_ph?tracking_number=' . urlencode($tracking), null);
                $profile = $p['data']['data'] ?? $p['data'] ?? null;
                if (is_array($profile) && $this->profile_ready($profile)) {
                    $ret['ok'] = true;
                    $ret['profile'] = $profile;
                    return $ret;
                }
            }
            $ret['pending_consent'] = true;
            $ret['error'] = 'Approve the verification request in the eGovPH app on the ID holder\'s phone, then scan again.';
            return $ret;
        }

        // Card type exists in the registry but carries no identity payload
        // (e.g. "Philsys Card Number") — still a positive registry hit.
        if ($qrType !== '') {
            $ret['ok'] = true;
            return $ret;
        }
        $ret['error'] = 'ID not found in the PhilSys registry.';
        return $ret;
    }

    /** A profile counts as usable once it carries identity signals. */
    private function profile_ready(array $p): bool
    {
        if (array_key_exists('verified', $p)) return $p['verified'] === true;
        foreach (['first_name', 'full_name', 'last_name', 'face_url', 'image', 'pcn', 'reference', 'code'] as $k) {
            if (!empty($p[$k])) return true;
        }
        return false;
    }

    /** Map an eVerify/eGovPH profile onto our v1-style subject keys. */
    public function everify_subject(array $p): array
    {
        return [
            'fName' => (string)($p['first_name'] ?? $p['given_name'] ?? ''),
            'lName' => (string)($p['last_name']  ?? $p['surname'] ?? ''),
            'mName' => (string)($p['middle_name'] ?? $p['middle_initial'] ?? ''),
            'Suffix' => (string)($p['suffix'] ?? ''),
            'sex'   => (string)($p['gender'] ?? $p['sex'] ?? ''),
            'DOB'   => (string)($p['birth_date'] ?? $p['dob'] ?? ''),
            'POB'   => (string)($p['place_of_birth'] ?? $p['pob'] ?? ''),
            'PCN'   => (string)($p['pcn'] ?? $p['reference'] ?? ''),
        ];
    }

    private function http_json(string $method, string $url, ?array $body): ?array
    {
        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_USERAGENT      => self::UA,
            CURLOPT_HTTPHEADER     => ['Accept: application/json, text/plain, */*'],
        ];
        if ($method === 'POST') {
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE);
            $opts[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }
        curl_setopt_array($ch, $opts);
        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($resp === false || $code === 404) return null;
        $json = json_decode($resp, true);
        return is_array($json) ? $json : null;
    }

    /* ---------------------------------------------------------------- *
     *  v3 — ePhilID (base45 → CBOR COSE_Sign1)
     * ---------------------------------------------------------------- */

    private function analyze_v3(string $raw, array $out): array
    {
        try {
            // Payload carries a 4-char header before the base45 body.
            $bin  = $this->base45_decode(substr($raw, 4));
            $cose = $this->cbor_decode($bin);
            if (!is_array($cose) || count($cose) < 4) {
                $out['error'] = 'Unrecognized ePhilID QR.';
                return $out;
            }
            $claim = $this->cbor_decode((string)$cose[2]);
            if (!is_array($claim) || ($claim[1] ?? '') !== 'PH') {
                $out['error'] = 'Not a Philippine (PH) credential.';
                return $out;
            }
            $cred = $claim[169] ?? null;   // credential data map
            if (!is_array($cred) || empty($cred['sb'])) {
                $out['error'] = 'Missing credential data in ePhilID.';
                return $out;
            }
            $sb = $cred['sb'];

            $out['ok']        = true;
            $out['version']   = 3;
            $out['card_type'] = 'ePhilID';
            // Offline COSE signature check needs PSA's cert chain; the online
            // check is what proves authenticity for ePhilID.
            $out['sig_valid'] = null;

            // Embedded photo arrives as a byte string — keep it off the log
            // and only forward it (base64) to the verifier.
            if (isset($cred['img']) && is_string($cred['img'])) {
                $cred['img'] = base64_encode($cred['img']);
            }
            $out['normalized'] = $cred;

            $this->fill_common($out, [
                'fName' => $sb['fn'] ?? '', 'lName' => $sb['ln'] ?? '',
                'mName' => $sb['mn'] ?? '', 'Suffix' => $sb['sf'] ?? '',
                'sex' => $sb['s'] ?? '', 'DOB' => $sb['DOB'] ?? '',
                'POB' => $sb['POB'] ?? '', 'PCN' => $sb['PCN'] ?? '',
            ]);
            return $out;
        } catch (Throwable $e) {
            $out['error'] = 'Could not decode ePhilID QR (' . $e->getMessage() . ').';
            return $out;
        }
    }

    private function base45_decode(string $str): string
    {
        $map = array_flip(str_split(self::B45_CHARS));
        $out = '';
        $len = strlen($str);
        for ($i = 0; $i + 2 < $len; $i += 3) {
            $v = $map[$str[$i]] + $map[$str[$i + 1]] * 45 + $map[$str[$i + 2]] * 2025;
            $out .= chr(($v >> 8) & 0xff) . chr($v & 0xff);
        }
        if ($i < $len) { // 2-char tail → 1 byte
            $v = $map[$str[$i]] + $map[$str[$i + 1]] * 45;
            $out .= chr($v & 0xff);
        }
        return $out;
    }

    /* ---------------- minimal CBOR decoder (definite lengths) -------- */

    private function cbor_decode(string $bin)
    {
        $p = 0;
        return $this->cbor_read($bin, $p);
    }

    private function cbor_read(string $b, int &$p)
    {
        if ($p >= strlen($b)) throw new RuntimeException('CBOR: truncated');
        $ib = ord($b[$p++]);
        $mt = $ib >> 5;
        $ai = $ib & 0x1f;
        $val = $this->cbor_arg($b, $p, $ai);
        switch ($mt) {
            case 0:
                return $val;
            case 1:
                return -1 - $val;
            case 2:
            case 3:
                $s = substr($b, $p, $val);
                $p += $val;
                return $s;
            case 4:
                $a = [];
                for ($i = 0; $i < $val; $i++) $a[] = $this->cbor_read($b, $p);
                return $a;
            case 5:
                $m = [];
                for ($i = 0; $i < $val; $i++) {
                    $k = $this->cbor_read($b, $p);
                    $m[$k] = $this->cbor_read($b, $p);
                }
                return $m;
            case 6:
                return $this->cbor_read($b, $p); // unwrap tag
            case 7:
                if ($ai === 20) return false;
                if ($ai === 21) return true;
                if ($ai === 22 || $ai === 23) return null;
                if ($ai === 25) { // half float
                    $h = $val;
                    $exp = ($h >> 10) & 0x1f; $man = $h & 0x3ff;
                    if ($exp === 0) return ldexp($man, -24);
                    if ($exp === 31) return $man ? NAN : INF;
                    return ldexp(1024 + $man, $exp - 25);
                }
                if ($ai === 26) return unpack('G', pack('N', $val))[1];
                if ($ai === 27) {
                    $hi = $val >> 32; $lo = $val & 0xffffffff;
                    return unpack('E', pack('NN', $hi, $lo))[1];
                }
                throw new RuntimeException('CBOR: unsupported simple ' . $ai);
        }
        throw new RuntimeException('CBOR: bad major type');
    }

    private function cbor_arg(string $b, int &$p, int $ai): int
    {
        if ($ai < 24) return $ai;
        if ($ai === 24) { $v = ord($b[$p]); $p += 1; return $v; }
        if ($ai === 25) { $v = unpack('n', substr($b, $p, 2))[1]; $p += 2; return $v; }
        if ($ai === 26) { $v = unpack('N', substr($b, $p, 4))[1]; $p += 4; return $v; }
        if ($ai === 27) { $u = unpack('N2', substr($b, $p, 8)); $p += 8; return ($u[1] << 32) + $u[2]; }
        throw new RuntimeException('CBOR: indefinite length not supported');
    }

    /* ---------------------------------------------------------------- *
     *  Shared helpers
     * ---------------------------------------------------------------- */

    private function fill_common(array &$out, array $s): void
    {
        $out['subject'] = $s;
        $name = trim(implode(' ', array_filter([
            $s['fName'], $s['mName'], $s['lName'], $s['Suffix'],
        ])));
        $out['full_name'] = preg_replace('/\s+/', ' ', $name);
        $out['dob'] = $this->norm_date((string)($s['DOB'] ?? ''));
        $pcn = preg_replace('/\D/', '', (string)($s['PCN'] ?? ''));
        $out['pcn_hash'] = $pcn !== '' ? hash('sha256', $pcn) : null;
    }

    /** 'May 05, 1982' / '1982-05-05' / '05 May 1982' → '1982-05-05'|null */
    private function norm_date(string $s): ?string
    {
        $s = trim($s);
        if ($s === '') return null;
        $t = strtotime($s);
        return $t === false ? null : date('Y-m-d', $t);
    }
}
