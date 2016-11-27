<?php
/**
 * Author: Lying
 * Data: 2016-11-05
 * description: Smarty 桥梁
 */
class SmartyView{
	//静态成员变量 用户保存实例化的smarty对象
	private static $smarty;	
	function __construct(){
		//已经实例化过了
		if(!is_null(self::$smarty)) return;
		$smarty = new Smarty();
		//模板目录
		$smarty->template_dir = APP_TPL_PATH .'/' . CONTROLLER .'/';
		//编译目录
		$smarty->compile_dir = APP_COMPLILE_PATH;
		//缓存目录
		$smarty->cache_dir = APP_CACHE_PATH;
		//定界符
		$smarty->left_delimiter = C('LEFT_DELIMITER');
		$smarty ->right_delimiter = C('RIGHT_DELIMITER');
		//是否开启缓存
		$smarty->caching = C('CACHE_ON');
		//缓存时间
		$smarty->cache_lifetime = C('CACHE_TIME');
		self::$smarty = $smarty;
	}
	/**
	 * smarty的 display方法
	 */
	protected function display($tpl){
		self::$smarty->display($tpl,$_SERVER['REQUEST_URI']);
	}
	/**
	 * smarty的 assign方法
	 */
	protected function assign($var,$value){
		self::$smarty->assign($var,$value);
	}
	/**
	 * 当前模板是否已有缓存
	 */
	protected function is_cached($tpl){
		if(!C('SMARTY_ON'))	halt('请先开启缓存');
		$path = $this->get_tpl_path($tpl);
		self::$smarty->is_cached($tpl,$_SERVER['REQUEST_URI']);
	}
}
?>