@extends("templates.app")

@section("body")

@can('listar curso')
  <style>
    pagination {
      display: inline-block;

    }

    .pagination a {
      color: black;
      float: left;
      padding: 8px 16px;
      text-decoration: none;
      transition: background-color .3s;
      border: 1px solid #ddd;
      margin: 10px 4px;
    }

    .pagination a.active {
      background-color: #3B864F;
      color: white;
      border: 1px solid #3B864F;
    }

    .pagination a:hover:not(.active) {
      background-color: #34A853;
    }
</style>

<div class="container-fluid">
  @if (session('sucesso'))
  <div class="alert alert-success">
    {{session('sucesso')}}
  </div>
  @endif
  <br>

  <div style="display: flex; justify-content: space-evenly; align-items: center;">
      <h1 class = "titulo"><strong>Cursos</strong></h1>
    </div>

    <form class="search-container" action="{{route('cursos.index')}}" method="GET">
        <input class="search-input" onkeyup="" type="text" placeholder="Digite a busca" title="" id="valor" name="valor" style="text-align: start">
        <input class="search-button" title="Fazer a pesquisa" type="submit" value=""></input>
        @can('cadastrar curso')
          <button class="cadastrar-botao" type="button" onclick="window.location.href = '{{ route("cursos.create") }}'">Cadastrar curso</button>
        @endcan
      </form>

      <br>
      <br>

</div>
    @if (sizeof($cursos) == 0)
    <div class="empty">
      <p>
        Não há cursos cadastrados
      </p>
    </div>
    @else
    @endif

    <div class="d-flex flex-wrap justify-content-center" style="flex-direction: row-reverse;">
      <div class="col-md-9 corpo p-2 px-3">
        <table class="table">
          <thead>
            <tr class= table-head>
              <th scope="col" class="text-center align-middle">Nome</th>
              <th class="text-center">
                Ações
                <button type="button" class="infobutton align-bottom" data-bs-toggle="modal" data-bs-target="#modal_legenda" title="Ver legenda dos ícones">
                    <img src="{{ asset('images/infolegenda.svg') }}" alt="Legenda" style="height: 20px; width: 20px;">
                </button>
            </th>
            </tr>
          </thead>
            @foreach ($cursos as $cursos)
          <tbody>
            <tr>
              <td class="align-middle">{{$cursos->nome}}</td>
              <td class="align-middle">
                <a type="button" data-bs-toggle="modal" data-bs-target="#modal_show_{{$cursos->id}}">
                  <img src="{{asset('images/information.svg')}}" title="Informações" alt="Info curso" style="height: 30px; width: 30px;">
                </a>
                @can('cadastrar disciplina')
                  <a href="{{ route('disciplinas_curso.create', $cursos->id) }}" type="button"> 
                    <img src="{{asset('images/add_disciplina.svg')}}" title="Adicionar uma disciplina" alt="Cadastrar Disciplina no curso" style="height: 27px; width: 30px;">
                  </a>
                @endcan
                @can('editar curso')
                  <a href="{{ route('cursos.edit', $cursos->id) }}" type="button"> 
                    <img src="{{asset('images/pencil.svg')}}" title="Editar" alt="Editar curso">
                  </a>
                @endcan
                @can('deletar curso')
                  <a type="button" data-bs-toggle="modal" data-bs-target="#modal_delete_{{$cursos->id}}">
                    <img src="{{asset('images/delete.svg')}}"title="Remover" alt="Deletar curso" style="height: 30px; width: 30px;">
                  </a>
                @endcan
              </td>
            </tr>
            @include("Curso.components.modal_show", ["curso" => $cursos, "disciplinas" => $disciplinas])
            @include("Curso.components.modal_delete", ["curso" => $cursos])
            @include("Curso.components.modal_legenda")
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

<script type="text/javascript">
  function exibirModalDeletar(id){
    $('#modal_delete_' + id).modal('show');
  }
  function exibirModalVisualizar(id){
    $('#modal_show_' + id).modal('show');
  }
</script>

<script >
    $("#cursos").chosen({
              placeholder_text_multiple: "Selecione um curso",
              // max_shown_results : 5,
              no_results_text: "Não possui cursos."
          });
</script>
@else
  <h3 style="margin-top: 1rem">Você não possui permissão!</h3>
  <a class="btn btn-primary submit" style="margin-top: 1rem" href="{{url("/login")}}">Voltar</a>
@endcan

@endsection
