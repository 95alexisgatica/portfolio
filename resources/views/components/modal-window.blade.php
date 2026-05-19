@props(['id', 'title', 'icon' => '⬛'])

<div id="modal-{{ $id }}"
    style="display:none; position:fixed; inset:0; z-index:1000; align-items:center; justify-content:center;">

    <div onclick="closeModal('{{ $id }}')" style="position:absolute; inset:0; background:rgba(0,0,0,0.5);">
    </div>

    <div class="modal-window panel" style="position:relative; width:480px; z-index:1001;">

        <div class="panel-header">
            <span>{{ $icon }} {{ $title }}</span>
            <div style="display:flex; gap:4px;">
                <div class="win-btn">_</div>
                <div class="win-btn">□</div>
                <div class="win-btn" onclick="closeModal('{{ $id }}')"
                    style="background:#c0392b; color:white; cursor:pointer;">✕</div>
            </div>
        </div>

        <div style="padding: 16px;">
            {{ $slot }}
        </div>

    </div>
</div>
