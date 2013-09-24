<?php

class model_base
{
    protected $db = NULL;
    protected $id = 0;
    //protected $page = 0;
    protected $auth = false;
    //protected $key = '';
    
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    protected $filters = array();
    protected $filtersBase = array();
    protected $joins = array();
    protected $joinsBase = array();
    protected $aliases = array();
    protected $aliasesBase = array();
    protected $tableName = null; //Имя таблицы
    protected $alias = "";
    protected $keys = array();
    protected $primaryKey = false;
    protected $indexes = array();
    protected $uniques = array();
    protected $fields = array(); //Список полей в таблице
    protected $data = NULL;
    protected $lastInsertId = NULL;
    protected $orders = array();
    protected $ordersBase = array();
    protected $pageSize = 5;
    protected $currentPage = 1;

    
    public function __construct()
    {
        $this->db = db::getInstance();
    }

    public function setFilter($field, $op, $value)
    {
        $expression = "$field $op " . (is_array($value) ? implode(',', $value) : $value);

        unset($this->filters[$expression]);

        $this->filters[$expression] = array('field' => $field, 'op' => $op, 'value' => $value);

        return $this;
    }

    public function getAll()
    {
        list($query, $bindings) = $this->_formatGetAllQuery();

//        var_dump($query);
//        var_dump($bindings);
        $all = $this->db->fetchAll($query, $bindings, true);

        return $this->_postProcess($all);
    }

    public function getCount()
    {
        list($query, $bindings) = $this->_formatGetAllQuery(true);

        $all = $this->db->fetchOne($query, $bindings);

        return $all;
    }

    public function _postProcess(&$result)
    {
        foreach ($result as $key => $row) {
            foreach ($row as $field => $value) {
                if (preg_match('/([0-9a-z_]+)\[([0-9a-z_]+)\]/i', $field, $matches)) {
                    unset($result[$key][$field]);
                    $result[$key][$matches[1]][$matches[2]] = $value;
                }
            }
        }

        return $result;
    }

    protected function _formatGetAllQuery($count = false)
    {
        $bindings = array();
        $filterCopy = array();

        $joins = array_merge($this->joinsBase, $this->joins);
        $joinedFrom = array();
        $joinClauses = array();

        foreach ($joins as $key => $join) {
            if (!is_object($join['model'])) continue;
            $joinedFrom[] = $join['model']->_getSelectFields($join['alias'], $join['alias'], $join['fields']);
            $joinClauses[] = $this->_getJoinClause($join);
        }

        foreach ($this->filters as $key => $filter) {
            $field = $filter["field"];

            if (strpos($field, '.')) {
                list($alias, $field) = explode('.', $field, 2);
            } else {
                $alias = $this->alias;
            }

            if (is_array($filter['value']) && strtolower($filter['op']) == 'in') {
                $placeholderArray = array();
                foreach ($filter['value'] as $index => $value) {
                    $placeholder = $field . hashKey($key) . "_{$index}";
                    $bindings[":{$placeholder}"] = $value;
                    $placeholderArray[] = ":{$placeholder}";
                }
                $filterCopy[] = "`$alias`.`$field` IN (" . implode(',', $placeholderArray) . ")";
            } elseif (strtolower($filter['op']) == 'not null') {
                $filterCopy[] = "`$alias`.`$field` IS NOT NULL";
            } elseif (strtolower($filter['op']) == 'null') {
                $filterCopy[] = "`$alias`.`$field` IS NULL";
            } else {
                $placeholder = $field . hashKey($key);
                $bindings[":{$placeholder}"] = $filter["value"];
                $filterCopy[] = "`$alias`.`$field` {$filter["op"]} :{$placeholder}";
            }
        }

        if (count($filterCopy) > 0)
            $conditions = " WHERE " . implode(" AND ", $filterCopy);
        else
            $conditions = "";

        $joinedFrom = ($joinedFrom) ? ("\n," . implode("\n,", $joinedFrom)) : "";

        if ($count) {
            $query = "SELECT COUNT(*) AS `count` FROM `$this->tableName` AS `$this->alias`";
        } else {
            $query = "SELECT " . $this->_getSelectFields() . " $joinedFrom FROM `$this->tableName` AS `$this->alias`";
        }

        if ($joinClauses) $query .= "\n" . implode("\n", $joinClauses);
        $query .= $conditions;

        if (!$count) {
            $orders = array_merge($this->ordersBase, $this->orders);

            foreach ($orders as $key => $order) {
                if (strpos('.', $key)) {
                    list($alias, $field) = explode('.', $key);
                } else {
                    $alias = $this->alias;
                    $field = $key;
                }
                $orders[$key] = "`$alias`.`$field` $order";
            }

            if ($orders) {
                $query .= ' ORDER BY ' . implode(',', $orders);
            }

            if ($this->pageSize && $this->currentPage) {
                $query .= (' LIMIT ' . ((intval($this->currentPage) - 1) * intval($this->pageSize)) . ',' . intval($this->pageSize));
            }
        }
        
        return array($query, $bindings);
    }

    protected function _getJoinClause($join)
    {
        $clause = "{$join['type']} JOIN ";
        /* @var $model basicModel */
        $model = $join['model'];
        $clause .= "`" . $model->getTableName() . "` AS `{$join['alias']}` ON ";
        $clause .= $this->_joinConditionClause($join['conditions'], $join['alias']);

        return $clause;
    }

    public function resetFilters()
    {
        $this->filters = $this->filtersBase;
        return $this;
    }

    public function resetOrders()
    {
        $this->orders = $this->ordersBase;
        return $this;
    }

