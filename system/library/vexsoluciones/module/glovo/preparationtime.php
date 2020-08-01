<?php
namespace Vexsoluciones\Module\Glovo;

use Codeigniter\Service;

class PreparationTime
{
    const ATTRIBUTE_TABLE = 'product_attribute';

    /** Used for check Preparation Time id from product_attribute table */
    private $attributeId = null;
    private $languageId;

    public function __construct($attributeId, $languageId)
    {
        $this->attributeId = $attributeId > 0 ? (int)$attributeId : null;
        $this->languageId = (int) $languageId;
    }

    public static function instance($attributeId, $languageId)
    {
        return new static($attributeId, $languageId);
    }

    /**
     * @deprecated
     * @param $productId
     * @return int
     */
    public function obtainFromProduct($productId)
    {
        $time = Service::db()->get_where(self::ATTRIBUTE_TABLE, [
            'product_id' => $productId,
            'language_id' => $this->languageId,
            'attribute_id' => $this->attributeId
        ])->row();

        return (int) $time->text;
    }

    public function obtainFromProducts($productIds = [])
    {
        $data = Service::db()
            ->where_in('product_id', $productIds)
            ->where('language_id', $this->languageId)
            ->where('attribute_id', $this->attributeId)
            ->get(self::ATTRIBUTE_TABLE)
            ->result();
        return $data;
    }

    public function calculateFromCartItems($items)
    {
        if (null === $this->attributeId) return 0;

        $time = 0;
        $productIds = [];

        foreach ($items as $item) $productIds[] = $item['product_id'];

        $values = $this->obtainFromProducts($productIds);
        foreach ($values as $value)
            $time = (int)$value->text > $time ? (int)$value->text : $time;

        return $time;
    }
}