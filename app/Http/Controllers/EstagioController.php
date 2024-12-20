<?php

namespace App\Http\Controllers;

use App\Exports\EstagiosExport;
use App\Http\Requests\EstagioStoreFormRequest;
use App\Http\Requests\EstagioUpdateFormRequest;
use App\Models\Aluno;
use App\Models\Curso;
use App\Models\Disciplina;
use App\Models\DocumentoEstagio;
use App\Models\Estagio;
use App\Models\Instituicao;
use App\Models\Orientador;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ListaDocumentosObrigatorios;
use Kyslik\ColumnSortable\Sortable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Filters\EstagioFilter;
use App\Http\Requests\EstagioFilterRequest;

class EstagioController extends Controller
{
    use Sortable;

    public function index(EstagioFilterRequest $request, EstagioFilter $filtro)
    {
        $estagios = Estagio::sortable();
        $filtro->apply($estagios, $request);
        $estagios = $estagios->orderBy('updated_at', 'desc')->paginate(15)->appends($request->except('page'));
        $cursos = Curso::all();
        $disciplinas = Disciplina::distinct('nome')->get();
        $alunos = Aluno::all();
        $orientadores = Orientador::all();

        return view('Estagio.index', compact('estagios', 'cursos', 'disciplinas', 'alunos', 'orientadores'));
    }

    public function create()
    {
        $aluno = null;
        $disciplinas = null;

        if (auth()->user()->hasRole('estudante')) {
            //Se for aluno, vamos obter o aluno pelo typage_id
            $aluno_id = auth()->user()->typage_id;
            $aluno = Aluno::Where('id', $aluno_id)->first();

            $disciplinas = $aluno->curso->disciplinas; //seleciona apenas as disciplinas dos alunos
            //dd($aluno);
        } else {
            $disciplinas = Disciplina::all();
        }

        $orientadors = Orientador::all();
        $cursos = Curso::all();
        //$supervisors = Supervisor::all();

        return view('Estagio.cadastrar', compact('orientadors', 'cursos', 'aluno', 'disciplinas'));
    }

    public function store(EstagioStoreFormRequest $request)
    {
        DB::beginTransaction();

        $estagio = new Estagio();
        $estagio->status = $request->checkStatus;
        $estagio->descricao = $request->descricao;
        $estagio->supervisor = $request->supervisor;
        $estagio->data_inicio = $request->data_inicio;
        $estagio->data_fim = $request->data_fim;

        $aluno = Aluno::Where('cpf', $request->cpf_aluno)->first();

        $estagio->aluno_id = $aluno->id;
        $estagio->orientador_id = $request->orientador;
        $estagio->curso_id = $request->curso;
        $estagio->disciplina_id = $request->disciplina;
        $estagio->tipo = $request->checkTipo;
        $estagio->save();
        DB::commit();

        if (auth()->user()->hasRole('estudante')) {
            return redirect('/meus-estagios')->with('sucesso', 'Estágio cadastrado com sucesso.');
        }

        return redirect('/estagio')->with('sucesso', 'Estágio cadastrado com sucesso.');
    }

    public function edit($id)
    {
        $aluno = null;
        $disciplinas = null;

        if (auth()->user()->hasRole('estudante')) {
            //Se for aluno, vamos obter o aluno pelo typage_id
            $aluno_id = auth()->user()->typage_id;
            $aluno = Aluno::Where('id', $aluno_id)->first();

            $disciplinas = $aluno->curso->disciplinas; //seleciona apenas as disciplinas dos alunos
            //dd($aluno);
        } else {
            $disciplinas = Disciplina::all();
        }

        $orientadors = Orientador::all();
        $cursos = Curso::all();
        //$supervisors = Supervisor::all();

        $estagio = Estagio::Where('id', $id)->first();
        return view("Estagio.editar", compact('estagio', 'aluno', 'disciplinas', 'orientadors', 'cursos'));
    }

    public function update(EstagioUpdateFormRequest $request, $id)
    {
        $dados = $request->validated();
        $estagio = Estagio::find($id);

        if (!$estagio) {
            abort(404, 'Estágio não encontrado');
        }

        $estagio->update($dados);

        return redirect()->route('estagio.index')->with('sucesso', 'Estágio editado com sucesso.');
    }


    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $estagio = Estagio::Where('id', $id)->first();

            $estagio->delete();

