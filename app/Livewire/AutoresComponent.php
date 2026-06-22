<?php
namespace App\Livewire;
use Livewire\Component;
use App\Models\Autor;
use Livewire\WithFileUploads;
use Illuminate\Pagination\LengthAwarePaginator;
class AutoresComponent extends Component
{
    use WithFileUploads;
    public $search = '';
    public $sortField = 'id';
    public $sortAsc = true;
    public $page = 1;
    public $autor_id, $nome, $foto, $new_foto;
    public $isModalOpen = false;
    public function render()
    {
        $all = Autor::all();
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
        $autores = new LengthAwarePaginator($items, $all->count(), $perPage, $this->page);
        return view('livewire.autores-component', ['autores' => $autores])
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
        $this->autor_id = null;
        $this->nome = '';
        $this->foto = null;
        $this->new_foto = null;
    }
    public function store()
    {
        $this->validate([
            'nome' => 'required',
            'new_foto' => 'nullable|image|max:2048',
        ]);
        $path = $this->foto;
        if ($this->new_foto) {
            $path = $this->new_foto->store('fotos', 'public');
        }
        Autor::updateOrCreate(['id' => $this->autor_id], [
            'nome' => $this->nome,
            'foto' => $path,
        ]);
        session()->flash('message', $this->autor_id ? 'Autor atualizado com sucesso.' : 'Autor criado com sucesso.');
        $this->closeModalPopover();
        $this->resetCreateForm();
    }
    public function edit($id)
    {
        $autor = Autor::findOrFail($id);
        $this->autor_id = $id;
        $this->nome = $autor->nome;
        $this->foto = $autor->foto;
        $this->openModalPopover();
    }
    public function delete($id)
    {
        Autor::find($id)->delete();
        session()->flash('message', 'Autor apagado com sucesso.');
    }
}
