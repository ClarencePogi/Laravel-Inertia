<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PrivilegesControlller;

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $permissions = auth()->user() ? auth()->user()->getAllPermissions()->pluck('name') : [];

    return Inertia::render('Dashboard', ['permissions' => $permissions]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['prefix' => 'management'], function () {
    Route::get('/tasks', [TaskController::class, 'index'])->middleware('permission:read task')->name('task.view');
    Route::post('/tasks/store', [TaskController::class, 'store'])->name('task.store');
    Route::delete('/tasks/delete/${id}', [TaskController::class, 'delete'])->name('task.delete');
    Route::put('/tasks/update', [TaskController::class, 'update'])->name('task.update');
    // Route::get('tasks/assign', [EmployeeController::class, 'index'])->name('assign.view');

    Route::get('/users', [EmployeeController::class, 'index'])->middleware('permission:read user')->name('users.manage.view');
    Route::post('/users/store', [RegisteredUserController::class, 'store'])->name('users.manage.store');
    Route::put('/users/update', [EmployeeController::class, 'update'])->name('users.manage.update');

    Route::get('/privileges', [PrivilegesControlller::class, 'index'])->middleware('permission:read privileges')->name('privileges.manage.view');
    Route::delete('/privileges/delete/${id}', [PrivilegesControlller::class, 'delete'])->name('privileges.manage.delete');
    Route::post('/privileges/store', [PrivilegesControlller::class, 'store'])->name('privileges.manage.store');
    Route::put('/privileges/update-role', [PrivilegesControlller::class, 'update'])->name('privileges.manage.update');

    Route::get('/view-tasks', [TaskController::class, 'view_task'])->name('view.task');
    Route::post('/task-assign', [TaskController::class, 'assignedTask'])->name('assign.task');

    
});




require __DIR__ . '/auth.php';
