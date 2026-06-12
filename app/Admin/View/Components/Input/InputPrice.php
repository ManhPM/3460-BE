<?php

namespace App\Admin\View\Components\Input;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class InputPrice extends Input
{
    public $required;
    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($required = false)
    {
        parent::__construct('text', $required);
    }

    public function isRequired(): array
    {
        return $this->required === true ? [
            'required' => true,
            'data-parsley-required-message' => __('msgValidateFieldEmpty')
        ] : [];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return Application|Factory|View
     */
    public function render(): View|Factory|Application
    {
        return view('components.input.price');
    }
}
