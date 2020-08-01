<?php
namespace Vexsoluciones\Framework;

/**
 * Class Service
 * @package Vexsoluciones\Framework
 * @deprecated
 */
class Service
{
    /**
     * @deprecated
     * @param $db
     * @return QueryBuilder
     */
    public static function queryBuilder($db)
    {
        return QueryBuilder::get_instance($db);
    }

    public static function db()
    {

    }
}

/**
 * @property \DB $db
 */
class QueryBuilder
{
    protected $db;

    private $sql = '';
    private $select = [];
    private $from = '';
    private $where = [];
    private $additional = '';

    private $temp = [
        'insert' => [
            'table' => '',
            'data' => []
        ]
    ];

    private $insert = ['fields' => []];

    public function __construct($db)
    {
        $this->db = $db;
    }

    public static function get_instance($db)
    {
        return new self($db);
    }

    public static function dateNow()
    {
        return date('Y-m-d h:i:s');
    }

    public function select($field, $as = '')
    {
        if ($as == '')
            $this->select[] = $field;
        else
            $this->select[] = "{$field} AS {$as}";

        return $this;
    }

    public function from($table, $as = '')
    {
        $table = '`' . DB_PREFIX . $table . '`';

        if ($as == '')
            $this->from = $table;
        else
            $this->from = "{$table} {$as}";

        return $this;
    }

    public function where($field, $value)
    {
        $this->where[] = "{$field} = {$value}";
        return $this;
    }

    public function orderBy($field, $order = 'ASC')
    {
        $this->additional = "ORDER BY {$field} {$order}";
        return $this;
    }

    public function insert($table)
    {
        $table = DB_PREFIX . $table;
        $fields = implode(', ', $this->insert['fields']);

        $sql = "INSERT INTO `{$table}` SET {$fields}";
        $this->insert['data'] = [];
        return $sql;
    }

    public function update($table)
    {
        $table = DB_PREFIX . $table;
        $fields = implode(', ', $this->insert['fields']);

        $sql = "UPDATE `{$table}` SET {$fields}";

        if (!empty($this->where))
            $sql .= ' WHERE ' . implode(' AND ', $this->where);

        $this->insert['data'] = [];
        $this->insert['fields'] = [];
        return $sql;
    }

    public function set($field, $value = null)
    {
        if (gettype($value) == 'string')
            $value = "'{$this->db->escape($value)}'";

        $this->insert['fields'][] = null === $value ? "{$field}" : "{$field} = {$value}";
        return $this;
    }

    public function output()
    {
        $this->sql = 'SELECT ' . implode(', ', $this->select) . " FROM {$this->from}";

        if (!empty($this->where))
        {
            $this->sql .= ' WHERE ' . implode(' AND ', $this->where);
        }

        if ($this->additional !== '')
            $this->sql = "{$this->sql} {$this->additional}";

        return $this->sql;
    }
}