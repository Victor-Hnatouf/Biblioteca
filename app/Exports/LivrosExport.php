<?php

namespace App\Exports;

use App\Models\Livro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LivrosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Livro::with(['editora', 'autores'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'ISBN',
            'Nome',
            'Editora',
            'Autores',
            'Preço',
        ];
    }

    public function map($livro): array
    {
        return [
            $livro->id,
            $livro->isbn,
            $livro->nome,
            $livro->editora ? $livro->editora->nome : '',
            $livro->autores->pluck('nome')->implode(', '),
            $livro->preco,
        ];
    }
}
