<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome'
    ];

    public function alunos()
    {
        return $this->hasMany(Aluno::class, "curso_id");
    }
    public function disciplinas()
    {
        return $this->hasMany(Disciplina::class, "curso_id");
    }

    public function orientadores(){
        return $this->belongsToMany(Orientador::class, 'orientador_cursos');
    
    }

    public function estagios(){
        return $this->hasMany(Estagio::class, "curso_id");
    }
}
