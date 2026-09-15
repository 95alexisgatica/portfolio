<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExpensePageController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['es', 'en', 'pt'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::get('/', function () {
    return view('portfolio');
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->middleware('throttle:5,1')
    ->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->middleware('throttle:5,1')
    ->name('auth.google.callback');

Route::post('/logout', [GoogleAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/calculadora-gastos', [ExpensePageController::class, 'show'])
    ->name('expenses.index');

Route::post('/calculadora-gastos/nickname', [ProfileController::class, 'storeNickname'])
    ->middleware('auth')
    ->name('expenses.nickname.store');

Route::middleware(['auth', 'nickname'])->group(function () {
    Route::put('/calculadora-gastos/perfil', [ProfileController::class, 'update'])
        ->name('expenses.profile.update');
    Route::post('/calculadora-gastos/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('expenses.avatar.update');
    Route::delete('/calculadora-gastos/avatar', [ProfileController::class, 'destroyAvatar'])
        ->name('expenses.avatar.destroy');

    Route::get('/calculadora-gastos/data', [ExpensesController::class, 'data'])
        ->name('expenses.data');
    Route::post('/calculadora-gastos/comentarios', [CommentController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('expenses.comments.store');
    Route::post('/calculadora-gastos/inspiracion', [ExpensesController::class, 'storeInspiration'])
        ->name('expenses.inspiration.store');
    Route::delete('/calculadora-gastos/inspiracion', [ExpensesController::class, 'destroyInspiration'])
        ->name('expenses.inspiration.destroy');
    Route::post('/calculadora-gastos', [ExpensesController::class, 'store'])
        ->name('expenses.store');
    Route::post('/calculadora-gastos/gastos-fijos', [ExpensesController::class, 'storeFixedExpense'])
        ->name('expenses.fixed.store');
    Route::patch('/calculadora-gastos/gastos-fijos/{fixedExpense}/estado', [ExpensesController::class, 'updateFixedStatus'])
        ->name('expenses.fixed.status');
    Route::delete('/calculadora-gastos/gastos-fijos/{fixedExpense}/mes', [ExpensesController::class, 'excludeFixedForMonth'])
        ->name('expenses.fixed.exclude');
    Route::patch('/calculadora-gastos/{expense}', [ExpensesController::class, 'update'])
        ->name('expenses.update');
    Route::delete('/calculadora-gastos/{expense}', [ExpensesController::class, 'destroy'])
        ->name('expenses.destroy');
    Route::put('/calculadora-gastos/ingreso', [ExpensesController::class, 'income'])
        ->name('expenses.income');
    Route::post('/calculadora-gastos/ingresos-extra', [ExpensesController::class, 'storeExtraIncome'])
        ->name('expenses.extra-income.store');
    Route::delete('/calculadora-gastos/ingresos-extra/{extraIncome}', [ExpensesController::class, 'destroyExtraIncome'])
        ->name('expenses.extra-income.destroy');
    Route::post('/calculadora-gastos/categorias', [ExpensesController::class, 'category'])
        ->name('expenses.category');
    Route::put('/calculadora-gastos/categorias/{category}', [ExpensesController::class, 'updateCategory'])
        ->name('expenses.category.update');
    Route::patch('/calculadora-gastos/categorias/{category}/color', [ExpensesController::class, 'categoryColor'])
        ->name('expenses.category.color');
    Route::delete('/calculadora-gastos/categorias/{category}', [ExpensesController::class, 'destroyCategory'])
        ->name('expenses.category.destroy');
});

Route::middleware(['auth', 'nickname', 'super_admin'])->group(function () {
    Route::get('/calculadora-gastos/usuarios', [UserController::class, 'index'])
        ->name('expenses.admin.users');
    Route::get('/calculadora-gastos/comentarios', [CommentController::class, 'index'])
        ->name('expenses.comments.index');
    Route::patch('/calculadora-gastos/comentarios/{comment}/leido', [CommentController::class, 'markRead'])
        ->name('expenses.comments.read');
});
