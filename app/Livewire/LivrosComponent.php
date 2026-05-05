<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Livewire\WithFileUploads;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LivrosExport;

class LivrosComponent extends Component
{
    use WithFileUploads;

    public $search = '';
    public $sortField = 'id';
    public $sortAsc = true;
    public $page = 1;

    public $livro_id, $isbn, $nome, $editora_id, $bibliografia, $imagem_capa, $new_imagem_capa, $preco;
    public $autores_selecionados = [];
    public $isModalOpen = false;

    public function render()
    {
        $all = Livro::with(['editora', 'autores'])->get();

        if ($this->search) {
            $all = $all->filter(function($item) {
                return stripos($item->nome, $this->search) !== false || stripos($item->isbn, $this->search) !== false;
            });
        }

        if ($this->sortAsc) {
            $all = $all->sortBy($this->sortField);
        } else {
            $all = $all->sortByDesc($this->sortField);
        }

        $perPage = 10;
        $items = $all->forPage($this->page, $perPage);
        $livros = new LengthAwarePaginator($items, $all->count(), $perPage, $this->page);

        return view('livewire.livros-component', [
            'livros' => $livros,
            'editoras_list' => Editora::all(),
            'autores_list' => Autor::all(),
        ])->layout('layouts.app');
    }

    public function setPage($page) { $this->page = $page; }
    public function previousPage() { $this->page--; }
    public function nextPage() { $this->page++; }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    public function create()
    {
        $this->resetCreateForm();
        $this->openModalPopover();
    }

    public function generateISBN()
    {
        $prefix = "978";
        $group = rand(0, 9);
        $publisher = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $title = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        $isbn = $prefix . "-" . $group . "-" . $publisher . "-" . $title . "-" . rand(0, 9);
        $this->isbn = $isbn;
    }

    public function openModalPopover()
    {
        $this->isModalOpen = true;
    }

    public function closeModalPopover()
    {
        $this->isModalOpen = false;
    }

    private function resetCreateForm()
    {
        $this->livro_id = null;
        $this->isbn = '';
        $this->nome = '';
        $this->editora_id = '';
        $this->bibliografia = '';
        $this->imagem_capa = null;
        $this->new_imagem_capa = null;
        $this->preco = '';
        $this->autores_selecionados = [];
    }

    public function store()
    {
        $this->validate([
            'isbn' => 'required',
            'nome' => 'required',
            'editora_id' => 'required|exists:editoras,id',
            'preco' => 'nullable|numeric',
            'new_imagem_capa' => 'nullable|image|max:2048',
        ]);

        $path = $this->imagem_capa;
        if ($this->new_imagem_capa) {
            $path = $this->new_imagem_capa->store('capas', 'public');
        }

        $livro = Livro::updateOrCreate(['id' => $this->livro_id], [
            'isbn' => $this->isbn,
            'nome' => $this->nome,
            'editora_id' => $this->editora_id,
            'bibliografia' => $this->bibliografia,
            'imagem_capa' => $path,
            'preco' => $this->preco,
        ]);

        $livro->autores()->sync($this->autores_selecionados);

        session()->flash('message', $this->livro_id ? 'Livro atualizado com sucesso.' : 'Livro criado com sucesso.');
        $this->closeModalPopover();
        $this->resetCreateForm();
    }

    public function edit($id)
    {
        $livro = Livro::with('autores')->findOrFail($id);
        $this->livro_id = $id;
        $this->isbn = $livro->isbn;
        $this->nome = $livro->nome;
        $this->editora_id = $livro->editora_id;
        $this->bibliografia = $livro->bibliografia;
        $this->imagem_capa = $livro->imagem_capa;
        $this->preco = $livro->preco;
        $this->autores_selecionados = $livro->autores->pluck('id')->toArray();
        $this->openModalPopover();
    }

    public function delete($id)
    {
        Livro::find($id)->delete();
        session()->flash('message', 'Livro apagado com sucesso.');
    }

    public function exportExcel()
    {
        return Excel::download(new LivrosExport, 'livros.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