    public function setOrder($field, $order = self::ORDER_ASC)
    {
        $this->orders[$field] = $order;
        return $this;
    }

    public function getLastInsertId()
    {
        return ($this->lastInsertId) ? $this->lastInsertId : $this->db->lastInsertId();
    }

    /**
     * Returns fields list for select query
     *
     * @param string $prefix will be used in fields list names after AS
     * @param string $alias will be used for fields list as table alias
     * @param mixed $whichfields which fields to add. NULL - for all model fields
     * @return string
     */

    protected function _getSelectFields($prefix = "", $alias = NULL, $whichfields = NULL)
    {
        $fields = array();
        if (!$alias) $alias = $this->alias;
        foreach ($this->fields as $field) {
            //$name = $field->getName();
            $name = $field;
            if (is_array($whichfields) && !in_array($name, $whichfields)) {
                continue;
            }
            $fields[] = "`$alias`.`$name` AS `" . (($prefix != "") ? ($prefix . "[") : "") . $name . (($prefix != "")
                ? "]" : "") . "`";
        }

        return ($fields) ? implode(", ", $fields) : "*";
    }

    /**
     * Add join to all select queries
     *
     * @param basicModel|string $dataObject
     * @param array|bool|string $cond
     * @param bool|string $alias
     * @param array $whichfields
     * @return basicModel
     *
     * @see addJoin()
     */
    public function addLeftJoin($dataObject, $cond = false, $alias = false, $whichfields = NULL)
    {
        return $this->addJoin($dataObject, $cond, $alias, $whichfields, "LEFT");
    }

    /**
     * Add join to all select queries
     *
     * @param basicModel|string $dataObject
     * @param array|bool|string $condition
     * @param bool|string $alias
     * @param array $whichFields
     * @param string $type join type
     * @return basicModel
     */
    public function addJoin($dataObject, $condition = false, $alias = false, $whichFields = NULL, $type = "INNER")
    {
        if (is_string($dataObject))
            $dataObject = new $dataObject();
        if ($alias === false || isset($this->aliases[$alias])) {
            $i = 1;
            $dataObjectName = $dataObject->getTableName();
            do {
                $alias = substr($dataObjectName, 0, $i++);
            } while (isset($this->aliases[$alias]) && $i < strlen($dataObjectName));
            $i = 2;
            while (isset($this->aliases[$alias]))
                $alias = $dataObjectName . ($i++);
        }
        $this->aliases[$alias] = true;

        $do = clone($dataObject);
        $do->joins = array();
        $this->joins[] = array("model" => $do, "conditions" => $condition, "alias" => $alias, "fields" => $whichFields, "type" => $type);

        $index = count($this->joins) - 1;
        foreach ($dataObject->joins as $j) {
            $joinAlias = $j["alias"] = $alias . "_" . $j["alias"];
            $condition = $j["conditions"];
            // make sure that the joins of this dataobject join to the
            // joined dataobject and not to this dataobject, snap je
            if (is_string($condition)) {
                $condition = array($alias . "." . $condition => $joinAlias . "." . $condition);
            } else {
                // array
                foreach ($condition as $here => $there) {
                    $k = $here;
                    if (is_numeric($here))
                        $here = $there;

                    if (!preg_match("~^'~", $here))
                        if (preg_match("~\.~", $here))
                            $here = $alias . "_" . $here;
                        else
                            $here = $alias . "." . $here;

                    if (!preg_match("~^'~", $there))
                        if (preg_match("~\.~", $there))
                            $there = $joinAlias . "_" . $there;
                        else
                            $there = $joinAlias . "." . $there;

                    unset($condition[$k]);
                    $condition[$here] = $there;
                }
            }
            $j["conditions"] = $condition;
            $this->joins[] = $j;
            if (is_numeric($index))
                $index = array($index);
            $index[] = count($this->joins) - 1;
        }

        return $this;
    }

    public function removeJoin($index)
    {
        if (!is_array($index))
            $index = array($index);
        foreach ($index as $i)
            if (!empty($this->joins[$i])) {
                unset($this->aliases[$this->joins[$i][2]]);
                unset($this->joins[$i]);
            }
    }

    public function resetJoins()
    {
        $this->joins = $this->joinsBase;
        $this->aliases = array_merge($this->aliasesBase, array($this->alias => true));
        return $this;
    }

    protected function _joinConditionClause($cond, $alias)
    {
        if (is_string($cond) && preg_match("!^[\w_]+$!", $cond)) {
            // single field: implies corresponding keys
            $x[] = "(" . $this->alias . ".$cond = $alias.$cond)";
        } elseif (is_string($cond)) {
            $x[] = "($cond)";
        } elseif (is_array($cond)) {
            foreach ($cond as $here => $there) {
                if (is_numeric($here))
                    $here = $there;
                if ($here{0} != "'" && strpos($here, ".") === false)
                    $here = "`{$this->alias}`.`$here`";
                if ($there{0} != "'" && strpos($there, ".") === false)
                    $there = "`$alias`.`$there`";
                $x[] = "($here = $there)";
                // $x[] = "(".$this->alias.".$here = $alias.$there)";
            }
        }
        return implode(" AND ", $x);
    }

    public function getOne($key)
    {
        $result = $this->db->fetchOne("SELECT `{$this->alias}`.*
                                     FROM `{$this->tableName}` AS `{$this->alias}`
                                     WHERE `{$this->alias}`.`{$this->primaryKey}`=:pkey", array(':pkey' => $key), true);

        if (is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }

    public function assign ($var, $value)
    {
        $this->$var = $value;
    }
    
}