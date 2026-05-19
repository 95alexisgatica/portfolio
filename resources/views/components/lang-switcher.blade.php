{{-- Language switcher --}}
<div style="position: relative; display: inline-block; margin-left: 10px;" id="lang-dropdown">
    <button onclick="toggleLangMenu()"
        style="display: flex;font-family: var(--font-pixel); font-size: var(--font-size-base); background: none; padding: 2px 6px; cursor: pointer; color: var(--color-text);gap: 3px;">
        @if (app()->getLocale() == 'es')
            <img src="/img/flags/AR.png" alt="ES" style="width: 20px;">
            <span>ES</span>
        @elseif(app()->getLocale() == 'pt')
            <img src="/img/flags/PT.png" alt="PT" style="width: 20px;">
            <span>PT</span>
        @else
            <img src="/img/flags/EN.png" alt="EN" style="width: 20px;">
            <span>EN</span>
        @endif
        ▾
    </button>
    <div id="lang-menu"
        style="position: absolute; right: -51px; top: 50%; background: var(--color-bg-panel); border: 1px solid var(--color-border-dark); z-index: 100;">
        <a href="{{ route('lang.switch', 'es') }}"
            style="display: flex; padding: 4px 8px; gap: 3px; font-size: var(--font-size-base); color: var(--color-text); text-decoration: none;">
            <img src="/img/flags/AR.png" alt="ES" style="width: 20px; vertical-align: middle;">
            <span>ES</span>
        </a>
        <a href="{{ route('lang.switch', 'en') }}"
            style="display: flex; padding: 4px 8px; gap: 3px; font-size: var(--font-size-base); color: var(--color-text); text-decoration: none;">
            <img src="/img/flags/EN.png" alt="EN" style="width: 20px; vertical-align: middle;">
            <span>EN</span>
        </a>
        <a href="{{ route('lang.switch', 'pt') }}"
            style="display: flex; padding: 4px 8px; gap: 3px; font-size: var(--font-size-base); color: var(--color-text); text-decoration: none;">
            <img src="/img/flags/PT.png" alt="PT" style="width: 20px; vertical-align: middle;">
            <span>PT</span>
        </a>
    </div>
</div>
