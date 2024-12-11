<div class="modal " id="modal_documents{{$aluno->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-create p-2">
      <div class="modal-header border-0">
        <p class="titulomodal">Documentos do Discentes</p>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="text-align: start">
        <div class="mb-3">
          <label for="termo_compromisso_aluno" class="tituloinfomodal form-label mt-3">Termo de compromisso</label>
          <div class="baixar-arquivo">
            <a href="{{ route('termo_aluno.download', ['fileName' => $vinculo->termo_compromisso_aluno]) }}" target="_blank" class="link">
              <img src="{{asset('images/download.svg')}}" alt="baixar arquivo" style="width: 20px; height: 20px; margin-right: 5px;">
              Baixar
            </a>
            <br>
            <br>
          </div>
        </div>
        @if ($vinculo->edital->programa->nome == 'Monitoria' && $vinculo->frequencias->count() > 0)
        <div class="mb-3">
          <label for="frequencia_mensal" class="tituloinfomodal form-label mt-3">Frequência Mensal</label>
          <div class="baixar-arquivo">
            @php
            $latestFrequencia = null;
            @endphp

            @foreach ($vinculo->frequencias as $frequencia)
            @if ($frequencia->frequencia_mensal != null)
            @php
            $latestFrequencia = $frequencia;
            @endphp
            @endif
            @endforeach

            @if ($latestFrequencia)
            <a href="{{ route('frequencia.download', ['fileName' => $latestFrequencia->frequencia_mensal]) }}" target="_blank" class="link">
              <img src="{{ asset('images/download.svg') }}" alt="baixar arquivo" style="width: 20px; height: 20px; margin-right: 5px;">
              Baixar
            </a>
            <br>
            <br>
            @endif
          </div>
        </div>
        @endif

      </div>
    </div>
  </div>
</div>