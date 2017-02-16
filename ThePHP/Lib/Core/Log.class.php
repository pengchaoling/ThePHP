<?php
/**
 * Author: Lying
 * Data: 2016-11-01
 * description: 日志处理类
 */
class Log{
	/**
	 * 写入日志
	 * @param  [type]  $msg   错误信息
	 * @param  string  $level 错误级别
	 * @param  integer $type  [description]
	 * @param  [type]  $dest  日志存放路径
	 * @return [type]         [description]
	 */
	static public function write($msg,$level='ERROR',$type=3,$dest=NULL){
		if(!C('SAVE_LOG'))	return;		//没有开启保存日志
		if(is_null($dest)){
			$dest = LOG_PATH . '/' . date('Y-m-d') .'.log';
		}

		if(is_dir(LOG_PATH)) error_log("[TIME]: ".date('Y-m-d H:i:s')."{$level}:{$msg}\r\n",$type,$dest);
	}
}
?>