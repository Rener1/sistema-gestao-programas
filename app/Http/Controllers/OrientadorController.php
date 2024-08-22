<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrientadorFormRequest;
use App\Http\Requests\OrientadorFormUpdateRequest;
use App\Models\Curso;
use App\Models\Edital;
use App\Models\EditalAlunoOrientadors;
use App\Models\Orientador;
use App\Models\User;
use App\Services\ManipulacaoImagens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Database\QueryException;

class OrientadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (sizeof($request-> query()) > 0){
            $campo = $request->query('campo');
            $valor = $request->query('valor');

            if ($valor == null){
                return redirect()->back()->withErrors( "Deve ser informado algum valor para o filtro." );
            }

            $orientadors = Orientador::join("users", "users.typage_id", "=", "orientadors.id");
            $orientadors = $orientadors->where(function ($query) use ($valor) {
                if ($valor) {
                    $query->orWhere("users.name", "LIKE", "%{$valor}%");
                    $query->orWhere("users.email", "LIKE", "%{$valor}%");
                    $query->orWhere("orientadors.cpf", "LIKE", "%{$valor}%");
                    $query->orWhere("orientadors.matricula", "LIKE", "%{$valor}%");
                }
            })->orderBy('orientadors.created_at', 'desc')->select("orientadors.*")->distinct()->get();


