<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\MonthlyIncome;
use Illuminate\Database\Seeder;

class ExpenseDesignSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            '🍽️ Alimentación', '🚌 Transporte', '🏠 Hogar', '💊 Salud', '🎬 Entretenimiento',
            '👕 Ropa', '📚 Educación', '🐶 Mascotas', '💡 Servicios', '🛒 Supermercado',
            '☕ Cafetería', '🏋️ Deporte', '✈️ Viajes', '🎁 Regalos', '💻 Tecnología',
            '📱 Suscripciones', '🧾 Impuestos', '💇 Cuidado personal', '🎨 Hobbies', '💰 Ahorro',
        ];

        $colors = [
            '#d94f4f', '#3f78b5', '#6b9b45', '#a65d9b', '#d88732',
            '#5c8f8f', '#815da8', '#a6764b', '#4e9b75', '#c05c82',
            '#5374b8', '#8c753f', '#4c9aa6', '#bc6e45', '#6e65a8',
            '#4b8b56', '#9a5a5a', '#587e9f', '#b17936', '#3c8b78',
        ];

        foreach ($categories as $index => $name) {
            ExpenseCategory::updateOrCreate(
                ['name' => $name],
                ['color' => $colors[$index]],
            );
        }

        $expenses = [
            ['2026-09-01', '🍽️ Alimentación', 'Compra semanal', 'Efectivo', 18500, 'Supermercado'],
            ['2026-09-02', '🚌 Transporte', 'Carga de tarjeta', 'Efectivo', 4200, 'Viajes de la semana'],
            ['2026-09-03', '🏠 Hogar', 'Productos de limpieza', 'Tarjeta', 7600, ''],
            ['2026-09-04', '💊 Salud', 'Farmacia', 'Tarjeta', 5300, 'Medicamentos'],
            ['2026-09-05', '🎬 Entretenimiento', 'Entrada al cine', 'Efectivo', 4800, ''],
            ['2026-09-06', '👕 Ropa', 'Remera', 'Tarjeta', 12500, 'Oferta'],
            ['2026-09-07', '📚 Educación', 'Curso online', 'Transferencia', 22000, 'Cuota mensual'],
            ['2026-09-08', '🐶 Mascotas', 'Alimento', 'Tarjeta', 9800, 'Bolsa grande'],
            ['2026-09-09', '💡 Servicios', 'Factura de luz', 'Transferencia', 11700, ''],
            ['2026-09-10', '🛒 Supermercado', 'Compra del mes', 'Tarjeta', 28600, ''],
            ['2026-09-11', '☕ Cafetería', 'Desayunos', 'Efectivo', 3900, 'Varios'],
            ['2026-09-12', '🏋️ Deporte', 'Gimnasio', 'Tarjeta', 15000, 'Abono mensual'],
            ['2026-09-13', '✈️ Viajes', 'Reserva de hospedaje', 'Tarjeta', 31500, 'Fin de semana'],
            ['2026-09-14', '🎁 Regalos', 'Cumpleaños', 'Efectivo', 6500, ''],
            ['2026-09-15', '💻 Tecnología', 'Accesorios', 'Tarjeta', 8900, 'Cable y adaptador'],
            ['2026-09-16', '📱 Suscripciones', 'Servicios digitales', 'Tarjeta', 4600, ''],
            ['2026-09-17', '🧾 Impuestos', 'Monotributo', 'Transferencia', 12100, 'Pago mensual'],
            ['2026-09-18', '💇 Cuidado personal', 'Peluquería', 'Efectivo', 7200, ''],
            ['2026-09-19', '🎨 Hobbies', 'Materiales', 'Tarjeta', 5400, 'Proyecto personal'],
            ['2026-09-20', '💰 Ahorro', 'Transferencia a ahorro', 'Transferencia', 25000, 'Objetivo mensual'],
        ];

        foreach ($expenses as [$date, $category, $detail, $paymentType, $amount, $notes]) {
            Expense::firstOrCreate(
                ['date' => $date, 'detail' => $detail],
                [
                    'category' => $category,
                    'payment_type' => $paymentType,
                    'amount' => $amount,
                    'notes' => $notes ?: null,
                ],
            );
        }

        MonthlyIncome::firstOrCreate(
            ['year' => 2026, 'month' => 9],
            ['amount' => 150000],
        );
    }
}
