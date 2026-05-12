<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-200 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex justify-between items-center flex-wrap gap-3 mb-6">
                <h2 class="font-semibold text-xl text-base-content leading-tight">📖 Catálogo Público</h2>
                <input wire:model.live="search" type="text" placeholder="🔍 Procurar por ISBN ou título…" class="input input-bordered w-full max-w-xs" />
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Capa</th>
                            <th>Título</th>
                            <th>ISBN</th>
                            <th>Editora</th>
                            <th>Disponibilidade</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livros as $livro)
                            @php
                                $disponivel = $livro->requisicaoAtiva === null;
                            @endphp
                            <tr>
                                <td>
                                    @if($livro->imagem_capa)
                                        <div class="avatar">
                                            <div class="w-10 h-10 rounded">
                                                <img src="{{ asset('storage/'.$livro->imagem_capa) }}" alt="Capa" />
                                            </div>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="font-semibold">{{ $livro->nome }}</td>
                                <td>{{ $livro->isbn }}</td>
                                <td>{{ $livro->editora?->nome ?? '—' }}</td>
                                <td>
                                    @if($disponivel)
                                        <span class="badge badge-success">Disponível</span>
                                    @else
                                        <span class="badge badge-error">Indisponível</span>
                                    @endif
                                </td>
                                <td>
                                    @auth
                                        @if($disponivel)
                                            <a class="btn btn-sm btn-primary" href="{{ route('requisicoes', ['livro' => $livro->id]) }}">Requisitar</a>
                                        @else
                                            —
                                        @endif
                                    @else
                                        <a class="btn btn-sm" href="{{ route('login') }}">Login para requisitar</a>
                                    @endauth
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

