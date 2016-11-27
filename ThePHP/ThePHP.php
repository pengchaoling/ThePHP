<?php
/**
 * Author: Lying
 * Data: 2016-10-31
 * description: ThePHP入口文件 核心类
 */
final class ThePHP{
	/**
	 * 核心类开始方法
	 */
	public static function start(){
		self::_set_const();			//定义常量
		defined('DEBUG') || define('DEBUG',FALSE);
		if(DEBUG){
			self::_create_dir();		//创建目录	
			self::_import_file();		//引入文件
		}else{
			error_reporting(0);
		}
		Application::run();			//执行应用类
	}
	/**
	 * 设置常量
	 */
	private static function _set_const(){
		$path = str_replace("\\",'/',__FILE__);  			//在win下的路径中的\替换成/
		define('THEPHP_PATH',dirname($path));				//ThePHP框架入口的路径
		define('CONFIG_PATH',THEPHP_PATH.'/Config');		//配置文件路径
		define('DATA_PATH',THEPHP_PATH.'/Data');			//数据文件路径
		define('LIB_PATH',THEPHP_PATH.'/Lib');				//库文件路径
		define('CORE_PATH',LIB_PATH.'/Core');				//核心文件路径
		define('FUNCTION_PATH',LIB_PATH.'/Function');		//功能文件路径
		define('EXTENDS_PATH',THEPHP_PATH.'/Extends');		//扩展存放路径
		define('TOOLS_PATH',EXTENDS_PATH.'/Tools');			//工具类路径
		define('ORG_PATH',EXTENDS_PATH.'/Org');				//扩展库 比如smarty
		
		define('ROOT_PATH',dirname(THEPHP_PATH));			//网站根目录
		define('TEMP_PATH',ROOT_PATH.'/Temp');				//缓存目录
		define('LOG_PATH',TEMP_PATH.'/Log');				//日志存放目录

		define('APP_PATH',ROOT_PATH.'/'.APP_NAME);			//项目的目录
		define('APP_CONFIG_PATH',APP_PATH.'/Config');		//项目的Config文件夹
		define('APP_CONTROLLER_PATH',APP_PATH.'/Controller');
		define('APP_TPL_PATH',APP_PATH.'/Tpl');
		define('APP_PUBLIC_PATH',APP_TPL_PATH.'/Public');	//项目公共文件夹
		define('THEPHP_VERSION','1.0');						//框架版本

		//创建公共
		define('COMMON_PATH',ROOT_PATH.'/Common');	
		define('COMMON_CONFIG_PATH',COMMON_PATH.'/Config');	//公共配置文件夹
		define('COMMON_LIB_PATH',COMMON_PATH.'/Lib');		//公共库文件夹
		define('COMMON_MODEL_PATH',COMMON_PATH.'/Model');	//公共模型文件夹
		//smarty编译目录
		define('APP_COMPLILE_PATH',TEMP_PATH.'/'.APP_NAME.'/Complile'); 
		//smarty 缓存目录
		define('APP_CACHE_PATH',TEMP_PATH.'/'.APP_NAME.'/Cache');
		// 定义当前请求的系统常量
        define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
        //IS_AJAX
        define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false);

	}
	/**
	 * 创建目录 主要是项目的基础目录
	 */
	private static function _create_dir(){
		$arr = array(
			APP_PATH,
			APP_CONFIG_PATH,
			APP_CONTROLLER_PATH,
			APP_TPL_PATH,
			APP_PUBLIC_PATH,
			TEMP_PATH,
			LOG_PATH,
			COMMON_PATH,
			COMMON_CONFIG_PATH,
			COMMON_LIB_PATH,
			COMMON_MODEL_PATH,
			APP_COMPLILE_PATH,
			APP_CACHE_PATH
			);
		foreach ($arr as $value) {
			is_dir($value) || mkdir($value,0777,true);
		}
		//复制dispatch_jump.tpl 即success或error提示模板到应用目录
		$path = APP_TPL_PATH . '/dispatch_jump.tpl';
		is_file($path)||copy(DATA_PATH.'/Tpl/dispatch_jump.tpl',$path);
	}
	/**
	 * 引入文件
	 */
	private static function _import_file(){
		$arr = array(
			FUNCTION_PATH . '/function.php',			//公共方法
			ORG_PATH . '/Smarty/Smarty.class.php',		//Smarty模板引擎
			CORE_PATH . '/SmartyView.class.php',
			CORE_PATH . '/Log.class.php',				//日志类
			CORE_PATH . '/Controller.class.php',		//控制器基类
			CORE_PATH . '/Application.class.php',		//app类
			);
		$str = "<?php\r\n";										
		//循环引入上面数组定义的文件
		foreach ($arr as $value) {
			$str .= trim(substr(file_get_contents($value),5,-2));
			require_once $value;
		}
		//把这些系统文件都放到一个文件里面取 非调试模式下直接调用这个文件
		file_put_contents(TEMP_PATH.'/~systemFiles.php',$str);
	}

}//class end

ThePHP::start();
?>