<?php
/**
 * Author: Lying
 * Data: 2016-11-02
 * description: 如果乱输控制器时候自动会走到这里  可以用于自定义404页面？
 */
class EmptyController extends Controller{
	public function index(){
		header("Content-type:text/html;charset=utf-8");
		echo "请勿输入非法信息";
	}
}
?>