            DB::commit();
            return redirect()->route('estagio.index')->with('sucesso', 'Estágio deletado com sucesso.');
        } catch (exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors("Falha ao deletar Estágio. tente novamente mais tarde.");
        }
    }

    public function export(Request $request)
    {
        $estagios = DB::table('estagios');

        $cpf = $request->query('cpf');
        if ($cpf != null) {
            $aluno = Aluno::where('cpf', $cpf)->first();

            if (!$aluno) {
                return redirect()->route('estagio.export-form')->with('erro', 'Aluno não encontrado para o CPF fornecido.');
            }

            $estagios->where('aluno_id', $aluno->id);
        }

        $data_inicio = $request->query('data_inicio');
        $data_fim = $request->query('data_fim');

        if ($data_inicio != null && $data_fim != null) {
            $estagios->whereBetween('data_inicio', [$request->query('data_inicio'), $request->input('data_fim')])
                ->orderBy('data_inicio', 'asc');
        }

        $status = $request->query('status');
        if ($status != null) {
            $estagios->where('status', $request->query('status'));
        }

        $tipo = $request->query('tipo');
        if ($tipo != null) {
            $estagios->where('tipo', $request->query('tipo'));
        }

        $curso = $request->query('curso');
        if ($curso != null) {
            $estagios->where('curso_id', $request->query('curso'));
        }

        $orientadorCPF = $request->query('orientador');
        $orientador = Orientador::where('cpf', $orientadorCPF)->first();
        if ($orientador != null) {
            $estagios->where('orientador_id', $orientador->id);
        }

        $export = new EstagiosExport($estagios->orderBy('id', 'asc'));
        $extensao = $request->query('extensao');

        return Excel::download($export, 'estagios.' . $extensao)->deleteFileAfterSend(true);
    }

    public function export_form()
    {
        $alunos = Aluno::all();
        $estagios = Estagio::all();
        $cursos = Curso::all();
        $orientadors = Orientador::all();
        $estagiosAtivos = Estagio::where('status', true)->get();


        $alunosComEstagio = Aluno::join('estagios', 'alunos.id', '=', 'estagios.aluno_id')
            ->select('alunos.*')
            ->distinct()
            ->get();
        return view('Estagio.exportar-dados', compact('alunos', 'estagios', 'alunosComEstagio', 'cursos', 'orientadors', 'estagiosAtivos'));
    }

    public function estagios_profile(Request $request)
    {
        $aluno_id = auth()->user()->typage_id;

        $valorBusca = $request->input('valor');
        $estagios = Estagio::where('aluno_id', $aluno_id)
            ->where(function ($query) use ($valorBusca) {
                $query->where('descricao', 'LIKE', "%$valorBusca%")
                    ->orWhere('created_at', 'LIKE', "%$valorBusca%")
                    ->orWhere('data_inicio', 'LIKE', "%$valorBusca%")
                    ->orWhere('data_fim', 'LIKE', "%$valorBusca%");
            })
            ->get();

        return view('Estagio.estagios-aluno', compact('estagios'));
    }

    public function estagios_orientador(Request $request)
    {
        $orientador_id = auth()->user()->typage_id;

        $valorBusca = $request->input('valor');
        $estagios = Estagio::where('orientador_id', $orientador_id)
            ->where(function ($query) use ($valorBusca) {
                $query->where('descricao', 'LIKE', "%$valorBusca%")
                    ->orWhere('created_at', 'LIKE', "%$valorBusca%")
                    ->orWhere('data_inicio', 'LIKE', "%$valorBusca%")
                    ->orWhere('data_fim', 'LIKE', "%$valorBusca%");
            })
            ->get();

        return view('Estagio.estagios-orientador', compact('estagios'));
    }


    public function showDocuments($id)
    {
        $estagio = Estagio::findOrFail($id);
        $aluno = aluno::findOrFail($estagio->aluno_id);
        $instituicao = Instituicao::pluck('sigla')->first();

        $documentos = DocumentoEstagio::join('lista_documentos_obrigatorios', function ($join) use ($aluno, $estagio) {
            $join->on('documentos_estagios.lista_documentos_obrigatorios_id', '=', 'lista_documentos_obrigatorios.id')
                ->where('documentos_estagios.aluno_id', $aluno->id)
                ->where('documentos_estagios.estagio_id', $estagio->id);
        })
            ->select('documentos_estagios.*', 'lista_documentos_obrigatorios.*')
            ->get();

        $lista_documentos = ListaDocumentosObrigatorios::leftJoin('documentos_estagios', function ($join) use ($aluno, $estagio) {
            $join->on('lista_documentos_obrigatorios.id', '=', 'documentos_estagios.lista_documentos_obrigatorios_id')
                ->where('documentos_estagios.aluno_id', $aluno->id)
                ->where('documentos_estagios.estagio_id', $estagio->id);
        })
            ->where('lista_documentos_obrigatorios.instituicao', $instituicao)
            ->select(
                'lista_documentos_obrigatorios.*',
                'documentos_estagios.status',
                'documentos_estagios.estagio_id',
                'documentos_estagios.observacao',
                'documentos_estagios.created_at as data_envio',
                'documentos_estagios.updated_at as data_atualizacao',
                'documentos_estagios.id as documento_id',
                'documentos_estagios.is_completo',
                'documentos_estagios.is_visualizado'
            )
            ->get();

        return view('Estagio.documentos.documentos_show', compact("estagio", "documentos", "lista_documentos", "aluno", "instituicao"));
    }

    public function getEstagioAtual()
    {
        $aluno_id = auth()->user()->typage_id;

        $estagioAtual = Estagio::where('aluno_id', $aluno_id)
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->first();

        return $estagioAtual;
    }

    public function verificar_aluno_view()
    {
        return view('Estagio.verificar-aluno');
    }

    public function verificarAluno(Request $request)
    {
        $cpf = $request->input('cpf');

        $aluno = Aluno::where('cpf', $cpf)->first();

        $orientadors = Orientador::all();
        $cursos = Curso::all();
        $disciplinas = Disciplina::all();

        if ($aluno) {
            return view('Estagio.cadastrar', compact('aluno', 'orientadors', 'cursos', 'disciplinas'));
        } else {
            return view("Alunos.cadastro-aluno", compact('cursos', 'cpf'));
        }
    }
}
