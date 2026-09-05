<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\LeadApiController;

/*
|--------------------------------------------------------------------------
| DEFAULT / PUBLIC APIs (UNCHANGED)
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/institutes', [MasterDataController::class, 'institutes']);
Route::get('/institutes/{id}/programs', [MasterDataController::class, 'programs']);
Route::get('/programs/{id}/courses', [MasterDataController::class, 'courses']);

Route::get('/countries', [MasterDataController::class, 'countries']);
Route::get('/states', [MasterDataController::class, 'states']);
Route::get('/cities', [MasterDataController::class, 'cities']);

Route::post('/leads/add', [LeadApiController::class, 'store']);
Route::get('/leads/add',  [LeadApiController::class, 'store']); // optional

Route::get('/lead-statuses', [MasterDataController::class, 'leadStatuses']);

/*
|--------------------------------------------------------------------------
| ORGANIZATION RELATED APIs (WEB + ADMIN SESSION ENABLED)
|--------------------------------------------------------------------------
| ⚠️ YAHI MAIN FIX HAI
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'admin'])->group(function () {

    Route::get('/org/email-templates',    [MasterDataController::class, 'emailTemplates']);
    Route::get('/org/whatsapp-templates', [MasterDataController::class, 'whatsappTemplates']);
    Route::get('/org/sms-templates',      [MasterDataController::class, 'smsTemplates']);

    Route::get('/org/teams', [MasterDataController::class, 'teams']);
    Route::get('/org/users', [MasterDataController::class, 'users']);
});

// ✅ ADD THIS
use App\Http\Controllers\Api\OtpController;

Route::post('/send-otp', [OtpController::class, 'send']);
Route::post('/verify-otp', [OtpController::class, 'verify']);