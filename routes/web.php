<?php

use App\Http\Controllers\Animal\CreateAnimalController;
use App\Http\Controllers\Animal\DeleteAnimalController;
use App\Http\Controllers\Animal\EditAnimalController;
use App\Http\Controllers\Animal\ListAnimalController;
use App\Http\Controllers\Animal\ShowAnimalController;
use App\Http\Controllers\Animal\StoreAnimalController;
use App\Http\Controllers\Animal\UpdateAnimalController;
use App\Http\Controllers\Appointment\CreateAppointmentController;
use App\Http\Controllers\Appointment\DeleteAppointmentController;
use App\Http\Controllers\Appointment\EditAppointmentController;
use App\Http\Controllers\Appointment\ListAppointmentController;
use App\Http\Controllers\Appointment\ShowAppointmentController;
use App\Http\Controllers\Appointment\StoreAppointmentController;
use App\Http\Controllers\Appointment\UpdateAppointmentController;
use App\Http\Controllers\Customer\ListCustomerController;
use App\Http\Controllers\Customer\ShowCustomerController;
use App\Http\Controllers\Doctor\ListDoctorController;
use App\Http\Controllers\Doctor\ShowDoctorController;
use App\Http\Controllers\User\CreateUserController;
use App\Http\Controllers\User\DeleteUserController;
use App\Http\Controllers\User\EditUserController;
use App\Http\Controllers\User\ListUserController;
use App\Http\Controllers\User\ShowUserController;
use App\Http\Controllers\User\StoreUserController;
use App\Http\Controllers\User\UpdateUserController;
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
    Route::get('/new', CreateAnimalController::class)->name('.create');
    Route::post('/', StoreAnimalController::class)->name('.store');
    Route::get('/{animal}', ShowAnimalController::class)->name('.show');
    Route::get('/{animal}/edit', EditAnimalController::class)->name('.edit');
    Route::put('/{animal}', UpdateAnimalController::class)->name('.update');
    Route::delete('/{animal}', DeleteAnimalController::class)->name('.delete');
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
    Route::get('/', ListUserController::class)->name('.list');
    Route::get('/new', CreateUserController::class)->name('.create');
    Route::post('/', StoreUserController::class)->name('.store');
    Route::get('/{user}', ShowUserController::class)->name('.show');
    Route::get('/{user}/edit', EditUserController::class)->name('.edit');
    Route::put('/{user}', UpdateUserController::class)->name('.update');
    Route::delete('/{user}', DeleteUserController::class)->name('.delete');
});

Route::get('/debug-auth', function () {
    return response()->json([
        'auth-check' => auth()->check(),
        'user' => auth()->user(),
        'session' => session()->all(),
    ]);
});

require __DIR__.'/settings.php';
