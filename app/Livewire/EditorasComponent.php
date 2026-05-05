<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Editora;
use Livewire\WithFileUploads;
use Illuminate\Pagination\LengthAwarePaginator;

class EditorasComponent extends Component
{
    use WithFileUploads;

    public $search = '';
    public $sortField = 'id';
    public $sortAsc = true;
    public $page = 1;

    public $editora_id, $nome, $logotipo, $new_logotipo;
    public $isModalOpen = false;

    public function render()
    {
        $all = Editora::all();

        if ($this->search) {
            $all = $all->filter(function($item) {
                return stripos($item->nome, $this->search) !== false;
            });
        }

        if ($this->sortAsc) {
            $all = $all->sortBy($this->sortField);
        } else {
            $all = $all->sortByDesc($this->sortField);
        }

        $perPage = 10;
        $items = $all->forPage($this->page, $perPage);
        $editoras = new LengthAwarePaginator($items, $all->count(), $perPage, $this->page);

        return view('livewire.editoras-component', ['editoras' => $editoras])
            ->layout('layouts.app');
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
        $this->editora_id = null;
        $this->nome = '';
        $this->logotipo = null;
        $this->new_logotipo = null;
    }

    public function store()
    {
        $this->validate([
            'nome' => 'required',
            'new_logotipo' => 'nullable|image|max:2048',
        ]);

        $path = $this->logotipo;
        if ($this->new_logotipo) {
            $path = $this->new_logotipo->store('logotipos', 'public');
        }

        Editora::updateOrCreate(['id' => $this->editora_id], [
            'nome' => $this->nome,
            'logotipo' => $path,
        ]);

        session()->flash('message', $this->editora_id ? 'Editora atualizada com sucesso.' : 'Editora criada com sucesso.');
        $this->closeModalPopover();
        $this->resetCreateForm();
    }

    public function edit($id)
    {
        $editora = Editora::findOrFail($id);
        $this->editora_id = $id;
        $this->nome = $editora->nome;
        $this->logotipo = $editora->logotipo;
        $this->openModalPopover();
    }

    public function delete($id)
    {
        Editora::find($id)->delete();
        session()->flash('message', 'Editora apagada com sucesso.');
    }
}
