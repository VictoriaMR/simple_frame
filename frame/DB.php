<?php

class DB
{
	protected static $_query = null;

	function __construct(Query $query)
	{
		self::$_query = $query;
	}

	public static function getInstance($database = null) 
	{
		//检测类是否被实例化
		if (!(self::$_instance instanceof self)) {

			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function connection($conn = 'default')
	{
		self::$_query->_database = $conn;
		return self::$_query;
	}

	public static function table($table = '')
	{
		self::$_query->_table = $table;
		return $this->query;
	}

	/**
	 * @method 获取一行数据
	 * @author Victoria
	 * @date   2020-04-11
	 * @return array
	 */
	public function getOne($table, $where=[], $fileds=[])
	{
		if (empty($table)) return [];

		$whereArr = $this->analyzeWhere($where);

		$sql = sprintf('SELECT %s FROM %s WHERE %s LIMIT 0, 1', !empty($fileds) ? implode(',', $fileds) : '*', $table, $whereArr['where'] ?? '');

		$result = $this->query($sql, $whereArr['param']);
		
		return $result[0] ?? [];
	}

	/**
	 * @method 获取数据行
	 * @author Victoria
	 * @date   2020-04-13
	 * @return array
	 */
	public function getList($table, $where=[], $fileds=[], $page=[], $orderBy=[])
	{
		if (empty($table)) return [];

		$whereArr = $this->analyzeWhere($where);
		$orderBy = $this->analyzeOrderBy($orderBy);

		if (!empty($page)) {
			$pagesize = (int) $page['pagesize'];
			$page = (int) $page['page'];
			$sql = sprintf('SELECT %s FROM %s WHERE %s %s LIMIT %d, %d', !empty($fileds) ? implode(',', $fileds) : '*', $table, $whereArr['where'] ?? '', $orderBy, ($page-1)*$pagesize < 0 ? 0 : ($page-1)*$pagesize, $pagesize);
		} else {
			$sql = sprintf('SELECT %s FROM %s WHERE %s %s', !empty($fileds) ? implode(',', $fileds) : '*', $table, $whereArr['where'] ?? '', $orderBy);
		}

		$result = $this->query($sql, $whereArr['param']);
		
		return $result ?? [];
	}

	/**
	 * @method 更新
	 * @author Victoria
	 * @date   2020-04-13
	 * @return boolean
	 */
	public function update($table, $where, $data)
	{
		if (empty($table)) return false;

		$whereArr = $this->analyzeWhere($where);
		$data = $this->analyzeData($data);

		$sql = sprintf('UPDATE %s SET %s WHERE %s',$table, $data['data'] ?? '', $whereArr['where']);

		$result = $this->query($sql, array_merge($data['param'], $whereArr['param']));

		return true;
	}

	/**
	 * @method 新增 (返回主键ID)
	 * @author Victoria
	 * @date   2020-04-15
	 * @return 
	 */
	public function insertGetId($data = [])
	{
		$table = $this->table;

		$sql = sprintf('SELECT MAX(%s) max_id FROM %s', $this->primaryKey, $table);
		$result = $this->query($sql);
		$maxId = $result[0]['max_id'] ?? 0;
		$data[$this->primaryKey] = ++$maxId;

		$result = $this->insert($data);
		return $maxId;
	}

	/**
	 * @method 新增
	 * @author Victoria
	 * @date   2020-04-15
	 * @return 
	 */
	public function insert($data = [])
	{
		$table = $this->table;

		$sql = sprintf('INSERT INTO %s (%s) VALUES (%s) ', $table, implode(',', array_keys($data)), $this->analyzeMatch($data));

		$result = $this->query($sql, array_values($data));

		return $result;
	}

	/**
	 * @method 删除数据
	 * @author Victoria
	 * @date   2020-04-18
	 * @return boolean
	 */
	public function deleteData($idArr = [])
	{
		if (empty($idArr)) return false;

		$where = [[$this->primaryKey, 'in', $idArr]];

		$where = $this->analyzeWhere($where);

		$sql = sprintf('DELETE FROM %s WHERE %s', $this->table, $where['where']);

		$result = $this->query($sql, $where['param']);

		return $result;
	}

	

	protected function analyzeOrderBy($orderBy = [])
	{
		$str = '';
		if (empty($orderBy)) return $str;

		foreach ($orderBy as $key => $value) {
			if (count($value) == 2) {
				$str .= $value[0].' '.strtoupper($value[1]).', ';
			} else {
				$str .= $value[0].' DESC, ';
			}
		}

		return 'ORDER BY '.trim(trim(trim($str), ','));
	}

	protected function analyzeData($data = [])
	{
		$returnData = ['data'=>'', 'param' => []];
		if (empty($data)) return $returnData;

		foreach ($data as $key => $value) {
			$returnData['data'] .= ', '.$key.' = ?';
			$returnData['param'][] = $value;
		}

		$returnData['data'] = trim(trim(trim($returnData['data']), ','));

		return $returnData;
	}

	protected function analyzeWhere($where = [])
	{
		$returnData = ['where'=>'', 'param' => []];
		if (empty($where)) return ['where'=>'', 'param' => []];

		foreach ($where as $key => $value) {
			if (is_array($value)) {
				if (is_array($value[0])) { // OR 分组
					$tempOrStr = '';
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
							$returnData['param'][] = is_array($v[2]) ? implode(',', $v[2]) : $v[2];
						} else if (!empty($v[0]) && $tempCount == 2){ //默认 = 条件的
							$tempOrStr .= ' OR '.$v[0].' = ?';
							$returnData['param'][] = $v[1];
						} else { //键值对条件的
							foreach ($v as $kk => $vv) { 
								$tempOrStr .= ' OR '.$k.' = ?';
								$returnData['param'][] = $v;
							}
						}
					}
					$returnData['where'] .= ' AND ('.trim(trim(trim($tempOrStr), 'OR')).')';
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
								$returnData['where'] .= sprintf(' AND %s %s (%s)', $value[0], strtoupper($value[1]), $inStr);
								break;
							default:
								$returnData['where'] .= sprintf(' AND %s %s ?', $value[0], strtoupper($value[1]));
								break;
						}

						$returnData['param'] = array_merge($returnData['param'], is_array($value[2]) ? $value[2] : [$value[2]]);
					} else if (!empty($value[0]) && $tempCount == 2){ //默认 = 条件的
						$returnData['where'] .= ' AND '.$value[0].' = ?';
						$returnData['param'][] = $value[1];
					} else { //键值对条件的
						foreach ($value as $k => $v) { 
							$returnData['where'] .= ' AND '.$k.' = ?';
							$returnData['param'][] = $v;
						}
					}
				}
				$returnData['where'] .= $orStr;
			} else {
				$returnData['where'] .= ' AND '.$key.' = ?';
				$returnData['param'][] = $value;
			}
		}

		$returnData['where'] = trim(trim(trim($returnData['where']), 'AND'));

		return $returnData;
	}

	/**
	 * @method 解析 where 组合
	 * @author Victoria
	 * @date   2020-04-11
	 */
	protected function analyzeType($where = [])
	{
		if (empty($where)) return '';

		$typeStr = '';
		foreach ($where as $key => $value) {
			if (is_numeric($value)) $typeStr .= 'd';
			else $typeStr .= 's';
		}

		return $typeStr;
	}
}