<x-modal-window id="skills" title="SKILLS.SET" icon="🎮">
    <div class="flex flex-col gap-4">

        {{-- LANGUAGES --}}
        <div>
            <div class="panel-header-secondary mb-2">> {{ __('portfolio.skills_languages') }}</div>
            <div class="grid grid-cols-4 gap-3">
                @foreach ([['PHP', 'icon_6'], ['JavaScript', 'icon_4'], ['TypeScript', 'icon_20'], ['SQL', 'icon_10']] as [$name, $icon])
                    <div class="flex flex-col items-center gap-1 text-center">
                        <img src="/img/icons/{{ $icon }}.png" alt="{{ $name }}"
                            class="w-10 h-10 object-contain">
                        <span class="text-sm-base">{{ $name }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <hr>

        {{-- FRAMEWORKS --}}
        <div>
            <div class="panel-header-secondary mb-2">> {{ __('portfolio.skills_frameworks') }}</div>
            <div class="grid grid-cols-4 gap-3">
                @foreach ([['Laravel', 'icon_8'], ['Tailwind CSS', 'icon_19']] as [$name, $icon])
                    <div class="flex flex-col items-center gap-1 text-center">
                        <img src="/img/icons/{{ $icon }}.png" alt="{{ $name }}"
                            class="w-10 h-10 object-contain">
                        <span class="text-sm-base">{{ $name }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <hr>

        {{-- TOOLS --}}
        <div>
            <div class="panel-header-secondary mb-2">> {{ __('portfolio.skills_tools') }}</div>
            <div class="grid grid-cols-4 gap-3">
                @foreach ([['Git & GitHub', 'icon_11'], ['Docker', 'icon_23'], ['Postman', 'icon_22'], ['VS Code', 'icon_9'], ['Linux', 'icon_21']] as [$name, $icon])
                    <div class="flex flex-col items-center gap-1 text-center">
                        <img src="/img/icons/{{ $icon }}.png" alt="{{ $name }}"
                            class="w-10 h-10 object-contain">
                        <span class="text-sm-base">{{ $name }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-modal-window>
