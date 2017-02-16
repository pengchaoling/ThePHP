<?php
/**
 * Author: Lying
 * Data: 2016-11-01
 * description: 核心方法库
 */

/**
 * 打印函数
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
function p($arr){
	if(is_bool($arr))
		var_dump($arr);
	else if(is_null($arr))
		var_dump(NULL);
	else{
		echo '<pre style="padding:10px;border-radius:5px;background:#f5f5f5;border:1px solid #ccc;font-size:14px;">';
		print_r($arr);
		echo '</pre>';
	}
}
/**
 * 读取和设置配置函数
 * @param [type] $var   配置项
 * @param [type] $value 值
 * 用法：
 * 		1、C('CODE_LEN');                      加载配置项
 * 		2、C('CODE_LEN',6);                    临时配置
 * 		3、C($userConfig),C($systemConfig);    读取配置项
 */
function C($var = NULL,$value = NULL){
	static $config = array();		//静态变量，下次执行这个函数时这个还会保存着
	//读取配置项
	if(is_array($var)){
		//两个数组合并 实现用户的配置项优先级
		$config = array_merge($config,array_change_key_case($var,CASE_UPPER));
		return;
	}
	//读取配置项或者临时配置
	if(is_string($var)){
		$str = strtoupper($var);	//字符串转换为大写
		//临时配置
		if(!is_null($value)){		
			$config[$var] = $value;
		}
		//这个配置项是否存在，如果存在就返回 否则为空
		return isset($config[$var])?$config[$var]:null;
	}
	//两个参数都为空 则返回所有东西
	if(is_null($var)&&is_null($value)){
		return $config;
	}
	return null;	//避免非法传参
}
/**
 * 跳转函数
 * @param  [type]  $url  跳转地址
 * @param  integer $time 等等事件
 * @param  string  $msg  提示信息
 * @return [type]        [description]
 */
function go($url,$time=0,$msg=''){
	//检查是否已经发送登陆http头部，如果已发送则不能发了	
	if(!headers_sent()){
		$time==0 ? header('Location:'.$url) : header("refresh:{$time};url={$url}");
		die($msg);
	}else{
		echo "<meta http-equiv=refresh content='{$time}; url={$url}'>"; 
		if($time) die($msg);
	}

}
/**
 * 错误信息保存提示和页面trace
 * @param  [type]  $error 错误信息 可能是数组或者字符串
 * @param  string  $level 错误等级
 * @param  integer $type  用于日志保存函数
 * @param  [type]  $dest  日志保存地址
 * @return [type]         [description]
 */
function halt($error,$level='ERROR',$type=3,$dest=NULL){
	//把错误信息写入日志里
	if(is_array($error)){
		Log::write($error['message'],$level,$type,$dest);
	}else{
		Log::write($error,$level,$type,$dest);
	}
	$e = array();
	if(DEBUG){
		if(is_array($error)){
			$e = $error;
		}else{
			$trace = debug_backtrace();
			$e['message'] = $error;
			$e['file'] = $trace[0]['file'];
			$e['line'] = $trace[0]['line'];
			$e['class'] = isset($trace[0]['class'])?$trace[0]['class'] : '';
			$e['function'] = isset($trace[0]['function'])?$trace[0]['function'] : '';
			//开启缓冲区
			ob_start();
			debug_print_backtrace();
			//获取缓冲区的内容
			$e['trace'] = htmlspecialchars(ob_get_clean());
			ob_end_clean();
		}
	}else{
		$url = C('ERROR_URL');
		if($url) go($url);
		else $e['message'] = C('ERROR_MSG');
	}
	include DATA_PATH . '/Tpl/halt.tpl';
	die;
}
/**
 * 打印出所有已经定义的常量
 * @return [type] [description]
 */
function print_const(){
	$const = get_defined_constants(true);
	p($const['user']);
}
/**
 * 实例化模型类
 * @param [type] $table [description]
 */
function M($table=NULL){
	$obj = new Model($table);
	return $obj;
}
?>