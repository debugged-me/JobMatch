<?php defined("BASEPATH") or exit("No direct script access allowed");

class Address extends CI_Controller
{
    private const LIMIT_PROVINCE = "Davao Oriental";

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Address_model");
        $this->output->set_content_type("application/json; charset=utf-8");
        if (method_exists($this->output, "enable_profiler")) {
            $this->output->enable_profiler(false);
        }
        if (ob_get_level() > 0) {
            @ob_end_clean();
        }
    }

    private function is_worker(): bool
    {
        return strtolower((string) $this->session->userdata("role")) ===
            "worker";
    }

    public function api()
    {
        try {
            $scope = strtolower(
                trim((string) $this->input->get("scope", true)),
            );
            $province = trim((string) $this->input->get("province", true));
            $city = preg_replace(
                "/\s+/",
                " ",
                trim((string) $this->input->get("city", true)),
            );

            $isWorker = $this->is_worker();

            switch ($scope) {
                case "province":
                    if ($isWorker) {
                        return $this->_out(true, "ok", [self::LIMIT_PROVINCE]);
                    }
                    return $this->_out(
                        true,
                        "ok",
                        $this->Address_model->get_provinces(),
                    );

                case "city":
                    if ($isWorker) {
                        // ✅ worker: province fixed, but return ALL cities in that province
                        $items = $this->Address_model->get_cities(
                            self::LIMIT_PROVINCE,
                        );
                        return $this->_out(true, "ok", $items);
                    }

                    if ($province === "") {
                        return $this->_out(false, "Missing province", [], 400);
                    }
                    return $this->_out(
                        true,
                        "ok",
                        $this->Address_model->get_cities($province),
                    );

                case "brgy":
                    if ($isWorker) {
                        // ✅ worker: province fixed; brgy depends on selected city
                        $province = self::LIMIT_PROVINCE;

                        if ($city === "") {
                            return $this->_out(false, "Missing city", [], 400);
                        }

                        // Optional: normalize city to DB exact value (case/spacing)
                        $allCities = $this->Address_model->get_cities(
                            $province,
                        );
                        $exactCity = null;

                        foreach ($allCities as $c) {
                            $cNorm = preg_replace(
                                "/\s+/",
                                " ",
                                trim((string) $c),
                            );
                            if (
                                $cNorm !== "" &&
                                strcasecmp($cNorm, $city) === 0
                            ) {
                                $exactCity = $cNorm;
                                break;
                            }
                        }

                        // If the city typed doesn't exist in DB, return empty (do NOT force Mati)
                        if ($exactCity === null) {
                            return $this->_out(true, "ok", []);
                        }

                        $city = $exactCity;
                    } else {
                        if ($province === "" || $city === "") {
                            return $this->_out(
                                false,
                                "Missing province/city",
                                [],
                                400,
                            );
                        }
                    }

                    $items = $this->Address_model->get_barangays(
                        $province,
                        $city,
                    );
                    return $this->_out(true, "ok", $items);

                default:
                    return $this->_out(false, "Invalid scope", [], 400);
            }
        } catch (\Throwable $e) {
            log_message("error", "Address API error: " . $e->getMessage());
            return $this->_out(false, "Server error", [], 500);
        }
    }

    private function _out($ok, $msg, $items = [], $status = 200)
    {
        $payload = [
            "ok" => (bool) $ok,
            "msg" => (string) $msg,
            "items" => $items,
        ];
        if ($this->config->item("csrf_protection")) {
            $payload["csrf_name"] = $this->security->get_csrf_token_name();
            $payload["csrf_hash"] = $this->security->get_csrf_hash();
        }

        $this->output->set_status_header($status);
        $this->output->set_output(
            json_encode($payload, JSON_UNESCAPED_UNICODE),
        );
        return;
    }
}
