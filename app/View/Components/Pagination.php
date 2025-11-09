<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Pagination extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $fechtedData;

    public function __construct($fechtedData)
    {
        //
        $this->fechtedData = $fechtedData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.pagination');
    }
}
