<?php
namespace Vexsoluciones;

use Vexsoluciones\Module\BaseConstants;

/**
 * @property string $settingCode
 * @property array $error
 */
class AdminController extends \Controller
{
    /** @var BaseConstants */
    static $constants = null;

    protected $settingCode = '';
    protected $error = [];
    protected $myModel = null;
    protected $_code = '';

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('setting/setting');
    }

    public function init()
    {
        $constants = self::$constants;

        $this->load->model($constants::ADMIN_MAIN_MODEL_ROUTE);
        $this->myModel =& $this->{$constants::ADMIN_MAIN_MODEL_PREFIX};
        $this->settingCode = $constants::SETTINGS_CODE;

        $this->load->language($constants::ADMIN_MAIN_MODEL_ROUTE);
        $this->document->setTitle($this->language->get('heading_title'));
    }

    public function saveSettingValues(array $values)
    {
        foreach ($values as $key => $value)
            $this->saveSettingValue($key, $value);
    }

    public function saveSettingValue($key, $value)
    {
        $constants = self::$constants;
        $this->model_setting_setting->editSettingValue($constants::SETTINGS_CODE, $key, $value);
    }

    protected function isValidSubscription(&$data)
    {
        return true;

        $license = $this->getSettingValue('license');

        // -------------------------------------------------
        try {
            if (isset($this->request->post["{$this->_code}_license"]))
            {
                $license = $this->request->post["{$this->_code}_license"];
                $this->myModel->forceValidation($license);
            } else {
                $this->myModel->isValidSubscription($license);
            }
        } catch (\Exception $e) {
            $this->error['warning'] = $e->getMessage();

            static::loadBreadcrumbs($data);
            $this->setAdminFieldsForView($data, ['license']);
            $constants = self::$constants;
            $this->response->setOutput($this->load->view($constants::ADMIN_MAIN_MODEL_ROUTE . '_license', $data));
            return false;
        }

        $data['success_license'] = 'You have successfully activated your license.';

        return true;
    }

    protected function getSettingValue($key)
    {
        return $this->model_setting_setting->getSettingValue("{$this->_code}_{$key}");
    }

    /**
     * @deprecated
     * @param $data
     */
    public function setAdminFields(&$data)
    {
        $library = new \VexFormLibrary($this->registry);
        $library->init($this->settingCode, $this->error);

        $data['shipping_glovo_status'] = $library->getConfig('status');
        $data['shipping_glovo_test'] = $library->getConfig('test');
        $data['shipping_glovo_tax_class_id'] = $library->getConfig('tax_class_id');
        $data['shipping_glovo_geo_zone_id'] = $this->config->get('geo_zone_id');

        $data['field_glovo_api_key'] = $library->printInputTextField('api_key', true);
        $data['field_glovo_api_secret'] = $library->printInputTextField('api_secret', true);
        $data['field_glovo_sort_order'] = $library->printInputTextField('sort_order', true, 'hidden');
    }

    public function setAdminFieldsForView(&$data, array $fields)
    {
        $library = new \VexFormLibrary($this->registry);
        $library->init($this->settingCode, $this->error);

        foreach ($fields as $field)
        {
            $constants = self::$constants;
            $code = $constants::PREFIX_CODE;
            $data["field_{$code}_{$field}"] = $library->printInputTextField($field, true);
        }
    }

    public function setAdminHiddenFieldsForView(&$data, array $fields)
    {
        $library = new \VexFormLibrary($this->registry);
        $library->init($this->settingCode, $this->error);

        foreach ($fields as $field)
        {
            $constants = self::$constants;
            $code = $constants::PREFIX_CODE;
            $data["field_{$code}_{$field}"] = $library->printInputTextField($field, true, 'hidden');
        }
    }

    public function setAdminInputForView(&$data, array $fields)
    {
        $library = new \VexFormLibrary($this->registry);
        $library->init($this->settingCode, $this->error);
        $constants = self::$constants;
        $code = $constants::PREFIX_CODE;

        foreach ($fields as $field)
        {
            $str = '<input type="text" class="form-control" name="%s" id="%s" value="%s">';
            $data["input_{$code}_{$field}"] = sprintf($str, "shipping_{$code}_{$field}", "input_{$field}", $library->getConfig($field));
        }
    }

    public function addConfigValueForView(&$data, array $fields)
    {
        foreach ($fields as $field)
            $this->addSingleConfigValueForView($data, $field);
    }

    public function addSingleConfigValueForView(&$data, $key)
    {
        $data["{$this->settingCode}_{$key}"] = $this->model_setting_setting->getSettingValue("{$this->settingCode}_{$key}");
    }

    protected function hasPermission()
    {
        $constants = self::$constants;
        if (!$this->user->hasPermission('modify', $constants::ADMIN_MAIN_MODEL_ROUTE)) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }

        return true;
    }

    protected function addAlertMessage($type, $message = '')
    {
        $str = '<div class="alert alert-%s alert-dismissible"><i class="fa fa-exclamation-circle"></i> %s<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
        return sprintf($str, $type, $message);
    }

    // ========================================================================
    // ------------------------------------------------------------------------
    // ========================================================================

    public function install()
    {
        $constants = self::$constants;
        $this->_beforeEditSetting($constants::SETTINGS_FIELDS);
    }
    public function uninstall()
    {
        $constants = self::$constants;
        $this->_beforeEditSetting($constants::SETTINGS_FIELDS);
        $this->model_setting_setting->deleteSetting($constants::SETTINGS_CODE);
    }

    private function _beforeEditSetting(array $data, array $tmp = [])
    {
        $constants = self::$constants;
        $code = $constants::SETTINGS_CODE;
        foreach ($data as $key => $value)
            $tmp[$code . "_{$key}"] = $value;

        $this->model_setting_setting->editSetting($code, $tmp);
    }
}