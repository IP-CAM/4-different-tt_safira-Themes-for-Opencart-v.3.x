<?php
namespace Vexsoluciones\Framework\Form;

class SelectEnableDisable extends SelectTwoOptions
{
    protected $textOptionOne = 'Disabled';
    protected $textOptionTwo = 'Enabled';

    public static function create($id, $value)
    {
        $object = new static();
        $object->id = $id;
        $object->name = $id;
        $object->initialValue = $value;

        return $object->doCreate();
    }
}

class SelectTwoOptions
{
    protected $textOptionOne = 'No';
    protected $textOptionTwo = 'Yes';

    protected $title = '';
    protected $id = '';
    protected $name = '';
    protected $initialValue = '';

    public function doCreate()
    {
        $op1 = $this->initialValue == '0' ? 'selected' : '';
        $op2 = $this->initialValue == '1' ? 'selected' : '';

        $options = '<option value="0" '.$op1.'>'.$this->textOptionOne.'</option>';
        $options .= '<option value="1" '.$op2.'>'.$this->textOptionTwo.'</option>';

        $str = '<div class="form-group">
					<label for="input_'.$this->id.'" class="col-sm-2 control-label">'.$this->id.'</label>
					<div class="col-sm-10">
					    <select name="active" id="input_'.$this->id.'" class="form-control">
					        '.$options.'
                        </select>
					</div>
				</div>';

        return $str;
    }
}