<?php

Class Query
{
	public $_database = null;
	public $_table = null;
	public $_where = [];

	public function where($column, $operator, $value = null)
	{
		if (empty($value)) {
			$value = $operator;
			$operator = '=';
		}

		$this->_where[] = [$column, $operator, $value];

		return $this;
	}

	public function orWhere($column, $operator, $value = null)
	{
		if (empty($value)) {
			$value = $operator;
			$operator = '=';
		}

		if (!empty($this->_where)) {
			// $this->_where = [$this->_where];
		}

		$this->_where[] = [[$column, $operator, $value]];

		return $this;

	}

	public function get()
	{
		dd($this->analyzeWhere());
		return $this;
	}

	/**
	 * @method 查询语句 sql + 预处理语句结果
	 * @date   2020-04-11
	 * @return array
	 */
	public function getQuery($sql = '', $params = [])
	{
		$returnData = [];
		if (empty($sql)) return $returnData;

		if (!empty($params)) {

			if ($stmt = $this->Db->prepare($sql)) {
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
		        	die('SQL 参数设置错误!');
		        }

			    $stmt->free_result();
			    $stmt->close();
			} else {
				die((getenv('APP_DEBUG') ? $sql : '') . ' SQL 错误!');
			}
		} else {
			if ($stmt = $this->Db->query($sql)) {
				// Cycle through results
				while ($row = $stmt->fetch_assoc()){
				 	$returnData[] = $row;
				}

				$stmt->free();

				return $returnData;
			}
		}

		return !empty($returnData) ? $returnData : true;
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

		foreach ($this->_where as $key => $value) {
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
						} else if (!empty($v[0]) && $tempCount == 2){ 
							//默认 = 条件的
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
			} else {
				$returnData['where'] .= ' AND '.$key.' = ?';
				$returnData['param'][] = $value;
			}
		}

		$returnData['where'] = trim(trim(trim($returnData['where']), 'AND'));

		return $returnData;
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