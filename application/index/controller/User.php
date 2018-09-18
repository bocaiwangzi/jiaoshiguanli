<?php 
namespace app\index\controller;

// use app\index\Base;
use think\Request;
use think\facade\Session;
use think\Controller;
use app\index\model\User as UserModel;

class User extends Base
{
	public function login()
	{
		$this->view->assign('title','用户登录');
		$this->view->assign('keywords','php中文网');
		$this->view->assign('desc','php中文网Think5实战开发教学案例');
		$this->view->assign('copyRight','本案例仅供PHP中文网(www.php.cn)学员学习交流，请勿商用，责任自负');
		return $this->view->fetch();
	}
	//登录验证
	public function checkLogin(Request $request)
	{
		$status = 0;
		$result = '验证失败';
		$data = $request->param();

		//验证规则
		$rule = [
			'name|姓名' => 'require',
			'password|密码' => 'require',
			//'captcha|验证码' => 'require|captcha'
		];
		$result = $this->validate($data,$rule);
		if(true === $result)
		{
			$map = [
				'name' => $data['name'],
				'password' => md5($data['password'])
			];
			$user = userModel::get($map);
			if(null === $user)
			{
				$result = '没有该用户，请检查';
			} else {
				$status = 1;
				$result = '验证通过，点击[确定]后进入后台';
				Session::set('use_id',$user -> id);
				Session::set('user_info',$user -> getData());
				//更新用户登录次数：自增1
				$user ->setInc('login_count');
			}
		}
		return ['status'=>$status,'message'=>$result,'data'=>$data];		
	}
	//退出登录
	public function logout()
	{
		//退出前先更新登录时间字段，下次登录时就知道上次登录时间了
		userModel::update(['login_time'=>time()],['id'=>Session::get('user_id')]);
		Session::delete('user_id');
		Session::delete('user_info');
		$this->success('注销登录，正在返回',url('user/login'));
	}
	//管理员列表
	public function adminList()
	{
		$this->view->assign('title','管理员列表');
		$this->view->assign('keywords','PHP中文网教学系统');
		$this->view->assign('desc','教学案例');
		$this->view->count = UserModel::count();
		//判断当前是不是admin用户
		//先通过session获取用户登录名
		$userName = Session::get('user_info.name');
		if($userName == 'admin')
		{	
			//admin用户可以查看所有记录，数据要经过模型获取处理
			$list = UserModel::all();
		} else {
			$list = UserModel::all(['name'=>$userName]);
		}
		$this->view->assign('list',$list);
		//渲染管理员列表
		return $this->view->fetch('admin_list');
	}

	//管理员状态变更
	public function setStatus(Request $request)
    {
        $user_id = $request -> param('id');
        $result = UserModel::get($user_id);
        if($result->getData('status') == 1) {
            UserModel::update(['status'=>0],['id'=>$user_id]);
        } else {
            UserModel::update(['status'=>1],['id'=>$user_id]);
        }
    }

	//渲染编辑管理员界面
	public function adminEdit(Request $request)
	{
		$user_id=$request->param('id');
		$result=UserModel::get($user_id);
		$this->view->assign('title','编辑管理员界面');
		$this->view->assign('keywords','php.cn');
		$this->view->assign('desc','php中文网Think5实战开发教学案例');
		$this->view->assign('user_info',$result->getData());
		return $this->view->fetch('admin_edit');
	}

	//修改管理员信息
	public function edit(Request $request)
	{
		$data = $request->param();
		// foreach($param as $key=>$value)
		// {
		// 	if(!empty($value))
		// 	{
		// 		$data[$key] = $value;
		// 	}
		// }
		$condition=['id'=>$data['id']];
		$result=UserModel::update($data,$condition);
		if(Session::get('user_info.name')=='admin')
		{
			Session::set('user_info.role',$data['role']);
		}
		if(true == $result)
		{
			return ['status'=>1,'message'=>'更新成功'];
		} else {
			return ['status'=>0,'message'=>'更新失败，请检查'];
		}
	}

	//删除操作
	public function deleteUser(Request $request)
	{
		$user_id=$request->param('id');
		Usermodel::update(['is_delete'=>1],['id'=>$user_id]);
		UserModel::destroy($user_id);
	}

	//恢复删除
	public function unDelete()
	{
		$user = UserModel::onlyTrashed()->find();
    	$user->restore();
	}
}

 ?>