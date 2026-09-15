<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('portfolio.expenses_admin_users_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main class="expense-page">
        <div class="expense-window">
            <div class="panel-header">
                <span>> {{ __('portfolio.expenses_admin_users_title') }}</span>
                <a href="{{ route('expenses.index') }}" class="expense-back-link">{{ __('portfolio.expenses_admin_back') }}</a>
            </div>
            <div class="expense-content">
                <p>{{ __('portfolio.expenses_admin_users_intro') }}</p>
                <div class="expense-users-list">
                    @forelse ($users as $registered)
                        <article class="expense-user-card panel">
                            @if ($registered->avatarUrl())
                                <img src="{{ $registered->avatarUrl() }}" alt="" class="expense-user-avatar">
                            @else
                                <span class="expense-user-avatar expense-user-avatar-fallback">{{ strtoupper(substr($registered->nickname ?: $registered->email, 0, 1)) }}</span>
                            @endif
                            <div>
                                <strong>{{ $registered->nickname ?: '—' }}</strong>
                                <p>{{ $registered->email }}</p>
                                <small>{{ $registered->role }} · {{ $registered->created_at?->format('Y-m-d') }}</small>
                            </div>
                        </article>
                    @empty
                        <p>{{ __('portfolio.expenses_admin_users_empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>

</html>
