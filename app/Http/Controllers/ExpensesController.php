<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExtraIncome;
use App\Models\FixedExpense;
use App\Models\FixedExpenseOccurrence;
use App\Models\MonthlyIncome;
use App\Models\MonthlyInspiration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExpensesController extends Controller
{
    public function data(): JsonResponse
    {
        $userId = auth()->id();

        return response()->json([
            'expenses' => Expense::query()->where('user_id', $userId)->latest('date')->latest('id')->get(),
            'incomes' => MonthlyIncome::query()->where('user_id', $userId)->get()->mapWithKeys(
                fn (MonthlyIncome $income) => [sprintf('%d-%02d', $income->year, $income->month) => $income->amount]
            ),
            'extra_incomes' => ExtraIncome::query()->where('user_id', $userId)->latest('id')->get(),
            'categories' => ExpenseCategory::query()->where('user_id', $userId)->orderBy('name')->get(['id', 'name', 'color']),
            'inspirations' => MonthlyInspiration::query()->where('user_id', $userId)->get()->mapWithKeys(
                fn (MonthlyInspiration $inspiration) => [sprintf('%d-%02d', $inspiration->year, $inspiration->month) => [
                    'url' => $inspiration->image_url ?: asset('storage/'.$inspiration->path),
                    'original_name' => $inspiration->original_name,
                ]]
            ),
            'fixed_expenses' => FixedExpense::query()->where('user_id', $userId)->where('active', true)->get(),
            'fixed_occurrences' => FixedExpenseOccurrence::query()
                ->whereHas('fixedExpense', fn ($query) => $query->where('user_id', $userId))
                ->get()
                ->mapWithKeys(
                    fn (FixedExpenseOccurrence $occurrence) => [sprintf('%d-%02d-%d', $occurrence->year, $occurrence->month, $occurrence->fixed_expense_id) => [
                        'amount' => $occurrence->amount,
                        'detail' => $occurrence->detail,
                        'payment_type' => $occurrence->payment_type,
                        'notes' => $occurrence->notes,
                        'status' => $occurrence->status,
                        'excluded' => $occurrence->excluded,
                    ]]
                ),
        ]);
    }

    public function storeFixedExpense(Request $request): JsonResponse
    {
        $fixedExpense = FixedExpense::query()->create([
            ...$request->validate([
                'category' => ['nullable', 'string', 'max:100'],
                'detail' => ['nullable', 'string', 'max:255'],
                'payment_type' => ['required', 'string', 'max:50'],
                'notes' => ['nullable', 'string', 'max:2000'],
                'amount' => ['nullable', 'numeric', 'min:0'],
                'starts_at' => ['required', 'date'],
            ]),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($fixedExpense, 201);
    }

    public function updateFixedStatus(Request $request, FixedExpense $fixedExpense): JsonResponse
    {
        $this->ownedFixed($fixedExpense);

        $data = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'status' => ['required', 'in:paid,unpaid'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'detail' => ['nullable', 'string', 'max:255'],
            'payment_type' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $occurrence = FixedExpenseOccurrence::updateOrCreate(
            ['fixed_expense_id' => $fixedExpense->id, 'year' => $data['year'], 'month' => $data['month']],
            [
                'amount' => $data['amount'] ?? null,
                'detail' => $data['detail'] ?? null,
                'payment_type' => $data['payment_type'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
                'excluded' => false,
            ],
        );

        return response()->json($occurrence);
    }

    public function excludeFixedForMonth(Request $request, FixedExpense $fixedExpense): JsonResponse
    {
        $this->ownedFixed($fixedExpense);

        $data = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'scope' => ['required', 'in:month,future'],
        ]);

        if ($data['scope'] === 'future') {
            $fixedExpense->update([
                'ends_at' => sprintf('%d-%02d-01', $data['year'], $data['month']),
                'active' => false,
            ]);

            return response()->json(['message' => 'Fixed expense ended from this month onward.']);
        }

        FixedExpenseOccurrence::updateOrCreate(
            ['fixed_expense_id' => $fixedExpense->id, 'year' => $data['year'], 'month' => $data['month']],
            ['status' => 'unpaid', 'excluded' => true],
        );

        return response()->json(['message' => 'Fixed expense excluded for this month.']);
    }

    public function storeInspiration(Request $request): JsonResponse
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'image' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/gif', 'max:2048', 'required_without:url'],
            'url' => ['nullable', 'url:http,https', 'max:2048', 'required_without:image'],
        ]);

        $existing = MonthlyInspiration::query()
            ->where('user_id', $request->user()->id)
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->first();

        if ($existing && $existing->path) {
            Storage::disk('public')->delete($existing->path);
        }

        $path = $request->hasFile('image') ? $request->file('image')->store('monthly-inspirations', 'public') : null;
        $imageUrl = $data['url'] ?? null;
        $originalName = $request->file('image')?->getClientOriginalName() ?: $imageUrl;

        if (! $request->hasFile('image') && $this->isPinterestUrl($imageUrl)) {
            [$path, $originalName] = $this->downloadPinterestImage($imageUrl);
            $imageUrl = null;
        }

        $inspiration = MonthlyInspiration::updateOrCreate(
            ['user_id' => $request->user()->id, 'year' => $data['year'], 'month' => $data['month']],
            [
                'path' => $path,
                'image_url' => $imageUrl,
                'original_name' => $originalName,
            ],
        );

        return response()->json([
            'url' => $inspiration->image_url ?: asset('storage/'.$inspiration->path),
            'original_name' => $inspiration->original_name,
        ]);
    }

    public function destroyInspiration(Request $request): JsonResponse
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
        ]);
        $inspiration = MonthlyInspiration::query()
            ->where('user_id', $request->user()->id)
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->first();

        if ($inspiration) {
            if ($inspiration->path) {
                Storage::disk('public')->delete($inspiration->path);
            }
            $inspiration->delete();
        }

        return response()->json(['message' => 'Inspiration deleted.']);
    }

    public function store(Request $request): JsonResponse
    {
        $expense = Expense::query()->create([
            ...$request->validate([
                'date' => ['required', 'date'],
                'category' => ['nullable', 'string', 'max:100'],
                'detail' => ['nullable', 'string', 'max:255'],
                'payment_type' => ['required', 'string', 'max:50'],
                'notes' => ['nullable', 'string', 'max:2000'],
                'amount' => ['required', 'numeric', 'min:0'],
            ]),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($expense, 201);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $this->ownedExpense($expense)->delete();

        return response()->json(['message' => 'Expense deleted.']);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $this->ownedExpense($expense)->update($request->validate([
            'date' => ['required', 'date'],
            'category' => ['nullable', 'string', 'max:100'],
            'detail' => ['nullable', 'string', 'max:255'],
            'payment_type' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]));

        return response()->json($expense);
    }

    public function income(Request $request): JsonResponse
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        $income = MonthlyIncome::updateOrCreate(
            ['user_id' => $request->user()->id, 'year' => $data['year'], 'month' => $data['month']],
            ['amount' => $data['amount']],
        );

        return response()->json($income);
    }

    public function storeExtraIncome(Request $request): JsonResponse
    {
        $extraIncome = ExtraIncome::query()->create([
            ...$request->validate([
                'year' => ['required', 'integer', 'between:2000,2100'],
                'month' => ['required', 'integer', 'between:1,12'],
                'detail' => ['required', 'string', 'max:255'],
                'amount' => ['required', 'numeric', 'gt:0'],
            ]),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($extraIncome, 201);
    }

    public function destroyExtraIncome(ExtraIncome $extraIncome): JsonResponse
    {
        abort_unless($extraIncome->user_id === auth()->id(), 404);
        $extraIncome->delete();

        return response()->json(['message' => 'Extra income deleted.']);
    }

    public function category(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $category = ExpenseCategory::updateOrCreate(
            ['user_id' => $request->user()->id, 'name' => $data['name']],
            ['color' => $data['color'] ?? sprintf('#%06X', random_int(0, 0xFFFFFF))],
        );

        return response()->json($category, 201);
    }

    public function categoryColor(Request $request, ExpenseCategory $category): JsonResponse
    {
        $this->ownedCategory($category);

        $data = $request->validate([
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $category->update(['color' => $data['color']]);

        return response()->json($category);
    }

    public function updateCategory(Request $request, ExpenseCategory $category): JsonResponse
    {
        $this->ownedCategory($category);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('expense_categories', 'name')
                    ->where('user_id', $request->user()->id)
                    ->ignore($category->id),
            ],
        ]);

        Expense::query()
            ->where('user_id', $request->user()->id)
            ->where('category', $category->name)
            ->update(['category' => $data['name']]);
        $category->update(['name' => $data['name']]);

        return response()->json($category);
    }

    public function destroyCategory(ExpenseCategory $category): JsonResponse
    {
        $this->ownedCategory($category);

        Expense::query()
            ->where('user_id', auth()->id())
            ->where('category', $category->name)
            ->update(['category' => null]);
        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }

    private function ownedExpense(Expense $expense): Expense
    {
        abort_unless($expense->user_id === auth()->id(), 404);

        return $expense;
    }

    private function ownedCategory(ExpenseCategory $category): ExpenseCategory
    {
        abort_unless($category->user_id === auth()->id(), 404);

        return $category;
    }

    private function ownedFixed(FixedExpense $fixedExpense): FixedExpense
    {
        abort_unless($fixedExpense->user_id === auth()->id(), 404);

        return $fixedExpense;
    }

    private function isPinterestUrl(?string $url): bool
    {
        $host = strtolower((string) parse_url($url ?? '', PHP_URL_HOST));

        return $host === 'pinterest.com'
            || str_ends_with($host, '.pinterest.com')
            || $host === 'pin.it';
    }

    private function downloadPinterestImage(string $pinUrl): array
    {
        $page = Http::withUserAgent('Mozilla/5.0')->timeout(10)->get($pinUrl);
        if (! $page->successful()) {
            throw ValidationException::withMessages(['url' => 'No se pudo abrir el pin de Pinterest.']);
        }

        $document = new \DOMDocument;
        @$document->loadHTML($page->body());
        $imageUrl = null;

        foreach ($document->getElementsByTagName('meta') as $meta) {
            if (strtolower($meta->getAttribute('property')) === 'og:image') {
                $imageUrl = html_entity_decode($meta->getAttribute('content'));
                break;
            }
        }

        if (! $imageUrl || ! filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['url' => 'Pinterest no devolvió una imagen para este pin.']);
        }

        $image = Http::withUserAgent('Mozilla/5.0')->timeout(15)->get($imageUrl);
        $mime = $image->header('Content-Type');
        $mime = strtolower(trim(explode(';', (string) $mime)[0]));
        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
        ];

        if (! $image->successful() || ! isset($allowedMimes[$mime]) || strlen($image->body()) > 2 * 1024 * 1024) {
            throw ValidationException::withMessages(['url' => 'La imagen de Pinterest no es válida o supera los 2 MB.']);
        }

        $path = 'monthly-inspirations/'.Str::uuid().'.'.$allowedMimes[$mime];
        Storage::disk('public')->put($path, $image->body());

        return [$path, $pinUrl];
    }
}
