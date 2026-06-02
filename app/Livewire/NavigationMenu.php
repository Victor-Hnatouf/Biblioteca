<?php

namespace App\Livewire;

use Livewire\Component;

class NavigationMenu extends Component
{
    protected $listeners = ['cart-updated' => '$refresh'];

    public function render()
    {
        return view('navigation-menu');
    }
}
