<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated user routes (Frontend cliente)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        return view('dashboard', [
            'quotesCount'   => method_exists($user, 'quotes') ? $user->quotes()->count() : 0,
            'ordersCount'   => method_exists($user, 'orders') ? $user->orders()->count() : 0,
            'projectsCount' => method_exists($user, 'projects') ? $user->projects()->count() : 0,
        ]);
    })->name('dashboard');

    // Profilo
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ Templates
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');

    // Preventivi
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{quote}/accept', [QuoteController::class, 'accept'])->name('quotes.accept');
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');

    // Ordini
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::patch('/orders/{order}/deposit-paid', [OrderController::class, 'markDepositPaid'])
        ->name('orders.depositPaid');
    Route::patch('/orders/{order}/balance-paid', [OrderController::class, 'markBalancePaid'])
        ->name('orders.balancePaid');

    // Progetti FE
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // Upload FE (nested su progetto)
    Route::post('/projects/{project}/files', [ProjectFileController::class, 'store'])->name('project-files.store');
    Route::get('/projects/{project}/files/{file}/download', [ProjectFileController::class, 'download'])->name('project-files.download');
    Route::delete('/projects/{project}/files/{file}', [ProjectFileController::class, 'destroy'])->name('project-files.destroy');

    // Commenti FE
    Route::post('/projects/{project}/comments', [ProjectController::class, 'storeComment'])
        ->name('projects.comments.store');
});

/*
|--------------------------------------------------------------------------
| Admin routes (Back Office)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Utenti
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])
            ->name('users.toggleAdmin');

        // Progetti (BO)
        Route::get('/projects', [AdminProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [AdminProjectController::class, 'show'])->name('projects.show');
        Route::patch('/projects/{project}', [AdminProjectController::class, 'update'])->name('projects.update');

        // NOTE ADMIN (private) + timeline
        Route::patch('/projects/{project}/notes', [AdminProjectController::class, 'updateNotes'])
            ->name('projects.notes.update');

        // Commenti ADMIN + timeline
        Route::post('/projects/{project}/comments', [AdminProjectController::class, 'storeComment'])
            ->name('projects.comments.store');

        // BO: download file cliente
        Route::get('/projects/{project}/files/{file}/download', [AdminProjectController::class, 'downloadFile'])
            ->name('projects.files.download');

        // BO: download snapshot
        Route::get('/projects/{project}/snapshot/download', [AdminProjectController::class, 'downloadSnapshot'])
            ->name('projects.snapshot.download');
    });

/*
|--------------------------------------------------------------------------
| Auth routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';