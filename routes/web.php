<?php

use App\Http\Controllers\Animal\ListAnimalController;
use App\Http\Controllers\Appointment\CreateAppointmentController;
use App\Http\Controllers\Appointment\DeleteAppointmentController;
use App\Http\Controllers\Appointment\ListAppointmentsController;
use App\Http\Controllers\Appointment\ShowAppointmentController;
use App\Http\Controllers\Customer\ListCustomersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');


Route::prefix('appointments')->middleware(['auth', 'verified'])->name('appointments')->group(function () {
    Route::get('/', ListAppointmentsController::class)->name('.list')->middleware('can:admin,receptionist,customer,doctor');
    Route::get('/new', [CreateAppointmentController::class, 'create'])->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{id}', ShowAppointmentController::class)->name('.index')->middleware('can:admin,receptionist,doctor,customer');
    Route::get('/{id}/edit', ListAppointmentsController::class)->name('.edit')->middleware('can:admin,receptionist,doctor');
});

Route::prefix('customers')->middleware(['auth', 'verified'])->name('customers')->group(function () {
    Route::get('/', ListCustomersController::class)->name('.list')->middleware('can:admin,receptionist,customer,doctor');
    Route::get('/new', [CreateAppointmentController::class, 'create'])->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{id}', ShowAppointmentController::class)->name('.index')->middleware('can:admin,receptionist,doctor,customer');
    Route::get('/{id}/edit', ListAppointmentsController::class)->name('.edit')->middleware('can:admin,receptionist,doctor');
});

Route::prefix('animals')->middleware(['auth', 'verified'])->name('animals')->group(function () {
    Route::get('/', ListAnimalController::class)->name('.list')->middleware('can:admin,receptionist,customer,doctor');
    Route::get('/new', [CreateAppointmentController::class, 'create'])->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{id}', ShowAppointmentController::class)->name('.index')->middleware('can:admin,receptionist,doctor,customer');
    Route::get('/{id}/edit', ListAppointmentsController::class)->name('.edit')->middleware('can:admin,receptionist,doctor');
});

Route::prefix('doctors')->middleware(['auth', 'verified'])->name('doctors')->group(function () {
    Route::get('/', ListAppointmentsController::class)->name('.list')->middleware('can:admin,receptionist,customer,doctor');
    Route::get('/new', [CreateAppointmentController::class, 'create'])->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{id}', ShowAppointmentController::class)->name('.index')->middleware('can:admin,receptionist,doctor,customer');
    Route::get('/{id}/edit', ListAppointmentsController::class)->name('.edit')->middleware('can:admin,receptionist,doctor');
});

Route::get('/debug-auth', function () {
    return response()->json([
        'auth-check' => auth()->check(),
        'user' => auth()->user(),
        'session' => session()->all(),
    ]);
});

require __DIR__.'/settings.php';
