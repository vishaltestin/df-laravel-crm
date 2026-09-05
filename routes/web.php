<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontEnd\TeamController;
use App\Http\Controllers\Api\MasterDataController;
/*
|--------------------------------------------------------------------------
| Default Welcome Route (Commented)
|--------------------------------------------------------------------------
| Ye Laravel ka default welcome page route tha, abhi disabled hai
|--------------------------------------------------------------------------
*/

/*
Route::get('/', function () {
    return view('welcome');
});
*/
 	
Route::middleware('admin')->group(function () {

    Route::get('/api/institutes', [MasterDataController::class, 'institutes']);
    Route::get('/api/programs',   [MasterDataController::class, 'programs']);
    Route::get('/api/courses',    [MasterDataController::class, 'courses']);

    Route::get('/api/countries',  [MasterDataController::class, 'countries']);
    Route::get('/api/states',     [MasterDataController::class, 'states']);
    Route::get('/api/cities',     [MasterDataController::class, 'cities']);
	
	Route::get('/api/email-templates', [MasterDataController::class, 'emailTemplates']);
    Route::get('/api/sms-templates', [MasterDataController::class, 'smsTemplates']);
    Route::get('/api/whatsapp-templates', [MasterDataController::class, 'whatsappTemplates']);
	
	Route::get('/api/template-by-id', [MasterDataController::class,'templateById']);
	
	Route::get('/api/leadStatusesapi', [MasterDataController::class, 'leadStatusesapi']);	
	Route::get('/api/lead-status-substatus', [MasterDataController::class, 'leadStatusSubStatusAjax']);
	
	Route::post('/api/lead-status-substatus', [MasterDataController::class, 'leadStatusSubStatusAjax']);

	
	
});






