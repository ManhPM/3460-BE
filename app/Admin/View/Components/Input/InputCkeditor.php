<?php

namespace App\Admin\View\Components\Input;

use Illuminate\View\Component;

class InputCkeditor extends Component
{
    public $name;
    public $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.input.ckeditor', [
            'name' => $this->name,
            'value' => $this->value,
        ]);
    }
}