            return view("Orientador.index", compact("orientadors"));
        } else {
            $orientadors = Orientador::all();
            return view("Orientador.index", compact("orientadors"));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cursos = Curso::all();
        return view("Orientador.cadastrar", compact("cursos"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrientadorFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $orientador = new Orientador();
            $orientador->cpf = $request->cpf;
            $orientador->matricula = $request->matricula;
            $orientador->instituicaoVinculo = $request->instituicaoVinculo;

            #$orientador->curso = 'Teste';
            $imageName = null;
            if($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageName = ManipulacaoImagens::salvarImagem($request->image);                
            }

            if ($orientador->save()){

                #Adicionar os cursos pegando o ID do Orientador gerado no banco
                #Temos que salvar primeiro, para pegar o ID do Orientador
                $cursos_id = $request->cursos;
                foreach ($cursos_id as $id) {
                    $curso = Curso::findorFail($id);
                    $curso->orientadores()->attach($orientador->id);
                }


                if (
                    $orientador->user()->create([
                        'name' => $request->name,
                        'name_social' => $request->name_social,
                        'cpf' => $request->cpf,
                        'email' => $request->email,
                        'password' => Hash::make($request->senha),
                        'image' => $imageName
                    ])->givePermissionTo('orientador')
                ){
                    $confirm = new ConfirmandoEmail($request);
                    $confirm -> enviandoEmail();
                    $mensagem_sucesso = "Orientador cadastrado com sucesso.";

                    DB::commit();
                    return redirect('/orientadors')->with('sucesso', 'Orientador cadastrado com sucesso.');

                } else {
                    DB::rollBack();

                    return redirect()->back()->withErrors( "Falha ao cadastrar orientador. tente novamente mais tarde." );
                }
            }else{
                DB::rollBack();

                return redirect()->back()->withErrors( "Falha ao cadastrar orientador. tente novamente mais tarde." );
            }
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors("Falha ao cadastrar orientador. Tente novamente mais tarde.");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orientador = Orientador::find($id);
        $cursos = Curso::all();
        $cursosSelecionados = $orientador->cursos->pluck('id')->toArray();
        return view("Orientador.editar-orientador", compact('orientador', 'cursos', 'cursosSelecionados'));
    }

    public function editarmeuperfil($id)
    {
        $orientador = Orientador::find($id);
        $cursos = Curso::all();
        $cursosSelecionados = $orientador->cursos->pluck('id')->toArray();

        // Verifique se o ID do orientador corresponde ao ID do usuário autenticado
        if ($orientador->user->id !== auth()->user()->id) {
            return redirect()->route('home')->with('erro', 'Você não tem permissão para editar este perfil.');
        }


        return view("Orientador.editarmeuperfil", compact('orientador', 'cursos', 'cursosSelecionados'));
    }

    public function update(OrientadorFormUpdateRequest $request, $id)
    {
        try{
            $orientador = Orientador::find($id);

            $orientador->cpf = $request->cpf == $orientador->cpf ? $orientador->cpf : $request->cpf;
            $orientador->matricula = $request->matricula;
            $orientador->instituicaoVinculo = $request->instituicaoVinculo == $orientador->instituicaoVinculo ? $orientador->instituicaoVinculo : $request->instituicaoVinculo;

            $orientador->user->name = $request->name;
            $orientador->user->email = $request->email;
            $orientador->user->name_social = $request->name_social;
            $orientador->user->cpf = $request->cpf;

            $imageName = null;
            if($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageName = ManipulacaoImagens::salvarImagem($request->image);
                if($orientador->user->image){
                    ManipulacaoImagens::deletarImagem($orientador->user->image); //Se houver uma nova imagem, remove a anterior do servidor                                
                }                
            }
            $orientador->user->image = $request->image == null ? $orientador->user->image : $imageName;


            $cursos_id = $request->cursos;
            if($cursos_id == null){
                return redirect()->back()->withErrors( "Selecione pelo menos um Curso" );
            }

            if ($request->senha && $request->senha != null){
                if (strlen($request->senha) > 3 && strlen($request->senha) < 31){
                    $orientador->user->password = Hash::make($request->senha);
                } else {
                    return redirect()->back()->withErrors( "Senha deve ter entre 4 e 30 dígitos" );
                }
            }

            if ($orientador->save()){
                #Atualiza os cursos de Orientador
                $orientador->cursos()->sync($request->cursos);

                if ($orientador->user->update()){
                    return redirect('/orientadors')->with('sucesso', 'Orientador Atualizado com sucesso.');
                    // return redirect('/meu-perfil-orientador')->with('sucesso', 'Orientador Atualizado com sucesso.');

                } else {
                    return redirect()->back()->withErrors( "Falha ao editar orientador. tente novamente mais tarde." );
                }

            } else {
                return redirect()->back()->withErrors( "Falha ao editar orientador. tente novamente mais tarde." );
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors("Falha ao editar orientador. Tente novamente mais tarde.");
        }
    }

    public function atualizerPerfilOrientador(OrientadorFormUpdateRequest $request, $id)
    {
        try{
            $orientador = Orientador::find($id);

            $orientador->cpf = $request->cpf == $orientador->cpf ? $orientador->cpf : $request->cpf;
            $orientador->matricula = $request->matricula;
            $orientador->instituicaoVinculo = $request->instituicaoVinculo == $orientador->instituicaoVinculo ? $orientador->instituicaoVinculo : $request->instituicaoVinculo;

            $orientador->user->name = $request->name;
            $orientador->user->email = $request->email;
            $orientador->user->name_social = $request->name_social;
            $orientador->user->cpf = $request->cpf;

            $imageName = null;
            if($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageName = ManipulacaoImagens::salvarImagem($request->image);
                if($orientador->user->image){
                    ManipulacaoImagens::deletarImagem($orientador->user->image); //Se houver uma nova imagem, remove a anterior do servidor                                
                }                
            }
            $orientador->user->image = $request->image == null ? $orientador->user->image : $imageName;

            $cursos_id = $request->cursos;
            if($cursos_id == null){
                return redirect()->back()->withErrors( "Selecione pelo menos um Curso" );
            }

            if ($request->senha && $request->senha != null){
                if (strlen($request->senha) > 3 && strlen($request->senha) < 31){
                    $orientador->user->password = Hash::make($request->senha);
                } else {
                    return redirect()->back()->withErrors( "Senha deve ter entre 4 e 30 dígitos" );
                }
            }

            if ($orientador->save()){
                #Atualiza os cursos de Orientador
                $orientador->cursos()->sync($request->cursos);

                if ($orientador->user->update()){
                    return redirect('/meu-perfil-orientador')->with('sucesso', 'Orientador Atualizado com sucesso.');
                    // return redirect('/meu-perfil-orientador')->with('sucesso', 'Orientador Atualizado com sucesso.');

                } else {
                    return redirect()->back()->withErrors( "Falha ao editar orientador. tente novamente mais tarde." );
                }

            } else {
                return redirect()->back()->withErrors( "Falha ao editar orientador. tente novamente mais tarde." );
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors("Falha ao editar orientador. Tente novamente mais tarde.");
        }
    }

    public function delete($id)
    {
        $orientador = Orientador::findOrFail($id);
        return view('orientadors.delete', ['orientador' => $orientador]);
    }

    public function destroy(Request $request)
    {
        try{
            DB::beginTransaction();

            $id = $request->only(['id']);
            $orientador = Orientador::findOrFail($id)->first();

            $orientador->cursos()->detach($request->cursos);
            
            $imageName = $orientador->user->image;            
            
            $orientador->user->delete();
            $orientador->delete();            
            ManipulacaoImagens::deletarImagem($imageName); 
            DB::commit();

            return redirect(route("orientadors.index"))->with('sucesso', 'Orientador Deletado com sucesso.');;
            
        } catch(QueryException $e){
            DB::rollback();
            return redirect()->back()->withErrors( "Falha ao deletar Orientador. O Orientador possui vínculo com algum Edital." );
        }catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors("Falha ao deletar orientador. Tente novamente mais tarde.");
        }
    }

    public function profile(Request $request)
    {
        $id = $request->user()->typage_id; // Obtém o ID do usuário autenticado
       // $user = $request->user(); // Obtém o usuário autenticado

        $orientador = Orientador::find($id);

        return view('Perfil.meu-perfil-orientador', ['orientador' => $orientador]);
    }   

    public function editais_profile_orientador(Request $request) {

        //Manda apenas os vinculos ativos
        $vinculos = EditalAlunoOrientadors::where('orientador_id', $request->user()->typage_id)->where('status', true)->get();
        // $editais = array();
        // foreach ($pivos as $pivo){
        //     array_push($editais, $pivo->edital);
        // }

        return view('Orientador.editais-orientador',compact("vinculos"));
    }


    public function lista_alunos_profile_orientador(Request $request) {
        $pivos = EditalAlunoOrientadors::where('orientador_id', $request->user()->typage_id)->get();
        $alunos = array();
        
        return view('Orientador.listar_alunos-orientador',compact("pivos"));
    }
}
