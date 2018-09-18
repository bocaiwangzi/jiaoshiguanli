<?php 
namespace app\index\controller;

use think\Request;
use app\index\model\Student as StudentModel;
use app\index\model\Grade as GradeModel;

class Student extends Base
{
	public function studentList()
	{
		//获取所有学生数量
		$studentList = StudentModel::paginate(5);
		//获取记录数量
		$count = StudentModel::count();
		//给结果集对象数组中的每个模板对象添加班组关联数据
		foreach ($studentList as $value)
		{
			$value -> grade = $value -> grade -> name;
		}	
		$this-> view -> assign('studentList',$studentList);
		$this-> view ->assign('count',$count);
		return $this-> view->fetch('student_list');
	}
	//学生状态变更
	public function setStatus(Request $request)
	{
		$student_id = $request -> param('id');
		$result = StudentModel::get($student_id);
		if($result->getData('status') == 1){
			StudentModel::update(['status'=>0],['id'=>$student_id]);
		} else {
			StudentModel::update(['status'=>1],['id'=>$student_id]);
		}
	}
	//更新学生信息
	public function doEdit(Request $request)
	{
		$data = $request -> param();
		//设置更新条件
		$condition = ['id' => $data['id']];
		//update必须有条件，否则不会执行
		$result = StudentModel::update($data,$condition);
		$status = 0;
		$message = '更新失败，请检查';
		if(true == $result)
		{
			$status = 1;
			$message = '恭喜，更新成功~~';
		}
		return ['status'=>$status,'message'=>$message];
	}
	//渲染学生添加界面
	public function studentEdit(Request $request)
	{
		$student_id = $request ->param('id');
		$result = StudentModel::get($student_id);
		//获取关联表:grade数据
		$result -> grade = $result -> grade->name;
		$this->view->assign('title','编辑班级');
		$this->view->assign('keywords','php.cn');
		$this->view->assign('desc','PHP中文网ThinkPHP5开发实战课程');
		$this->view->assign('student_info',$result);
		//将班级表中所有数据赋值给当前模板
		$this->view->assign('gradeList',\app\index\model\Grade::all());
		return $this->view->fetch('student_edit');
	}
	//更新学生信息
    public function Edit(Request $request)
    {
        //获取前端提交过来的表单数据,此处可以修改班级信息,请不要过滤过grade字段
        $data = $request -> param();


        //设置更新条件
        $condition = ['id'=>$data['id']];

        //更新当前记录,update必须要有条件,否则不会执行
        $result = StudentModel::update($data,$condition);

        //设置返回数据给前端ajax处理
        $status = 0;
        $message = '更新失败,请检查';

        //检测更新结果,将结果返回给student_edit模板中的ajax提交回调处理
        if (true == $result) {
            $status = 1;
            $message = '恭喜, 更新成功~~';
        }
        return ['status'=>$status, 'message'=>$message];
    }
    //添加学生
    public function studentAdd(){
    	$this->view->assign('title','添加学生');
    	$this->view->assign('keywords','php.cn');
    	$this->view->assign('desc','PHP中文网ThinkPHP5开发实战课程');
    	$gradeList = GradeModel::all();
    	$this->view->assign('gradeList',$gradeList);
    	return $this->view->fetch('student_add');
    }
    //添加
    public function add(Request $request)
    {
    	$data = $request->param();
    	$result = StudentModel::create($data);
    	$status = 0;
    	$message = '添加失败，请检查';
    	if(true == $result)
    	{
    		$status = 1;
    		$message = '恭喜，添加成功~~';
    	}
    	return['status'=>$status,'message'=>$message];
    }
    //软删除操作
    public function deleteStudent(Request $request)
    {
    	$user_id = $request ->param('id');
    	StudentModel::update(['is_delete'=>1],['id'=>$user_id]);
    	StudentModel::destroy($user_id);
    }
    //恢复删除操作
    public function unDelete()
    {
    	$student = StudentModel::onlyTrashed()->find();
    	$student->restore();
    }
}







 ?>