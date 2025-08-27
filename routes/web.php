<?php

use App\Http\Controllers\CustomToolsController;
use App\Http\Controllers\DeviceMeasurementsController;
use App\Http\Controllers\InvoiceCloudController;
use App\Http\Controllers\NeptuneController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExternalApiController;
use App\Http\Controllers\FieldActivityReportsController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');
//Route::get('/external-data', [ExternalApiController::class, 'fetchData']);
/*Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
*/
Route::get('/dashboard', [ExternalApiController::class, 'fetchData'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard'); 


Route::get('/toolsDashboard', [CustomToolsController::class, 'toolsDashboard'])
    ->middleware(['auth', 'verified'])
    ->name('tools.dashboard'); 
    Route::get('/tools/jsonFileReader', [CustomToolsController::class, 'jsonFileReader'])
    ->middleware(['auth', 'verified'])
    ->name('tools.jsonFileReader'); 

    
    Route::get('/tools/json-comparator', [CustomToolsController::class, 'jsonComparator'])
    ->middleware(['auth', 'verified'])
    ->name('tools.json-comparator'); 
        Route::get('/tools/open-ai-analyzer', [CustomToolsController::class, 'openAiAnalyzer'])
    ->middleware(['auth', 'verified'])
    ->name('tools.open-ai-analyzer'); 
    
    Route::get('/tools/intervalGenerator', [CustomToolsController::class, 'intervalGenerator'])
    ->middleware(['auth', 'verified'])
    ->name('tools.interval-generator');  
     Route::get('/tools/neptuneFileAnalyzer', [CustomToolsController::class, 'neptuneFileAnalyzer'])
    ->middleware(['auth', 'verified'])
    ->name('tools.neptuneFileAnalyzer'); 


Route::get('/field-activity', [FieldActivityReportsController::class, 'fetchData'])
    ->middleware(['auth', 'verified'])
    ->name('field-activity');
Route::get('/field-activity/getActivities', [FieldActivityReportsController::class, 'getActivities'])
    ->middleware(['auth', 'verified'])
    ->name('field-activities');
Route::get('/field-activity/getActivity/{activityId}', [FieldActivityReportsController::class, 'getActivity'])
    ->middleware(['auth', 'verified'])
    ->name('field-activity');

Route::get('/neptune/get-scalar-imd/{external_id}', [NeptuneController::class, 'getNeptuneScalarImd'])
    ->middleware(['auth', 'verified'])
    ->name('neptune.get-scalar-imd');

Route::get('/device-measurements/{id}', [DeviceMeasurementsController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('device-measurements.show');


Route::get('/invoice-cloud/customer-info', [InvoiceCloudController::class, 'getCustomerInfo'])
    ->middleware(['auth', 'verified'])
    ->name('invoice-cloud.customer-info');

Route::get('/invoice-cloud/retrieve-customer', [InvoiceCloudController::class, 'retrieveCustomerGuid'])
    ->middleware(['auth', 'verified'])
    ->name('invoice-cloud.retrieve-customer');


Route::get('/invoice-cloud/update-customer', [InvoiceCloudController::class, 'updateCustomerRecord'])
    ->middleware(['auth', 'verified'])
    ->name('invoice-cloud.update-customer');
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
