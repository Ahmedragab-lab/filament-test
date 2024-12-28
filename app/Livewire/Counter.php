<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class Counter extends Component
{
    use WithFileUploads;
    public function render()
    {
        return view('livewire.counter');
    }
}