/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Public Access)
|--------------------------------------------------------------------------
| Ye routes bina login ke access ho sakte hain
| Sirf login page dikhane aur login submit karne ke liye
|--------------------------------------------------------------------------
*/
Route::group([
    'namespace' => 'App\Http\Controllers\FrontEnd',
    'prefix'    => ''
], function () {

    // âœ… Login page view route
    Route::get('/',        ['uses' => 'LoginController@index']);
    Route::get('/login',   ['uses' => 'LoginController@index']);

    // âœ… Login form submit handler
    Route::post('/',       ['uses' => 'LoginController@postLogin']);
    Route::post('/login',  ['uses' => 'LoginController@postLogin']);
});
/*
|--------------------------------------------------------------------------
| ADMIN PANEL ROUTES (Login Required)
|--------------------------------------------------------------------------
| Ye routes sirf Admin login ke baad access honge
| 'admin' middleware se protected hain
|--------------------------------------------------------------------------
*/
Route::group([
    'namespace'  => 'App\Http\Controllers\FrontEnd',
    'prefix'     => '',
    'middleware' => 'admin'
], function () {
// SHOW Reset Request Form
	Route::get('password/forgot', ['uses' => 'LoginController@forgotPasswordForm','as'   => 'password.request']);
	// HANDLE Email Submission
	Route::post('password/forgot', [  'uses' => 'LoginController@sendResetLink',    'as'   => 'password.email']);
	// SHOW Reset Form
	Route::get('password/reset/{code}', [    'uses' => 'LoginController@resetPasswordForm',    'as'   => 'password.reset']);
	// HANDLE Password Reset
	Route::post('password/reset/{code}', [    'uses' => 'LoginController@resetPassword',    'as'   => 'password.update']);
	Route::get('switch-institute/{id}',  ['uses' => 'LoginController@switchInstitute','as'   => 'switch.institute']);
Route::get('clear-institute',  ['uses' => 'LoginController@clearInstitute','as'   => 'clear.institute']);
    /*
    |------------------------------------------------------
    | DASHBOARD
    |------------------------------------------------------
    | Admin dashboard dikhata aur dashboard form submit handle karta hai
    */
	Route::get('/dashboard',  ['uses' => 'DashboardController@index',     'as' => 'User.Dashboard']);
	Route::get('/api/dashboard/graphs', [
		'uses' => 'DashboardController@graphs',
		'as'   => 'dashboard.graphs'
	]);

	    /*
    |------------------------------------------------------
    | LOGOUT
    |------------------------------------------------------
    | Admin user logout karta hai
    */
	Route::post('/logout', ['uses' => 'LoginController@logout']);
	Route::post('/logout', ['uses' => 'LoginController@logout','as'   => 'logout']);

    /*
    |------------------------------------------------------
    | USERS AJAX
    |------------------------------------------------------
    | Frontend me ajax request se user list return karta hai
    */
	Route::post('/userAjax', ['uses' => 'UserController@listUserAjax']);

    /*
    |------------------------------------------------------
    | USER CRUD
    |------------------------------------------------------
    | User add, edit, view, activate, deactivate, delete operations
    */
	Route::get('/user',            ['uses' => 'UserController@listUser',       'as' => 'User.ListView']);
	Route::get('/user/add/new',    ['uses' => 'UserController@Add']);
	Route::post('/user/add/new',   ['uses' => 'UserController@AddUser',        'as' => 'User.AddUser']);
	Route::get('/user/get/{id}',   ['uses' => 'UserController@getUserData',    'as' => 'User.Get']);
	Route::get('/user/edit/{id}',  ['uses' => 'UserController@viewEditUser']);
	Route::post('/user/edit/{id}', ['uses' => 'UserController@editUser',       'as' => 'User.EditUser']);
	Route::get('/user/{id}', [   'uses' => 'UserController@show',   'as'   => 'User.Show' ]);
  
/*
    |------------------------------------------------------
    | USER STATUS CONTROLS
    |------------------------------------------------------
    | User ko activate, deactivate, delete, permissions manage karta hai
    */
    Route::get('user/{user}/activate',    ['uses' => 'UserController@activate',       'as' => 'User.Activate']);
    Route::get('user/{user}/deactivate',  ['uses' => 'UserController@deactivate',     'as' => 'User.Deactivate']);
    Route::get('user/{user}/destroy',     ['uses' => 'UserController@destroy',        'as' => 'User.Destroy']);
    Route::get('user/{user}/permissions', ['uses' => 'UserController@permissions',    'as' => 'User.Permissions']);
    Route::post('user/{user}/save',       ['uses' => 'UserController@save']);

    /*
    |------------------------------------------------------
    | ROLES & ROLE PERMISSION
    |------------------------------------------------------
    | System me roles add/edit aur permissions manage karta hai
    */
    Route::get('/role',                ['uses' => 'RoleController@listRole',      'as' => 'Role.ListView']);
    Route::get('/role/add/new',        ['uses' => 'RoleController@role']);
    Route::post('/role/add/new',       ['uses' => 'RoleController@postRole',      'as' => 'Role.AddRole']);
    Route::get('/role/edit/{id}',      ['uses' => 'RoleController@viewRole',      'as' => 'Role.EditRole']);
    Route::post('/role/edit/{id}',     ['uses' => 'RoleController@editRole',      'as' => 'Role.EditRole']);
    Route::get('role/{user}/rolepermission', ['uses' => 'RoleController@rolepermission', 'as' => 'Role.Permission']);
    Route::post('role/{user}/save',          ['uses' => 'RoleController@save']);
	// ðŸ‘¤ Update User Details
	Route::post('/update-profile/{id}',     ['uses' => 'HomeController@updateProfile', 'as' => 'updateProfile']);
	Route::post('/update-password/{id}',     ['uses' => 'HomeController@updatePassword', 'as' => 'updatePassword']);
 
	Route::get('/calendar/events',['uses' => 'LeadController@getEvents', 'as' => 'calendar.events']);

Route::post('/leads/{lead}/payment/apply-coupon',['uses' => 'LeadController@applyCoupon', 'as' => 'leads.payment.apply_coupon']);

Route::post('/leads/{lead}/payment/save',['uses' => 'LeadController@savePayment', 'as' => 'leads.payment.save']);
Route::post('/leads/{lead}/documents', ['uses' => 'LeadController@uploadDocument', 'as' => 'leads.documents.upload']);
Route::get('/leads/documents/{doc}/view', ['uses' => 'LeadController@viewDocument', 'as' => 'leads.documents.view']);
Route::delete('/leads/documents/{doc}', ['uses' => 'LeadController@deleteDocument', 'as' => 'leads.documents.delete']);


   /*
    |------------------------------------------------------
    | LEADS
    |------------------------------------------------------
    | Lead list, create, store, view operations
    */
  Route::get('leads/upload', [
    'uses' => 'LeadController@uploadForm',
    'as'   => 'leads.upload'
]);

Route::post('leads/upload', [
    'uses' => 'LeadController@uploadStore',
    'as'   => 'leads.upload.store'
]);
Route::post('/leads/upload/store-file', [
    'uses' => 'LeadController@storeFile',
    'as'   => 'leads.upload.storeFile']);

Route::post('/leads/upload/process/{id}', [
    'uses' => 'LeadController@processUpload',
    'as'   => 'leads.upload.process']);
Route::get('/leads/upload/sample', [
    'uses' => 'LeadController@downloadSample',
    'as'   => 'leads.upload.sample']);
Route::get('leads/upload/logs', [
    'uses' => 'LeadController@uploadLogs',
    'as'   => 'leads.upload.logs'
]);

Route::get('/test-lead-api',        ['uses' => 'LeadController@testLeadApi',  'as' => 'TestLeadApi.View']);
 
  Route::get('leads',        ['uses' => 'LeadController@index',  'as' => 'Leads.ListView']);
    Route::get('leads/create', ['uses' => 'LeadController@create', 'as' => 'Leads.AddLead']);
    Route::post('leads',       ['uses' => 'LeadController@store',  'as' => 'Leads.StoreLead']);
    Route::get('leads/{id}',   ['uses' => 'LeadController@show',   'as' => 'Leads.LeadShow']);
	// LEAD SHOW
	 
	// Modals / Actions (POST)
	Route::post('leads/{id}/notes',   ['uses' => 'LeadController@addNote',  'as' => 'leads.addNote']);
	Route::post('leads/{id}/change-stage',   ['uses' => 'LeadController@changeStage',  'as' => 'leads.changeStage']);
	Route::post('leads/{id}/events',   ['uses' => 'LeadController@addEvent',  'as' => 'leads.addEvent']);

	Route::post('leads/{id}/communicate/email',   ['uses' => 'LeadController@communicateEmail',  'as' => 'leads.communicate.email']);
	Route::post('leads/{id}/communicate/sms',   ['uses' => 'LeadController@communicateSms',  'as' => 'leads.communicate.sms']);
	Route::post('leads/{id}/communicate/whatsapp',   ['uses' => 'LeadController@communicateWhatsapp',  'as' => 'leads.communicate.whatsapp']);

	Route::post('leads/{id}/reassign',   ['uses' => 'LeadController@reassign',  'as' => 'leads.reassign']);
	Route::post('leads/{id}/whatsapp/send',   ['uses' => 'LeadController@whatsappReply',  'as' => 'leads.whatsapp.reply']);
	Route::post('leads/{id}/update-pipeline', ['uses' => 'LeadController@updatePipeline']);
	Route::post('leads/{id}/update-source', ['uses' => 'LeadController@updateSource']);
	Route::get('leads/{id}/json', ['uses' => 'LeadController@json']);

	Route::get('leads/{lead}/tickets', ['uses' => 'LeadController@tickets']);
	Route::post('leads/{lead}/tickets/create', ['uses' => 'LeadController@createTicket']);
	Route::get('tickets/{id}', ['uses' => 'LeadController@showTicket']);
	Route::post('tickets/reply/{id}', ['uses' => 'LeadController@replyTicket']);
	Route::get('/leads/{id}/json', ['uses' => 'LeadController@getJson']);
	Route::get('/api/users-for-reassign', ['uses' => 'LeadController@getUsersForReassign']);		
	Route::delete('/leads/{id}', ['uses' => 'LeadController@destroy',  'as' => 'leads.destroy']);
 

//Route::post('leads/bulk-action', [    'uses' => 'FrontEnd\LeadController@bulkAction',  'as'   => 'leads.bulk.action']);

Route::post('/leads/bulk/google', [
    'uses' => 'LeadController@bulkGoogle',
    'as'   => 'leads.bulk.google'
]);
Route::post('/leads/bulk/facebook', [
    'uses' => 'LeadController@bulkFacebook',
    'as'   => 'leads.bulk.facebook'
]);
Route::post('/leads/bulk/download', [
    'uses' => 'LeadController@bulkDownload',
    'as'   => 'leads.bulk.download'
]);
Route::post('/leads/bulk/delete', [
    'uses' => 'LeadController@bulkDelete',
    'as'   => 'leads.bulk.delete'
]);
Route::post('/leads/bulk/pipeline', [
    'uses' => 'LeadController@bulkPipelineUpdate',
    'as'   => 'leads.bulk.pipeline'
]);
Route::post('/leads/bulk/data-update', [
    'uses' => 'LeadController@bulkDataUpdate',
    'as'   => 'leads.bulk.data.update'
]);
Route::post('/leads/bulk/reassign', [
    'uses' => 'LeadController@bulkReassign',
    'as'   => 'leads.bulk.reassign'
]);
Route::post('/leads/bulk/stage', [
    'uses' => 'LeadController@bulkStageUpdate',
    'as'   => 'leads.bulk.stage'
]);
Route::post('/leads/bulk/email', [
    'uses' => 'LeadController@bulkCommunicateEmail',
    'as'   => 'leads.bulk.email'
]);

Route::post('/leads/bulk/sms', [
    'uses' => 'LeadController@bulkCommunicateSms',
    'as'   => 'leads.bulk.sms'
]);

Route::post('/leads/bulk/whatsapp', [
    'uses' => 'LeadController@bulkCommunicateWhatsapp',
    'as'   => 'leads.bulk.whatsapp'
]);


    /*
    |------------------------------------------------------
    | ORGANIZATIONS
    |------------------------------------------------------
    | Silver page ke organizations/add/edit/update ke liye bhi use ho sakta hai
    */
    Route::get('organizations',           ['uses' => 'OrganizationController@index', 'as' => 'Organization.ListView']);
    Route::post('organizations/store',    ['uses' => 'OrganizationController@store', 'as' => 'Organization.Store']);
    Route::get('organizations/{id}',      ['uses' => 'OrganizationController@show',  'as' => 'Organization.Show']);
    Route::get('organizations/{id}/edit', ['uses' => 'OrganizationController@edit',  'as' => 'Organization.Edit']);
    Route::put('organizations/{id}',      ['uses' => 'OrganizationController@update','as' => 'Organization.Update']);
	Route::get('switch-organization/{id}', [
		'uses' => 'OrganizationController@switchOrganization',
		'as'   => 'organization.switch'
	]);
    /*
    |------------------------------------------------------
    | DESIGNATIONS
    |------------------------------------------------------
    | Organization designation manage karta hai
    */
    Route::get('designations',           ['uses' => 'DesignationController@index', 'as' => 'Designation.ListView']);
    Route::post('designations/store',    ['uses' => 'DesignationController@store', 'as' => 'Designation.Store']);
    Route::get('designations/{id}',      ['uses' => 'DesignationController@show',  'as' => 'Designation.Show']);
    Route::get('designations/{id}/edit', ['uses' => 'DesignationController@edit',  'as' => 'Designation.Edit']);
    Route::put('designations/{id}',      ['uses' => 'DesignationController@update','as' => 'Designation.Update']);

    /*
    |------------------------------------------------------
    | TEAMS (Manual + Resource Combined)
    |------------------------------------------------------
    | TeamController ke through teams manage karta hai
    */
    Route::get('teams',                  ['uses' => 'TeamController@index', 'as' => 'Team.ListView']);
    Route::post('teams/store',           ['uses' => 'TeamController@store', 'as' => 'Team.Store']);
    Route::get('teams/{id}',             ['uses' => 'TeamController@show',  'as' => 'Team.Show']);
    Route::get('teams/{id}/edit',        ['uses' => 'TeamController@edit',  'as' => 'Team.Edit']);
    Route::put('teams/{id}',             ['uses' => 'TeamController@update','as' => 'Team.Update']);
    Route::resource('teams', TeamController::class);

    // âœ… Team me organization ke users ajax se fetch karta hai
    Route::get('/get-users/{orgId}', [TeamController::class, 'getUsers'])->name('get.users');

    /*
    |------------------------------------------------------
    | GENERAL SETTINGS
    |------------------------------------------------------
    | System global configurations/settings pages
    */
    Route::get('general-settings',  ['uses' => 'GeneralSettingController@index',  'as' => 'GeneralSetting.ListView']);
    Route::get('graphics-settings', ['uses' => 'GraphicsSettingController@index', 'as' => 'GraphicsSetting.ListView']);
    Route::get('email-settings',    ['uses' => 'EmailSettingController@index',    'as' => 'EmailSetting.ListView']);
    Route::get('whatsapp-settings', ['uses' => 'WhatsappSettingController@index', 'as' => 'WhatsappSetting.ListView']);
    Route::get('sms-settings',      ['uses' => 'SmsSettingController@index',      'as' => 'SmsSetting.ListView']);
	Route::post('sms/test',         ['uses' => 'SmsSettingController@testSms',       'as' => 'sms.test']);
    // âœ… General settings page update handlers
    Route::post('general/update',   ['uses' => 'GeneralSettingController@update',   'as' => 'GeneralSetting.Update']);
    Route::post('graphics/update',  ['uses' => 'GraphicsSettingController@update',  'as' => 'GraphicsSetting.Update']);
    Route::post('email/update',     ['uses' => 'EmailSettingController@update',     'as' => 'EmailSetting.Update']);
    Route::post('whatsapp/update',  ['uses' => 'WhatsappSettingController@update',  'as' => 'WhatsappSetting.Update']);
    Route::post('sms/update',       ['uses' => 'SmsSettingController@update',       'as' => 'SmsSetting.Update']);

    Route::get('ivr-settings',      ['uses' => 'IvrSettingController@index',      'as' => 'IvrSetting.ListView']);
    Route::post('ivr/update',       ['uses' => 'IvrSettingController@update',       'as' => 'IvrSetting.Update']);
	Route::post('ivr/test', ['uses' => 'IvrSettingController@testivr', 'as' => 'ivr.test']);
	Route::post('leads/{lead_id}/call', [
    'uses' => 'LeadController@callNow',
    'as' => 'leads.callNow'
]);

  /*
    |------------------------------------------------------
    | PIPELINES
    |------------------------------------------------------
    | Lead pipeline and pipeline stages management
    */
	// MAIN PIPELINE ROUTES
	Route::get('pipelines', ['uses' => 'LeadPipelineController@index', 'as' => 'pipelines.index']);
	Route::post('pipelines/store', ['uses' => 'LeadPipelineController@store', 'as' => 'pipelines.store']);
	Route::delete('pipelines/{id}', ['uses' => 'LeadPipelineController@destroy', 'as' => 'pipelines.delete']);

	// AJAX route for popup edit builder JSON
	Route::get('pipelines/{id}/edit-data', ['uses' => 'LeadPipelineController@editData', 'as' => 'pipelines.editData']);

	// Save pipeline builder result (stages + final stage)
	Route::post('pipelines/{id}/update', ['uses' => 'LeadPipelineController@update', 'as' => 'pipelines.update']);


	// STAGE ROUTES
	Route::post('pipeline-stages/store', ['uses' => 'LeadPipelineStageController@store', 'as' => 'pipeline-stages.store']);
	Route::post('pipeline-stages/{id}/update', ['uses' => 'LeadPipelineStageController@update', 'as' => 'pipeline-stages.update']);
	Route::delete('pipeline-stages/{id}', ['uses' => 'LeadPipelineStageController@destroy', 'as' => 'pipeline-stages.delete']);

    /*
    |------------------------------------------------------
    | SOURCES
    |------------------------------------------------------
    | Lead source management + billing settings
    */
    Route::get('sources',       ['uses' => 'LeadSourceController@index',  'as' => 'sources.index']);
    Route::post('sources/store',['uses' => 'LeadSourceController@store',  'as' => 'sources.store']);
    Route::get('sources/{id}/edit', ['uses' => 'LeadSourceController@edit',   'as' => 'sources.edit']);
    Route::post('sources/{id}/update', ['uses' => 'LeadSourceController@update','as' => 'sources.update']);
    Route::delete('sources/{id}', ['uses' => 'LeadSourceController@destroy','as' => 'sources.delete']);

    // âœ… Lead source me billing update save karta hai
    Route::post('sources/{id}/billing-update', ['uses' => 'LeadSourceController@billingUpdate','as' => 'sources.billing.update']);

    /*
    |------------------------------------------------------
    | TYPES
    |------------------------------------------------------
    | Lead types/stages management
    */
    Route::get('types',          ['uses' => 'LeadTypeController@index', 'as' => 'types.index']);
    Route::post('types/store',   ['uses' => 'LeadTypeController@store', 'as' => 'types.store']);
    Route::get('types/{id}/edit',['uses' => 'LeadTypeController@edit',  'as' => 'types.edit']);
    Route::post('types/{id}/update', ['uses' => 'LeadTypeController@update','as' => 'types.update']);
    Route::delete('types/{id}',  ['uses' => 'LeadTypeController@destroy','as' => 'types.delete']);

    /*
    |------------------------------------------------------
    | ATTRIBUTES
    |------------------------------------------------------
    | Custom lead attributes fields management
    */
    Route::get('attributes',  ['uses' => 'AttributeController@index', 'as' => 'attributes.index']);
    Route::post('attributes/store', ['uses' => 'AttributeController@store','as' => 'attributes.store']);
    Route::get('attributes/{id}/edit', ['uses' => 'AttributeController@edit','as' => 'attributes.edit']);
    Route::post('attributes/{id}/update',['uses' => 'AttributeController@update','as' => 'attributes.update']);
    Route::delete('attributes/{id}', ['uses' => 'AttributeController@destroy','as' => 'attributes.delete']);

    /*
    |------------------------------------------------------
    | EMAIL & MESSAGE TEMPLATES
    |------------------------------------------------------
    | Marketing/communication templates management
    */
    Route::get('email-templates', ['uses' => 'EmailTemplateController@index','as' => 'email.templates.index']);
    Route::post('email-templates/store', ['uses' => 'EmailTemplateController@store','as' => 'email.templates.store']);
    Route::get('email-templates/{id}/edit', ['uses' => 'EmailTemplateController@edit','as' => 'email.templates.edit']);
    Route::post('email-templates/{id}/update',['uses' => 'EmailTemplateController@update','as' => 'email.templates.update']);
    Route::delete('email-templates/{id}', ['uses' => 'EmailTemplateController@destroy','as' => 'email.templates.delete']);
Route::post('email-templates/bulk-delete', [
    'uses' => 'EmailTemplateController@bulkDelete',
    'as'   => 'email.templates.bulk-delete'
]);

    // âœ… Message templates (SMS/Other communications)
    Route::get('message-templates', ['uses' => 'MessageTemplateController@index', 'as' => 'message.templates.index']);
    Route::post('message-templates/store', ['uses' => 'MessageTemplateController@store', 'as' => 'message.templates.store']);
    Route::get('message-templates/{id}/edit', ['uses' => 'MessageTemplateController@edit']);
    Route::post('message-templates/{id}/update', ['uses' => 'MessageTemplateController@update']);
    Route::delete('message-templates/{id}', ['uses' => 'MessageTemplateController@destroy']);
	Route::post('message-templates/bulk-delete', ['uses' => 'MessageTemplateController@bulkDelete', 'as' => 'message.templates.bulk-delete']);
    /*
    |------------------------------------------------------
    | WHATSAPP TEMPLATES
    |------------------------------------------------------
    | WhatsApp message automation templates manage à¤•à¤°à¤¤à¤¾ à¤¹à¥ˆ
    */
    Route::get('whatsapp-templates', ['uses' => 'WhatsappTemplateController@index','as' => 'whatsapp.templates.index']);
    Route::get('whatsapp-templates/create',['uses' => 'WhatsappTemplateController@create','as' => 'whatsapp.templates.create']);
    Route::post('whatsapp-templates/store', ['uses' => 'WhatsappTemplateController@store','as' => 'whatsapp.templates.store']);
    Route::get('whatsapp-templates/{id}/edit',['uses' => 'WhatsappTemplateController@edit','as' => 'whatsapp.templates.edit']);
    Route::post('whatsapp-templates/{id}/update',['uses' => 'WhatsappTemplateController@update','as' => 'whatsapp.templates.update']);
    Route::delete('whatsapp-templates/{id}', ['uses' => 'WhatsappTemplateController@destroy','as' => 'whatsapp.templates.delete']);
Route::post('bulk-delete', [
        'uses' => 'WhatsappTemplateController@bulkDelete',
        'as'   => 'whatsapp.templates.bulk-delete'
    ]);
    /*
    |------------------------------------------------------
    | MARKETING EVENTS
    |------------------------------------------------------
    | Marketing events add/edit/update/delete karta hai
    */
    Route::get('marketing-events',        ['uses' => 'MarketingEventController@index',  'as' => 'marketing.events.index']);
    Route::post('marketing-events/store', ['uses' => 'MarketingEventController@store',  'as' => 'marketing.events.store']);
    Route::get('marketing-events/{id}/edit',['uses' => 'MarketingEventController@edit','as' => 'marketing.events.edit']);
    Route::post('marketing-events/{id}/update',['uses' => 'MarketingEventController@update','as' => 'marketing.events.update']);
    Route::delete('marketing-events/{id}', ['uses' => 'MarketingEventController@destroy','as' => 'marketing.events.delete']);

    /*
    |------------------------------------------------------
    | CAMPAIGNS
    |------------------------------------------------------
    | Lead campaigns manage karta hai
    */
    Route::get('campaigns',        ['uses' => 'CampaignController@index', 'as' => 'campaigns.index']);
    Route::post('campaigns/store', ['uses' => 'CampaignController@store', 'as' => 'campaigns.store']);
    Route::get('campaigns/{id}/edit',['uses' => 'CampaignController@edit','as' => 'campaigns.edit']);
    Route::post('campaigns/{id}/update',['uses' => 'CampaignController@update','as' => 'campaigns.update']);
    Route::delete('campaigns/{id}', ['uses' => 'CampaignController@destroy','as' => 'campaigns.delete']);

    /*
    |------------------------------------------------------
    | WEBHOOKS
    |------------------------------------------------------
    | External API callbacks/automation triggers ke liye
    */
    Route::get('webhooks',         ['uses' => 'WebhookController@index','as' => 'webhooks.index']);
    Route::get('webhooks/create',  ['uses' => 'WebhookController@create','as' => 'webhooks.create']);
    Route::post('webhooks/store',  ['uses' => 'WebhookController@store','as' => 'webhooks.store']);
    Route::get('webhooks/{id}/edit',['uses' => 'WebhookController@edit','as' => 'webhooks.edit']);
    Route::post('webhooks/{id}/update',['uses' => 'WebhookController@update','as' => 'webhooks.update']);
    Route::get('webhooks/{id}/delete',['uses' => 'WebhookController@destroy','as' => 'webhooks.delete']);

    /*
    |------------------------------------------------------
    | WEBFORMS
    |------------------------------------------------------
    | Dynamic form builder + public submit system
    */
    Route::get('webforms',             ['uses' => 'WebFormController@index', 'as' => 'webforms.index']);
    Route::get('webforms/create',      ['uses' => 'WebFormController@create','as' => 'webforms.create']);
    Route::post('webforms/store',      ['uses' => 'WebFormController@store','as' => 'webforms.store']);
    Route::get('webforms/{id}/edit',   ['uses' => 'WebFormController@edit','as' => 'webforms.edit']);
    Route::post('webforms/{id}/update',['uses' => 'WebFormController@update','as' => 'webforms.update']);
    Route::delete('webforms/{id}',     ['uses' => 'WebFormController@destroy','as' => 'webforms.delete']);

    /*
    |------------------------------------------------------
    | PRODUCTS
    |------------------------------------------------------
    | Silver items ya educational courses bhi ho sakte hain
    */
    Route::get('products',           ['uses' => 'ProductController@index','as' => 'products.index']);
    Route::get('products/create',    ['uses' => 'ProductController@create','as' => 'products.create']);
    Route::post('products/store',    ['uses' => 'ProductController@store','as' => 'products.store']);
    Route::get('products/{id}',      ['uses' => 'ProductController@show','as' => 'products.show']);
    Route::get('products/{id}/edit', ['uses' => 'ProductController@edit','as' => 'products.edit']);
    Route::post('products/{id}/update',['uses' => 'ProductController@update','as' => 'products.update']);
    Route::delete('products/{id}',   ['uses' => 'ProductController@destroy','as' => 'products.delete']);

    /*
    |------------------------------------------------------
    | LEAD DISTRIBUTION SYSTEM
    |------------------------------------------------------
    | Lead auto/manual assignment rules manage à¤•à¤°à¤¤à¤¾ à¤¹à¥ˆ
    */
    Route::get('/lead-distribution', ['uses' => 'LeadDistributionController@index','as' => 'lead.distribution.index']);
    Route::post('/lead-distribution/store',['uses' => 'LeadDistributionController@store','as' => 'lead.distribution.store']);
    Route::get('/lead-distribution/{id}/edit',['uses' => 'LeadDistributionController@edit','as' => 'lead.distribution.edit']);
    Route::post('/lead-distribution/{id}/update',['uses' => 'LeadDistributionController@update','as' => 'lead.distribution.update']);
    Route::delete('/lead-distribution/{id}',['uses' => 'LeadDistributionController@destroy','as' => 'lead.distribution.delete']);
    Route::post('/lead-distribution/simulate',['uses'=>'LeadDistributionController@simulate','as'=>'lead.distribution.simulate']);
    Route::get('reports/lead-distribution',['uses'=>'LeadAssignmentReportController@index','as'=>'reports.lead.distribution']);
Route::get('/reports/communications', [\App\Http\Controllers\FrontEnd\CommunicationReportController::class, 'index'])
    ->name('reports.communications')
    ->middleware(['web','admin']);
	  /*
	|------------------------------------------------------
	| LEAD STATUS SETTINGS
	|------------------------------------------------------
	*/
 
	Route::get('lead-settings/statuses', ['uses' => 'LeadStatusSettingController@index','as'   => 'lead.status.settings']);
	Route::get('lead-settings/sub-statuses', ['uses' => 'LeadStatusSettingController@subStatusIndex','as'   => 'lead.substatus.settings']);
	Route::post('lead-settings/status/store', ['uses' => 'LeadStatusSettingController@storeStatus','as'   => 'lead.status.store']);
	Route::post('lead-settings/status/{id}/update', ['uses' => 'LeadStatusSettingController@updateStatus','as'   => 'lead.status.update']);
	Route::delete('lead-settings/status/{id}', ['uses' => 'LeadStatusSettingController@destroy','as'   => 'lead.status.delete']);
	Route::post('lead-settings/status/{statusId}/sub/store', ['uses' => 'LeadStatusSettingController@storeSubStatus','as'   => 'lead.substatus.store']);
	Route::post('lead-settings/sub-status/{id}/update', ['uses' => 'LeadStatusSettingController@updateSubStatus','as'   => 'lead.substatus.update']);
	Route::delete('lead-settings/sub-status/{id}', ['uses' => 'LeadStatusSettingController@deleteSubStatus','as'   => 'lead.substatus.delete'	]);
	Route::post('lead-settings/ajax/sub-statuses', ['uses' => 'LeadStatusSettingController@getSubStatusesAjax','as'   => 'ajax.lead.substatuses']);
	Route::get('webforms/{id}/embed-code', [ 'uses' => 'WebFormController@embedCode', 	'as'   => 'webforms.embed' ]);
	Route::get('public/webform/{form_id}', [ 'uses' => 'WebFormController@publicView', 'as'   => 'webform.public' ]);


	Route::get('public/webform/{form_id}', [ 'uses' => 'WebFormController@publicView', 'as'   => 'webform.public' ]);


	// FORMS LIST
	Route::get('application_forms', ['uses' => 'ApplicationFormController@index',		'as'   => 'application.forms'	]);
	Route::get('application_forms/create', ['uses' => 'ApplicationFormController@create',		'as'   => 'application.forms.create'	]);
	Route::post('application_forms/store', ['uses' => 'ApplicationFormController@store','as'   => 'application.forms.store']);
	Route::get('application_form_show/{code}/{lead_id}', ['uses' => 'ApplicationFormViewController@showForm',		'as'   => 'application.form.show'	]);
	Route::post('application_form_submit', ['uses' => 'ApplicationFormViewController@submitForm',		'as'   => 'application.form.submit'	]);
	Route::get('application_form_submissions/{id}', ['uses' => 'ApplicationFormController@submissions',		'as'   => 'application.form.submissions'	]);
	Route::get('application_form_submission/{id}', ['uses' => 'ApplicationFormController@viewSubmission',		'as'   => 'application.form.submission.view'	]);
	// EDIT FORM
	Route::get('application_forms/{id}/edit', ['uses' => 'ApplicationFormController@edit','as'   => 'application.forms.edit']);
	// UPDATE FORM
	Route::post('application_forms/{id}/update', ['uses' => 'ApplicationFormController@update','as'   => 'application.forms.update']);

Route::post('application-form/partial-save',  ['uses' => 'ApplicationFormViewController@partialSave','as'   => 'application.form.partial.save']);

 Route::post('application-form/submit', [
  'uses' => 'ApplicationFormViewController@finalSubmit',
  'as'   => 'application.form.final.submit'
]);


	/*|------------------------------------------------------
    | CRON JOB TRIGGERS (For Testing Only)
    |------------------------------------------------------
    | Ye manually browser me hit karke cron run test karne ke liye hain
    */
 Route::get('/cron/run-auto-assign', function () {
    \Artisan::call('leads:auto-assign');
    return "Lead assignment cron executed.";
});

Route::get('/cron/run-auto-reassign', function () {
    \Artisan::call('leads:auto-reassign-inactive');
    return "Auto reassign cron executed.";
});

Route::get('/cron/run-raw-leads-process', function () {
    \Artisan::call('raw-leads:process --limit=1');
    return nl2br(\Artisan::output());
});

Route::get('/cron/run-send-sms', function () {
    \Artisan::call('crm:send-pending-sms --limit=25');
    return nl2br(\Artisan::output());
});

Route::get('/cron/run-send-email', function () {
    \Artisan::call('crm:send-pending-email --limit=10');
    return nl2br(\Artisan::output());
});

Route::get('/cron/run-send-whatsapp', function () {
    \Artisan::call('crm:send-pending-whatsapp --limit=20');
    return nl2br(\Artisan::output());
});
Route::get('cron/run-lead-uploads', function () {

    $uploads = \DB::table('lead_uploads')
        ->where('status', 'uploaded')
        ->limit(5)
        ->get();

    foreach ($uploads as $upload) {
        dispatch(new \App\Jobs\ProcessLeadUploadJob($upload->id));
    }

    return "Lead upload processing dispatched successfully.";

});

    /*
    |------------------------------------------------------
    | RAW LEAD AUTOMATION (Bulk Import + IVR)
    |------------------------------------------------------
    | Raw lead table ka data list, IVR send, IVR callback, manual convert etc.
    */
	// 1) Upload Logs List (NEW)
	Route::get('automation/raw-leads/uploads', [
		'uses' => 'AutomationRawLeadController@uploadsList',
		'as'   => 'automation.raw-leads.uploads'
	]);

	// 2) Upload File (POST)
	Route::post('automation/raw-leads/upload', [
		'uses' => 'AutomationRawLeadController@upload',
		'as'   => 'automation.raw-leads.upload'
	]);

	// 3) View one upload rows (already)
	Route::get('automation/raw-leads/upload/{uploadId}', [
		'uses' => 'AutomationRawLeadController@viewUpload',
		'as'   => 'automation.raw-leads.upload.view'
	]);
 

  Route::get('automation/raw-leads',['uses'=>'AutomationRawLeadController@index','as'=>'automation.raw-leads.index']);
    Route::post('automation/raw-leads/send-ivr',['uses'=>'App\Http\Controllers\FrontEnd\AutomationRawLeadController@sendIvr','as'=>'automation.raw-leads.send-ivr']);
	Route::match(['GET', 'POST'], 'automation/ivr-callback',['uses'=>'AutomationRawLeadController@ivrCallback','as'=>'automation.ivr-callback']);
    Route::post('automation/raw-leads/{id}/convert',['uses'=>'AutomationRawLeadController@manualConvert','as'=>'automation.raw-leads.convert']);
    Route::post('automation/raw-leads/import',['uses'=>'AutomationRawLeadImportController@import','as'=>'automation.raw-leads.import']);
    Route::get('automation/ivr-welcome', function(){
        return response('<?xml version="1.0" encoding="UTF-8"?>
        <Response>
            <Gather numDigits="1" action="'.route('automation.ivr-callback').'" method="POST">
                <Say voice="alice">Thank you for confirming. Press 1 if interested, 2 if not interested.</Say>
            </Gather>
            <Say>No input received.</Say>
        </Response>',200)->header('Content-Type','text/xml');
    })->name('automation.ivr-welcome');

	// LOG LIST PAGES
	Route::get('automation/ivr',['uses'=>'IvrLogController@index','as'=>'automation.ivr']);
	Route::get('automation/whatsapp',['uses'=>'WhatsAppLogController@index','as'=>'automation.whatsapp']);
	Route::get('automation/sms',['uses'=>'SmsLogController@index','as'=>'automation.sms']);
	Route::get('automation/verified-leads',['uses'=>'VerifiedLeadController@index','as'=>'automation.verified-leads']);
	Route::get('automation/rejected-leads',['uses'=>'RejectedLeadController@index','as'=>'automation.rejected-leads']);
	Route::get('/smtp-test', 'EmailSettingController@testMail');


	// -----------------------------------------------
	// INSTITUTE MANAGER (CANVAS + AJAX)
	// -----------------------------------------------
	Route::get('institute-manager', [    'uses' => 'InstituteManagerController@index',    'as'   => 'institute.manager']);
	Route::post('institute/store', [    'uses' => 'InstituteManagerController@storeInstitute',    'as'   => 'institute.store']);
	Route::post('institute/update/{id}', [    'uses' => 'InstituteManagerController@updateInstitute',    'as'   => 'institute.update']);
	Route::post('institute/delete/{id}', [    'uses' => 'InstituteManagerController@deleteInstitute',    'as'   => 'institute.delete']);
	 	Route::get('institute/{institute_id}/programs', [    'uses' => 'InstituteManagerController@programsByInstitute',    'as'   => 'institute.programs']);


	// ------------------------------------------------
	// PROGRAM ROUTES
	// ------------------------------------------------

	// Create Program
	Route::post('program/store', [    'uses' => 'InstituteManagerController@storeProgram',    'as'   => 'program.store']);

	Route::post('program/update/{id}', [    'uses' => 'InstituteManagerController@updateProgram',    'as'   => 'program.update']);
	Route::post('program/delete/{id}', [    'uses' => 'InstituteManagerController@deleteProgram',    'as'   => 'program.delete']);
	Route::post('course/assign-program', [    'uses' => 'InstituteManagerController@assignCourseToProgram',    'as'   => 'course.assign-program']);
	Route::get('program/{id}/courses', [    'uses' => 'InstituteManagerController@coursesByProgram',    'as'   => 'program.courses']);

	// ------------------------------------------------
	// CLEAN SHOW ROUTES (renamed to avoid conflict)
	// ------------------------------------------------

	// Show one institute details
	Route::get('view-institute/{id}', [    'uses' => 'InstituteManagerController@showInstitute',    'as'   => 'institute.show']);
	Route::get('view-program/{id}', [    'uses' => 'InstituteManagerController@showProgram',    'as'   => 'program.show']);

	Route::get('programs/all', [    'uses' => 'InstituteManagerController@allPrograms',    'as'   => 'programs.all']);
	Route::get('institute/{id}', [    'uses' => 'InstituteManagerController@showInstitute',    'as'   => 'institute.show']);

	Route::get('program/{id}', [    'uses' => 'InstituteManagerController@showProgram',    'as'   => 'program.show']);
	Route::post('course/store', [    'uses' => 'ProductController@storeCourse',    'as'   => 'course.store']);
	Route::post('course/update/{id}', [    'uses' => 'ProductController@updateCourse',    'as'   => 'course.update']);
	Route::get('course/{id}/json', [    'uses' => 'InstituteManagerController@courseJson','as'   => 'course.json']);
	Route::post('course/delete/{id}', ['uses' => 'ProductController@deleteCourse','as'   => 'course.delete']);

	// PUBLIC LANDING PAGE
	Route::get('/lp/{slug}', ['uses' => 'LandingPageController@render','as'   => 'landing.render']);
	// ADMIN LANDING PAGE CRUD
	Route::resource('landing-pages', 'LandingPageController');

	// SECTION BUILDER
	Route::get('landing-pages/{id}/sections', ['uses' => 'LandingPageController@sectionList','as'   => 'landing-pages.sections']);
	Route::post('landing-pages/{id}/sections', ['uses' => 'LandingPageController@sectionStore','as'   => 'landing-pages.sections.store']);
	Route::delete('landing-pages/section/{sid}', ['uses' => 'LandingPageController@sectionDelete','as'   => 'landing-pages.sections.delete']);
	Route::post('landing-pages/{id}/sections/sort', ['uses' => 'LandingPageController@updateSort','as'   => 'landing-pages.sort.update']);
	Route::post('landing-pages/sections/{id}/update', ['uses' => 'LandingPageController@sectionUpdate','as'   => 'landing-pages.sections.update']);



	Route::get('programs/all', function() {return \App\Program::select('id','name')->get();
	});

	Route::get('products/{id}/json', function($id){return App\Product::findOrFail($id);
	});



	Route::post('/leads/quick-store', ['uses' => 'LeadController@quickStore','as'   => 'Leads.QuickStore']);


 
    Route::get('facebook', [
        'uses' => 'LeadIntegrationController@facebook',
        'as'   => 'lead.integrations.facebook'
    ]);

 

    Route::get('instagram', [
        'uses' => 'LeadIntegrationController@instagram',
        'as'   => 'lead.integrations.instagram'
    ]);
 

    Route::get('google', [
        'uses' => 'LeadIntegrationController@google',
        'as'   => 'lead.integrations.google'
    ]);

    Route::post('save', [
        'uses' => 'LeadIntegrationController@save',
        'as'   => 'lead.integrations.save' 
	  ]);

    Route::get('logs', [
        'uses' => 'LeadIntegrationController@logs',
        'as'   => 'lead.integrations.logs'
    ]);

 Route::post('webhook/{provider}', [
    'uses' => 'LeadWebhookController@handle',
    'as'   => 'integration.webhook'
]);

 
Route::post('tinymce/upload-image', [
    'uses' => 'EditorUploadController@uploadImage']);
Route::post('tinymce/upload-file', [
    'uses' => 'EditorUploadController@uploadFile']);

  

Route::get('lead-settings/settings', [
    'uses' => 'LeadSettingsController@settings',
    'as'   => 'lead-settings/settings'
]);

Route::post('lead-settings/settings', [
    'uses' => 'LeadSettingsController@saveSettings',
    'as'   => 'lead-settings/save'
]);




Route::post('leads/saved-filters', [
    'uses' => 'LeadSavedFilterController@store',
    'as'   => 'leads.saved_filters.store'
]);

Route::get('leads/saved-filters', [
    'uses' => 'LeadSavedFilterController@list',
    'as'   => 'leads.saved_filters.list'
]);

Route::delete('leads/saved-filters/{id}', [
    'uses' => 'LeadSavedFilterController@delete',
    'as'   => 'leads.saved_filters.delete'
]);

Route::post('leads/saved-filters/{id}/default', [
    'uses' => 'LeadSavedFilterController@makeDefault',
    'as'   => 'leads.saved_filters.default'
]);


Route::post('leads/columns/save', [
    'uses' => 'LeadColumnSettingController@save',
    'as'   => 'leads.columns.save'
]);

Route::get('leads/columns/get', [
    'uses' => 'LeadColumnSettingController@get',
    'as'   => 'leads.columns.get'
]);

Route::get('notifications/list', [
    'uses' => 'NotificationController@list',
    'as'   => 'notifications.list'
]);

Route::post('notifications/read/{id}', [
    'uses' => 'NotificationController@markRead',
    'as'   => 'notifications.read'
]);

Route::post('notifications/read-bulk', [
    'uses' => 'NotificationController@markBulkRead',
    'as'   => 'notifications.read_bulk'
]);

Route::post('notifications/read-all', [
    'uses' => 'NotificationController@markAllRead',
    'as'   => 'notifications.read_all'
]);


Route::get('coupons', ['uses'=>'CouponController@index', 'as'=>'coupons.index']);
Route::get('coupons/create', ['uses'=>'CouponController@create', 'as'=>'coupons.create']);
Route::post('coupons', ['uses'=>'CouponController@store', 'as'=>'coupons.store']);
Route::get('coupons/{id}/edit', ['uses'=>'CouponController@edit', 'as'=>'coupons.edit']);
Route::post('coupons/{id}', ['uses'=>'CouponController@update', 'as'=>'coupons.update']);
Route::delete('coupons/{id}', ['uses'=>'CouponController@destroy', 'as'=>'coupons.destroy']);

Route::post('coupons/apply', ['uses'=>'CouponController@applyCoupon', 'as'=>'coupons.apply']);
Route::get('coupons/{id}/usage', ['uses'=>'CouponController@usageLogs', 'as'=>'coupons.usage']);


});

 /*
|--------------------------------------------------------------------------
| RAW LEAD SAMPLE CSV DOWNLOAD
|--------------------------------------------------------------------------
| System me raw-leads import format samjhane ke liye sample download à¤¦à¥‡à¤¤à¤¾ à¤¹à¥ˆ
|--------------------------------------------------------------------------
*/
Route::get('automation/raw-leads/download-sample', function(){return response()->download(resource_path('views/FrontEnd/automation/raw_leads/sample.csv'));
})->name('automation.raw-leads.download-sample');

 /*
|--------------------------------------------------------------------------
| LARAVEL FILE MANAGER
|--------------------------------------------------------------------------
| Media uploads, images, files manage karne à¤µà¤¾à¤²à¤¾ package routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix'=>'laravel-filemanager'], function(){\UniSharp\LaravelFilemanager\Lfm::routes();
});
use App\Http\Controllers\FrontEnd\WebFormController;

//Route::get('api/programmes', [WebFormController::class, 'programmes']);
//Route::get('api/courses', [WebFormController::class, 'courses']);

use App\Http\Controllers\Api\LeadApiController;

Route::post('/leads/add', [LeadApiController::class, 'store']);
Route::get('/leads/add',  [LeadApiController::class, 'store']); // optional

use App\Http\Controllers\FrontEnd\AutomationRuleController;

Route::prefix('settings/automation')->group(function () {

    Route::get('/',        [AutomationRuleController::class, 'index'])->name('automation.index');
    Route::get('/create',  [AutomationRuleController::class, 'create'])->name('automation.create');
    Route::post('/store',  [AutomationRuleController::class, 'store'])->name('automation.store');
    Route::get('/edit/{id}', [AutomationRuleController::class, 'edit'])->name('automation.edit');
    Route::post('/update/{id}', [AutomationRuleController::class, 'update'])->name('automation.update');
    Route::post('/toggle/{id}', [AutomationRuleController::class, 'toggle'])->name('automation.toggle');

});

Route::get('/captcha-image', function () {

    $code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 6);
    session(['captcha_code' => $code]);

    // 👇 Image size bada
    $image = imagecreatetruecolor(220, 80);

    $bg = imagecolorallocate($image, 240, 240, 240);
    $textcolor = imagecolorallocate($image, 0, 0, 0);

    imagefilledrectangle($image, 0, 0, 220, 80, $bg);

    // noise lines
    for ($i = 0; $i < 7; $i++) {
        $lineColor = imagecolorallocate($image, rand(100,255), rand(100,255), rand(100,255));
        imageline($image, rand(0,220), rand(0,80), rand(0,220), rand(0,80), $lineColor);
    }

    // 👇 Font 5 max hota hai built-in me
    // Isliye position adjust kar rahe hain
    $x = 60;
    $y = 30;

    imagestring($image, 5, $x, $y, $code, $textcolor);

    header("Content-type: image/png");
    imagepng($image);
    imagedestroy($image);
    exit;
});
 // PUBLIC WEBFORM ROUTES
Route::get('webform/{form_id}', 
    [\App\Http\Controllers\FrontEnd\WebFormController::class, 'publicView']
)->name('webform.public');

Route::post('webform/{form_id}/submit', 
    [\App\Http\Controllers\FrontEnd\WebFormController::class, 'publicSubmit']
)->name('webform.public.submit');


// PUBLIC API ROUTES
Route::get('/api/institutes', [MasterDataController::class, 'institutes']);
Route::get('/api/programs',   [MasterDataController::class, 'programs']);
Route::get('/api/courses',    [MasterDataController::class, 'courses']);
Route::get('/api/states',     [MasterDataController::class, 'states']);
Route::get('/api/cities',     [MasterDataController::class, 'cities']);
Route::get('/api/countries',  [MasterDataController::class, 'countries']);
Route::post('/send-otp', [OtpController::class, 'send']);
Route::post('/verify-otp', [OtpController::class, 'verify']);