<?php

namespace frame;

Class Query
{
	private static $_instance = null;
	public $_db = null;
	public $_database = null;
	public $_table = null;
	protected $_columns = null;
	protected $_where = [];
	protected $_whereStr = '';
	protected $_param = [];
	protected $_groupBy = '';
	protected $_orderBy = '';
	protected $_offset = null;
	protected $_limit = null;

	public static function getInstance() 
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function table($table = '')
	{
		if (empty($table))
			throw new \Exception('MySQL SELECT QUERY, table can not be empty!', 1);

		$this->_table = $table;

		return $this;
	}

	public function where($column, $operator, $value = null)
	{
		if (empty($value)) {
			$value = $operator;
			$operator = '=';
		}

		$this->_where[] = [$column, $operator, $value];

		return $this;
	}

	public function whereIn($column, $value = []) 
	{
		return $this->where($column, 'IN', $value);
	}

	public function orWhere($column, $operator, $value = null)
	{
		if (empty($value)) {
			$value = $operator;
			$operator = '=';
		}

		$this->_where[] = [[$column, $operator, $value]];

		return $this;
	}

	public function select($columns = ['*'])
	{
		$this->columns = [];

        $columns = is_array($columns) ? $columns : func_get_args();

        $this->columns = $columns;

        return $this;
	}

	public function groupBy($value = '')
	{
		$this->_groupBy = $value;

		return $this;
	}

	public function orderBy($value = '', $type = 'DESC')
	{
		$this->_orderBy = $value . ' ' . strtoupper($type);

		return $this;
	}

	public function limit($value)
    {
    	$value = (int) $value;
        $this->_limit = $value > 0 ? $value : 0;
        return $this;
    }

	public function offset($value)
    {
    	$value = (int) $value;
        $this->_offset = $value > 0 ? $value : 0;

        return $this;
    }

	public function get()
	{
		return $this->getResult();
	}

	public function find()
	{
		$this->_offset = 0;
		$this->_limit = 1;
		return $this->getResult()[0] ?? [];
	}

	public function count()
	{
		$this->_columns = ['COUNT(*) as count'];
		$this->_offset = 0;
		$this->_limit = 1;

		$result = $this->getResult();

		return $result[0]['count'] ?? 0;
	}

	public function getResult()
	{
		$sql = $this->getSql();
		return $this->getQuery($sql, $this->_param);
	}

	public function getSql()
	{
		if (empty($this->_table)) {
			throw new \Exception('MySQL SELECT QUERY, table not exist!', 1);
		}

		//解析条件
		$this->analyzeWhere();

		$sql = sprintf('SELECT %s FROM %s', !empty($this->_columns) ? implode(',', $this->_columns) : '*', $this->_table ?? '');

		if (!empty($this->_whereStr)) {
			$sql .= ' WHERE ' . $this->_whereStr;
		}

		if (!empty($this->_groupBy)) {
			$sql .= ' GROUP BY ' . $this->_groupBy;
		}

		if (!empty($this->_orderBy)) {
			$sql .= ' ORDER BY ' . $this->_orderBy;
		}

		if ($this->_offset !== null) {
			$sql .= ' LIMIT ' . (int) $this->_offset;
		}

		if ($this->_limit !== null ) {
			$sql .= ',' . (int) $this->_limit;
		}

		//清空储存数据
		$this->_columns = null;
		$this->_where = [];
		$this->_whereStr = '';
		$this->_param = [];
		$this->_groupBy = '';
		$this->_orderBy = '';
		$this->_offset = null;
		$this->_limit = null;

		return $sql;
	}

	/**
	 * @method 查询语句 sql + 预处理语句结果
	 * @date   2020-04-11
	 * @return array
	 */
	public function getQuery($sql = '', $params = [])
	{
		$conn = \frame\Connection::getInstance($this->_db, $this->_database);

		$returnData = [];
		if (empty($sql)) return $returnData;

		if (!empty($params)) {

			if ($stmt = $conn->prepare($sql)) {
				//这里是引用传递参数
			    if(is_array($params))
		        {
		        	if (!is_array(current($params))) {
		        		reset($params);
		        		$params = [$params];
		        	}

		        	foreach ($params as $key => $value) {
		        		$type = $this->analyzeType($value);
			            $bind_names[] = $type;
			            for ($i=0; $i < count($value); $i++) 
			            {
			                $bind_name = 'bind_' . $i;
			                $$bind_name = $value[$i];
			                $bind_names[] = &$$bind_name;
			            }

		            	call_user_func_array(array($stmt, 'bind_param'), $bind_names);

		            	/* execute query */
			    		$stmt->execute();
				        $meta = $stmt->result_metadata(); 

				        if ($meta->type) {
					        $variables = [];
					        $data = [];
					        while ($field = $meta->fetch_field()) { 
					            $variables[] = &$data[$field->name];
					        }

					        call_user_func_array(array($stmt, 'bind_result'), $variables); 

					        $i=0;
					        while($stmt->fetch())
					        {
					            $returnData[$i] = [];
					            foreach($data as $k => $v) {
					                $returnData[$i][$k] = $v;
					            }

					            $i++;
					        }
				        }
		        	}
		        } else {
		        	throw new \Exception('SQL 参数设置错误!');
		        }

			    $stmt->free_result();
			    $stmt->close();
			} else {
				throw new \Exception($sql . ' SQL 错误!');
			}
		} else {
			if ($stmt = $conn->query($sql)) {
				while ($row = $stmt->fetch_assoc()){
				 	$returnData[] = $row;
				}

				$stmt->free();

				return $returnData;
			}
		}

		return $returnData;
	}

	/**
	 * @method 参数匹配 ? 占位符
	 * @date   2020-05-19
	 * @return string
	 */
	private function analyzeMatch($data)
	{
		if (empty($data) || !is_array($data)) return '';
		$str = '';
		for ($i=0; $i < count($data); $i++) { 
			$str .= '? ,';
		}

		return trim(trim(trim($str), ','));
	}

	/**
	 * @method 处理 where 条件
	 * @date   2020-05-19
	 * @return array
	 */
	private function analyzeWhere()
	{
		$returnData = ['where'=>'', 'param' => []];
		if (empty($this->_where)) return ['where'=>'', 'param' => []];

		$orStatus = false;
		foreach ($this->_where as $key => $value) {
			$where = '';
			$param = [];
			if (is_array($value)) {
				if (is_array($value[0])) { // OR 分组
					$tempOrStr = '';
					$orStatus = true;
					foreach ($value as $k => $v) {
						$tempCount = count($v);
						if ($tempCount == 3) {
							switch (strtoupper($v[1])) {
								case 'IN':
									$tempOrStr .= sprintf(' OR %s %s (?)', $v[0], strtoupper($v[1]));
									break;
								default:
									$tempOrStr .= sprintf(' OR %s %s ?', $v[0], strtoupper($v[1]));
									break;
							}
							$param[] = is_array($v[2]) ? implode(',', $v[2]) : $v[2];
						} else if (!empty($v[0]) && $tempCount == 2){ 
							//默认 = 条件的
							$tempOrStr .= ' OR '.$v[0].' = ?';
							$param[] = $v[1];
						} else { //键值对条件的
							foreach ($v as $kk => $vv) { 
								$tempOrStr .= ' OR '.$k.' = ?';
								$param[] = $v;
							}
						}
					}

					$where = $tempOrStr;
				} else {
					$tempCount = count($value);
					if ($tempCount == 3) {
						switch (strtoupper($value[1])) {
							case 'IN':
								$inStr = '';
								foreach ($value[2] as $invalue) {
									$inStr .= ' ? ,';
								}
								$inStr = trim(trim($inStr, ','));
								$where .= sprintf(' AND %s %s (%s)', $value[0], strtoupper($value[1]), $inStr);
								break;
							default:
								$where .= sprintf(' AND %s %s ?', $value[0], strtoupper($value[1]));
								break;
						}

						$param = array_merge($param, is_array($value[2]) ? $value[2] : [$value[2]]);
					} else if (!empty($value[0]) && $tempCount == 2){ //默认 = 条件的
						$where .= ' AND '.$value[0].' = ?';
						$param[] = $value[1];
					} else { //键值对条件的
						foreach ($value as $k => $v) { 
							$where .= ' AND '.$k.' = ?';
							$param[] = $v;
						}
					}
				}
			} else {
				$where .= ' AND '.$key.' = ?';
				$param[] = $value;
			}

			if (empty($returnData['where']) && $orStatus) {
				$returnData['where'] = trim(trim(trim($where), 'OR'));
			} else if (!empty($returnData['where'])) {
				if ($orStatus) {
					$returnData['where'] = '('.trim(trim(trim($returnData['where']), 'AND')).')'.$where;
				} else {
					$returnData['where'] .= $where;
					$orStatus = false;
				}
			} else {
				$returnData['where'] .= $where;
				$orStatus = false;
			}

			$returnData['param'] = array_merge($returnData['param'] ?? [], $param);
		}

		$this->_whereStr = trim(trim(trim($returnData['where']), 'AND'));
		$this->_param = $returnData['param'] ?? [];

		return $this;
	}

	/**
	 * @method 解析 where 参数组合类型
	 * @date   2020-04-11
	 */
	private function analyzeType()
	{
		if (empty($this->_where)) return '';

		$typeStr = '';
		foreach ($this->_where as $key => $value) {
			if (is_numeric($value)) $typeStr .= 'd';
			else $typeStr .= 's';
		}

		return $typeStr;
	}
}