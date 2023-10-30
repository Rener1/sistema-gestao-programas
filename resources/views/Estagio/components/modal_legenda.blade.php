<div class="modal" id="modal_legenda" tabindex="-1" aria-labelledby="modal_legenda" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-create p-2">
            <div class="modal-header border-0">
                <p class="titulomodal">Legenda dos ícones</p>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="text-align: start">
                @canany(['admin', 'servidor', 'pro_reitor', 'gestor'])
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
                <div class="mb-3">
                    <img src="{{ asset('images/pencil_red.svg') }}" alt="Editar estágio"
                        style="height: 30px; width: 30px; padding-bottom: 5px">
                    <span class="textoinfomodal">Editar o estágio</span>
                </div>
                <div class="mb-3">
                    <img src="{{ asset('images/file_red.svg') }}" alt="Ver documentos do estágio"
                        style="height: 30px; width: 30px; padding-bottom: 5px">
                    <span class="textoinfomodal">Ver os documentos do estágio</span>
                </div>
                <div class="mb-3">
                    <img src="{{ asset('images/delete_red.svg') }}" alt="Deletar estágio"
                        style="height: 30px; width: 30px; padding-bottom: 5px">
                    <span class="textoinfomodal">Deletar o estágio</span>
                </div>
                @endcan
                @can('aluno')
                <div class="mb-3">
                    <img src="{{ asset('images/mostrar-documentos-red.svg') }}" alt="Ver documentos do estágio"
                        style="height: 30px; width: 30px; padding-bottom: 5px">
                    <span class="textoinfomodal">Ver os documentos do estágio</span>
                </div>
                @endcan
            </div>
            <div class="modal-footer border-0"></div>
        </div>
    </div>
</div>
