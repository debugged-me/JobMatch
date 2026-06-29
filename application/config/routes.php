<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'landing';
$route['login'] = 'auth/login';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['auth/resend'] = 'auth/resend_activation';
$route['profile']        = 'profile/edit';
$route['profile/edit']   = 'profile/edit';
$route['profile/update'] = 'profile/update';
$route['portfolio']        = 'portfolio/index';
$route['portfolio/create'] = 'portfolio/create';
$route['portfolio/store']  = 'portfolio/store';

$route['reviews/store'] = 'reviews/store';
$route['dashboard/client']       = 'dashboard/client';
$route['client/profile/edit']    = 'client/edit';
$route['client/profile/update']  = 'client/update'; 
$route['projects/active']  = 'projects/active';
$route['projects/history'] = 'projects/history';
$route['projects/create']  = 'projects/create';
$route['projects/store']   = 'projects/store';
$route['projects/close/(:num)'] = 'projects/close/$1';

$route['search'] = 'Search/index';

$route['media/preview'] = 'media/preview';
$route['media/wm']      = 'media/wm_image';
$route['media/wm-pdf']   = 'media/wm_pdf';  
$route['message/start']                       = 'client/message_start';
$route['client/notify_hire']                  = 'client/notify_hire';
$route['client/notifications_feed']           = 'client/notifications_feed';
$route['client/notifications_count']          = 'client/notifications_count';
$route['client/notifications_mark_read/(:num)']= 'client/notifications_mark_read/$1';
$route['notifications/feed']                 = 'notifications/feed';
$route['notifications/count']                = 'notifications/count';
$route['notifications/mark_read/(:num)']     = 'notifications/mark_read/$1';
$route['notifications/notify_hire']          = 'notifications/notify_hire';
$route['client/notify_hire']        = 'notifications/notify_hire';
$route['projects/api/active-min']   = 'projects/api_active_min';
$route['projects/api/one/(:num)']   = 'projects/api_one/$1';

$route['message/start']                      = 'messages/start';
$route['message/t/(:num)']                   = 'messages/t/$1';
$route['message/api/thread/(:num)']          = 'messages/api_thread/$1';
$route['message/api/send']                   = 'messages/api_send';
$route['message/api/read/(:num)']            = 'messages/api_read/$1';

$route['messages/start']                     = 'messages/start';
$route['messages/t/(:num)']                  = 'messages/t/$1';
$route['messages/api/thread/(:num)']         = 'messages/api_thread/$1';
$route['messages/api/send']                  = 'messages/api_send';
$route['messages/api/read/(:num)']           = 'messages/api_read/$1';
$route['messages/count'] = 'messages/api_unread';
$route['messages/feed']  = 'messages/api_feed';
$route['messages/api/start'] = 'messages/api_start';

$route['projects/api/one/(:num)'] = 'projects/api_one/$1';
$route['transactions/api/accept'] = 'transactions/api_accept';
$route['transactions/api/decline']= 'transactions/api_decline';

$route['messages/api/invite-action']     = 'messages/api_invite_action';
$route['messages/api_invite_action']     = 'messages/api_invite_action';

$route['admin/skills']      = 'admin/skills';
$route['admin/skills/save'] = 'admin/saveSkill';
$route['admin/skills/update/(:num)'] = 'admin/updateSkill/$1';
$route['admin/skills/delete/(:num)'] = 'admin/deleteSkill/$1';

