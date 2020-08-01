<?php
namespace Vexsoluciones\Module\Glovo\Model;

use Codeigniter\Service;

class WorkingArea
{
    const TABLE_NAME = 'vex_glovo_working_areas';

    private $id = null;
    public $code;
    public $polygons = [];
    public $workingTimes;

    public function __construct($data)
    {
        $this->id = $data->id;
        $this->code = $data->code;
        $this->workingTimes = json_decode($data->working_times);

        $this->preparePolygons(json_decode($data->polygons));
    }

    private function preparePolygons($encodedPolygons)
    {
        include_once DIR_APPLICATION . 'model/extension/module/emcconville/google-map-polyline-encoding-tool/src/Polyline.php';

        foreach ($encodedPolygons as $polygon)
            $this->polygons[] = \Polyline::pair(\Polyline::decode($polygon));

    }

    public static function createNewAndSave($data)
    {
        $db = Service::db();
        $db->insert(self::TABLE_NAME, [
            'id' => null,
            'code' => $data->code,
            'polygons' => json_encode($data->polygons),
            'working_times' => json_encode($data->workingTimes)
        ]);
    }

    public static function loadByCode($code)
    {
        $db = Service::db();
        $result = $db->get_where(self::TABLE_NAME, ['code' => $code])->row();

        if (null === $result)
        {
            throw new \Exception("Working area with code {$code} doesn't exists.");
        }

        return new static($result);
    }
}