<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            // Flujos de dinero regulares
            ['nombre' => 'Sueldo / Ingresos', 'tipo' => 'INGRESO'],
            ['nombre' => 'Alimentación', 'tipo' => 'GASTO'],
            ['nombre' => 'Transporte', 'tipo' => 'GASTO'],
            ['nombre' => 'Vivienda', 'tipo' => 'GASTO'],
            ['nombre' => 'Cuidado Personal', 'tipo' => 'GASTO'],
            ['nombre' => 'Tecnología', 'tipo' => 'GASTO'],

            // Flujos para cuando hay préstamos de por medio
            ['nombre' => 'Préstamo Recibido (Me prestaron)', 'tipo' => 'INGRESO'],
            ['nombre' => 'Préstamo Otorgado (Presté dinero)', 'tipo' => 'GASTO'],

            // Flujos para cuando se amortizan o pagan esas deudas
            ['nombre' => 'Pago de Préstamo (Pagué lo que debía)', 'tipo' => 'GASTO'],
            ['nombre' => 'Cobro de Préstamo (Me pagaron lo que debían)', 'tipo' => 'INGRESO'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }
    }
}
