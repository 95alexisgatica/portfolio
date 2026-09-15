<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Expense;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_fetch_expense_data(): void
    {
        $this->getJson('/calculadora-gastos/data')->assertUnauthorized();
    }

    public function test_guest_sees_the_google_login_screen(): void
    {
        $this->get('/calculadora-gastos')
            ->assertOk()
            ->assertSee(__('portfolio.expenses_auth_google'), false);
    }

    public function test_user_cannot_see_another_users_expenses(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        $expense = Expense::query()->create([
            'user_id' => $owner->id,
            'date' => '2026-09-12',
            'category' => 'Casa',
            'detail' => 'Private',
            'payment_type' => 'cash',
            'amount' => 100,
        ]);

        $this->actingAs($stranger)
            ->getJson('/calculadora-gastos/data')
            ->assertOk()
            ->assertJsonMissing(['id' => $expense->id])
            ->assertJsonMissing(['detail' => 'Private']);
    }

    public function test_user_cannot_delete_another_users_expense(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        $expense = Expense::query()->create([
            'user_id' => $owner->id,
            'date' => '2026-09-12',
            'category' => 'Casa',
            'detail' => 'Private',
            'payment_type' => 'cash',
            'amount' => 100,
        ]);

        $this->actingAs($stranger)
            ->deleteJson('/calculadora-gastos/'.$expense->id)
            ->assertNotFound();

        $this->assertDatabaseHas('expenses', ['id' => $expense->id]);
    }

    public function test_non_admin_cannot_list_comments(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/calculadora-gastos/comentarios')
            ->assertForbidden();
    }

    public function test_admin_can_list_comments(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $author = User::factory()->create();

        Comment::query()->create([
            'user_id' => $author->id,
            'name' => $author->nickname,
            'message' => 'Please add dark mode',
        ]);

        $this->actingAs($admin)
            ->getJson('/calculadora-gastos/comentarios')
            ->assertOk()
            ->assertJsonFragment(['message' => 'Please add dark mode']);
    }

    public function test_user_without_nickname_is_blocked_from_calculator_data(): void
    {
        $user = User::factory()->withoutNickname()->create();

        $this->actingAs($user)
            ->getJson('/calculadora-gastos/data')
            ->assertForbidden();

        $this->actingAs($user)
            ->get('/calculadora-gastos')
            ->assertOk()
            ->assertSee(__('portfolio.expenses_nickname_title'), false);
    }

    public function test_seeder_creates_the_super_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'alexisgatica95@gmail.com',
            'role' => 'super_admin',
            'nickname' => 'Alexis',
        ]);
    }

    public function test_monthly_income_cannot_be_zero(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->putJson('/calculadora-gastos/ingreso', [
                'year' => 2026,
                'month' => 9,
                'amount' => 0,
            ])
            ->assertUnprocessable();
    }

    public function test_user_cannot_see_another_users_extra_income(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();

        $this->actingAs($owner)
            ->postJson('/calculadora-gastos/ingresos-extra', [
                'year' => 2026,
                'month' => 9,
                'detail' => 'Bonus',
                'amount' => 2500,
            ])
            ->assertCreated();

        $this->actingAs($stranger)
            ->getJson('/calculadora-gastos/data')
            ->assertOk()
            ->assertJsonMissing(['detail' => 'Bonus']);
    }
}
