@extends('templates.app')

@section('body')
    @canany(['admin', 'servidor', 'gestor', 'aluno'])
        <div class="container-fluid">
            @if (Session::has('pdf_generated_success'))
                <div class="alert alert-success">
                    {{ Session::get('pdf_generated_success') }}
                </div>
            @endif
            <br>
            <div style="display: flex; flex-direction:column; justify-content: space-evenly; align-items: center;">
                <h1 class="titulo-estagio"><strong>Documentos do estágio - {{ $aluno->nome_aluno }}</strong></h1>
                <h2 class="titulopequeno">{{ $estagio->descricao }}</h2>
            </div>
            <div class="d-flex flex-wrap justify-content-center" style="flex-direction: row-reverse;">
                <div class="col-md-9 corpo p-2 px-6">
                    <table class="table">
                        <thead>
                            <tr class="table-head">
                                <th scope="col" class="text-center align-middle">Nome</th>
                                <th scope="col" class="text-center align-middle">Data limite</th>
                                <th scope="col" class="text-center align-middle">Data de envio</th>
                                <th scope="col" class="text-center align-middle">Última data de atualização</th>
                                <th scope="col" class="text-center align-middle">Status</th>
                                <th scope="col" class="text-center align-middle">Ações
                                    <button type="button" class="infobutton align-bottom" data-bs-toggle="modal"
                                        data-bs-target="#modal_legenda_doc" title="Ver legenda dos ícones">
                                        <img src="{{ asset('images/infolegenda.svg') }}" alt="Legenda"
                                            style="height: 20px; width: 20px;">
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        @foreach ($lista_documentos as $lista_documento)
                            <tbody>
                                <td class="align-middle">{{ $lista_documento->titulo }}</td>
                                <td class="align-middle">
                                    @php
                                        $dataLimite = $lista_documento->data_limite ?? null;
                                        $hoje = now();
                                    @endphp
                                    @if ($dataLimite)
                                        {{ date('d/m/Y', strtotime($dataLimite)) }}
                                    @else
                                        A definir
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @php
                                        $documento_enviado = $lista_documento->data_envio ?? null;
                                    @endphp
                                    @if ($documento_enviado)
                                        {{ date_format(date_create($documento_enviado), 'd/m/Y') }}
                                    @else
                                        Não enviado
                                    @endif
                                </td>
                                <td class= "align-middle">
                                    @php
                                        $documento_enviado = $lista_documento->data_atualizacao ?? null;
                                    @endphp
                                    @if ($documento_enviado)
                                        {{ date_format(date_create($documento_enviado), 'd/m/Y') }}
                                    @else
                                        Nunca atualizado
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @php
                                        $status = $lista_documento->status ?? null;
                                    @endphp
                                    @if ($status)
                                        {{ $status }}
                                    @else
                                        Não enviado
                                    @endif
                                </td>

                                @php
                                    switch ($lista_documento->id) {
                                        case 1:
                                            $rota = 'estagio.documentos.UPE.termo-de-encaminhamento';
                                            break;
                                        case 2:
                                            $rota = 'estagio.documentos.UPE.termo-de-compromisso';
                                            break;
                                        case 3:
                                            $rota = 'estagio.documentos.UPE.plano-de-atividades';
                                            break;
                                        case 4:
                                            $rota = 'estagio.documentos.UPE.ficha-frequencia';
                                            break;
                                        case 5:
                                            $rota = 'estagio.documentos.UPE.relatorio-acompanhamento-campo';
                                            break;
                                        case 6:
                                            $rota = 'estagio.documentos.UPE.relatorio-supervisor';
                                            break;
                                        case 7:
                                            $rota = 'estagio.documentos.UPE.frequencia-residente';
                                            break;
                                        case 8:
                                            $rota = 'estagio.documentos.UFAPE.seguro';
                                            break;
                                        case 9:
                                            $rota = 'estagio.documentos.UFAPE.termo-de-compromisso';
                                            break;
                                        case 10:
                                            $rota = 'estagio.documentos.UFAPE.carta-aceite-supervisor';
                                            break;
                                        case 11:
                                            $rota = 'estagio.documentos.UFAPE.ficha-frequencia';
                                            break;
                                        case 12:
                                            $rota = 'estagio.documentos.UFAPE.termo-aditivo';
                                            break;
                                        case 13:
                                            $rota = 'estagio.documentos.UFAPE.ficha-frequencia';
                                            break;
                                        default:
                                            $rota = null;
                                            break;
                                    }
                                @endphp
                                <td class="align-middle">
                                    <!-- <a>
                                                                                                                            <img src="{{ asset('images/information.svg') }}" title="Informações" alt="Info documento" style="height: 30px; width: 30px;">
                                                                                                                        </a> -->
                                    @if ($documento_enviado)
                                        @can('aluno')
                                            <!-- Verifica se o usuário tem a função de aluno -->

                                            @if (
                                                $lista_documento->status == 'Aguardando documento assinado' ||
                                                    $lista_documento->status == 'Aguardando verificação' ||
                                                    $lista_documento->status == 'Negado')
                                                {{-- <a type="button" href="{{ route($rota, ['id' => $estagio->id, 'edit' => true]) }}">
                                                <img src="{{ asset('images/pencil.svg') }}" alt="Editar Documento" title="Editar documento" style="height: 30px; width: 30px;">
                                                </a> --}}

                                                {{-- <a type="button" data-bs-toggle="modal"
                                                    data-bs-target="#modal_observacao_{{ $lista_documento->documento_id }}">
                                                    <img src="{{ asset('images/information.svg') }}" title="Observação"
                                                        alt="Ver Observação" style="height: 30px; width: 30px;">
                                                </a> --}}

                                                {{-- <a type="button" href="{{ route('observacao.show', ['id' => $lista_documento->id]) }}">
                                                <img src="{{ asset('images/information_red.svg') }}" alt="Ver Observação" style="height: 30px; width: 30px;">
                                                </a> --}}

                                                @if ($hoje > $dataLimite)
                                                    <!-- <a type="button" data-bs-toggle="modal" data-bs-target="#modal_observacao_{{ $lista_documento->documento_id }}">
                                                                                                                                                                                            <img src="{{ asset('images/information.svg') }}" title="Informações" alt="Ver Observação" style="height: 30px; width: 30px;">
                                                                                                                                                                                        </a> -->
                                                @elseif ($lista_documento->is_completo == 0)
                                                    <!-- Se o documento não estiver completo -->
                                                    @if (!empty(trim($lista_documento->observacao)))
                                                        <a type="button" data-bs-toggle="modal"
                                                            data-bs-target="#modal_observacao_{{ $lista_documento->documento_id }}">
                                                            <img src="{{ asset('images/information.svg') }}" title="Observação"
                                                                alt="Ver Observação" style="height: 30px; width: 30px;">
                                                        </a>
                                                    @endif
                                                    <a type="button"
                                                        href="{{ route($rota, ['id' => $estagio->id, 'edit' => true]) }}">
                                                        <img src="{{ asset('images/pencil.svg') }}" alt="Editar Documento"
                                                            title="Editar documento" style="height: 30px; width: 30px;">
                                                    </a>

                                                    <a type="button"
                                                        href="{{ route('estagio.documentos.documento-completo', ['id' => $lista_documento->documento_id]) }}">
                                                        <img src="{{ asset('images/mostrar-documentos.svg') }}"
                                                            alt="Enviar Documento Completo" style="height: 30px; width: 30px;">
                                                    </a>
                                                @else
                                                    <!-- Se o documento estiver completo -->
                                                    @if (!empty(trim($lista_documento->observacao)))
                                                        <a type="button" data-bs-toggle="modal"
                                                            data-bs-target="#modal_observacao_{{ $lista_documento->documento_id }}">
                                                            <img src="{{ asset('images/information.svg') }}" title="Observação"
                                                                alt="Ver Observação" style="height: 30px; width: 30px;">
                                                        </a>
                                                    @endif
                                                    <a type="button"
                                                        href="{{ route('estagio.documentos.documento-completo', ['id' => $lista_documento->documento_id]) }}">
                                                        <img src="{{ asset('images/mostrar-documentos.svg') }}"
                                                            alt="Enviar Documento Completo" style="height: 30px; width: 30px;">
                                                    </a>
                                                @endif
                                            @endif
                                        @endcan

                                        @canany(['admin', 'servidor', 'gestor'])
                                            @if ($lista_documento->is_visualizado == 1)
                                                <a type="button"
                                                    href="{{ route('aprovar.documento', ['id' => $lista_documento->documento_id]) }}"
                                                    class="aprovar-documento-link">
                                                    <img src="{{ asset('images/document-checkmark.svg') }}" alt="Aprovar Documento"
                                                        title="Aprovar documento" style="height: 30px; width: 30px;">
                                                </a>

                                                <a type="button"
                                                    href="{{ route('negar.documento', ['id' => $lista_documento->documento_id]) }}"
                                                    class="negar-documento-link">
                                                    <img src="{{ asset('images/document-dismiss.svg') }}" alt="Negar Documento"
                                                        title= "Negar documento" style="height: 30px; width: 30px;">
                                                </a>

                                                <a type="button"
                                                    href="{{ route('observacao.edit', ['id' => $lista_documento->documento_id]) }}">
                                                    <img src="{{ asset('images/information.svg') }}" alt="Ver Observação"
                                                        style="height: 30px; width: 30px;">
                                                </a>
                                            @else
                                                <img src="{{ asset('images/document-checkmark.svg') }}" alt="Aprovar Documento"
                                                    title="Aprovar documento" style="height: 30px; width: 30px; opacity: 50%;"
                                                    disabled>

                                                <img src="{{ asset('images/document-dismiss.svg') }}" alt="Negar Documento"
                                                    title="Negar documento" style="height: 30px; width: 30px; opacity: 50%;"
                                                    disabled>

                                                <a type="button"
                                                    href="{{ route('observacao.edit', ['id' => $lista_documento->documento_id]) }}">
                                                    <img src="{{ asset('images/information_red.svg') }}" alt="Ver Observação"
                                                        style="height: 30px; width: 30px;">
                                                </a>
                                            @endif

                                        @endcan
                                    @else
                                        @can('aluno')
                                            <!-- Verifica se o usuário tem a função de aluno -->
                                            @if ($hoje > $dataLimite)
                                                <img src="{{ asset('images/add_disciplina.svg') }}" alt="Documento Preenchido"
                                                    title="Documento não preenchido"
                                                    style="height: 30px; width: 30px; opacity: 50%;" disabled>
                                            @else
                                                <a type="button" href="{{ route($rota, ['id' => $estagio->id]) }}">
                                                    <img src="{{ asset('images/add_disciplina.svg') }}" alt="Preencher Documento"
                                                        title="Preencher documento" style="height: 30px; width: 30px;">
                                                </a>
                                            @endif
                                        @endcan
                                    @endif
                                    @if ($documento_enviado)
                                        @if ($lista_documento->id == 8 && $instituicao == 'UFAPE')
                                            <a type="button "href="{{ route('estagio.documentos.UFAPE.seguro', ['id' => $lista_documento->estagio_id]) }}"
                                                target="_blank" id="pdfLink"
                                                onclick="return openPdfLinkInNewTab(this.href)">
                                                <img src="{{ asset('images/listar_edital.svg') }}" alt="Documento Preenchido"
                                                    title="Documento preenchido" style="height: 30px; width: 30px;">
                                            </a>
                                        @else
                                            <a type="button"
                                                href="{{ route('visualizar.pdf', ['docId' => $lista_documento->documento_id]) }}"
                                                target="_blank" id="pdfLink"
                                                onclick="return openPdfLinkInNewTab(this.href)">
                                                <img src="{{ asset('images/listar_edital.svg') }}" alt="Documento Preenchido"
                                                    title="Documento preenchido" style="height: 30px; width: 30px;">
                                            </a>
                                            <a href="{{ route('visualizar.doc', ['docId' => $lista_documento->documento_id]) }}"
                                                target="_blank" id="pdfLink"
                                                onclick="return openPdfLinkInNewTab(this.href)">
                                                <img src="{{ asset('images/listar_edital.svg') }}"
                                                    alt="Documento Preenchido TEMPORARIO"
                                                    title="Documento preenchido TEMPORARIO"
                                                    style="height: 30px; width: 30px;">
                                            </a>
                                        @endif

                                        <iframe id="pdfIframe" style="display: none;"></iframe>
                                    @else
                                        <img src="{{ asset('images/listar_edital.svg') }}" alt="Documento Preenchido"
                                            title="Documento não preenchido" style="height: 30px; width: 30px; opacity: 50%;"
                                            disabled>
                                    @endif
                                </td>
                            </tbody>
                            @include('Estagio.components.modal_legenda_doc')
                            @include('Estagio.components.modal_legenda_doc')
                            @include('Estagio.components.modal_observacao', [
                                'lista_documento' => $lista_documento,
                                'documentos' => $documentos,
                            ])
                        @endforeach
                    </table>
                </div>
            </div>
            <script>
                function openPdfLinkInNewTab(pdfUrl) {
                    // Create an invisible iframe
                    const iframe = document.getElementById('pdfIframe');

                    // Load the PDF link in the iframe
                    iframe.src = pdfUrl;

                    // Reload the main page
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000); // Adjust the delay as needed

                    return true;
                }
            </script>
        </div>
    @endcan
@endsection
