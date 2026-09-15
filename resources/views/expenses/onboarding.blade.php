<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('portfolio.expenses_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main class="expense-page">
        <div class="expense-window expense-auth-window">
            <div class="panel-header">
                <span>> {{ __('portfolio.expenses_nickname_title') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="expense-back-link expense-text-button">{{ __('portfolio.expenses_logout') }}</button>
                </form>
            </div>
            <div class="expense-content expense-auth-content">
                <h1>{{ __('portfolio.expenses_nickname_title') }}</h1>
                <p>{{ __('portfolio.expenses_nickname_intro') }}</p>
                <form method="POST" action="{{ route('expenses.nickname.store') }}" class="expense-auth-form">
                    @csrf
                    <label>
                        {{ __('portfolio.expenses_nickname_label') }}
                        <input type="text" name="nickname" minlength="2" maxlength="24" required autofocus value="{{ old('nickname') }}">
                    </label>
                    @error('nickname')
                        <small class="expense-required-message">{{ $message }}</small>
                    @enderror
                    <button type="submit" class="btn-accent">{{ __('portfolio.expenses_nickname_save') }}</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
