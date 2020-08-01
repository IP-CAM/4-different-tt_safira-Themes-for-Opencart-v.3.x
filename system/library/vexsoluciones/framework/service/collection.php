<?php
namespace Vexsoluciones\Framework\Service;

class Collection
{
    public static function toJSON($entities)
    {
        $result = [];

        foreach ($entities as $entity)
            $result[] = $entity->toJSON();

        return $result;
    }
}