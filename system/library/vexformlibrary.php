<?php
class VexFormLibrary extends VexRegistryLibrary
{
    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    public function test()
    {
        return 'test';
    }

    public function printInputTextField($key, $required = false, $type = 'text')
    {
        $value = $this->getConfig($key);
        $error = '';

        if ($value == '' && $required)
            $error = self::showErrorWithHtml($this->language->get("error_{$key}"));

//        if ($this->type == 'error' && isset($this->error["error_{$key}"]))
//            $error = self::showErrorWithHtml($this->language->get("error_{$key}"));

        return self::printInputTextFieldHtml(
            $this->code,
            $key,
            $this->language->get("entry_{$key}"),
            $value,
            $required,
            $type,
            $error
        );
    }

    public static function showErrorWithHtml($text)
    {
        return '<div class="text-danger">'.$text.'</div>';
    }

    public static function printInputTextFieldHtml($moduleCode, $id, $label, $value, $required = false, $type = 'text', $error = '')
    {
        if ($type == 'hidden')
        {
            $text = '<input type="hidden" class="form-control" name="%s_%s" id="input_%s" value="%s">';
            return sprintf($text , $moduleCode, $id, $id, $value);
        }

        $text = '<div class="form-group %s">
					<label for="input_%s" class="col-sm-2 control-label">%s</label>
					<div class="col-sm-10">
						<input type="%s" class="form-control" name="%s_%s" id="input_%s" value="%s">
						%s
					</div>
				</div>';
        return sprintf($text,($required ? 'required' : '') , $id, $label, $type, $moduleCode, $id, $id, $value, $error);
    }
}

class VexFormLibraryOld
{
    private $error = [];
    private $configData = [];
    private $type = null;

    private $code;
    /** @var Language */
    private $language;

    public function __construct(string $code, $language)
    {
        $this->code = $code;
        $this->language = $language;
    }

    public function registry($config, $error)
    {
        $this->configData = $config;
        $this->error =& $error;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function printInputTextField($id, $required = false)
    {
        $value = $this->configData["{$this->code}_{$id}"];
        $error = '';

        if ($this->type == 'error' && isset($this->error["error_{$id}"]))
        {
            $error = self::showErrorWithHtml($this->language->get("error_{$id}"));
        }

        return self::printInputTextFieldHtml(
            $this->code,
            $id,
            $this->language->get("entry_{$id}"),
            $value,
            $required,
            $error
        );
    }

    public static function showErrorWithHtml($text)
    {
        return '<div class="text-danger">'.$text.'</div>';
    }

    public static function printInputTextFieldHtml($moduleCode, $id, $label, $value, $required = false, $error = '')
    {
        $text = '<div class="form-group required">
					<label for="input_%s" class="col-sm-2 control-label">%s</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="%s_%s" id="input_%s" value="%s">
						%s
					</div>
				</div>';
        return sprintf($text, $id, $label, $moduleCode, $id, $id, $value, $error);
    }
}