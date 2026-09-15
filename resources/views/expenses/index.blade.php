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
        <div class="expense-window">
            <div class="panel-header">
                <span>> {{ __('portfolio.expenses_title') }}</span>
                <div class="expense-header-actions">
                    <button id="open-avatar" type="button" class="expense-user-chip">
                        @if ($user->avatarUrl())
                            <img src="{{ $user->avatarUrl() }}" alt="" class="expense-user-avatar">
                        @else
                            <span class="expense-user-avatar expense-user-avatar-fallback">{{ strtoupper(substr($user->nickname, 0, 1)) }}</span>
                        @endif
                        <strong>{{ $user->nickname }}</strong>
                    </button>
                    @if ($isAdmin)
                        <a href="{{ route('expenses.admin.users') }}" class="expense-back-link">{{ __('portfolio.expenses_admin_users') }}</a>
                        <button id="open-messages" type="button" class="expense-back-link expense-text-button">
                            {{ __('portfolio.expenses_admin_messages') }}
                            <span id="comments-badge" class="expense-comments-badge" hidden title="{{ __('portfolio.expenses_comments_unread') }}"></span>
                        </button>
                    @endif
                    <button id="open-settings" type="button" class="expense-chart-settings">{{ __('portfolio.expenses_settings') }}</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="expense-back-link expense-text-button">{{ __('portfolio.expenses_logout') }}</button>
                    </form>
                    <a href="{{ url('/') }}" class="expense-back-link">{{ __('portfolio.expenses_back') }}</a>
                </div>
            </div>

            <div class="expense-content">
                <div class="expense-intro">
                    <h1>{{ __('portfolio.expenses_heading') }}</h1>
                </div>

                <section class="expense-feedback">
                    <div class="expense-feedback-sign">
                        <span class="expense-feedback-icon" aria-hidden="true">!</span>
                        <div class="expense-feedback-text">
                            <strong>{{ __('portfolio.expenses_feedback_title') }}</strong>
                            <p>{{ __('portfolio.expenses_feedback_message') }}</p>
                        </div>
                        <button id="open-feedback" type="button" class="btn-accent expense-feedback-button">
                            {{ __('portfolio.expenses_feedback_button') }}
                        </button>
                    </div>
                </section>

                <div id="settings-modal" class="category-manager-overlay" hidden>
                    <section class="category-manager modal-window">
                        <div class="panel-header">
                            <span>> {{ __('portfolio.expenses_settings_title') }}</span>
                            <button id="close-settings" type="button" class="category-manager-close">×</button>
                        </div>
                        <form method="POST" action="{{ route('expenses.profile.update') }}" class="expense-auth-form">
                            @csrf
                            @method('PUT')
                            <label>
                                {{ __('portfolio.expenses_nickname_label') }}
                                <input type="text" name="nickname" minlength="2" maxlength="24" required value="{{ $user->nickname }}">
                            </label>
                            <button type="submit" class="btn-accent">{{ __('portfolio.expenses_settings_save') }}</button>
                        </form>
                    </section>
                </div>

                <div id="avatar-modal" class="category-manager-overlay" hidden>
                    <section class="category-manager modal-window expense-avatar-modal">
                        <div class="panel-header">
                            <span>> {{ __('portfolio.expenses_avatar_title') }}</span>
                            <button id="close-avatar" type="button" class="category-manager-close">×</button>
                        </div>
                        <div class="expense-avatar-editor">
                            @if ($user->avatarUrl())
                                <img id="avatar-preview" class="expense-avatar-preview" src="{{ $user->avatarUrl() }}" alt="">
                                <span id="avatar-fallback" class="expense-avatar-preview expense-user-avatar-fallback" hidden>{{ strtoupper(substr($user->nickname, 0, 1)) }}</span>
                            @else
                                <img id="avatar-preview" class="expense-avatar-preview" alt="" hidden>
                                <span id="avatar-fallback" class="expense-avatar-preview expense-user-avatar-fallback">{{ strtoupper(substr($user->nickname, 0, 1)) }}</span>
                            @endif
                            <form method="POST" action="{{ route('expenses.avatar.update') }}" enctype="multipart/form-data" class="expense-auth-form">
                                @csrf
                                <label class="expense-avatar-file">
                                    {{ __('portfolio.expenses_avatar_change') }}
                                    <input id="avatar-input" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required>
                                </label>
                                <button type="submit" class="btn-accent">{{ __('portfolio.expenses_avatar_save') }}</button>
                            </form>
                            @if ($user->avatarUrl())
                                <form method="POST" action="{{ route('expenses.avatar.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="expense-delete">{{ __('portfolio.expenses_avatar_delete') }}</button>
                                </form>
                            @endif
                        </div>
                    </section>
                </div>

                <div id="feedback-modal" class="category-manager-overlay" hidden>
                    <section class="category-manager modal-window">
                        <div class="panel-header">
                            <span>> {{ __('portfolio.expenses_feedback_modal_title') }}</span>
                            <button id="close-feedback" type="button" class="category-manager-close">×</button>
                        </div>
                        <form id="feedback-form" class="feedback-form">
                            <input id="feedback-name" name="name" type="text" maxlength="60" autocomplete="name" placeholder="{{ __('portfolio.expenses_feedback_name_placeholder') }}">
                            <textarea id="feedback-message" name="message" maxlength="4000" required placeholder="{{ __('portfolio.expenses_feedback_message_placeholder') }}"></textarea>
                            <p class="feedback-note">{{ __('portfolio.expenses_feedback_note') }}</p>
                            <button type="submit" class="btn-accent expense-feedback-send">
                                {{ __('portfolio.expenses_feedback_send') }}
                            </button>
                        </form>
                    </section>
                </div>

                @if ($isAdmin)
                    <div id="messages-modal" class="category-manager-overlay" hidden>
                        <section class="category-manager modal-window expense-messages-modal">
                            <div class="panel-header">
                                <span>> {{ __('portfolio.expenses_comments_title') }}</span>
                                <button id="close-messages" type="button" class="category-manager-close">×</button>
                            </div>
                            <div id="comments-list" class="expense-comments-list"></div>
                        </section>
                    </div>
                @endif

                <section class="expense-period panel">
                    <div class="expense-period-header">
                        <button id="previous-year" type="button" class="expense-year-button" aria-label="{{ __('portfolio.expenses_previous_year') }}">&lt;</button>
                        <label>
                            {{ __('portfolio.expenses_year') }}
                            <input id="selected-year" type="number" min="2000" max="2100" step="1" value="2026">
                        </label>
                        <button id="next-year" type="button" class="expense-year-button" aria-label="{{ __('portfolio.expenses_next_year') }}">&gt;</button>
                    </div>
                    <div id="month-tabs" class="expense-month-tabs" role="tablist"></div>
                </section>

                <section class="expense-summary" aria-label="{{ __('portfolio.expenses_summary') }}">
                    <div class="expense-metrics">
                        <div class="expense-metric-cards">
                            <div class="expense-income panel">
                                <div class="panel-header-secondary">> {{ __('portfolio.expenses_income_title') }}</div>
                                <label class="expense-income-label">
                                    {{ __('portfolio.expenses_monthly_income') }}
                                    <input id="monthly-income" type="number" min="0.01" step="0.01" value="">
                                    <small id="income-required-message" class="expense-required-message" hidden>{{ __('portfolio.expenses_income_required') }}</small>
                                </label>
                                <div class="expense-extra-income">
                                    <button id="toggle-extra-incomes" type="button" class="expense-extra-toggle">
                                        {{ __('portfolio.expenses_extra_income_toggle') }}
                                        <strong id="extra-income-total">0.00</strong>
                                    </button>
                                </div>
                                <p class="expense-income-total">{{ __('portfolio.expenses_income_month_total') }} <strong id="income-month-total">0.00</strong></p>
                            </div>
                            <div class="expense-total-card panel">
                                <span>{{ __('portfolio.expenses_month_spent') }}</span>
                                <strong id="expense-total">0.00</strong>
                            </div>
                            <div class="expense-total-card panel">
                                <span>{{ __('portfolio.expenses_remaining') }}</span>
                                <strong id="expense-remaining">0.00</strong>
                            </div>
                        </div>
                        <section class="expense-chart panel">
                            <div class="panel-header-secondary">
                                <span>> {{ __('portfolio.expenses_chart_title') }}</span>
                                <button id="chart-settings" type="button" class="expense-chart-settings">{{ __('portfolio.expenses_chart_settings') }}</button>
                            </div>
                            <div class="expense-chart-content">
                                <div id="expense-pie-chart" class="expense-pie-chart" role="img" aria-label="{{ __('portfolio.expenses_chart_title') }}"></div>
                                <div id="expense-chart-list" class="expense-chart-list"></div>
                                <div class="expense-chart-total">
                                    <span>{{ __('portfolio.expenses_total') }}</span>
                                    <strong id="expense-chart-total">0.00</strong>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="expense-inspiration panel">
                        <div class="panel-header-secondary">> {{ __('portfolio.expenses_inspiration_title') }}</div>
                        <div class="expense-inspiration-body">
                            <div id="inspiration-empty" class="expense-inspiration-empty">{{ __('portfolio.expenses_inspiration_empty') }}</div>
                            <img id="inspiration-image" class="expense-inspiration-image" alt="{{ __('portfolio.expenses_inspiration_alt') }}" hidden>
                            <div class="expense-inspiration-url">
                                <input id="inspiration-url" type="url" placeholder="{{ __('portfolio.expenses_inspiration_url_placeholder') }}">
                                <button id="save-inspiration-url" type="button" class="btn-accent expense-category-button">{{ __('portfolio.expenses_inspiration_link') }}</button>
                            </div>
                            <div class="expense-inspiration-actions">
                                <label class="expense-upload-button">
                                    {{ __('portfolio.expenses_inspiration_upload') }}
                                    <input id="inspiration-input" type="file" accept="image/jpeg,image/png,image/gif">
                                </label>
                                <button id="delete-inspiration" type="button" class="expense-delete" hidden>{{ __('portfolio.expenses_inspiration_delete') }}</button>
                            </div>
                            <small id="inspiration-status" class="expense-required-message" hidden></small>
                        </div>
                    </div>
                </section>

                <div id="extra-incomes-modal" class="category-manager-overlay" hidden>
                    <section class="category-manager modal-window expense-extra-modal">
                        <div class="panel-header">
                            <span>> {{ __('portfolio.expenses_extra_income_title') }}</span>
                            <button id="close-extra-incomes" type="button" class="category-manager-close">×</button>
                        </div>
                        <p class="category-manager-warning">{{ __('portfolio.expenses_extra_income_description') }}</p>
                        <ul id="extra-incomes-list" class="expense-extra-list"></ul>
                        <form id="extra-income-form" class="expense-extra-form">
                            <input id="extra-income-detail" type="text" maxlength="255" required placeholder="{{ __('portfolio.expenses_extra_income_placeholder') }}">
                            <input id="extra-income-amount" type="number" min="0.01" step="0.01" required placeholder="0.00">
                            <button type="submit" class="btn-accent">{{ __('portfolio.expenses_extra_income_add') }}</button>
                        </form>
                    </section>
                </div>

                <div id="chart-settings-modal" class="category-manager-overlay" hidden>
                    <section class="category-manager modal-window">
                        <div class="panel-header">
                            <span>> {{ __('portfolio.expenses_chart_settings') }}</span>
                            <button id="close-chart-settings" type="button" class="category-manager-close">×</button>
                        </div>
                        <p class="category-manager-warning">{{ __('portfolio.expenses_chart_settings_description') }}</p>
                        <div id="chart-settings-list" class="category-manager-list"></div>
                    </section>
                </div>

                <form id="expense-form" class="expense-form">
                    <label>
                        {{ __('portfolio.expenses_date') }}
                        <input id="expense-date" type="date" name="date" required>
                    </label>

                    <div class="expense-category-field">
                        <span class="expense-category-label">
                            {{ __('portfolio.expenses_category') }}
                            <button id="manage-categories" type="button" class="expense-manage-categories">
                                {{ __('portfolio.expenses_manage_categories') }}
                            </button>
                        </span>
                        <div class="expense-category-picker">
                            <input id="expense-category" type="text" name="category" placeholder="{{ __('portfolio.expenses_category_placeholder') }}" autocomplete="off" required>
                            <div id="category-menu" class="expense-category-menu" hidden>
                                <div id="category-options" class="expense-category-options"></div>
                                <button id="show-category-form" type="button" class="expense-category-plus" title="{{ __('portfolio.expenses_new_category') }}" aria-label="{{ __('portfolio.expenses_new_category') }}">+</button>
                                <div id="category-add-form" class="expense-category-add" hidden>
                                    <input id="new-category" type="text" maxlength="40" placeholder="{{ __('portfolio.expenses_new_category_placeholder') }}">
                                    <button id="add-category" type="button" class="btn-accent expense-category-button">
                                        {{ __('portfolio.expenses_add_category') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <label>
                        {{ __('portfolio.expenses_detail') }}
                        <input type="text" name="detail" placeholder="{{ __('portfolio.expenses_detail_placeholder') }}">
                    </label>

                    <label>
                        {{ __('portfolio.expenses_payment_type') }}
                        <select name="payment_type" required>
                            <option value="{{ __('portfolio.expenses_payment_card') }}">{{ __('portfolio.expenses_payment_card') }}</option>
                            <option value="{{ __('portfolio.expenses_payment_cash') }}" selected>{{ __('portfolio.expenses_payment_cash') }}</option>
                            <option value="{{ __('portfolio.expenses_payment_transfer') }}">{{ __('portfolio.expenses_payment_transfer') }}</option>
                        </select>
                    </label>

                    <label>
                        {{ __('portfolio.expenses_notes') }}
                        <input type="text" name="notes" placeholder="{{ __('portfolio.expenses_notes_placeholder') }}">
                    </label>

                    <label>
                        {{ __('portfolio.expenses_amount') }}
                        <input type="number" name="amount" min="0" step="0.01" placeholder="0.00" required>
                    </label>

                    <button type="submit" class="btn-accent expense-submit">
                        {{ __('portfolio.expenses_add') }}
                    </button>
                    <button id="open-fixed-form" type="button" class="btn-accent-red expense-submit">
                        {{ __('portfolio.expenses_add_fixed') }}
                    </button>
                </form>

                <div id="category-manager" class="category-manager-overlay" hidden>
                    <section class="category-manager modal-window">
                        <div class="panel-header">
                            <span>> {{ __('portfolio.expenses_manage_categories') }}</span>
                            <button id="close-category-manager" type="button" class="category-manager-close">×</button>
                        </div>
                        <p class="category-manager-warning">{{ __('portfolio.expenses_delete_category_warning') }}</p>
                        <div id="category-manager-list" class="category-manager-list"></div>
                    </section>
                </div>

                <div id="fixed-expense-modal" class="category-manager-overlay" hidden>
                    <section class="category-manager modal-window">
                        <div class="panel-header">
                            <span>> {{ __('portfolio.expenses_fixed_title') }}</span>
                            <button id="close-fixed-form" type="button" class="category-manager-close">×</button>
                        </div>
                        <p class="category-manager-warning">{{ __('portfolio.expenses_fixed_description') }}</p>
                        <form id="fixed-expense-form" class="fixed-expense-form">
                            <div id="fixed-category-picker"></div>
                            <input name="detail" type="text" placeholder="{{ __('portfolio.expenses_detail') }}">
                            <select name="payment_type">
                                <option>{{ __('portfolio.expenses_payment_cash') }}</option>
                                <option>{{ __('portfolio.expenses_payment_card') }}</option>
                                <option>{{ __('portfolio.expenses_payment_transfer') }}</option>
                            </select>
                            <input name="amount" type="number" min="0" step="0.01" placeholder="{{ __('portfolio.expenses_amount') }} ({{ __('portfolio.expenses_optional') }})">
                            <input name="notes" type="text" placeholder="{{ __('portfolio.expenses_notes') }}">
                            <button type="submit" class="btn-accent">{{ __('portfolio.expenses_save_fixed') }}</button>
                        </form>
                    </section>
                </div>

                <div class="expense-bulk-actions">
                    <button id="toggle-multi-select" type="button" class="btn-accent">
                        {{ __('portfolio.expenses_multi_select') }}
                    </button>
                    <button id="delete-selected-expenses" type="button" class="btn-accent-red" hidden>
                        {{ __('portfolio.expenses_delete_selected') }}
                    </button>
                </div>

                <div class="expense-table-wrapper">
                    <table class="expense-table">
                        <thead>
                            <tr>
                                <th id="expense-select-header" hidden>{{ __('portfolio.expenses_select') }}</th>
                                <th>{{ __('portfolio.expenses_date') }}</th>
                                <th>{{ __('portfolio.expenses_category') }}</th>
                                <th>{{ __('portfolio.expenses_detail') }}</th>
                                <th>{{ __('portfolio.expenses_payment_type') }}</th>
                                <th>{{ __('portfolio.expenses_amount') }}</th>
                                <th>{{ __('portfolio.expenses_notes') }}</th>
                                <th>{{ __('portfolio.expenses_status') }}</th>
                                <th>{{ __('portfolio.expenses_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="expense-list"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" id="expense-table-total-label">{{ __('portfolio.expenses_total') }}</th>
                                <th id="expense-table-total">0.00</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <section class="expense-annual panel">
                    <div class="panel-header-secondary">> {{ __('portfolio.expenses_annual_summary') }}</div>
                    <div class="expense-annual-grid">
                        <div><span>{{ __('portfolio.expenses_annual_income') }}</span><strong id="annual-income">0.00</strong></div>
                        <div><span>{{ __('portfolio.expenses_annual_spent') }}</span><strong id="annual-spent">0.00</strong></div>
                        <div><span>{{ __('portfolio.expenses_annual_remaining') }}</span><strong id="annual-remaining">0.00</strong></div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <div id="income-gate" class="income-gate" hidden>
        <div class="income-gate-window">
            <div class="panel-header">
                <span>> {{ __('portfolio.expenses_income_required_title') }}</span>
            </div>
            <p>{{ __('portfolio.expenses_income_required_message') }}</p>
            <label>
                {{ __('portfolio.expenses_monthly_income') }}
                <input id="income-gate-input" type="number" min="0.01" step="0.01" required autofocus>
            </label>
            <button id="income-gate-submit" type="button" class="btn-accent">{{ __('portfolio.expenses_income_required_button') }}</button>
        </div>
    </div>

        <div id="delete-expense-modal" class="income-gate" hidden>
            <div class="income-gate-window expense-delete-window">
                <div class="panel-header"><span>> {{ __('portfolio.expenses_delete_title') }}</span></div>
                <p id="delete-expense-message">{{ __('portfolio.expenses_delete_message') }}</p>
                <div id="fixed-delete-options" class="fixed-delete-options" hidden>
                    <button id="delete-fixed-month" type="button" class="btn-accent-red">{{ __('portfolio.expenses_remove_fixed_month') }}</button>
                    <button id="delete-fixed-future" type="button" class="btn-accent-red">{{ __('portfolio.expenses_remove_fixed_future') }}</button>
                </div>
                <button id="confirm-delete-expense" type="button" class="btn-accent-red">{{ __('portfolio.expenses_delete_confirm') }}</button>
                <button id="cancel-delete-expense" type="button" class="expense-cancel-button">{{ __('portfolio.expenses_cancel') }}</button>
            </div>
        </div>

    <script>
        const expenseForm = document.getElementById('expense-form');
        const expenseList = document.getElementById('expense-list');
        const expenseTotal = document.getElementById('expense-total');
        const expenseTableTotal = document.getElementById('expense-table-total');
        const expenseRemaining = document.getElementById('expense-remaining');
        const monthlyIncome = document.getElementById('monthly-income');
        const extraIncomeTotal = document.getElementById('extra-income-total');
        const extraIncomesModal = document.getElementById('extra-incomes-modal');
        const extraIncomesList = document.getElementById('extra-incomes-list');
        const extraIncomeForm = document.getElementById('extra-income-form');
        const extraIncomeDetail = document.getElementById('extra-income-detail');
        const extraIncomeAmount = document.getElementById('extra-income-amount');
        const incomeMonthTotal = document.getElementById('income-month-total');
        const extraIncomeEmptyLabel = @json(__('portfolio.expenses_extra_income_empty'));
        const extraIncomeDeleteLabel = @json(__('portfolio.expenses_extra_income_delete'));
        const chartSettingsEmptyLabel = @json(__('portfolio.expenses_chart_settings_empty'));
        const expenseDate = document.getElementById('expense-date');
        const categoryInput = document.getElementById('expense-category');
        const newCategoryInput = document.getElementById('new-category');
        const categoryMenu = document.getElementById('category-menu');
        const categoryOptions = document.getElementById('category-options');
        const showCategoryForm = document.getElementById('show-category-form');
        const categoryAddForm = document.getElementById('category-add-form');
        const categoryManager = document.getElementById('category-manager');
        const categoryManagerList = document.getElementById('category-manager-list');
        const expenseChartList = document.getElementById('expense-chart-list');
        const expenseChartTotal = document.getElementById('expense-chart-total');
        const expensePieChart = document.getElementById('expense-pie-chart');
        const chartSettingsModal = document.getElementById('chart-settings-modal');
        const chartSettingsList = document.getElementById('chart-settings-list');
        const monthTabs = document.getElementById('month-tabs');
        const selectedYearInput = document.getElementById('selected-year');
        const annualIncome = document.getElementById('annual-income');
        const annualSpent = document.getElementById('annual-spent');
        const annualRemaining = document.getElementById('annual-remaining');
        const fixedExpenseModal = document.getElementById('fixed-expense-modal');
        const fixedExpenseForm = document.getElementById('fixed-expense-form');
        const fixedStatusLabel = @json(__('portfolio.expenses_status'));
        const paidLabel = @json(__('portfolio.expenses_paid'));
        const unpaidLabel = @json(__('portfolio.expenses_unpaid'));
        const removeFixedLabel = @json(__('portfolio.expenses_remove_fixed_month'));
        const incomeRequiredMessage = document.getElementById('income-required-message');
        const inspirationInput = document.getElementById('inspiration-input');
        const inspirationImage = document.getElementById('inspiration-image');
        const inspirationEmpty = document.getElementById('inspiration-empty');
        const deleteInspiration = document.getElementById('delete-inspiration');
        const inspirationStatus = document.getElementById('inspiration-status');
        const incomeGate = document.getElementById('income-gate');
        const incomeGateInput = document.getElementById('income-gate-input');
        const incomeGateSubmit = document.getElementById('income-gate-submit');
        const deleteExpenseModal = document.getElementById('delete-expense-modal');
        const fixedDeleteOptions = document.getElementById('fixed-delete-options');
        const confirmDeleteExpense = document.getElementById('confirm-delete-expense');
        const pendingDeleteMessage = document.getElementById('delete-expense-message');
        const deleteSelectedExpenses = document.getElementById('delete-selected-expenses');
        const feedbackModal = document.getElementById('feedback-modal');
        const feedbackForm = document.getElementById('feedback-form');
        const commentsList = document.getElementById('comments-list');
        const commentsBadge = document.getElementById('comments-badge');
        const anonymousLabel = @json(__('portfolio.expenses_comments_anonymous'));
        const markReadLabel = @json(__('portfolio.expenses_comments_mark_read'));
        const readLabel = @json(__('portfolio.expenses_comments_read'));
        const commentsEmptyLabel = @json(__('portfolio.expenses_comments_empty'));
        let comments = [];
        const toggleMultiSelect = document.getElementById('toggle-multi-select');
        const expenseSelectHeader = document.getElementById('expense-select-header');
        const expenseTableTotalLabel = document.getElementById('expense-table-total-label');
        const multiSelectLabel = @json(__('portfolio.expenses_multi_select'));
        const multiSelectActiveLabel = @json(__('portfolio.expenses_multi_select_active'));
        let multiSelectActive = false;
        let pendingDeletion = null;

        function categorySortKey(category) {
            return category
                .normalize('NFKD')
                .replace(/[\p{Extended_Pictographic}\uFE0F\u200D]/gu, '')
                .replace(/^[^\p{L}\p{N}]*/u, '')
                .trim()
                .toLocaleLowerCase(document.documentElement.lang);
        }

        function categoriesSorted(items = categories) {
            return [...items].sort((first, second) => categorySortKey(first).localeCompare(categorySortKey(second), document.documentElement.lang));
        }
        const inspirationUrl = document.getElementById('inspiration-url');
        const saveInspirationUrl = document.getElementById('save-inspiration-url');
        const deleteLabel = @json(__('portfolio.expenses_delete'));
        const deleteCategoryLabel = @json(__('portfolio.expenses_delete_category'));
        const undefinedCategoryLabel = @json(__('portfolio.expenses_undefined_category'));
        const editLabel = @json(__('portfolio.expenses_edit'));
        const saveLabel = @json(__('portfolio.expenses_save'));
        const cancelLabel = @json(__('portfolio.expenses_cancel'));
        const chartColorLabel = @json(__('portfolio.expenses_chart_color'));
        const paymentOptions = [
            @json(__('portfolio.expenses_payment_card')),
            @json(__('portfolio.expenses_payment_cash')),
            @json(__('portfolio.expenses_payment_transfer')),
        ];
        const categoryStorageKey = 'portfolio-expense-categories';
        const chartColorStorageKey = 'portfolio-expense-chart-colors';
        const expenseStorageKey = 'portfolio-expenses';
        const incomeStorageKey = 'portfolio-expense-income';
        const categoryRecords = {};
        const monthNames = [...Array(12)].map((_, month) => new Intl.DateTimeFormat(document.documentElement.lang, { month: 'short' }).format(new Date(2020, month, 1)).toUpperCase());
        const currentDate = new Date();
        let selectedYear = currentDate.getFullYear();
        let selectedMonth = currentDate.getMonth();
        let inspirations = {};
        let fixedExpenses = [];
        let fixedOccurrences = {};
        selectedYearInput.value = selectedYear;

        const defaultCategories = [
            @json(__('portfolio.expenses_example_category_food')),
            @json(__('portfolio.expenses_example_category_transport')),
            @json('🏠 ' . __('portfolio.expenses_category_home')),
            @json('💊 ' . __('portfolio.expenses_category_health')),
        ];
        let savedCategories = [];
        try {
            savedCategories = JSON.parse(localStorage.getItem(categoryStorageKey) || '[]');
        } catch (error) {
            localStorage.removeItem(categoryStorageKey);
        }
        const categories = [...new Set([...defaultCategories, ...savedCategories])];
        const defaultExpenses = [
            { id: crypto.randomUUID(), date: '2026-08-01', category: @json(__('portfolio.expenses_example_category_food')), detail: @json(__('portfolio.expenses_example_detail_food')), paymentType: @json(__('portfolio.expenses_payment_card')), amount: 12500, notes: @json(__('portfolio.expenses_example_notes_food')) },
            { id: crypto.randomUUID(), date: '2026-08-03', category: @json(__('portfolio.expenses_example_category_transport')), detail: @json(__('portfolio.expenses_example_detail_transport')), paymentType: @json(__('portfolio.expenses_payment_cash')), amount: 3200, notes: @json(__('portfolio.expenses_example_notes_transport')) },
        ];
        let expenses = [];
        let incomes = {};
        let extraIncomes = [];
        try {
            expenses = JSON.parse(localStorage.getItem(expenseStorageKey) || 'null') || defaultExpenses;
            incomes = JSON.parse(localStorage.getItem(incomeStorageKey) || '{}');
        } catch (error) {
            localStorage.removeItem(expenseStorageKey);
            localStorage.removeItem(incomeStorageKey);
            expenses = defaultExpenses;
        }
        if (incomes['2026-08'] === undefined && expenses.some((expense) => expense.date.startsWith('2026-08'))) {
            incomes['2026-08'] = 50000;
        }
        localStorage.setItem(expenseStorageKey, JSON.stringify(expenses));

        async function apiRequest(url, options = {}) {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    ...options.headers,
                },
            });

            if (!response.ok) throw new Error(`Request failed: ${response.status}`);
            return response.status === 204 ? null : response.json();
        }

        function mapExpense(expense) {
            return {
                id: String(expense.id),
                date: expense.date,
                category: expense.category,
                detail: expense.detail || '',
                paymentType: expense.payment_type,
                amount: Number(expense.amount),
                notes: expense.notes || '',
            };
        }

        async function loadDatabaseState() {
            try {
                const data = await apiRequest('{{ route('expenses.data') }}');
                if (data.expenses.length) {
                    expenses = data.expenses.map(mapExpense);
                } else if (expenses.length) {
                    const migratedExpenses = [];
                    for (const expense of expenses) {
                        const savedExpense = await apiRequest('{{ route('expenses.store') }}', {
                            method: 'POST',
                            body: JSON.stringify({
                                date: expense.date,
                                category: expense.category,
                                detail: expense.detail,
                                payment_type: expense.paymentType,
                                notes: expense.notes,
                                amount: expense.amount,
                            }),
                        });
                        migratedExpenses.push(mapExpense(savedExpense));
                    }
                    expenses = migratedExpenses;
                }
                incomes = Object.fromEntries(Object.entries(data.incomes));
                extraIncomes = data.extra_incomes || [];
                inspirations = Object.fromEntries(Object.entries(data.inspirations || {}).map(([key, inspiration]) => [
                    key,
                    { ...inspiration, url: new URL(inspiration.url, window.location.origin).href },
                ]));
                fixedExpenses = data.fixed_expenses || [];
                fixedOccurrences = data.fixed_occurrences || {};
                if (!Object.keys(incomes).length) {
                    for (const [key, amount] of Object.entries(JSON.parse(localStorage.getItem(incomeStorageKey) || '{}'))) {
                        const [year, month] = key.split('-').map(Number);
                        if (!year || !month) continue;
                        await apiRequest('{{ route('expenses.income') }}', {
                            method: 'PUT',
                            body: JSON.stringify({ year, month, amount }),
                        });
                        incomes[key] = amount;
                    }
                }
                data.categories.forEach((category) => {
                    categoryRecords[category.name] = category;
                    if (!categories.includes(category.name)) categories.push(category.name);
                    chartColors[category.name] = category.color;
                });
                if (!data.categories.length) {
                    for (const category of defaultCategories) {
                        const savedCategory = await apiRequest('{{ route('expenses.category') }}', {
                            method: 'POST',
                            body: JSON.stringify({ name: category }),
                        });
                        categoryRecords[category] = savedCategory;
                        chartColors[category] = savedCategory.color;
                    }
                }
                if (!data.categories.length) {
                    for (const category of categories.filter((item) => !defaultCategories.includes(item))) {
                        const savedCategory = await apiRequest('{{ route('expenses.category') }}', {
                            method: 'POST',
                            body: JSON.stringify({ name: category, color: chartColors[category] }),
                        });
                        categoryRecords[category] = savedCategory;
                        chartColors[category] = savedCategory.color;
                    }
                }
                renderPeriod();
            } catch (error) {
                console.error('Could not load expenses from the database.', error);
            }
        }
        let chartColors = {};
        try {
            chartColors = JSON.parse(localStorage.getItem(chartColorStorageKey) || '{}');
        } catch (error) {
            localStorage.removeItem(chartColorStorageKey);
        }

        function periodKey(year = selectedYear, month = selectedMonth) {
            return `${year}-${String(month + 1).padStart(2, '0')}`;
        }

        function renderMonthTabs() {
            monthTabs.replaceChildren();
            monthNames.forEach((monthName, month) => {
                const tab = document.createElement('button');
                tab.type = 'button';
                tab.className = `expense-month-tab${month === selectedMonth ? ' active' : ''}`;
                tab.textContent = monthName;
                tab.setAttribute('role', 'tab');
                tab.setAttribute('aria-selected', month === selectedMonth);
                tab.addEventListener('click', () => {
                    selectedMonth = month;
                    renderPeriod();
                });
                monthTabs.appendChild(tab);
            });
        }

        function renderPeriod() {
            selectedYear = Number.parseInt(selectedYearInput.value, 10) || 2026;
            selectedYearInput.value = selectedYear;
            monthlyIncome.value = Number(incomes[periodKey()] || 0) > 0 ? incomes[periodKey()] : '';
            renderMonthTabs();
            renderExpenseRows();
            renderExtraIncomes();
            updateExpenseTotal();
            renderAnnualSummary();
            renderInspiration();
        }

        function updateIncomeGate() {
            const blocked = !hasMonthlyIncome();
            incomeGate.hidden = !blocked;
            if (blocked) {
                incomeGateInput.value = '';
                requestAnimationFrame(() => incomeGateInput.focus());
            }
        }

        function renderInspiration() {
            const inspiration = inspirations[periodKey()];
            inspirationImage.hidden = !inspiration;
            inspirationEmpty.hidden = Boolean(inspiration);
            deleteInspiration.hidden = !inspiration;
            if (inspiration) inspirationImage.src = inspiration.url;
            inspirationUrl.value = inspiration?.original_name?.startsWith('http') ? inspiration.original_name : '';
            inspirationStatus.hidden = true;
        }

        async function saveInspiration(formData) {
            inspirationStatus.hidden = true;
            const response = await fetch('{{ route('expenses.inspiration.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            if (!response.ok) throw new Error(`Request failed: ${response.status}`);
            inspirations[periodKey()] = await response.json();
            renderInspiration();
        }

        function extrasForPeriod() {
            return extraIncomes.filter((item) => item.year === selectedYear && item.month === selectedMonth + 1);
        }

        function extraIncomeSum(items = extrasForPeriod()) {
            return items.reduce((sum, item) => sum + Number(item.amount || 0), 0);
        }

        function monthIncomeTotal() {
            return (Number.parseFloat(monthlyIncome.value || 0) || 0) + extraIncomeSum();
        }

        function renderExtraIncomes() {
            extraIncomesList.replaceChildren();
            const items = extrasForPeriod();
            extraIncomeTotal.textContent = extraIncomeSum(items).toFixed(2);
            if (!items.length) {
                const empty = document.createElement('li');
                empty.className = 'expense-extra-empty';
                empty.textContent = extraIncomeEmptyLabel;
                extraIncomesList.appendChild(empty);
                return;
            }
            items.forEach((item) => {
                const row = document.createElement('li');
                row.className = 'expense-extra-item';
                const label = document.createElement('span');
                label.textContent = `${item.detail} · ${Number(item.amount).toFixed(2)}`;
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'expense-delete';
                remove.textContent = extraIncomeDeleteLabel;
                remove.addEventListener('click', () => deleteExtraIncome(item.id));
                row.append(label, remove);
                extraIncomesList.appendChild(row);
            });
        }

        async function deleteExtraIncome(id) {
            try {
                await apiRequest(`/calculadora-gastos/ingresos-extra/${id}`, { method: 'DELETE' });
                extraIncomes = extraIncomes.filter((item) => item.id !== id);
                renderExtraIncomes();
                updateExpenseTotal();
                renderAnnualSummary();
            } catch (error) {
                console.error('Could not delete extra income.', error);
            }
        }

        function hasMonthlyIncome() {
            return monthlyIncome.value.trim() !== '' && Number.parseFloat(monthlyIncome.value) > 0;
        }

        function requireMonthlyIncome() {
            if (hasMonthlyIncome()) {
                incomeRequiredMessage.hidden = true;
                return true;
            }

            incomeRequiredMessage.hidden = false;
            incomeGate.hidden = false;
            incomeGateInput.value = '';
            requestAnimationFrame(() => incomeGateInput.focus());
            return false;
        }

        inspirationInput.addEventListener('change', async () => {
            const file = inspirationInput.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                inspirationStatus.textContent = @json(__('portfolio.expenses_inspiration_error'));
                inspirationStatus.hidden = false;
                inspirationInput.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('year', selectedYear);
            formData.append('month', selectedMonth + 1);
            formData.append('image', file);
            inspirationStatus.hidden = true;

            try {
                await saveInspiration(formData);
                inspirationInput.value = '';
            } catch (error) {
                inspirationStatus.textContent = @json(__('portfolio.expenses_inspiration_error'));
                inspirationStatus.hidden = false;
                console.error('Could not save monthly inspiration.', error);
            }
        });

        saveInspirationUrl.addEventListener('click', async () => {
            const url = inspirationUrl.value.trim();
            if (!url) return;
            const formData = new FormData();
            formData.append('year', selectedYear);
            formData.append('month', selectedMonth + 1);
            formData.append('url', url);
            try {
                await saveInspiration(formData);
                inspirationUrl.value = '';
            } catch (error) {
                inspirationStatus.textContent = @json(__('portfolio.expenses_inspiration_error'));
                inspirationStatus.hidden = false;
                console.error('Could not save monthly inspiration URL.', error);
            }
        });

        deleteInspiration.addEventListener('click', async () => {
            try {
                await apiRequest('{{ route('expenses.inspiration.destroy') }}', {
                    method: 'DELETE',
                    body: JSON.stringify({ year: selectedYear, month: selectedMonth + 1 }),
                });
                delete inspirations[periodKey()];
                renderInspiration();
            } catch (error) {
                inspirationStatus.textContent = @json(__('portfolio.expenses_inspiration_error'));
                inspirationStatus.hidden = false;
                console.error('Could not delete monthly inspiration.', error);
            }
        });

        function renderExpenseRows() {
            expenseList.replaceChildren();
            const occurrenceKey = (fixedId) => `${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-${fixedId}`;
            const fixedRows = fixedExpenses
                .filter((fixed) => {
                    const currentPeriod = periodKey();
                    const startPeriod = fixed.starts_at?.slice(0, 7);
                    const endPeriod = fixed.ends_at?.slice(0, 7);
                    return (!startPeriod || currentPeriod >= startPeriod) && (!endPeriod || currentPeriod < endPeriod);
                })
                .filter((fixed) => !fixedOccurrences[occurrenceKey(fixed.id)]?.excluded)
                .map((fixed) => ({
                    ...fixed,
                    isFixed: true,
                    occurrence: fixedOccurrences[occurrenceKey(fixed.id)] || {},
                    status: fixedOccurrences[occurrenceKey(fixed.id)]?.status || 'unpaid',
                }));
            const regularRows = expenses
                .filter((expense) => expense.date.startsWith(periodKey()))
                .map((expense) => ({ ...expense, isFixed: false }));

            [...fixedRows, ...regularRows].forEach((expense) => {
                    const row = document.createElement('tr');
                    row.dataset.id = expense.id;
                    row.dataset.fixed = expense.isFixed ? 'true' : 'false';
                    if (expense.isFixed) row.classList.add(expense.status === 'paid' ? 'expense-fixed-paid' : 'expense-fixed-unpaid');
                    if (multiSelectActive) {
                        const selectCell = document.createElement('td');
                        if (!expense.isFixed) {
                            const select = document.createElement('input');
                            select.type = 'checkbox';
                            select.className = 'expense-select';
                            select.dataset.id = expense.id;
                            select.title = @json(__('portfolio.expenses_delete_selected'));
                            select.addEventListener('change', updateBulkDeleteButton);
                            selectCell.appendChild(select);
                        }
                        row.appendChild(selectCell);
                    }
                    const effectiveAmount = expense.isFixed && expense.occurrence.amount !== null && expense.occurrence.amount !== undefined
                        ? expense.occurrence.amount
                        : expense.amount;
                    const effectiveDetail = expense.isFixed ? (expense.occurrence.detail || expense.detail) : expense.detail;
                    const effectivePayment = expense.isFixed ? (expense.occurrence.payment_type || expense.payment_type) : (expense.payment_type || expense.paymentType);
                    const effectiveNotes = expense.isFixed ? (expense.occurrence.notes || expense.notes) : expense.notes;
                    [expense.isFixed ? '🔁' : expense.date, expense.category || undefinedCategoryLabel, effectiveDetail || '-', effectivePayment || '-',].forEach((value) => {
                        const cell = document.createElement('td');
                        cell.textContent = value;
                        row.appendChild(cell);
                    });
                    const amountCell = document.createElement('td');
                    amountCell.className = 'expense-amount';
                    amountCell.textContent = effectiveAmount === null || effectiveAmount === undefined ? '-' : Number(effectiveAmount).toFixed(2);
                    row.appendChild(amountCell);
                    const notesCell = document.createElement('td');
                    notesCell.textContent = effectiveNotes || '-';
                    row.appendChild(notesCell);
                    const statusCell = document.createElement('td');
                    if (expense.isFixed) {
                        const status = document.createElement('select');
                        status.className = 'fixed-status-select';
                        status.add(new Option(unpaidLabel, 'unpaid'));
                        status.add(new Option(paidLabel, 'paid'));
                        status.value = expense.status;
                        status.addEventListener('change', () => updateFixedStatus(expense.id, status.value));
                        statusCell.appendChild(status);
                    } else {
                        statusCell.textContent = '-';
                    }
                    row.appendChild(statusCell);
                    const actionCell = document.createElement('td');
                    if (expense.isFixed) {
                        const editButton = document.createElement('button');
                        editButton.type = 'button';
                        editButton.className = 'expense-edit';
                        editButton.textContent = '✎';
                        editButton.title = editLabel;
                        editButton.addEventListener('click', () => startEditingFixed(row, expense, effectiveAmount, effectiveDetail, effectivePayment, effectiveNotes));
                        actionCell.appendChild(editButton);
                        const removeMonthButton = document.createElement('button');
                        removeMonthButton.type = 'button';
                        removeMonthButton.className = 'expense-fixed-remove';
                        removeMonthButton.textContent = '🗑';
                        removeMonthButton.title = removeFixedLabel;
                        removeMonthButton.addEventListener('click', () => openDeleteDialog('fixed', expense.id));
                        const removeFutureButton = document.createElement('button');
                        removeFutureButton.type = 'button';
                        actionCell.appendChild(removeMonthButton);
                    } else {
                        const editButton = document.createElement('button');
                        editButton.type = 'button';
                        editButton.className = 'expense-edit';
                        editButton.textContent = '✎';
                        editButton.title = editLabel;
                        editButton.addEventListener('click', () => startEditingExpense(row, expense));
                        actionCell.appendChild(editButton);
                    }
                    const deleteButton = document.createElement('button');
                    if (!expense.isFixed) {
                        deleteButton.type = 'button';
                        deleteButton.className = 'expense-delete';
                        deleteButton.dataset.id = expense.id;
                        deleteButton.textContent = '🗑';
                        deleteButton.title = deleteLabel;
                        deleteButton.addEventListener('click', () => openDeleteDialog('expense', expense.id));
                        actionCell.appendChild(deleteButton);
                    }
                    row.appendChild(actionCell);
                    expenseList.appendChild(row);
                });
            updateBulkDeleteButton();
        }

        function updateBulkDeleteButton() {
            const count = document.querySelectorAll('.expense-select:checked').length;
            deleteSelectedExpenses.hidden = count === 0;
        }

        function setMultiSelectMode(enabled) {
            multiSelectActive = enabled;
            expenseSelectHeader.hidden = !enabled;
            expenseTableTotalLabel.colSpan = enabled ? 5 : 4;
            toggleMultiSelect.textContent = enabled ? multiSelectActiveLabel : multiSelectLabel;
            if (!enabled) {
                document.querySelectorAll('.expense-select:checked').forEach((input) => {
                    input.checked = false;
                });
            }
            updateBulkDeleteButton();
            renderPeriod();
        }

        function formatCommentDate(createdAt) {
            const date = new Date(createdAt);
            return new Intl.DateTimeFormat(document.documentElement.lang, {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            }).format(date);
        }

        function updateCommentsBadge() {
            if (!commentsBadge) return;
            const unread = comments.filter((comment) => !comment.is_read).length;
            commentsBadge.hidden = unread === 0;
            commentsBadge.textContent = unread;
        }

        function renderComments() {
            if (!commentsList) return;
            commentsList.replaceChildren();
            if (!comments.length) {
                const empty = document.createElement('p');
                empty.className = 'expense-comments-empty';
                empty.textContent = commentsEmptyLabel;
                commentsList.appendChild(empty);
                return;
            }
            comments.forEach((comment) => {
                const item = document.createElement('div');
                item.className = `expense-comment${comment.is_read ? '' : ' is-unread'}`;
                item.dataset.id = comment.id;

                const header = document.createElement('div');
                header.className = 'expense-comment-header';
                const who = document.createElement('strong');
                who.textContent = comment.name || anonymousLabel;
                const when = document.createElement('time');
                when.textContent = formatCommentDate(comment.created_at);
                header.append(who, when);

                const body = document.createElement('p');
                body.textContent = comment.message;

                const actions = document.createElement('div');
                actions.className = 'expense-comment-actions';
                if (comment.is_read) {
                    const tag = document.createElement('small');
                    tag.textContent = readLabel;
                    actions.appendChild(tag);
                } else {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'expense-comment-read';
                    button.textContent = markReadLabel;
                    button.addEventListener('click', () => markCommentRead(comment.id));
                    actions.appendChild(button);
                }

                item.append(header, body, actions);
                commentsList.appendChild(item);
            });
        }

        async function loadComments() {
            if (!commentsList) return;
            try {
                const data = await apiRequest('{{ route('expenses.comments.index') }}');
                comments = data.comments;
                renderComments();
                updateCommentsBadge();
            } catch (error) {
                console.error('Could not load comments.', error);
            }
        }

        async function markCommentRead(id) {
            try {
                await apiRequest(`/calculadora-gastos/comentarios/${id}/leido`, { method: 'PATCH' });
                const comment = comments.find((item) => item.id === id);
                if (comment) comment.is_read = true;
                renderComments();
                updateCommentsBadge();
            } catch (error) {
                console.error('Could not mark comment as read.', error);
            }
        }

        async function updateFixedStatus(id, status) {
            if (!requireMonthlyIncome()) return;
            const key = `${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-${id}`;
            const current = fixedOccurrences[key] || {};
            const response = await apiRequest(`/calculadora-gastos/gastos-fijos/${id}/estado`, {
                method: 'PATCH',
                body: JSON.stringify({
                    year: selectedYear,
                    month: selectedMonth + 1,
                    status,
                    amount: current.amount ?? null,
                    detail: current.detail ?? null,
                    payment_type: current.payment_type ?? null,
                    notes: current.notes ?? null,
                }),
            });
            fixedOccurrences[key] = response;
            renderPeriod();
        }

        function startEditingFixed(row, fixed, amount, detail, paymentType, notes) {
            row.replaceChildren();
            const values = [
                ['text', '🔁', true],
                ['text', fixed.category || undefinedCategoryLabel, true],
                ['text', detail || '', false],
                ['select', paymentType || '', false],
                ['number', amount ?? '', false],
                ['text', notes || '', false],
            ];
            const controls = [];
            values.forEach(([type, value, locked]) => {
                const cell = document.createElement('td');
                const control = type === 'select' ? document.createElement('select') : document.createElement('input');
                if (type !== 'select') control.type = type;
                control.value = value;
                control.disabled = locked;
                if (type === 'number') {
                    control.min = '0';
                    control.step = '0.01';
                }
                if (type === 'select') {
                    paymentOptions.forEach((option) => control.add(new Option(option, option)));
                    control.value = value;
                }
                cell.appendChild(control);
                row.appendChild(cell);
                controls.push(control);
            });
            const statusCell = document.createElement('td');
            const status = document.createElement('select');
            status.add(new Option(unpaidLabel, 'unpaid'));
            status.add(new Option(paidLabel, 'paid'));
            status.value = fixed.status;
            statusCell.appendChild(status);
            row.appendChild(statusCell);
            const actionCell = document.createElement('td');
            const saveButton = document.createElement('button');
            saveButton.type = 'button';
            saveButton.className = 'expense-save';
            saveButton.textContent = saveLabel;
            saveButton.addEventListener('click', () => saveFixedMonth(fixed, controls, status.value));
            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'expense-cancel';
            cancelButton.textContent = cancelLabel;
            cancelButton.addEventListener('click', renderPeriod);
            actionCell.append(saveButton, cancelButton);
            row.appendChild(actionCell);
        }

        async function saveFixedMonth(fixed, controls, status) {
            if (!requireMonthlyIncome()) return;
            const response = await apiRequest(`/calculadora-gastos/gastos-fijos/${fixed.id}/estado`, {
                method: 'PATCH',
                body: JSON.stringify({
                    year: selectedYear,
                    month: selectedMonth + 1,
                    status,
                    detail: controls[2].value.trim() || null,
                    payment_type: controls[3].value || null,
                    amount: controls[4].value === '' ? null : controls[4].value,
                    notes: controls[5].value.trim() || null,
                }),
            });
            fixedOccurrences[`${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-${fixed.id}`] = response;
            renderPeriod();
        }

        async function excludeFixedExpense(id, scope) {
            if (!requireMonthlyIncome()) return;
            await apiRequest(`/calculadora-gastos/gastos-fijos/${id}/mes`, {
                method: 'DELETE',
            body: JSON.stringify({ year: selectedYear, month: selectedMonth + 1, scope }),
            });
            if (scope === 'future') {
                const fixed = fixedExpenses.find((item) => item.id === id);
                if (fixed) fixed.ends_at = `${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-01`;
            }
            fixedOccurrences[`${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-${id}`] = { status: 'unpaid', excluded: true };
            renderPeriod();
        }
        function openDeleteDialog(type, id) {
            if (!requireMonthlyIncome()) return;
            pendingDeletion = type === 'bulk'
                ? { type, ids: [...document.querySelectorAll('.expense-select:checked')].map((input) => input.dataset.id) }
                : { type, id };
            fixedDeleteOptions.hidden = type !== 'fixed';
            confirmDeleteExpense.hidden = type === 'fixed';
            pendingDeleteMessage.textContent = type === 'fixed'
                ? @json(__('portfolio.expenses_delete_fixed_message'))
                : type === 'bulk'
                    ? @json(__('portfolio.expenses_delete_selected_message'))
                : @json(__('portfolio.expenses_delete_message'));
            deleteExpenseModal.hidden = false;
        }

        async function deleteRegularExpense(id) {
            await apiRequest(`/calculadora-gastos/${id}`, { method: 'DELETE' });
            expenses = expenses.filter((expense) => expense.id !== id);
            renderPeriod();
        }

        document.getElementById('confirm-delete-expense').addEventListener('click', async () => {
            if (!pendingDeletion) return;
            try {
                if (pendingDeletion.type === 'bulk') {
                    for (const id of pendingDeletion.ids) await deleteRegularExpense(id);
                } else {
                    await deleteRegularExpense(pendingDeletion.id);
                }
                deleteExpenseModal.hidden = true;
                pendingDeletion = null;
            } catch (error) {
                console.error('Could not delete expense.', error);
            }
        });
        deleteSelectedExpenses.addEventListener('click', () => openDeleteDialog('bulk'));
        document.getElementById('delete-fixed-month').addEventListener('click', async () => {
            if (!pendingDeletion) return;
            await excludeFixedExpense(pendingDeletion.id, 'month');
            deleteExpenseModal.hidden = true;
            pendingDeletion = null;
        });
        document.getElementById('delete-fixed-future').addEventListener('click', async () => {
            if (!pendingDeletion) return;
            await excludeFixedExpense(pendingDeletion.id, 'future');
            deleteExpenseModal.hidden = true;
            pendingDeletion = null;
        });
        document.getElementById('cancel-delete-expense').addEventListener('click', () => {
            deleteExpenseModal.hidden = true;
            pendingDeletion = null;
        });
        deleteExpenseModal.addEventListener('click', (event) => {
            if (event.target === deleteExpenseModal) {
                deleteExpenseModal.hidden = true;
                pendingDeletion = null;
            }
        });

        function createCategoryEditor(value) {
            const picker = document.createElement('div');
            picker.className = 'expense-category-picker expense-inline-category-picker';
            const input = document.createElement('input');
            input.type = 'text';
            input.value = value;
            input.autocomplete = 'off';
            const menu = document.createElement('div');
            menu.className = 'expense-category-menu';
            menu.hidden = true;
            const options = document.createElement('div');
            options.className = 'expense-category-options';
            const plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'expense-category-plus';
            plus.textContent = '+';
            const addForm = document.createElement('div');
            addForm.className = 'expense-category-add';
            addForm.hidden = true;
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.maxLength = 40;
            newInput.placeholder = @json(__('portfolio.expenses_new_category_placeholder'));
            const addButton = document.createElement('button');
            addButton.type = 'button';
            addButton.className = 'btn-accent expense-category-button';
            addButton.textContent = @json(__('portfolio.expenses_add_category'));
            addForm.append(newInput, addButton);
            menu.append(options, plus, addForm);
            picker.append(input, menu);

            function renderOptions(searchValue = input.value) {
                const search = searchValue.trim().toLowerCase();
                options.replaceChildren();
                categoriesSorted().filter((category) => categorySortKey(category).includes(categorySortKey(search))).forEach((category) => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'expense-category-option expense-category-option-button';
                    option.textContent = category;
                    option.addEventListener('click', () => {
                        input.value = category;
                        menu.hidden = true;
                    });
                    options.appendChild(option);
                });
            }

            input.addEventListener('focus', () => {
                menu.hidden = false;
                renderOptions('');
            });
            input.addEventListener('input', () => {
                menu.hidden = false;
                renderOptions();
            });
            plus.addEventListener('click', () => {
                addForm.hidden = false;
                newInput.focus();
            });
            addButton.addEventListener('click', async () => {
                const category = newInput.value.trim();
                if (!category) return;
                try {
                    await persistCategory(category);
                    input.value = category;
                    newInput.value = '';
                    addForm.hidden = true;
                    menu.hidden = true;
                } catch (error) {
                    console.error('Could not save category.', error);
                }
            });

            return { picker, input };
        }

        function startEditingExpense(row, expense) {
            row.classList.add('is-editing');
            row.replaceChildren();
            const fields = [
                { type: 'date', value: expense.date },
                { type: 'text', value: expense.category || '' },
                { type: 'text', value: expense.detail || '' },
                { type: 'select', value: expense.paymentType },
                { type: 'number', value: expense.amount, step: '0.01', min: '0' },
                { type: 'text', value: expense.notes || '' },
            ];
            const controls = [];

            fields.forEach((field, index) => {
                const cell = document.createElement('td');
                if (index === 1) {
                    const categoryEditor = createCategoryEditor(field.value);
                    cell.appendChild(categoryEditor.picker);
                    controls.push(categoryEditor.input);
                    row.appendChild(cell);
                    return;
                }
                const control = field.type === 'select' ? document.createElement('select') : document.createElement('input');
                if (field.type !== 'select') control.type = field.type;
                control.value = field.value;
                if (field.step) control.step = field.step;
                if (field.min) control.min = field.min;
                if (field.type === 'select') {
                    paymentOptions.forEach((option) => control.add(new Option(option, option)));
                    control.value = field.value;
                }
                cell.appendChild(control);
                row.appendChild(cell);
                controls.push(control);
            });

            const actionCell = document.createElement('td');
            const saveButton = document.createElement('button');
            saveButton.type = 'button';
            saveButton.className = 'expense-save';
            saveButton.textContent = saveLabel;
            saveButton.addEventListener('click', () => saveEditedExpense(expense, controls));
            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'expense-cancel';
            cancelButton.textContent = cancelLabel;
            cancelButton.addEventListener('click', renderPeriod);
            controls.forEach((control) => control.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    saveButton.click();
                }
            }));
            actionCell.append(saveButton, cancelButton);
            row.appendChild(actionCell);
        }

        async function saveEditedExpense(expense, controls) {
            if (!requireMonthlyIncome()) return;

            try {
                const savedExpense = await apiRequest(`/calculadora-gastos/${expense.id}`, {
                    method: 'PATCH',
                    body: JSON.stringify({
                        date: controls[0].value,
                        category: controls[1].value.trim() || null,
                        detail: controls[2].value.trim() || null,
                        payment_type: controls[3].value,
                        amount: controls[4].value,
                        notes: controls[5].value.trim() || null,
                    }),
                });
                const index = expenses.findIndex((item) => item.id === expense.id);
                expenses[index] = mapExpense(savedExpense);
                renderPeriod();
            } catch (error) {
                console.error('Could not update expense.', error);
            }
        }

        function renderAnnualSummary() {
            const yearExpenses = expenses.filter((expense) => expense.date.startsWith(`${selectedYear}-`));
            const spent = yearExpenses.reduce((sum, expense) => sum + Number(expense.amount), 0);
            const salary = Object.entries(incomes)
                .filter(([key]) => key.startsWith(`${selectedYear}-`))
                .reduce((sum, [, value]) => sum + Number(value || 0), 0);
            const extras = extraIncomes
                .filter((item) => item.year === selectedYear)
                .reduce((sum, item) => sum + Number(item.amount || 0), 0);
            const income = salary + extras;
            annualIncome.textContent = income.toFixed(2);
            annualSpent.textContent = spent.toFixed(2);
            annualRemaining.textContent = (income - spent).toFixed(2);
        }

        function setToday() {
            const today = new Date();
            const localDate = new Date(today.getTime() - today.getTimezoneOffset() * 60000)
                .toISOString()
                .split('T')[0];
            expenseDate.value = localDate;
        }

        function renderCategories() {
            const search = categoryInput.value.trim().toLowerCase();
            categoryOptions.replaceChildren();
            categoriesSorted()
                .filter((category) => categorySortKey(category).includes(categorySortKey(search)))
                .forEach((category) => {
                    const option = document.createElement('div');
                    option.className = 'expense-category-option';
                    const selectButton = document.createElement('button');
                    selectButton.type = 'button';
                    selectButton.textContent = category;
                    selectButton.addEventListener('click', () => {
                        categoryInput.value = category;
                        categoryMenu.hidden = true;
                    });
                    option.appendChild(selectButton);
                    categoryOptions.appendChild(option);
                });
        }

        function renderCategoryManager() {
            categoryManagerList.replaceChildren();
            const managedCategories = [...new Set(categories)];

            if (!managedCategories.length) {
                const emptyMessage = document.createElement('p');
                emptyMessage.textContent = @json(__('portfolio.expenses_no_custom_categories'));
                categoryManagerList.appendChild(emptyMessage);
                return;
            }

            managedCategories.forEach((category) => {
                const item = document.createElement('div');
                item.className = 'category-manager-item';
                const name = document.createElement('span');
                name.textContent = category;
                const actions = document.createElement('div');
                actions.className = 'category-manager-actions';
                if (categoryRecords[category]) {
                    const editButton = document.createElement('button');
                    editButton.type = 'button';
                    editButton.className = 'expense-edit-category';
                    editButton.textContent = editLabel;
                    editButton.addEventListener('click', () => startEditingCategory(item, category));
                    const deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.className = 'expense-delete-category';
                    deleteButton.textContent = deleteCategoryLabel;
                    deleteButton.addEventListener('click', () => deleteCategory(category));
                    actions.append(editButton, deleteButton);
                } else {
                    const protectedLabel = document.createElement('small');
                    protectedLabel.textContent = @json(__('portfolio.expenses_category_protected'));
                    actions.appendChild(protectedLabel);
                }
                item.append(name, actions);
                categoryManagerList.appendChild(item);
            });
        }

        function startEditingCategory(item, category) {
            const record = categoryRecords[category];
            const input = document.createElement('input');
            input.type = 'text';
            input.value = category;
            input.maxLength = 100;
            input.className = 'category-manager-input';
            const saveButton = document.createElement('button');
            saveButton.type = 'button';
            saveButton.className = 'expense-save';
            saveButton.textContent = saveLabel;
            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'expense-cancel';
            cancelButton.textContent = cancelLabel;
            item.replaceChildren(input, saveButton, cancelButton);
            input.focus();
            const save = () => renameCategory(record, category, input.value.trim());
            saveButton.addEventListener('click', save);
            cancelButton.addEventListener('click', renderCategoryManager);
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') save();
                if (event.key === 'Escape') renderCategoryManager();
            });
        }

        async function renameCategory(record, oldName, newName) {
            if (!newName || newName === oldName) return renderCategoryManager();
            if (categories.includes(newName)) return;

            try {
                const savedCategory = await apiRequest(`/calculadora-gastos/categorias/${record.id}`, {
                    method: 'PUT',
                    body: JSON.stringify({ name: newName }),
                });
                const categoryIndex = categories.indexOf(oldName);
                categories[categoryIndex] = newName;
                categoryRecords[newName] = savedCategory;
                delete categoryRecords[oldName];
                chartColors[newName] = chartColors[oldName];
                delete chartColors[oldName];
                expenses = expenses.map((expense) => expense.category === oldName ? { ...expense, category: newName } : expense);
                if (categoryInput.value === oldName) categoryInput.value = newName;
                renderCategories();
                renderCategoryManager();
                renderPeriod();
            } catch (error) {
                console.error('Could not rename category.', error);
            }
        }

        async function deleteCategory(category) {
            const record = categoryRecords[category];
            if (!record) return;
            if (!window.confirm(@json(__('portfolio.expenses_delete_category_confirm')))) return;

            try {
                await apiRequest(`/calculadora-gastos/categorias/${record.id}`, { method: 'DELETE' });
                expenses = expenses.map((expense) => expense.category === category ? { ...expense, category: null } : expense);
                delete categoryRecords[category];
                delete chartColors[category];
                categories.splice(categories.indexOf(category), 1);
                categoryMenu.hidden = true;
                renderCategoryManager();
                renderPeriod();
            } catch (error) {
                console.error('Could not delete category.', error);
            }
        }

        async function persistCategory(category) {
            const duplicate = categories.find((existing) => categorySortKey(existing) === categorySortKey(category));
            if (duplicate) {
                window.alert(@json(__('portfolio.expenses_category_duplicate')));
                return categoryRecords[duplicate];
            }

            const savedCategory = await apiRequest('{{ route('expenses.category') }}', {
                    method: 'POST',
                    body: JSON.stringify({ name: category }),
            });
            categoryRecords[category] = savedCategory;
            chartColors[category] = savedCategory.color;
            categories.push(category);
            renderCategoryManager();
            return savedCategory;
        }

        async function addCategory() {
            const category = newCategoryInput.value.trim();
            if (!category) return;

            try {
                await persistCategory(category);
                categoryInput.value = category;
                newCategoryInput.value = '';
                categoryAddForm.hidden = true;
                categoryMenu.hidden = true;
            } catch (error) {
                console.error('Could not save category.', error);
            }
        }

        function updateExpenseTotal() {
            const total = [...document.querySelectorAll('.expense-amount')]
                .reduce((sum, amount) => {
                    const value = Number.parseFloat(amount.textContent);
                    return sum + (Number.isFinite(value) ? value : 0);
                }, 0);

            expenseTotal.textContent = total.toFixed(2);
            expenseTableTotal.textContent = total.toFixed(2);
            const income = monthIncomeTotal();
            incomeMonthTotal.textContent = income.toFixed(2);
            extraIncomeTotal.textContent = extraIncomeSum().toFixed(2);
            const remaining = income - total;
            expenseRemaining.textContent = remaining.toFixed(2);
            renderExpenseChart(total);
        }

        function randomColor() {
            return `#${Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0')}`;
        }

        function escapeHtml(value) {
            return value.replace(/[&<>'"]/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#039;',
                '"': '&quot;',
            })[character]);
        }

        function renderExpenseChart(total) {
            const groupedExpenses = {};

            expenseList.querySelectorAll('tr').forEach((row) => {
                const category = row.cells[1]?.textContent.trim();
                const amount = Number.parseFloat(row.querySelector('.expense-amount')?.textContent || 0);
                if (category) groupedExpenses[category] = (groupedExpenses[category] || 0) + amount;
            });

            expenseChartList.replaceChildren();
            const segments = [];
            let segmentStart = 0;
            Object.entries(groupedExpenses)
                .sort(([, first], [, second]) => second - first)
                .forEach(([category, amount]) => {
                    if (!chartColors[category]) chartColors[category] = randomColor();
                    const percentage = total ? (amount / total) * 100 : 0;
                    segments.push(`${chartColors[category]} ${segmentStart}% ${segmentStart + percentage}%`);
                    segmentStart += percentage;
                    const row = document.createElement('div');
                    row.className = 'expense-chart-row';
                    const safeCategory = escapeHtml(category);
                    row.innerHTML = `<div class="expense-chart-label"><span>${safeCategory}</span><span>${amount.toFixed(2)} · ${percentage.toFixed(1)}%</span></div>`;
                    expenseChartList.appendChild(row);
                });

            expensePieChart.style.background = total
                ? `conic-gradient(${segments.join(', ')})`
                : 'var(--color-bg-secondary)';
            localStorage.setItem(chartColorStorageKey, JSON.stringify(chartColors));
            expenseChartTotal.textContent = total.toFixed(2);
        }

        function stripLeadingEmoji(name) {
            return name.replace(/^[\p{Extended_Pictographic}\p{Emoji_Presentation}\uFE0F\u200D]+\s*/u, '').trim();
        }

        function monthCategoriesWithExpenses() {
            const names = new Set();
            expenseList.querySelectorAll('tr').forEach((row) => {
                const category = row.cells[1]?.textContent.trim();
                if (category) names.add(category);
            });
            return [...names].sort((first, second) => stripLeadingEmoji(first).localeCompare(
                stripLeadingEmoji(second),
                document.documentElement.lang || undefined,
                { sensitivity: 'base' },
            ));
        }

        function renderChartSettings() {
            chartSettingsList.replaceChildren();
            const visibleCategories = monthCategoriesWithExpenses();
            if (!visibleCategories.length) {
                const empty = document.createElement('p');
                empty.className = 'category-manager-warning';
                empty.textContent = chartSettingsEmptyLabel;
                chartSettingsList.appendChild(empty);
                return;
            }
            visibleCategories.forEach((category) => {
                if (!chartColors[category]) chartColors[category] = randomColor();
                const row = document.createElement('div');
                row.className = 'category-manager-item';
                const name = document.createElement('span');
                name.textContent = category;
                const color = document.createElement('input');
                color.type = 'color';
                color.value = chartColors[category];
                color.setAttribute('aria-label', `${chartColorLabel} ${category}`);
                color.addEventListener('input', () => saveCategoryColor(category, color.value));
                row.append(name, color);
                chartSettingsList.appendChild(row);
            });
        }

        async function saveCategoryColor(category, color) {
            chartColors[category] = color;
            localStorage.setItem(chartColorStorageKey, JSON.stringify(chartColors));
            try {
                const savedCategory = categoryRecords[category]
                    ? await apiRequest(`/calculadora-gastos/categorias/${categoryRecords[category].id}/color`, {
                        method: 'PATCH',
                        body: JSON.stringify({ color }),
                    })
                    : await apiRequest('{{ route('expenses.category') }}', {
                        method: 'POST',
                        body: JSON.stringify({ name: category, color }),
                    });
                categoryRecords[category] = savedCategory;
                renderExpenseChart(Number.parseFloat(expenseTotal.textContent) || 0);
            } catch (error) {
                console.error('Could not save category color.', error);
            }
        }

        document.getElementById('add-category').addEventListener('click', addCategory);
        document.getElementById('toggle-multi-select').addEventListener('click', () => {
            setMultiSelectMode(!multiSelectActive);
        });
        const fixedCategoryEditor = createCategoryEditor('');
        fixedCategoryEditor.input.name = 'category';
        fixedCategoryEditor.input.placeholder = @json(__('portfolio.expenses_category_placeholder'));
        document.getElementById('fixed-category-picker').replaceWith(fixedCategoryEditor.picker);
        document.getElementById('open-fixed-form').addEventListener('click', () => {
            fixedExpenseModal.hidden = false;
        });
        document.getElementById('close-fixed-form').addEventListener('click', () => {
            fixedExpenseModal.hidden = true;
        });
        const settingsModal = document.getElementById('settings-modal');
        const avatarModal = document.getElementById('avatar-modal');
        const avatarPreview = document.getElementById('avatar-preview');
        const avatarFallback = document.getElementById('avatar-fallback');
        const avatarInput = document.getElementById('avatar-input');
        const messagesModal = document.getElementById('messages-modal');
        document.getElementById('open-settings').addEventListener('click', () => {
            settingsModal.hidden = false;
        });
        document.getElementById('open-avatar').addEventListener('click', () => {
            avatarModal.hidden = false;
        });
        document.getElementById('close-avatar').addEventListener('click', () => {
            avatarModal.hidden = true;
        });
        avatarModal.addEventListener('click', (event) => {
            if (event.target === avatarModal) avatarModal.hidden = true;
        });
        avatarInput.addEventListener('change', () => {
            const file = avatarInput.files[0];
            if (!file) return;
            avatarPreview.src = URL.createObjectURL(file);
            avatarPreview.hidden = false;
            if (avatarFallback) avatarFallback.hidden = true;
        });
        if (messagesModal) {
            document.getElementById('open-messages').addEventListener('click', () => {
                messagesModal.hidden = false;
                loadComments();
            });
            document.getElementById('close-messages').addEventListener('click', () => {
                messagesModal.hidden = true;
            });
            messagesModal.addEventListener('click', (event) => {
                if (event.target === messagesModal) messagesModal.hidden = true;
            });
        }
        document.getElementById('close-settings').addEventListener('click', () => {
            settingsModal.hidden = true;
        });
        settingsModal.addEventListener('click', (event) => {
            if (event.target === settingsModal) settingsModal.hidden = true;
        });
        document.getElementById('open-feedback').addEventListener('click', () => {
            feedbackModal.hidden = false;
        });
        document.getElementById('close-feedback').addEventListener('click', () => {
            feedbackModal.hidden = true;
        });
        feedbackModal.addEventListener('click', (event) => {
            if (event.target === feedbackModal) feedbackModal.hidden = true;
        });
        feedbackForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const data = new FormData(feedbackForm);
            const message = data.get('message').toString().trim();
            if (!message) return;
            try {
                await apiRequest('{{ route('expenses.comments.store') }}', {
                    method: 'POST',
                    body: JSON.stringify({
                        name: data.get('name') ? data.get('name').toString().trim() : null,
                        message,
                    }),
                });
                feedbackForm.reset();
                feedbackModal.hidden = true;
                loadComments();
            } catch (error) {
                console.error('Could not send comment.', error);
            }
        });
        fixedExpenseModal.addEventListener('click', (event) => {
            if (event.target === fixedExpenseModal) fixedExpenseModal.hidden = true;
        });
        fixedExpenseForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!requireMonthlyIncome()) return;
            const data = new FormData(fixedExpenseForm);
            const fixedExpense = await apiRequest('{{ route('expenses.fixed.store') }}', {
                method: 'POST',
                body: JSON.stringify({
                    ...Object.fromEntries(data.entries()),
                    starts_at: `${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}-01`,
                }),
            });
            fixedExpenses.unshift(fixedExpense);
            fixedExpenseForm.reset();
            fixedExpenseModal.hidden = true;
            renderPeriod();
        });
        document.getElementById('chart-settings').addEventListener('click', () => {
            renderChartSettings();
            chartSettingsModal.hidden = false;
        });
        document.getElementById('close-chart-settings').addEventListener('click', () => {
            chartSettingsModal.hidden = true;
        });
        chartSettingsModal.addEventListener('click', (event) => {
            if (event.target === chartSettingsModal) chartSettingsModal.hidden = true;
        });
        document.getElementById('manage-categories').addEventListener('click', () => {
            renderCategoryManager();
            categoryManager.hidden = false;
        });
        document.getElementById('close-category-manager').addEventListener('click', () => {
            categoryManager.hidden = true;
        });
        categoryManager.addEventListener('click', (event) => {
            if (event.target === categoryManager) categoryManager.hidden = true;
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                categoryManager.hidden = true;
                chartSettingsModal.hidden = true;
                fixedExpenseModal.hidden = true;
                deleteExpenseModal.hidden = true;
                feedbackModal.hidden = true;
                pendingDeletion = null;
                document.querySelectorAll('.expense-category-menu').forEach((menu) => {
                    menu.hidden = true;
                });
            }
        });
        showCategoryForm.addEventListener('click', () => {
            categoryAddForm.hidden = false;
            newCategoryInput.focus();
        });
        categoryInput.addEventListener('focus', () => {
            categoryMenu.hidden = false;
            renderCategories();
        });
        categoryInput.addEventListener('input', () => {
            categoryMenu.hidden = false;
            renderCategories();
        });
        newCategoryInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                addCategory();
            }
        });
        monthlyIncome.addEventListener('input', async () => {
            if (!hasMonthlyIncome()) {
                incomeRequiredMessage.hidden = false;
                updateExpenseTotal();
                renderAnnualSummary();
                return;
            }
            incomes[periodKey()] = Number.parseFloat(monthlyIncome.value);
            localStorage.setItem(incomeStorageKey, JSON.stringify(incomes));
            try {
                await apiRequest('{{ route('expenses.income') }}', {
                    method: 'PUT',
                    body: JSON.stringify({ year: selectedYear, month: selectedMonth + 1, amount: incomes[periodKey()] }),
                });
            } catch (error) {
                console.error('Could not save monthly income.', error);
            }
            incomeRequiredMessage.hidden = true;
            updateExpenseTotal();
            renderAnnualSummary();
            incomeGate.hidden = true;
        });

        document.getElementById('toggle-extra-incomes').addEventListener('click', () => {
            extraIncomesModal.hidden = false;
        });
        document.getElementById('close-extra-incomes').addEventListener('click', () => {
            extraIncomesModal.hidden = true;
        });
        extraIncomesModal.addEventListener('click', (event) => {
            if (event.target === extraIncomesModal) extraIncomesModal.hidden = true;
        });

        extraIncomeForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!requireMonthlyIncome()) return;
            const detail = extraIncomeDetail.value.trim();
            const amount = Number.parseFloat(extraIncomeAmount.value);
            if (!detail || !Number.isFinite(amount) || amount <= 0) return;
            try {
                const saved = await apiRequest('{{ route('expenses.extra-income.store') }}', {
                    method: 'POST',
                    body: JSON.stringify({
                        year: selectedYear,
                        month: selectedMonth + 1,
                        detail,
                        amount,
                    }),
                });
                extraIncomes.unshift(saved);
                extraIncomeForm.reset();
                extraIncomesModal.hidden = false;
                renderExtraIncomes();
                updateExpenseTotal();
                renderAnnualSummary();
            } catch (error) {
                console.error('Could not save extra income.', error);
            }
        });

        expenseForm.addEventListener('click', (event) => {
            if (hasMonthlyIncome()) return;
            event.preventDefault();
            event.stopPropagation();
            requireMonthlyIncome();
        }, true);

        expenseForm.addEventListener('focusin', (event) => {
            if (hasMonthlyIncome()) return;
            event.target.blur();
            requireMonthlyIncome();
        });

        incomeGateSubmit.addEventListener('click', () => {
            const value = Number.parseFloat(incomeGateInput.value);
            if (!Number.isFinite(value) || value <= 0) {
                incomeGateInput.focus();
                return;
            }

            monthlyIncome.value = value;
            monthlyIncome.dispatchEvent(new Event('input', { bubbles: true }));
        });

        incomeGateInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                incomeGateSubmit.click();
            }
        });

        selectedYearInput.addEventListener('change', renderPeriod);
        document.getElementById('previous-year').addEventListener('click', () => {
            selectedYearInput.value = Number(selectedYearInput.value) - 1;
            renderPeriod();
        });
        document.getElementById('next-year').addEventListener('click', () => {
            selectedYearInput.value = Number(selectedYearInput.value) + 1;
            renderPeriod();
        });

        document.addEventListener('click', (event) => {
            const activePicker = event.target.closest('.expense-category-picker');
            document.querySelectorAll('.expense-category-menu').forEach((menu) => {
                if (!activePicker || !activePicker.contains(menu)) menu.hidden = true;
            });
        });

        expenseForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!requireMonthlyIncome()) return;

            const formData = new FormData(expenseForm);
            try {
                const savedExpense = await apiRequest('{{ route('expenses.store') }}', {
                    method: 'POST',
                    body: JSON.stringify({
                        date: formData.get('date'),
                        category: formData.get('category'),
                        detail: formData.get('detail'),
                        payment_type: formData.get('payment_type'),
                        notes: formData.get('notes'),
                        amount: formData.get('amount'),
                    }),
                });
                expenses.push(mapExpense(savedExpense));
                const savedDate = new Date(`${savedExpense.date}T00:00:00`);
                selectedYear = savedDate.getFullYear();
                selectedMonth = savedDate.getMonth();
                selectedYearInput.value = selectedYear;
                expenseForm.reset();
                setToday();
                renderPeriod();
            } catch (error) {
                console.error('Could not save expense.', error);
            }
        });

        setToday();
        renderPeriod();
        loadDatabaseState();
        if (commentsList) loadComments();
    </script>
</body>

</html>
