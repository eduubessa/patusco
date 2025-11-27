<?php

use App\Http\Controllers\Animal\ListAnimalController;
use App\Http\Controllers\Animal\ShowAnimalController;
use App\Http\Controllers\Appointment\CreateAppointmentController;
use App\Http\Controllers\Appointment\DeleteAppointmentController;
use App\Http\Controllers\Appointment\EditAppointmentController;
use App\Http\Controllers\Appointment\ListAppointmentsController;
use App\Http\Controllers\Appointment\ShowAppointmentController;
use App\Http\Controllers\Appointment\StoreAppointmentController;
use App\Http\Controllers\Appointment\UpdateAppointmentController;
use App\Http\Controllers\Customer\ListCustomersController;
use App\Http\Controllers\Doctors\ListDoctorController;
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
    Route::get('/', ListAppointmentsController::class)->name('.list');
    Route::get('/new', CreateAppointmentController::class)->name('.create');
    Route::post('/', StoreAppointmentController::class)->name('.store');
    Route::get('/{appointment}', ShowAppointmentController::class)->name('.show');
    Route::get('/{appointment}/edit', EditAppointmentController::class)->name('.edit');
    Route::put('/{appointment}', UpdateAppointmentController::class)->name('.update');
    Route::delete('/{appointment}', DeleteAppointmentController::class)->name('.destroy');
});

Route::prefix('customers')->middleware(['auth', 'verified'])->name('customers')->group(function () {
    Route::get('/', ListCustomersController::class)->name('.list');
    Route::get('/new', [CreateAppointmentController::class, 'create'])->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{id}', ShowAppointmentController::class)->name('.index');
    Route::get('/{id}/edit', ListAppointmentsController::class)->name('.edit');
});

Route::prefix('animals')->middleware(['auth', 'verified'])->name('animals')->group(function () {
    Route::get('/', ListAnimalController::class)->name('.list');
    Route::get('/new', [CreateAppointmentController::class, 'create'])->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{id}', ShowAnimalController::class)->name('.index');
    Route::get('/{id}/edit', ListAppointmentsController::class)->name('.edit');
});

Route::prefix('doctors')->middleware(['auth', 'verified'])->name('doctors')->group(function () {
    Route::get('/', ListDoctorController::class)->name('.list');
    Route::get('/new', [CreateAppointmentController::class, 'create'])->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{id}', ShowAppointmentController::class)->name('.index');
    Route::get('/{id}/edit', ListAppointmentsController::class)->name('.edit');
});

Route::get('/debug-auth', function () {
    return response()->json([
        'auth-check' => auth()->check(),
        'user' => auth()->user(),
        'session' => session()->all(),
    ]);
});

require __DIR__.'/settings.php';
