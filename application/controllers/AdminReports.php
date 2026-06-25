<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminReports extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        // TODO: add your admin auth/ACL here
        $this->load->model('AdminReport_model', 'R');
        $this->load->helper(['url','html']);
    }
public function index() {
    $this->load->model('AdminReport_model', 'R');

    $dateFrom = trim((string)$this->input->get('from', true));
    $dateTo   = trim((string)$this->input->get('to', true));
    $data['dateFrom'] = $dateFrom;
    $data['dateTo']   = $dateTo;

    // KPI tiles
    $data['total_jobs']            = $this->R->totalJobs($dateFrom, $dateTo);
    $data['jobs_with_apps']        = $this->R->jobsWithApps($dateFrom, $dateTo);
    $data['total_client_projects'] = $this->R->totalClientProjects($dateFrom, $dateTo);
    $data['projects_with_apps']    = $this->R->projectsWithApps($dateFrom, $dateTo);

    // Tables
    $data['jobs_all']      = $this->R->allJobsWithApplicantTotals($dateFrom, $dateTo);
    $data['jobs_applied']  = $this->R->jobsWithApplicantsOnly($dateFrom, $dateTo);
    $data['clients_sum']   = $this->R->clientProjectsSummary($dateFrom, $dateTo);

    $clientIDs = array_map(function($r){ return (int)$r['clientID']; }, $data['clients_sum']);
    $data['client_labels'] = $this->R->clientLabelMap($clientIDs);

  $data['printMode'] = (bool)$this->input->get('print');
  if ($data['printMode']) {
    // include names for print
    $data['jobApplicants'] = $this->R->applicantsByJobForPrint();
    $this->load->view('admin_reports_dashboard_print_min', $data);
    return; // IMPORTANT: prevent the normal view from loading
  }

  $data['page_title'] = 'Admin Reports — Jobs & Projects';
  $this->load->view('admin_reports_dashboard', $data);
}

    public function export_csv() {
        $this->load->model('AdminReport_model', 'R');
        $dateFrom = trim((string)$this->input->get('from', true));
        $dateTo   = trim((string)$this->input->get('to', true));

        $jobs_all = $this->R->allJobsWithApplicantTotals($dateFrom, $dateTo);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="jobmatch_reports_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Title', 'Type', 'Status', 'Created', 'Applicants']);
        foreach ($jobs_all as $r) {
            fputcsv($out, [
                $r['id'] ?? '',
                $r['title'] ?? '',
                $r['post_type'] ?? '',
                $r['status'] ?? '',
                $r['created_at'] ?? '',
                $r['applicant_count'] ?? 0,
            ]);
        }
        fclose($out);
        exit;
    }


    public function client($id = null) {
    $clientID = (int)$id;
    if ($clientID <= 0) show_404();

    $data['page_title'] = 'Client Projects — Report';
    $data['clientID']   = $clientID;
    $data['projects']   = $this->R->projectsByClient($clientID);

    $labMap = $this->R->clientLabelMap([$clientID]);
    $data['client_label'] = $labMap[$clientID] ?? ('Client #'.$clientID);

  $data['printMode'] = (bool)$this->input->get('print');
  if ($data['printMode']) {
    $data['projectApplicants'] = $this->R->applicantsByClientProjectForPrint($clientID);
    $this->load->view('admin_reports_client_projects_print_min', $data);
    return; // IMPORTANT
  }

  $this->load->view('admin_reports_client_projects', $data);
}

}
