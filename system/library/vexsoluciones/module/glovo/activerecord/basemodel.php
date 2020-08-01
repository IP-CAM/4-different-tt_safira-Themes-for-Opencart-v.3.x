<?php
namespace Vexsoluciones\Module\Glovo\ActiveRecord;

use Codeigniter\Service;

class BaseModel
{
    const TABLE_NAME = '';
    const PRIMARY_KEY = '';

    public function __construct($data = [])
    {
    }

    /**
     * @param array $data
     * @return static
     */
    public static function create($data = [])
    {
        return new static($data);
    }

    /**
     * @param $id
     * @return static
     * @throws \Exception
     */
    public static function find($id)
    {
        return self::findByKeyValuePair(static::PRIMARY_KEY, $id);
    }

    /**
     * @param string $resultType
     * @return array
     */
    public static function findAll($resultType = 'object')
    {
        $result = Service::db()->order_by('order_id', 'DESC')
            ->get(static::TABLE_NAME)
            ->result(static::class);

        if ($resultType == 'array')
        {
            $data = [];
            /** @var Order $row */
            foreach ($result as $row)
            {
                $data[] = $row->toJSON();
            }
            return $data;
        }

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @return static
     * @throws \Exception
     */
    public static function findByKeyValuePair($key, $value)
    {
        $entity = Service::db()->get_where(static::TABLE_NAME, [$key => $value])->row(0, static::class);

        if (null === $entity)
            throw new \Exception("Entity with {$key}: {$value} not exists in DB.");

        return $entity;
    }

    public function save()
    {

    }
}