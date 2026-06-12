<?php

namespace App\Views;

use Illuminate\View\Component;

class Label extends Component
{
    public $text;
    public $icon;
    public $required;
    public $class;
    public $for;

    /**
     * Create a new component instance.
     *
     * @param string $text
     * @param string $icon
     * @param bool $required
     * @param string $class
     * @param string $for
     */
    public function __construct($text = '', $icon = '', $required = false, $class = '', $for = '')
    {
        $this->text = $text;
        $this->icon = $icon;
        $this->required = $required;
        $this->class = $class;
        $this->for = $for;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.label');
    }
}
