<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class AdminLayout extends Component
{
    /**
     * Dapatkan tampilan / konten yang merepresentasikan komponen.
     */
    public function render(): View
    {
        // Kita akan membuat file 'layouts.admin' ini selanjutnya
        return view('layouts.admin');
    }
}