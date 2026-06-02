<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use Illuminate\Http\Request;

class CarrinhoCanceladoController extends Controller
{
    public function cancelado($id)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $encomenda = Encomenda::with('items')->where('user_id', $user->id)->findOrFail($id);

        return view('carrinho.cancelado', [
            'encomenda' => $encomenda,
        ]);
    }
}
