<?php

namespace Database\Seeders;

use App\Models\HistoricoVinculoAlunos;
use App\Models\Supervisor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsSeeder::class,
            CursoSeeder::class,
            AlunosSeeder::class,
            OrientadorsSeeder::class,
            ServidorsSeeder::class,
            ProgramasSeeder::class,
            DisciplinaSeeder::class,
            Programa_servidorsSeeder::class,
            EditalSeeder::class,
            Edital_Aluno_OrientadorSeeder::class,
            RelatorioSeeder::class,
            HistoricoVinculoAlunosSeeder::class,
            ListaDocumentosObrigatoriosSeeder::class,
            InstituicaoSeeder::class,
            EstagioSeeder::class,
            SiglaInstituicaoSeeder::class,
        ]);
    }
}
