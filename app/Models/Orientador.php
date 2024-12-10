<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orientador extends Model
{
    use HasFactory;

    protected $fillable = [
        'cpf',
        'instituicaoVinculo',
        #'curso',
        'matricula',
    ];

    public function user()
    {
        return $this->morphOne(User::class, "typage");
    }

    public function editais()
    {
        return $this->belongsToMany(Edital::class, 'edital_aluno_orientadors')
            ->withPivot([
                'data_inicio',
                'data_fim',
                'bolsa',
                'status',
                'info_complementares',
                #'disciplina_id',
                'aluno_id',
                'edital_id',
                'orientador_id',
                'termo_compromisso_aluno',
            ]);
    }

    public function alunosOrientados()
    {
        return $this->belongsToMany(Aluno::class, 'edital_aluno_orientadors', 'orientador_id', 'aluno_id')
                    ->wherePivot('status', true);
    }

    public function cursos(){
        return $this->belongsToMany(Curso::class, 'orientador_cursos');
    }
}
