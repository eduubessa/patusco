<?php

use App\Http\Controllers\Animal\ListAnimalController;
use App\Http\Controllers\Animal\ShowAnimalController;
use App\Http\Controllers\Appointment\CreateAppointmentController;
use App\Http\Controllers\Appointment\DeleteAppointmentController;
use App\Http\Controllers\Appointment\EditAppointmentController;
use App\Http\Controllers\Appointment\ListAppointmentController;
use App\Http\Controllers\Appointment\ShowAppointmentController;
use App\Http\Controllers\Appointment\StoreAppointmentController;
use App\Http\Controllers\Appointment\UpdateAppointmentController;
use App\Http\Controllers\Customer\CreateCustomerController;
use App\Http\Controllers\Customer\DeleteCustomerController;
use App\Http\Controllers\Customer\EditCustomerController;
use App\Http\Controllers\Customer\ListCustomerController;
use App\Http\Controllers\Customer\ShowCustomerController;
use App\Http\Controllers\Customer\StoreCustomerController;
use App\Http\Controllers\Customer\UpdateCustomerController;
use App\Http\Controllers\Doctor\ListDoctorController;
use App\Http\Controllers\Doctor\ShowDoctorController;
use App\Http\Controllers\User\DeleteUserController;
use App\Http\Controllers\User\EditUserController;
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
    Route::get('/', ListAppointmentController::class)->name('.list');
    Route::get('/new', CreateAppointmentController::class)->name('.create');
    Route::post('/', StoreAppointmentController::class)->name('.store');
    Route::get('/{appointment:slug}/edit', EditAppointmentController::class)->name('.edit');
    Route::get('/{appointment:slug}', ShowAppointmentController::class)->name('.show');
    Route::put('/{appointment:slug}', UpdateAppointmentController::class)->name('.update');
    Route::delete('/{appointment:slug}', DeleteAppointmentController::class)->name('.destroy');
});

Route::prefix('animals')->middleware(['auth', 'verified'])->name('animals')->group(function () {
    Route::get('/', ListAnimalController::class)->name('.list');
    Route::get('/new', CreateAppointmentController::class)->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{animal}', ShowAnimalController::class)->name('.show');
    Route::get('/{animal}/edit', ListAppointmentController::class)->name('.edit');
});


Route::prefix('customers')->middleware(['auth', 'verified'])->name('customers')->group(function () {
    Route::get('/', ListCustomerController::class)->name('.list');
    Route::get('/{customer}', ShowCustomerController::class)->name('.show');
});

Route::prefix('doctors')->middleware(['auth', 'verified'])->name('doctors')->group(function () {
    Route::get('/', ListDoctorController::class)->name('.list');
    Route::get('/{doctor}', ShowDoctorController::class)->name('.show');
});

Route::prefix('users')->middleware(['auth', 'verified'])->name('users')->group(function () {
    Route::get('/', ListAnimalController::class)->name('.list');
    Route::get('/new', CreateAppointmentController::class)->name('.create');
    Route::post('/', CreateAppointmentController::class)->name('.store');
    Route::get('/{user}', ShowAnimalController::class)->name('.show');
    Route::get('/{user}/edit', EditUserController::class)->name('.edit');
    Route::put('/{user}', EditUserController::class)->name('.edit');
    Route::delete('/{user}', DeleteUserController::class)->name('.edit');
});

Route::get('/debug-auth', function () {
    return response()->json([
        'auth-check' => auth()->check(),
        'user' => auth()->user(),
        'session' => session()->all(),
    ]);
});

require __DIR__.'/settings.php';
