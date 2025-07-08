<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\CurrencyList;
use App\Livewire\CurrencyChart;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    
Route::get('/currencies', CurrencyList::class)->name('currencies.index');
Route::get('/currencies/{fromCurrencySymbol}/{toCurrencySymbol}', CurrencyChart::class)->name('currencies.show');
});

require __DIR__.'/auth.php';