$route['admin/workers/upload']   = 'AdminWorkers/index';
$route['admin/workers/preview']  = 'AdminWorkers/preview';
$route['admin/workers/import']   = 'AdminWorkers/import';
$route['admin/workers/template'] = 'AdminWorkers/template';
$route['admin/users']            = 'users/index';
$route['admin/users/toggle']     = 'users/toggle';
$route['admin/users/approve']    = 'users/approve';
$route['admin/users/resend']     = 'users/resend'; 
$route['auth/activate']           = 'auth/activate'; 
$route['auth/activate/(:any)']    = 'auth/activate/$1'; 
$route['hires/accept']      = 'hires/accept';
$route['personnel/hired']   = 'personnel/hired';
$route['payments'] = 'payments/index';
$route['auth/email-available'] = 'auth/email_available';
$route['auth/forgot']         = 'auth/forgot';
$route['auth/reset/(:any)']   = 'auth/reset/$1';
$route['users']         = 'users/index';
$route['users/toggle']  = 'users/toggle';
$route['profile/delete_doc']['post'] = 'profile/delete_doc';
$route['client/delete_doc'] = 'client/delete_doc';
$route['deactivate']            = 'deactivate/index';
$route['deactivate/do_action']  = 'deactivate/do_action';
$route['visibility']      = 'visibility/index';
$route['visibility/set']  = 'visibility/set';
$route['services/mix'] = 'services/mix';
$route['complaints']                      = 'Complaints/index';
$route['complaints/new']                  = 'Complaints/create';
$route['complaints/store']                = 'Complaints/store';
$route['complaints/(:num)']               = 'Complaints/show/$1';
$route['admin/complaints']                = 'Complaints/admin_index';
$route['admin/complaints/(:num)']         = 'Complaints/admin_show/$1';
$route['admin/complaints/(:num)/status']  = 'Complaints/admin_update_status/$1';
$route['complaints/edit/(:num)']     = 'Complaints/edit/$1';
$route['complaints/update/(:num)']   = 'Complaints/update/$1';
$route['complaints/delete/(:num)']   = 'Complaints/delete/$1';
$route['worker/feed']                 = 'WorkerFeed/index';
$route['worker/feed/post']            = 'WorkerFeed/post';
$route['worker/feed/delete/(:num)']   = 'WorkerFeed/delete/$1';
$route['worker/feed/apply']           = 'WorkerFeed/apply';
$route['worker/feed/withdraw/(:num)'] = 'WorkerFeed/withdraw/$1';
$route['client/feed']      = 'ClientFeed/index';
$route['client/feed/post'] = 'ClientFeed/post';
$route['worker/feed/api_new']  = 'WorkerFeed/api_new';
$route['client/feed/api_new']  = 'ClientFeed/api_new';
$route['client/feed/delete/(:num)'] = 'ClientFeed/delete/$1';
$route['profile/experience_json']  = 'profile/experience_json';
$route['profile/save_experience']  = 'profile/save_experience';
$route['profile/delete_experience/(:num)'] = 'profile/delete_experience/$1';
$route['users/create_admin']['post'] = 'users/create_admin';
$route['admin/workers/store'] = 'AdminWorkers/store';
$route['admin/workers/address/provinces'] = 'AdminWorkers/address_provinces';
$route['admin/workers/address/cities'] = 'AdminWorkers/address_cities';
$route['admin/workers/address/brgys'] = 'AdminWorkers/address_brgys';
// TESDA (new aliases to reuse AdminWorkers)
$route['tesda/workers/upload']   = 'AdminWorkers/index';
$route['tesda/workers/template'] = 'AdminWorkers/template';
$route['tesda/workers/preview']  = 'AdminWorkers/preview';
$route['tesda/workers/import']   = 'AdminWorkers/import';
$route['tesda/workers/store']    = 'AdminWorkers/store';
$route['tesda/trainings']               = 'TesdaTrainings/index';
$route['tesda/trainings/edit/(:num)']   = 'TesdaTrainings/edit/$1';
$route['tesda/trainings/store']         = 'TesdaTrainings/store';
$route['tesda/trainings/update/(:num)'] = 'TesdaTrainings/update/$1';
$route['tesda/trainings/toggle/(:num)'] = 'TesdaTrainings/toggle/$1';
$route['tesda/trainings/delete/(:num)'] = 'TesdaTrainings/delete/$1';
$route['tesda/reports']                 = 'TesdaReports/index';
$route['dashboard/tesda'] = 'TesdaDashboard/index';
$route['dashboard/tesda'] = 'dashboard/tesda';
$route['admin/reports'] = 'AdminReports/index';
$route['admin/reports/export_csv'] = 'AdminReports/export_csv';
$route['admin/reports/client/(:num)'] = 'AdminReports/client/$1';
// Admin CRUD
$route['admin/hotlines']                 = 'AdminHotlines/index';
$route['admin/hotlines/create']          = 'AdminHotlines/create';
$route['admin/hotlines/edit/(:num)']     = 'AdminHotlines/edit/$1';
$route['admin/hotlines/delete/(:num)']   = 'AdminHotlines/delete/$1';
$route['admin/hotlines/toggle/(:num)']   = 'AdminHotlines/toggle/$1';

// Public (view-only)
$route['hotlines'] = 'Hotlines/index';
$route['users/delete'] = 'users/delete';

$route['dashboard/peso']        = 'PesoDashboard/index';
$route['peso']                  = 'PesoDashboard/index';
$route['peso/feed']             = 'PesoDashboard/feed';
$route['peso/store']            = 'PesoDashboard/store';
$route['peso/edit/(:num)']      = 'PesoDashboard/edit/$1';
$route['peso/update/(:num)']    = 'PesoDashboard/update/$1';
$route['peso/toggle/(:num)']    = 'PesoDashboard/toggle/$1';
$route['peso/delete/(:num)']    = 'PesoDashboard/delete/$1';
$route['peso/reports/hired-workers'] = 'PesoDashboard/hired_workers_report';

$route['school-admin']                 = 'SchoolAdmin/index';
$route['school-admin/workers']         = 'SchoolAdmin/workers';
$route['school-admin/create']          = 'SchoolAdmin/create';
$route['school-admin/store']           = 'SchoolAdmin/store';

$route['school-admin/edit/(:num)']     = 'SchoolAdmin/edit/$1';
$route['school-admin/update/(:num)']   = 'SchoolAdmin/update/$1';

$route['school-admin/delete/(:num)']   = 'SchoolAdmin/delete/$1';
$route['school-admin/resend/(:num)']   = 'SchoolAdmin/resend_email/$1';

$route['school-admin/bulk']            = 'SchoolAdmin/bulk';
$route['school-admin/bulk_preview']    = 'SchoolAdmin/bulk_preview';
$route['school-admin/bulk_commit']     = 'SchoolAdmin/bulk_commit';
