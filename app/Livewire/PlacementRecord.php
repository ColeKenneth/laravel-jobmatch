<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Placement Records')]
class PlacementRecord extends Component
{
    public function render() : View
    {
        return view('livewire.placement-record');
    }
}
