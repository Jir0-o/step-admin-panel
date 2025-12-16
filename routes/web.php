<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AsignTaskController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginDetailsController;
use App\Http\Controllers\ManageWorkController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectTitleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesDataController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreProxyController;
use App\Http\Controllers\StoreSummaryProxyController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkPlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\StockDataController;
use App\Http\Controllers\StoreRouteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::view('/store',         'backend.admin.store')->name('store.index');
    Route::view('/discount',      'backend.admin.discount')->name('discount.index');
    Route::view('/user',          'backend.admin.user')->name('user.index');

    Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');

        // Roles CRUD
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');

        // Permissions CRUD
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::put('/permissions/{permission}', [RolePermissionController::class, 'updatePermission'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('permissions.destroy');

        // Attach / detach permissions to role
        Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'syncRolePermissions'])->name('roles.permissions.sync');
    });

    Route::resource('dashboard', DashboardController::class);
    Route::resource('user', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('store-routes', StoreRouteController::class);

    Route::resource('stores', StoreController::class);

    Route::post('/store/sync-store-tokens', [StoreController::class,'sync'])
        ->name('ajax.sync.store.tokens');


    Route::get('/stores/{store}/fetch-data', [StoreProxyController::class, 'fetchData'])->name('stores.fetch-data');
    Route::match(['get','post'], '/stores/{store}/fetch-summary', [StoreSummaryProxyController::class, 'fetchSummary'])->name('stores.fetch-summary');

    Route::get('/store/{store}/stock-table', [StockDataController::class, 'index'])
        ->name('manager.stock-data.index');
        
    // API route for datatable AJAX
    Route::get('/store/{store}/stock-table/data', [StockDataController::class, 'getStockData'])
        ->name('manager.stock-data.data');

    Route::get('/store/{store}/stock-table/export', [StockDataController::class, 'exportCsv'])
            ->name('manager.stock-data.export');

    Route::get('/manager/{store}/sales', [SalesDataController::class, 'index'])->name('manager.sales.index');
    Route::get('/manager/{store}/sales/data', [SalesDataController::class, 'getSalesData'])->name('manager.sales-data.data');
    Route::get('/manager/{store}/sales/export', [SalesDataController::class, 'exportCsv'])->name('manager.sales-data.export');



    //Notification Route
    Route::get('/notifications/count', [NotificationController::class, 'notificationCount'])->name('notifications.count');
    Route::delete('/notifications/delete/{id}', [NotificationController::class, 'deleteNotification'])->name('notifications.delete');
    Route::post('/notifications/clear', [NotificationController::class, 'clearNotifications'])->name('notifications.clear');
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    //Settings Route
    Route::get('/settings',[SettingsController::class, 'index'])->name('settings');

    
});
