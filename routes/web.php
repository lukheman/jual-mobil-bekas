<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\CarModelController;
use App\Http\Controllers\Admin\ComponentDocsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LogoutController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Guest\LandingPageController;
use App\Http\Controllers\Guest\CarDetailController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', LandingPageController::class)->name('home');
Route::get('/mobil/{car:slug}', [CarDetailController::class, 'show'])->name('cars.show');

// Auth Routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Inventory
    Route::get('/cars', [CarController::class, 'index'])->name('admin.cars');
    Route::get('/cars/create', [CarController::class, 'create'])->name('admin.cars.create');
    Route::post('/cars', [CarController::class, 'store'])->name('admin.cars.store');
    Route::get('/cars/{car}/edit', [CarController::class, 'edit'])->name('admin.cars.edit');
    Route::put('/cars/{car}', [CarController::class, 'update'])->name('admin.cars.update');
    Route::delete('/cars/{car}', [CarController::class, 'destroy'])->name('admin.cars.destroy');
    Route::post('/cars/{car}/restore', [CarController::class, 'restore'])->name('admin.cars.restore');
    Route::delete('/cars/{car}/force', [CarController::class, 'forceDestroy'])->name('admin.cars.force-destroy');
    Route::post('/cars/{car}/status', [CarController::class, 'updateStatus'])->name('admin.cars.status');
    Route::post('/cars/{car}/featured', [CarController::class, 'toggleFeatured'])->name('admin.cars.featured');
    Route::post('/cars/{car}/photos', [CarController::class, 'uploadPhotos'])->name('admin.cars.photos.store');
    Route::post('/cars/{car}/photos/reorder', [CarController::class, 'reorderPhotos'])->name('admin.cars.photos.reorder');
    Route::post('/cars/{car}/photos/{photo}/primary', [CarController::class, 'setPrimaryPhoto'])->name('admin.cars.photos.primary');
    Route::delete('/cars/{car}/photos/{photo}', [CarController::class, 'destroyPhoto'])->name('admin.cars.photos.destroy');

    // Master data
    Route::get('/brands', [BrandController::class, 'index'])->name('admin.brands');
    Route::post('/brands', [BrandController::class, 'store'])->name('admin.brands.store');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('admin.brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('admin.brands.destroy');

    Route::get('/car-models', [CarModelController::class, 'index'])->name('admin.car-models');
    Route::post('/car-models', [CarModelController::class, 'store'])->name('admin.car-models.store');
    Route::put('/car-models/{carModel}', [CarModelController::class, 'update'])->name('admin.car-models.update');
    Route::delete('/car-models/{carModel}', [CarModelController::class, 'destroy'])->name('admin.car-models.destroy');

    Route::get('/features', [FeatureController::class, 'index'])->name('admin.features');
    Route::post('/features', [FeatureController::class, 'store'])->name('admin.features.store');
    Route::put('/features/{feature}', [FeatureController::class, 'update'])->name('admin.features.update');
    Route::delete('/features/{feature}', [FeatureController::class, 'destroy'])->name('admin.features.destroy');

    // Leads
    Route::get('/leads', [LeadController::class, 'index'])->name('admin.leads');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('admin.leads.update');
    Route::get('/leads/export', [LeadController::class, 'export'])->name('admin.leads.export');

    // Content & settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('admin.settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    Route::get('/pages', [PageController::class, 'index'])->name('admin.pages');
    Route::get('/pages/create', [PageController::class, 'create'])->name('admin.pages.create');
    Route::post('/pages', [PageController::class, 'store'])->name('admin.pages.store');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('admin.pages.edit');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('admin.pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('admin.pages.destroy');

    Route::get('/faqs', [FaqController::class, 'index'])->name('admin.faqs');
    Route::post('/faqs', [FaqController::class, 'store'])->name('admin.faqs.store');
    Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('admin.faqs.update');
    Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('admin.faqs.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('admin.profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('admin.profile.avatar.destroy');

    Route::get('/components', ComponentDocsController::class)->name('admin.components');
    Route::post('/logout', [LogoutController::class, '__invoke'])->name('logout');
});
