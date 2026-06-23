<?php

use App\Http\Controllers\PlatformDashboardController;
use App\Http\Controllers\TenantDashboardController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TenantNotificationSettingsController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ElectionExportController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

//Landing Page
Route::get('/', function () 
{
    // return view('welcome');
    return redirect('/login');
});

// Platform Owner Routes
// Route::prefix('admin')->middleware(['auth'])->domain('elections.test')->group(function ()
Route::middleware(['auth', 'enforce.tenant'])->domain(parse_url(config('app.url'), PHP_URL_HOST))->group(function ()
{
    Route::get('/dashboard', [PlatformDashboardController::class, 'index'])->name('platform.dashboard');

    //Platform Admin - Tenant Routes
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::get('/tenants/edit/{id}', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::get('/tenants/view/{id}', [TenantController::class, 'view'])->name('tenants.view');
    Route::post('/tenants/store', [TenantController::class, 'store'])->name('tenants.store');
    Route::post('/tenants/update/{id}', [TenantController::class, 'update'])->name('tenants.update');
    Route::post('/tenants/toggle/{id}', [TenantController::class, 'toggleStatus'])->name('tenants.toggle');

    //Platform Admin - User Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/view/{id}', [UserController::class, 'view'])->name('users.view');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');

    // Platform Owner - Notifications Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('/notifications', [NotificationController::class, 'update'])->name('notifications.update');
    Route::post('/notifications/test-email', [NotificationController::class, 'testEmail'])->name('notifications.test-email');
    Route::post('/notifications/test-sms', [NotificationController::class, 'testSms'])->name('notifications.test-sms');

    // Platform Owner - Setups Routes
    Route::get('/setups', [SetupController::class, 'index'])->name('setups');
    Route::put('/setups', [SetupController::class, 'update'])->name('setups.update');
    Route::post('/setups/test-email', [SetupController::class, 'testEmail'])->name('setups.test-email');
    Route::post('/setups/test-sms', [SetupController::class, 'testSms'])->name('setups.test-sms');

    // Platform Owner - Billing Routes
    Route::get('/billing', [BillingController::class, 'index'])->name('platform.billing.index');
    Route::get('/billing/tenant/{tenant}', [BillingController::class, 'tenantBillings'])->name('platform.billing.tenant');
    Route::get('/billing/{billing}', [BillingController::class, 'show'])->name('platform.billing.show');
    Route::get('/billing/{billing}/edit', [BillingController::class, 'edit'])->name('platform.billing.edit');
    Route::put('/billing/{billing}', [BillingController::class, 'update'])->name('platform.billing.update');
    Route::delete('/billing/{billing}', [BillingController::class, 'destroy'])->name('platform.billing.destroy');
    Route::post('/billing/generate', [BillingController::class, 'generate'])->name('platform.billing.generate');
    Route::post('/billing/generate/{tenant}', [BillingController::class, 'generate'])->name('platform.billing.generate.tenant');
    Route::patch('/billing/{billing}/mark-paid', [BillingController::class, 'markAsPaid'])->name('platform.billing.mark-paid');
});

// Tenant Routes
Route::middleware(['tenant','auth','enforce.tenant'])->group(function ()
{
    Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');

    //Tenant Admin - Elections Routes
    Route::get('/elections', [ElectionController::class, 'index'])->name('elections.index');
    Route::get('/elections/create', [ElectionController::class, 'create'])->name('elections.create');
    Route::get('/elections/edit/{id}', [ElectionController::class, 'edit'])->name('elections.edit');
    Route::get('/elections/view/{id}', [ElectionController::class, 'view'])->name('elections.view');
    Route::post('/elections/store', [ElectionController::class, 'store'])->name('elections.store');
    Route::post('/elections/update/{id}', [ElectionController::class, 'update'])->name('elections.update');
    
    // Election Status Management Routes
    Route::post('/elections/start/{id}', [ElectionController::class, 'start'])->name('elections.start');
    Route::post('/elections/pause/{id}', [ElectionController::class, 'pause'])->name('elections.pause');
    Route::post('/elections/resume/{id}', [ElectionController::class, 'resume'])->name('elections.resume');
    Route::delete('/elections/{id}', [ElectionController::class, 'destroy'])->name('elections.destroy');

    //Tenant Admin - Portfolios Routes
    Route::get('/portfolios', [PortfolioController::class, 'index'])->name('portfolios.index');
    Route::get('/portfolios/create', [PortfolioController::class, 'create'])->name('portfolios.create');
    Route::get('/portfolios/edit/{id}', [PortfolioController::class, 'edit'])->name('portfolios.edit');
    Route::get('/portfolios/view/{id}', [PortfolioController::class, 'view'])->name('portfolios.view');
    Route::post('/portfolios/store', [PortfolioController::class, 'store'])->name('portfolios.store');
    Route::post('/portfolios/update/{id}', [PortfolioController::class, 'update'])->name('portfolios.update');

    //Tenant Admin - Candidates Routes
    Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');
    Route::get('/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');
    Route::get('/candidates/edit/{id}', [CandidateController::class, 'edit'])->name('candidates.edit');
    Route::get('/candidates/view/{id}', [CandidateController::class, 'view'])->name('candidates.view');
    Route::get('/candidates/details/{id}', [CandidateController::class, 'details'])->name('candidates.details');
    Route::get('/candidates/upload/{id}', [CandidateController::class, 'upload'])->name('candidates.upload');
    Route::get('/candidates/assign-portfolios/{id}', [CandidateController::class, 'assignPortfolios'])->name('candidates.assign.portfolios');
    Route::post('/candidates/store', [CandidateController::class, 'store'])->name('candidates.store');
    Route::post('/candidates/update/{id}', [CandidateController::class, 'update'])->name('candidates.update');
    Route::post('/candidates/upload-file/{id}', [CandidateController::class, 'uploadFile'])->name('candidates.upload.file');
    Route::post('/candidates/save-with-portfolios/{id}', [CandidateController::class, 'saveCandidatesWithPortfolios'])->name('candidates.save.portfolios');
    Route::delete('/candidates/{id}', [CandidateController::class, 'destroy'])->name('candidates.delete');
    Route::get('/candidates/notify/{id}', [CandidateController::class, 'notify'])->name('candidates.notify');

    //Tenant Admin - Voters Routes
    Route::get('/voters', [VoterController::class, 'index'])->name('voters.index');
    Route::get('/voters/create', [VoterController::class, 'create'])->name('voters.create');
    Route::get('/voters/edit/{id}', [VoterController::class, 'edit'])->name('voters.edit');
    Route::get('/voters/view/{id}', [VoterController::class, 'view'])->name('voters.view');
    Route::get('/voters/details/{id}', [VoterController::class, 'details'])->name('voters.details');
    Route::get('/voters/upload/{id}', [VoterController::class, 'upload'])->name('voters.upload');
    Route::get('/voters/review/{id}', [VoterController::class, 'review'])->name('voters.review');
    Route::post('/voters/store', [VoterController::class, 'store'])->name('voters.store');
    Route::post('/voters/update/{id}', [VoterController::class, 'update'])->name('voters.update');
    Route::post('/voters/upload-file/{id}', [VoterController::class, 'uploadFile'])->name('voters.upload.file');
    Route::post('/voters/save/{id}', [VoterController::class, 'saveVoters'])->name('voters.save');
    Route::delete('/voters/{id}', [VoterController::class, 'destroy'])->name('voters.delete');
    Route::get('/voters/notify/{id}', [VoterController::class, 'notify'])->name('voters.notify');

    //Tenant Admin - Reports Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/view/{id}', [ReportController::class, 'view'])->name('reports.view');
    Route::post('/reports/release/{id}', [ReportController::class, 'releaseResults'])->name('reports.release');
    Route::get('/reports/approve/{id}', [ReportController::class, 'approveView'])->name('reports.approve.view');
    Route::post('/reports/approve/{id}', [ReportController::class, 'approve'])->name('reports.approve');

    // Election Results Export Routes
    Route::get('/reports/export/{id}/pdf', [ElectionExportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/{id}/csv', [ElectionExportController::class, 'exportCsv'])->name('reports.export.csv');

    // Tenant Admin - Notification Settings Routes
    Route::get('/notifications', [TenantNotificationSettingsController::class, 'index'])->name('tenant.notifications.index');
    Route::put('/notifications', [TenantNotificationSettingsController::class, 'update'])->name('tenant.notifications.update');
    Route::post('/notifications/test-email', [TenantNotificationSettingsController::class, 'testEmail'])->name('tenant.notifications.test-email');
    Route::post('/notifications/test-sms', [TenantNotificationSettingsController::class, 'testSms'])->name('tenant.notifications.test-sms');

    // Individual Notify Routes
    Route::get('/candidates/notify-single/{id}', [CandidateController::class, 'notifySingle'])->name('candidates.notify.single');
    Route::get('/voters/notify-single/{id}', [VoterController::class, 'notifySingle'])->name('voters.notify.single');

});

// Public Routes - Candidate Profile Edit (via secure token)
Route::get('/candidate/edit/{token}', [CandidateController::class, 'editProfile'])->name('candidate.edit.profile');
Route::post('/candidate/edit/{token}', [CandidateController::class, 'updateProfile'])->name('candidate.update.profile');

// Public Routes - Voting (via secure token)
Route::get('/vote/{token}', [VoteController::class, 'show'])->name('vote.cast');
Route::post('/vote/{token}', [VoteController::class, 'submit'])->name('vote.submit');




