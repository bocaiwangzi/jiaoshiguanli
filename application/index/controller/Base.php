<?php 
namespace app\index\controller;

use think\Controller;
use think\Session;

class Base extends Controller
{
	protected function _initialize()
	{
		parent::_initialize();
		define('USER_ID',Session::has('user_id')?Session::get('user_id'):null);
	}

	//判断用户是否登录，放在系统后台入口前面：index/index
	protected function isLogin()
	{
		if(is_null(USER_ID))
		{
			$this->error('用户未登录，无权访问',url('user/login'));
		}
	}

	//防止重复登录，放在登录操作前面：user/login
	protected function alreadyLogin()
	{
		if(USER_ID)
		{
			$this->error('用户已经登录，请勿重复登录',url('index/index'));
		}
	}
}


 ?>