<?php
class indexController extends Controller{
	public function index(){
		header("Content-type: text/html; charset=utf-8"); 
		echo "欢迎来到ThePHP";

		$hello = 'helloworld';
		$this->assign('hello',$hello);

		$this->display('1.html');

		
	}
}
?>