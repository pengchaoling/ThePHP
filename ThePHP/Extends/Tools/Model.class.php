<?php
/**
 * Author: Lying
 * Data: 2016-11-03
 * description: 模型基类	
 */
class Model{
	//静态成员变量，用于保存连接状态，如果已经有连接了就不再链接？ 这是单例模式？
	public static $link = NULL;
	//表名称
	protected $table = NULL;
	//初始化表信息
	private $opt;
	//保存一些数据库操作信息
	public static $sqls = array();

	public function __construct($table=NULL){
		//如果没传table进来 就用成员变量的，否则就操作传进来的这个表
		$this->table = is_null($table) ? C('DB_PREFIX').$this->table : C('DB_PREFIX').$table;	
		//链接数据库
		$this->_connect();
		//初始化sql信息
		$this->_opt();

	}
	/**
	 * 数据库连接函数
	 * @return [type] [description]
	 */
	private function _connect(){
		//如果没连接过
		if(is_null(self::$link)){
			$db = C('DB_DATABASE');
			if(empty($db))	halt('请先配置数据库');
			$link = new mysqli(C('DB_HOST'),C('DB_USER'),C('DB_PASSWORD'),C('DB_DATABASE'),C('DB_PORT'));
			//数据库链接错误
			if($link->connect_error) halt($link->connect_error);	
			//数据库编码
			$link->set_charset(C('DB_CHARSET'));
			self::$link = $link;
		}
	}
	/**
	 * 原生的sql查询方法
	 * @return [type] 关联结果集
	 */
	public function query($sql){
		self::$sqls[] = $sql;
		$link = self::$link;
		$result = $link->query($sql);
		//出错
		if($link->errno) halt('Mysql错误：'.$link->error.'<br>SQL: '.$sql);
		//把结果集组装成数组返回
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		//释放结果集
		$result->free();
		//执行完之后重置一下连贯操作的东西
		$this->_opt();
		return $rows;

	}
	/**
	 * 原生的sql 无结果集操作 也就是增删改
	 * @param  [type] $sql [description]
	 * @return [type]      [description]
	 */
	public function exe($sql){
		self::$sqls[] = $sql;
		$link = self::$link;
		$bool = $link->query($sql);
		//如果执行查询语句 会有结果集 $bool就是个对象
		if(is_object($bool)) halt('请使用query()方法来执行查询语句');

		if($bool){
			//如果是插入 则返回插入id  修改和删除 返回影响行数
			return $link->insert_id ? $link->insert_id : $link->affected_rows;
		}else{
			halt('Mysql错误：'.$link->error.'<br>SQL: '.$sql);
		}
	}
	/**
	 * 查询所有数据
	 * @return [type] [description]
	 */
	public function all(){
		$sql = 'SELECT '.$this->opt['filed'].' FROM '.$this->table.$this->opt['where'].$this->opt['group'].$this->opt['having'].$this->opt['order'].$this->opt['limit'];
		return $this->query($sql);
	}
	/**
	 * 查询所有数据 别名
	 * @return [type] [description]
	 */
	public function select(){
		return $this->all();
	}
	/**
	 * 获取一条数据
	 * @return [type] [description]
	 */
	public function find(){
		$result = $this->limit(1)->all();
		return current($result);
	}
	/**
	 * 删除操作
	 * @return [type] [description]
	 */
	public function delete(){
		if(empty($this->opt['where']))	halt('删除操作必须有where条件');
		$sql = 'DELETE FROM '.$this->table.$this->opt['where'];
		return $this->exe($sql);
	}
	/**
	 * 初始化sql信息
	 * @return [type] [description]
	 */
	private function _opt(){
		$this->opt = array(
			'filed'	=> '*',
			'where'	=> '',
			'group'	=> '',
			'having'=> '',
			'order'	=> '',
			'limit'	=> ''	
			);
	}
	/**
	 * 连贯操作field()
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function field($field){
		$this->opt['filed'] = $field;
		//连贯操作，返回一个对象
		return $this;
	}
	/**
	 * 连贯操作where()
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function where($where){
		$this->opt['where'] = ' WHERE '.$where;
		return $this;
	}
	/**
	 * 连贯操作 group()
	 * @param  [type] $group [description]
	 * @return [type]        [description]
	 */
	public function group($group){
		$this->opt['group'] = '	GROUP BY '.$group;
		return $this;
	}
	/**
	 * 连贯操作order()
	 * @param  [type] $order [description]
	 * @return [type]        [description]
	 */
	public function order($order){
		$this->opt['order'] = ' ORDER BY '.$order;
		return $this;
	}
	/**
	 * 连贯操作 having()
	 * @param  [type] $having [description]
	 * @return [type]         [description]
	 */
	public function having($having){
		$this->opt['having'] = ' HAVING '.$having;
		return $this;
	}
	/**
	 * 连贯操作limit()
	 * @param  [type] $limit [description]
	 * @return [type]        [description]
	 */
	public function limit($limit){
		$this->opt['limit'] = ' LIMIT '.$limit;
		return $this;
	}
	/**
	 * 字符串转义函数
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	private function _safe_str($str){
		//如果系统开启了自动转义 则反转义一下
		if(get_magic_quotes_gpc()){
			$str = stripcslashes($str);
		}
		//使用mysqli的转义
		return self::$link->real_escape_string($str);
	}
	/**
	 * 添加方法
	 * @param [type] $arr [description]
	 */
	public function add($arr=NULL){
		//如果用户传了arr就按用户的 否则就用POST
		$data = is_null($arr) ? $_POST : $arr;
		$fields = '';
		$values = '';
		foreach ($data as $k => $v) {
			$fields .= '`' . $this->_safe_str($k) .'`,';
			$values .= "'" . $this->_safe_str($v) . "',";
		}
		$fields = trim($fields,',');
		$values = trim($values,',');

		$sql = 'INSERT INTO '.$this->table.' ('.$fields.') '.'value('.$values.')';
		return $this->exe($sql);
	}
	/**
	 * 更新方法
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function save($arr){
		if(empty($this->opt['where'])) halt('更新操作必须有where条件');
		$data = is_null($arr) ? $_POST : $arr;
		$values = '';
		//链接出中间那部分
		foreach ($data as $k => $v) {
			$values .= '`'.$this->_safe_str($k).'`='."'".$this->_safe_str($v)."',";
		}
		$values = trim($values,',');
		$sql = 'UPDATE '.$this->table.' SET '.$values.$this->opt['where'];
		
		return $this->exe($sql);

	}
}
?>