<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('portfolio.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div class="min-h-screen p-6 flex items-start justify-center">
        <div class="portfolio-container">

            {{-- Title bar --}}
            <div class="flex items-center justify-between px-2 py-1 bg-header">
                <span class="font-pixel text-sm-base text-white">
                    ⬛ {{ __('portfolio.title') }}
                </span>
                <div class="flex gap-1">
                    <div class="win-btn">_</div>
                    <div class="win-btn">□</div>
                    <div class="win-btn win-btn-close">✕</div>
                </div>
            </div>

            {{-- Status bar --}}
            <div class="flex justify-between items-center px-4 bg-secondary border-b border-black">
                <span class="font-pixel text-sm-base">
                    ✦ STATUS: <span class="text-accent-green">{{ __('portfolio.status_online') }}</span>
                </span>
                <div class="flex items-center gap-4">
                    <span class="font-pixel text-sm-base">
                        ✦ BUILD: <span class="text-accent">{{ __('portfolio.build') }}</span>
                    </span>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex justify-between items-center pr-2 bg-secondary border-b-2 border-black">
                <div class="flex">
                    @foreach ([['data', '💾', __('portfolio.tab_data')], ['skills', '🎮', __('portfolio.tab_skills')], ['projects', '⚙️', __('portfolio.tab_projects')], ['experience', '📋', __('portfolio.tab_experience')], ['contact', '📡', __('portfolio.tab_contact')]] as [$key, $icon, $label])
                        <button class="tab-btn" id="tab-{{ $key }}" onclick="switchTab('{{ $key }}')">
                            {{ $icon }} {{ $label }}
                        </button>
                    @endforeach
                </div>
                <x-lang-switcher />
            </div>

            {{-- Main content --}}
            <div class="bg-main p-2">

                {{-- TAB DATA --}}
                <div id="content-data" class="tab-content flex flex-col gap-2">

                    {{-- TOP ROW: left col + center/right --}}
                    <div class="grid gap-2" style="grid-template-columns: 300px 1fr;">

                        {{-- LEFT COLUMN --}}
                        <div class="flex flex-col gap-2">

                            {{-- Profile --}}
                            <div class="panel">
                                <div class="panel-header">
                                    <span>> PROFILE.DAT</span>
                                    <span>✕</span>
                                </div>
                                <div class="p-3 text-center">
                                    <div class="profile-pic"></div>
                                    <div class="font-pixel text-2xl-base">{{ __('portfolio.name') }}</div>
                                    <div class="text-accent text-lg-base">{{ __('portfolio.role') }}</div>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="panel">
                                <div class="panel-header-secondary">> INFO.SYS</div>
                                <hr>
                                <div class="p-2 text-sm-base flex flex-col gap-1.5">
                                    <div>📍 <span class="text-accent">LOCATION:</span>
                                        {{ __('portfolio.info_location') }}</div>
                                    <div>✉️ <span class="text-accent">EMAIL:</span> {{ __('portfolio.info_email') }}
                                    </div>
                                    <div>⚡ <span class="text-accent">STATUS:</span> <span
                                            class="text-accent-green">{{ __('portfolio.info_status') }}</span></div>
                                    <div>📅 <span class="text-accent">EXP:</span> {{ __('portfolio.info_experience') }}
                                    </div>
                                    <div>🌐 <span class="text-accent">LANG:</span> {{ __('portfolio.info_language') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="panel">
                                <div class="panel-header-secondary">> STATS.EXE</div>
                                <hr>
                                <div class="p-2">
                                    @foreach ([[__('portfolio.stat_logic')], [__('portfolio.stat_creativity')], [__('portfolio.stat_problem_solving')], [__('portfolio.stat_discipline')], [__('portfolio.stat_adaptability')]] as [$stat])
                                        <div class="skill-container">
                                            <div class="skill-bar">
                                                <span class="text-sm-base">{{ $stat }}</span>
                                                <div class="flex-grow max-w-1/3">
                                                    <div class="skill-bar-track">
                                                        <div class="skill-bar-fill" style="--target-width: 100%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Terminal --}}
                            <div class="panel">
                                <div class="panel-header">
                                    <span>> TERMINAL.LOG</span>
                                    <span>✕</span>
                                </div>
                                <div class="bg-terminal p-2 text-sm-base text-terminal min-h-[338px]">
                                    <div>{{ __('portfolio.terminal_whoami') }}</div>
                                    <div class="pl-3">{{ __('portfolio.terminal_whoami_result') }}</div>
                                    <div>{{ __('portfolio.terminal_cat') }}</div>
                                    <div class="pl-3">{{ __('portfolio.terminal_skill1') }}</div>
                                    <div class="pl-3">{{ __('portfolio.terminal_skill2') }}</div>
                                    <div class="pl-3">{{ __('portfolio.terminal_skill3') }}</div>
                                    <div>{{ __('portfolio.terminal_status') }}</div>
                                    <div class="pl-3">{{ __('portfolio.terminal_status_result') }}</div>
                                    <div>$ <span class="blink">▋</span></div>
                                </div>
                            </div>

                        </div>

                        {{-- CENTER + RIGHT --}}
                        <div class="flex flex-col gap-2">
                            <div class="grid gap-2" style="grid-template-columns: 1fr 300px;">

                                {{-- CENTER --}}
                                <div class="flex flex-col gap-2">

                                    {{-- About --}}
                                    <div class="panel panel-about">
                                        <div class="panel-header">
                                            <span>> ABOUT.ME</span>
                                            <span>✕</span>
                                        </div>
                                        <div class="p-4">
                                            <p class="text-xl-base leading-relaxed mb-3">
                                                {{ __('portfolio.about_title') }}</p>
                                            <p class="text-xs-base text-muted leading-relaxed">
                                                {{ __('portfolio.about_desc') }}</p>
                                        </div>
                                    </div>

                                    {{-- Experience --}}
                                    <div class="panel">
                                        <div class="panel-header-secondary">> EXPERIENCE.LOG</div>
                                        <hr>
                                        <div class="p-3 flex flex-col gap-6">
                                            @foreach ([[__('portfolio.exp1_year'), __('portfolio.exp1_company'), __('portfolio.exp1_desc')], [__('portfolio.exp2_year'), __('portfolio.exp2_company'), __('portfolio.exp2_desc')], [__('portfolio.exp3_year'), __('portfolio.exp3_company'), __('portfolio.exp3_desc')]] as [$year, $company, $desc])
                                                <div class="flex gap-3">
                                                    <div class="text-accent-red text-sm-base">●</div>
                                                    <div>
                                                        <div class="text-sm-base">
                                                            {{ $year }} — <span
                                                                class="text-accent">{{ $company }}</span>
                                                        </div>
                                                        <div class="text-sm-base text-muted mt-1">{{ $desc }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Education --}}
                                    <div class="panel">
                                        <div class="panel-header-secondary">> EDUCATION.LOG</div>
                                        <hr>
                                        <div class="p-3 flex flex-col gap-2">
                                            @foreach ([[__('portfolio.edu3_year'), __('portfolio.edu3_title'), null]] as [$year, $title, $place])
                                                <div class="text-sm-base">
                                                    📋 {{ $year }}
                                                    <span class="text-accent ml-2">{{ $title }}</span>
                                                    @if ($place)
                                                        <span class="text-muted ml-2">
                                                            — {{ $place }}
                                                        </span>
                                                    @endif

                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>

                                {{-- RIGHT COLUMN: SKILLS --}}
                                <div class="flex flex-col gap-2 h-full">
                                    <div class="panel flex flex-col flex-1">
                                        <div class="panel-header">> SKILLS.SET</div>
                                        <div class="p-2">

                                            {{-- LANGUAGES --}}
                                            <div class="skill-dropdown">
                                                <button class="skill-dropdown-header open"
                                                    onclick="toggleSkillGroup(this)">
                                                    <span>{{ __('portfolio.skills_languages') }}</span>
                                                    <span class="skill-dropdown-arrow">▾</span>
                                                </button>
                                                <div class="skill-dropdown-body">
                                                    @foreach ([['PHP', 'icon_6'], ['JavaScript', 'icon_4'], ['TypeScript', 'icon_20'], ['SQL', 'icon_10']] as [$skill, $icon])
                                                        <div class="skill-container">
                                                            <div class="skill-bar">
                                                                <div class="flex items-center gap-1.5 max-w-[50%]">
                                                                    <div class="skill-icon w-8">
                                                                        <img src="{{ asset('img/icons/' . $icon . '.png') }}"
                                                                            alt="" class="max-h-8">
                                                                    </div>
                                                                    <span>{{ $skill }}</span>
                                                                </div>
                                                                <div class="flex-grow max-w-[50%]">
                                                                    <div class="skill-bar-track">
                                                                        <div class="skill-bar-fill"
                                                                            style="--target-width: 100%;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            {{-- FRAMEWORKS --}}
                                            <div class="skill-dropdown">
                                                <button class="skill-dropdown-header open"
                                                    onclick="toggleSkillGroup(this)">
                                                    <span>{{ __('portfolio.skills_frameworks') }}</span>
                                                    <span class="skill-dropdown-arrow">▾</span>
                                                </button>
                                                <div class="skill-dropdown-body">
                                                    @foreach ([['Laravel', 'icon_8'], ['Tailwind CSS', 'icon_19']] as [$skill, $icon])
                                                        <div class="skill-container">
                                                            <div class="skill-bar">
                                                                <div class="flex items-center gap-1.5 max-w-[50%]">
                                                                    <div class="skill-icon w-8">
                                                                        <img src="{{ asset('img/icons/' . $icon . '.png') }}"
                                                                            alt="" class="max-h-8">
                                                                    </div>
                                                                    <span>{{ $skill }}</span>
                                                                </div>
                                                                <div class="flex-grow max-w-[50%]">
                                                                    <div class="skill-bar-track">
                                                                        <div class="skill-bar-fill"
                                                                            style="--target-width: 100%;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            {{-- TOOLS --}}
                                            <div class="skill-dropdown">
                                                <button class="skill-dropdown-header open"
                                                    onclick="toggleSkillGroup(this)">
                                                    <span>{{ __('portfolio.skills_tools') }}</span>
                                                    <span class="skill-dropdown-arrow">▾</span>
                                                </button>
                                                <div class="skill-dropdown-body">
                                                    @foreach ([['Git & GitHub', 'icon_11'], ['Docker', 'icon_23'], ['Postman', 'icon_22'], ['VS Code', 'icon_9'], ['Linux', 'icon_21']] as [$skill, $icon])
                                                        <div class="skill-container">
                                                            <div class="skill-bar">
                                                                <div class="flex items-center gap-1.5 max-w-[50%]">
                                                                    <div class="skill-icon w-8">
                                                                        <img src="{{ asset('img/icons/' . $icon . '.png') }}"
                                                                            alt="" class="max-h-8">
                                                                    </div>
                                                                    <span>{{ $skill }}</span>
                                                                </div>
                                                                <div class="flex-grow max-w-[50%]">
                                                                    <div class="skill-bar-track">
                                                                        <div class="skill-bar-fill"
                                                                            style="--target-width: 100%;">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Projects --}}
                            <div class="grid gap-2">
                                <div class="panel">
                                    <div class="panel-header-secondary">> PROJECTS.SYS</div>
                                    <hr>
                                    <div class="p-2 grid grid-cols-4 gap-2">
                                        @foreach ([[__('portfolio.proj1_name'), __('portfolio.proj1_tech'), __('portfolio.proj1_desc'), __('portfolio.proj1_link'), __('portfolio.proj1_url'), __('portfolio.proj1_img')], [__('portfolio.proj3_name'), __('portfolio.proj3_tech'), __('portfolio.proj3_desc'), __('portfolio.proj3_link'), __('portfolio.proj3_url'), __('portfolio.proj3_img')], [__('portfolio.proj4_name'), __('portfolio.proj4_tech'), __('portfolio.proj4_desc'), __('portfolio.proj4_link'), '#', null]] as [$name, $tech, $desc, $link, $url, $img])
                                            <div class="project-card">
                                                <div class="font-pixel text-sm-base text-accent">> {{ $name }}
                                                </div>
                                                <div class="text-sm-base text-muted mt-0.5">{{ $tech }}</div>
                                                <div class="project-thumb"
                                                    @if ($img) style="background-image: url('{{ $img }}'); background-size: cover; background-position: center;" @endif>
                                                </div>
                                                <div class="text-sm-base mb-1.5">{{ $desc }}</div>
                                                <a href="{{ $url }}" class="text-sm-base text-accent-red"
                                                    target="_blank">{{ $link }}</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- BOTTOM ROW --}}
                                <div class="grid grid-cols-2 gap-2">

                                    {{-- Interests --}}
                                    <div class="panel">
                                        <div class="panel-header-secondary">> INTERESTS</div>
                                        <hr>
                                        <div class="p-2 grid grid-cols-4 gap-3">
                                            @foreach ([[__('portfolio.int1'), 'icon_1'], [__('portfolio.int2'), 'icon_3'], [__('portfolio.int3'), 'icon_6'], [__('portfolio.int4'), 'icon_7'], [__('portfolio.int5'), 'icon_2'], [__('portfolio.int6'), 'icon_4'], [__('portfolio.int7'), 'icon_5'], [__('portfolio.int8'), 'icon_8']] as [$label, $icon])
                                                <div class="flex flex-col items-center gap-1 text-center">
                                                    <img src="/img/interests/{{ $icon }}.png"
                                                        alt="{{ $label }}" class="w-12 h-12 object-contain">
                                                    <span class="text-sm-base">{{ $label }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Contact --}}
                                    <div class="panel">
                                        <div class="panel-header-secondary">> CONTACT.ME</div>
                                        <hr>
                                        <div class="p-2 text-sm-base">
                                            <p class="mb-2 text-muted">{{ __('portfolio.contact_cta') }}</p>
                                            <div class="flex flex-col gap-1.5">
                                                <a href="mailto:{{ __('portfolio.info_email') }}"
                                                    class="btn-accent text-center no-underline">
                                                    {{ __('portfolio.contact_email_btn') }}
                                                </a>
                                                <a href="https://ar.linkedin.com/in/alexis-gatica-161838208"
                                                    class="btn-accent-red text-center no-underline" target="_blank">
                                                    {{ __('portfolio.contact_cv_btn') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            {{-- Other tabs --}}
                            @foreach (['skills', 'projects', 'experience', 'education', 'contact'] as $t)
                                <div id="content-{{ $t }}" class="tab-content hidden p-5 text-muted">
                                    // {{ strtoupper($t) }} — coming soon
                                </div>
                            @endforeach

                        </div>
                    </div>
                    {{-- Status bar bottom --}}
                    <div class="flex justify-between px-4 py-1 bg-header text-white font-pixel text-sm-base mt-2">
                        <span>⬛ {{ __('portfolio.system_ready') }}</span>
                        <span>{{ __('portfolio.version') }} ⬛</span>
                    </div>
                </div>

                {{-- MODALES --}}
                <x-sections.skills />
                <x-sections.contact />

                <script>
                    function toggleLangMenu() {
                        const menu = document.getElementById('lang-menu');
                        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
                    }

                    document.addEventListener('click', function(e) {
                        if (!document.getElementById('lang-dropdown').contains(e.target)) {
                            document.getElementById('lang-menu').style.display = 'none';
                        }
                    });

                    switchTab('data');

                    function toggleSkillGroup(btn) {
                        const body = btn.nextElementSibling;
                        const isOpen = !body.classList.contains('closed');
                        body.classList.toggle('closed', isOpen);
                        btn.classList.toggle('open', !isOpen);
                    }

                    function animateSkillBars() {
                        document.querySelectorAll('.skill-bar-fill').forEach((bar, i) => {
                            const target = parseInt(bar.style.getPropertyValue('--target-width'));
                            if (!target) return;

                            const numEl = bar.closest('.skill-bar-track')
                                .closest('div')
                                .querySelector('[data-val]');

                            setTimeout(() => {
                                bar.classList.add('animated');

                                if (!numEl) return;

                                let current = 0;
                                const steps = 20;
                                const stepVal = target / steps;
                                const duration = 800;

                                const interval = setInterval(() => {
                                    current += stepVal;
                                    if (current >= target) {
                                        current = target;
                                        clearInterval(interval);
                                    }
                                    numEl.textContent = Math.round(current);
                                }, duration / steps);

                            }, i * 120);
                        });
                    }

                    function switchTab(tab) {
                        const modalTabs = ['contact', 'skills'];

                        if (modalTabs.includes(tab)) {
                            openModal(tab);
                            return;
                        }

                        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
                        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
                        document.getElementById('content-' + tab).style.display = tab === 'data' ? 'flex' : 'block';
                        document.getElementById('tab-' + tab).classList.add('active');
                    }

                    function openModal(id) {
                        const modal = document.getElementById('modal-' + id);
                        modal.style.display = 'flex';
                        const win = modal.querySelector('.modal-window');
                        win.style.opacity = '0';
                        win.style.transform = 'scale(0.92)';
                        win.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
                        requestAnimationFrame(() => {
                            win.style.opacity = '1';
                            win.style.transform = 'scale(1)';
                        });
                    }

                    function closeModal(id) {
                        const modal = document.getElementById('modal-' + id);
                        const win = modal.querySelector('.modal-window');
                        win.style.opacity = '0';
                        win.style.transform = 'scale(0.92)';
                        setTimeout(() => {
                            modal.style.display = 'none';
                        }, 150);
                    }

                    document.addEventListener('keydown', e => {
                        if (e.key === 'Escape') {
                            document.querySelectorAll('[id^="modal-"]').forEach(m => {
                                if (m.style.display === 'flex') closeModal(m.id.replace('modal-', ''));
                            });
                        }
                    });

                    animateSkillBars();
                </script>
</body>

</html>
