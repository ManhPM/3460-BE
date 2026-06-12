<?php

namespace App\Admin\View\Components\Input;

class InputBaseCheckbox extends Input
{
    public $checked;
    public $value;
    public function __construct(array $checked = [], $value = '', $type = 'text', $required = false)
    {
        //
        parent::__construct($type, $required);
        $this->value = $value;
        $this->checked = $checked;
    }
    public function isRequired()
    {
        return $this->required === true ? [
            'required' => true,
            'data-parsley-required-message' => __('Trường này không được bỏ trống.')
        ] : [];
    }
    public function isChecked($checked)
    {

        return in_array($this->value, $checked) ? 'checked' : '';
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input.base-checkbox');
    }
}
