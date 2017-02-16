<?php
/**
 * Author: Lying
 * Data: 2016-11-01
 * description: 可以理解为应用核心类 tp里面也有一个app类
 */
final class Application{
	/**
	 * run方法
	 * @return [type] [description]
	 */
	public static function run(){
		self::_init();											//初始化操作
		register_shutdown_function(array(__CLASS__,'fatal_error'));//致命错误
		set_error_handler(array(__CLASS__,'_error'));			//自定义错误处理
		self::_user_import();									//加载用户的文件
		self::_set_url();										//设置外部路径
		spl_autoload_register(array(__CLASS__,'_autoload'));	//自动加载
		self::_create_demo();									//创建demo的
		self::_app_run();										//控制器跑起来
	}
	/**
	 * 捕获致命错误 出错时或者执行完会调用到
	 * @return [type] [description]
	 */
	public static function fatal_error(){
		$e = error_get_last();
		if(!empty($e))
			self::_error($e['type'],$e['message'],$e['file'],$e['line']);
	}
	/**
	 * 自定义的 php错误处理函数 把那些系统错误拿过来重新操作
	 * @param  [type] $errno   错误类型
	 * @param  [type] $errstr  错误信息
	 * @param  [type] $errfile 错误所在文件
	 * @param  [type] $errline 错误所在行
	 * @return [type]          [description]
	 */
	public static function _error($errno, $errstr, $errfile, $errline){
		switch ($errno) {
			case    E_ERROR:
			case    E_USER_ERROR:
			case    E_CORE_ERROR:
			case    E_COMPILE_ERROR:
			case    E_PARSE:
				$msg = '致命的错误：'.$error.$errfile." 第{$errline}行";
				halt($msg);
				break;

			case 	E_STRICT:
			case 	E_USER_WARNING:
			case 	E_USER_NOTICE:
			//在break之前会一直执行下去
			default:
				if(DEBUG)
					include DATA_PATH.'/Tpl/error.tpl';
				break;
		}
	}
	/**
	 * 通过get参数实例化控制器   动态载入
	 * @return [type] [description]
	 */
	private static function _app_run(){
		$c = isset($_GET[C('VAR_CONTROLLER')])?$_GET[C('VAR_CONTROLLER')]:'Index';
		$a = isset($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:'index';
		define('CONTROLLER',$c);
		define('ACTION',$a);
		$c.= 'Controller';
		//判断用户输入的控制器是否存在，不存在则实例化空控制器
		if(class_exists($c)){
			$obj = new $c();
			if(!method_exists($obj, $a))	halt($c.'控制器中'.$a.'方法不存在');
			$obj->$a();
		}else{
			$obj = new EmptyController();
			$obj->$a();
		}
		
	}
	/**
	 * 自动加载函数 当类没有找到的时候会执行到这里
	 * @param  [type] $className 函数名
	 * @return [type]            [description]
	 */
	private static function _autoload($className){
		switch (true) {
			//判断是不是控制器  类名大于10  类名最后是个字符串是Controller
			case strlen($className)>10&&substr($className,-10)=='Controller':
				$path = APP_CONTROLLER_PATH . '/' . $className . '.class.php';
				if(!is_file($path)){
					//如果用户定义了空控制器，则先执行空控制器
					$emptyController = APP_CONTROLLER_PATH . '/EmptyController.class.php';
					if(is_file($emptyController)){
						include $emptyController;
						return;
					}else{
						halt($path.'控制器未找到');

					}
				} 
				include $path;
				break;
			//自动加载扩展模型类
			case strlen($className)>5&&substr($className,-5)=='Model':
				$path = COMMON_MODEL_PATH . '/' . $className .'.class.php';
				include($path);
				break;
			//自动加载工具库里面定义的类
			default:
				$path = TOOLS_PATH .'/' . $className .'.class.php';
				if(!is_file($path)) halt($path.'未找到类');
				include $path;
				break;
		}
	}		
	/**
	 * 初始化框架
	 * @return [type] [description]
	 */
	private static function _init(){
		//加载系统配置项
		C(include CONFIG_PATH . '/Config.php');
		$configStr= <<<str
<?php
return array(
	//'配置项 => '配置值',
);

?>
str;

		//公共配置项
		$commonPath = COMMON_CONFIG_PATH .'/Config.php';
		is_file($commonPath) || file_put_contents($commonPath,$configStr);
		C(include $commonPath);
		//用户配置项
		$userPath = APP_CONFIG_PATH . '/Config.php';

		//如果用户的配置文件不存在，那么就生成一个给他
		is_file($userPath)||file_put_contents($userPath,$configStr);
		C(include $userPath);

		//设置默认时区
		date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
		//session 自动开启
		C('SESSION_AUTO_START') & session_start();
	}
	/**
	 * 设置外部路径 带http的一些常量 比如引入外部资源的时候用到
	 */
	private static function _set_url(){
		$path ='http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
		$path = str_replace('\\','/',$path);

		define('__APP__',$path);
		define('__ROOT__',dirname(__APP__));

		define('__TPL__',__ROOT__ . '/' . APP_NAME . '/Tpl');
		define('__PUBLIC__',__TPL__ . '/Public');
	}
	/**
	 * 创建demo 默认给用户创建一个控制器吧
	 */
	private static function _create_demo(){
		$path = APP_CONTROLLER_PATH . '/IndexController.class.php';
		$content= <<<str
<?php
class indexController extends Controller{
	public function index(){
		header("Content-type: text/html; charset=utf-8"); 
		echo "欢迎来到ThePHP";
	}
}
?>
str;
		is_file($path)||file_put_contents($path,$content);
	}
	/**
	 * 引入common 下面的lib文件夹里面的文件（用户在配置文件里定义的）
	 * @return [type] [description]
	 */
	private static function _user_import(){
		$fileArr = C('AUTO_LOAD_FILE');
		if(is_array($fileArr)&&!empty($fileArr)){
			foreach ($fileArr as $v) {
				$path = COMMON_LIB_PATH . '/' .$v;
				is_file($path) & require_once $path;
			}
		}
	}

}//class end

?>