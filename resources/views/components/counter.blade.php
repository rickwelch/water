<?php

use Livewire\Component;


new class extends Component
{
    public $count = 0;

    public function mount($count = 0)
    {
        $this->count = $count;
    }

    public function increment()
    {
        $this->count++;
    }
};
?>

<div>
    Hello from a Livewire component!
    <div style="text-align: center;">
        <button wire:click="increment">+</button>
        <h1>{{$count }}</h1>
    </div>
</div>
