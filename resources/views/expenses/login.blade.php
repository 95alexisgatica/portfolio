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
                <span>> {{ __('portfolio.expenses_title') }}</span>
                <a href="{{ url('/') }}" class="expense-back-link">{{ __('portfolio.expenses_back') }}</a>
            </div>
            <div class="expense-content expense-auth-content">
                <h1>{{ __('portfolio.expenses_heading') }}</h1>
                <p>{{ __('portfolio.expenses_auth_intro') }}</p>
                @if ($authError)
                    <p class="expense-auth-error">{{ $authError }}</p>
                @endif
                @unless ($googleConfigured)
                    <p class="expense-auth-error">{{ __('portfolio.expenses_auth_missing_google') }}</p>
                @endunless
                <a href="{{ route('auth.google') }}" class="btn-accent expense-google-button">
                    {{ __('portfolio.expenses_auth_google') }}
                </a>
            </div>
        </div>
    </main>
</body>

</html>
