<?php 
namespace app\index\controller;

use app\index\model\Teacher as TeacherModel;
use think\Request;

class Teacher extends Base
{
	public function teacherList()
	{
		$teacher = TeacherModel::all();
		$count = TeacherModel::count();
		//遍历teacher表
		foreach ($teacher as $value)
		{
			$data = [
			'id' => $value->id, //主键
			'name' => $value->name, //姓名
			'degree' => $value->degree, //学历
			'school' => $value->school, //毕业学校
			'mobile' => $value->mobile, //手机号
			'hiredate' => $value->hiredate, //入职时间
			'status' => $value->status, //当前启用状态
			//关联grade访问grade表中数据
			'grade' => isset($value->grade->name)? $value->grade->name : "未分配"
			];
		    //每次关联查询结果，保存到数组
			$teacherList[] = $data;
		}
		$this->view->assign('teacherList',$teacherList);
		$this->view->assign('count',$count);
		$this->view->assign('title','编辑班级');
		$this->view->assign('keywords','php.cn');
		$this->view->assign('desc','PHP中文网ThinkPHP5开发实战课程');
		return $this->view->fetch('teacher_list');
	}
	//教师状态更改
	public function setStatus(Request $request)
	{
		$teacher_id = $request->param('id');
		$result = TeacherModel::get($teacher_id);
		if($result->getData('status') == 1)
		{
			TeacherModel::update(['status'=>0],['id'=>$teacher_id]);
		} else {
			TeacherModel::update(['status'=>1],['id'=>$teacher_id]);
		}
	}
	//渲染教师编辑界面
	public function teacherEdit(Request $request)
	{
		$teacher_id = $request->param('id');
		$result = TeacherModel::get($teacher_id);
		//设置当前页面的Seo模板变量
		$this->view->assign('title','编辑教师信息');
		$this->view->assign('keywords','php.cn');
		$this->view->assign('desc','PHP中文网ThinkPHP5开发实战课程');
		$this->view->assign('teacher_info',$result);
		$this->view->assign('gradeList',\app\index\model\Grade::all());
		return $this->view->fetch('teacher_edit');
	}
	//修改教师信息
	public function edit(Request $request)
	{
		//从提交的表单排除关联字段teacher字段
		$data = $request->except('grade');
		//设置更新条件
		$condition = ['id'=>$data['id']];
		//更新当前记录
		$result = TeacherModel::update($data,$condition);
		//设置返回数据
		$status = 0;
		$message = '更新失败，请检查';
		if(true == $result)
		{
			$status = 1;
			$message = '恭喜，更新成功~~';
		}
		return ['status'=>$status,'message'=>$message];
	}
	//渲染教师添加界面
	public function teacherAdd()
	{
		$this->view->assign('title','添加班级');
		$this->view->assign('keywords','php.cn');
		$this->view->assign('desc','PHP中文网ThinkPHP5开发实战课程');
		$this->view->assign('gradeList',\app\index\model\Grade::all());
		return $this->view->fetch('teacher_add');
	}
	//添加老师
	public function add(Request $request)
	{
		$data = $request->param();
		$result = TeacherModel::create($data);
		$status = 0;
		$message = '添加失败，请检查';
		if(true == $result)
		{
			$status = 1;
			$message = '恭喜，添加成功~~';
		}
		return ['status'=>$status,'message'=>$message];
	}
	//软删除
	public function deleteTeacher(Request $request)
	{
		$teacher_id = $request->param('id');
		TeacherMOdel::update(['is_delete'=>1],['id'=>$teacher_id]);
		TeacherModel::destroy($teacher_id);
	}
	//恢复
	public function unDelete()
	{
		$teacher = TeacherModel::onlyTrashed()->find();
    	$teacher->restore();
	}
}


 ?>