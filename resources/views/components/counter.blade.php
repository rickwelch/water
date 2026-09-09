<?php

use Livewire\Component;


new class extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }
};
?>

<div>
    Hello from Livewire!
    <div style="text-align: center;">
        <button wire:click="increment">+</button>
        <h1>{{$count }}</h1>
    </div>
</div>
