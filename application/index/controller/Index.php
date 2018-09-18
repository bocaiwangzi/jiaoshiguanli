<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
	public function index()
	{
		$this->view->assign('title','PHP中文网教学管理系统');
		return $this->view->fetch();
	}
}






  ?>