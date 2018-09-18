<?php 
namespace app\admin\controller;

use think\Controller;
use think\facade\View;

class Index extends Controller
{
	public function index()
	{
		$data = \app\admin\model\User::paginate(5);
		$this->view->assign('data',$data);
		return $this->view->fetch();
	}
}

 ?>