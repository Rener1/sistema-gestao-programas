<div class="modal" id="modal_legenda" tabindex="-1" aria-labelledby="modal_legenda" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-create p-2">
            <div class="modal-header border-0">
                <p class="titulomodal">Legenda dos ícones</p>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body" style="text-align: start">
                @role('estudante')
                    <div class="mb-3">
                        <img src="{{ asset('images/mostrar-documentos-red.svg') }}" alt="Ver documentos do estágio"
                            style="height: 30px; width: 30px; padding-bottom: 5px">
                        <span class="textoinfomodal">Ver os documentos do estágio</span>
                    </div>
                @else
                    @can('visualizar documento estagio')
                        <div class="mb-3">
                            <img src="{{ asset('images/information_red.svg') }}" alt="Info estágio"
                            style="height: 30px; width: 30px; padding-bottom: 5px">
                            <span class="textoinfomodal">Visualizar observação do documento</span>
                        </div>
                        <div class="mb-3">
                            <img src="{{ asset('images/file_red.svg') }}" alt="Info estágio"
                            style="height: 30px; width: 30px; padding-bottom: 5px">
                            <span class="textoinfomodal">Visualizar documentos</span>
                        </div>
                    @endcan
                    @can('editar estagio')
                        <div class="mb-3">
                            <img src="{{ asset('images/pencil_red.svg') }}" alt="Editar estágio"
                            style="height: 30px; width: 30px; padding-bottom: 5px">
                            <span class="textoinfomodal">Editar o estágio</span>
                        </div>
                    @endcan
                    @can('visualizar documento estagio')
                        <div class="mb-3">
                            <img src="{{ asset('images/file_red.svg') }}" alt="Ver documentos do estágio"
                            style="height: 30px; width: 30px; padding-bottom: 5px">
                            <span class="textoinfomodal">Ver os documentos do estágio</span>
                        </div>
                    @endcan
                    @can('deletar estagio')
                        <div class="mb-3">
                            <img src="{{ asset('images/delete_red.svg') }}" alt="Deletar estágio"
                            style="height: 30px; width: 30px; padding-bottom: 5px">
                            <span class="textoinfomodal">Deletar o estágio</span>
                        </div>
                    @endcan
                @endrole
            </div>
            <div class="modal-footer border-0"></div>
        </div>
    </div>
</div>
