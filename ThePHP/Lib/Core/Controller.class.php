<?php
/**
 * Author: Lying
 * Data: 2016-11-01
 * description: 控制器基类
 */
class Controller extends SmartyView{
	protected $var=array();	//用于保存assign过来的变量
	public function __construct(){
		//如果开启smarty
		if(C('SMARTY_ON'))	parent::__construct();			
		//给子类控制器使用的初始化函数
		if(method_exists($this,'__init')){	
			$this->__init();
		}
		//给子类控制器使用的初始化函数
		if(method_exists($this,'__load')){	
			$this->__load();
		}
	}
	/**
	 * 成功提示函数
	 * @param  [type] $msg  提示信息
	 * @param  [type] $url  跳转地址
	 * @param  [type] $time 等待时间
	 * @return [type]       [description]
	 */
	protected function success($success_msg,$url=NULL,$time=3){
		$url = $url ? $url : $_SERVER["HTTP_REFERER"]; 	//直接访问会为空
		include(APP_TPL_PATH . '/dispatch_jump.tpl');
	}
	/**
	 * 失败提示函数
	 * @param  [type]  $error_msg 提示信息
	 * @param  [type]  $url       跳转url
	 * @param  integer $time      等待时间
	 * @return [type]             [description]
	 */
	protected function error($error_msg,$url=NULL,$time=3){
		$url = $url ? $url : $_SERVER["HTTP_REFERER"]; 	//直接访问会为空
		include(APP_TPL_PATH . '/dispatch_jump.tpl');
	}
	/**
	 * 获取模板文件路径
	 * @param  [type] $tpl [description]
	 * @return [type]      [description]
	 */
	protected function get_tpl_path($tpl){
		if(is_null($tpl)){
			$path = APP_TPL_PATH . '/' .CONTROLLER .'/' .ACTION .'.html';
		}else{
			$suffix = strrchr($tpl, '.'); //从 . 后获取文件后缀
			$tpl = empty($suffix) ? $tpl.'.html' : $tpl;
			$path = APP_TPL_PATH . '/' . CONTROLLER .'/' .$tpl;
		}
		if(!is_file($path)) halt($path.'模板不存在');
		return $path;
	}
	/**
	 * 模板载入
	 * @param  [type] $tpl 自定义的模板
	 * @return [type]      [description]
	 */
	protected function display($tpl=NULL){
		$path = $this->get_tpl_path($tpl);
		//如果开启smarty则使用smarty的载入方法
		if(C('SMARTY_ON')){
			parent::display($path);
		}else{
			extract($this->var);	//将数组转成变量对应着值
			include $path;
		}		
	}
	protected function assign($var,$value){
		if(C('SMARTY_ON')){
			parent::assign($var,$value);
		}else{
			$this->var[$var] = $value;
		}
	}
}
?>