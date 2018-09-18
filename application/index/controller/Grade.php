<?php 
namespace app\index\controller;

use think\Request;
use app\index\model\Teacher;
use app\index\model\Grade as GradeModel;

class Grade extends Base
{
	public function gradeList()
	{
		$grade = GradeModel::all();
		$count = GradeModel::count();
		foreach ($grade as $value)
		{
			$data = [
				'id' => $value->id,
				'name' => $value->name,
				'length' => $value->length,
				'price' => $value->price,
				'status' => $value->status,
				'create_time' => $value->create_time,
				//用关联方法teacher属性方式访问teacher表中数据
                'teacher' => isset($value->teacher->name)?$value->teacher->name:'未分配'
			];
			$gradeList[] = $data;
		}
		$this->view->assign('gradeList',$gradeList);
		$this->view->assign('count',$count);
		return $this->view->fetch('grade_list');
	}

	//状态变更
	public function setStatus(Request $request)
	{
		$grade_id=$request->param('id');
		$result=GradeModel::get($grade_id);
		if($result->getData('status') == 1)
		{
			GradeModel::update(['status'=>0],['id'=>$grade_id]);
		} else {
			GradeModel::update(['status'=>1],['id'=>$grade_id]);
		}
	}

	//渲染班级编辑界面
    public function gradeEdit(Request $request)
    {
        //获取到要编辑的班级ID
        $grade_id = $request -> param('id');

        //根据ID进行查询
        $result = GradeModel::get($grade_id);

        //关联查询,获取与当前班级对应的教师姓名
        //$result -> teacher = $result -> teacher->name;
        if(!empty($result->teacher->name))
        {
        	$result -> teacher = $result -> teacher->name;
        }
        //给当前页面seo变量赋值
        $this->view->assign('title','编辑班级');
        $this->view->assign('keywords','php.cn');
        $this->view->assign('desc','PHP中文网ThinkPHP5开发实战课程');

        //给当前编辑模板赋值
        $this->view->assign('grade_info',$result);

        //渲染编辑模板
        return $this->view->fetch('grade_edit');
    }

	//班级修改
	public function edit(Request $request)
	{
		$data=$request->except('teacher');
		$condition = ['id'=>$data['id']];
		$result = GradeModel::update($data,$condition);
		$status = 0;
		$message = '更新失败，请检查';
		if(true == $result)
		{
			$status = 1;
			$message = '恭喜，更新成功~~';
		}
		return ['status'=>$status,'message'=>$message];
	}

	//渲染添加模板
	public function gradeAdd(Request $request)
	{
		$this->view->assign('title','编辑班级');
		$this->view->assign('keywords','php.cn');
		$this->view->assign('desc','PHP中文网ThinkPHP5开发实战课程');
		return $this->view->fetch('grade_add');

	}

	//添加班级
	public function add(Request $request)
	{
		$data=$request->param();
		$result=GradeModel::create($data);
		$status=0;
		$message='添加失败，请检查';
		if(true == $result)
		{
			$status=1;
			$message='恭喜，添加成功~~';
		}
		return ['status'=>$status,'message'=>$message];
	}

	//删除
	public function deleteGrade(Request $request)
	{
		$user_id=$request->param('id');
		GradeModel::update(['is_delete'=>1],['id'=>$user_id]);
		GradeModel::destroy($user_id);
	}

	//恢复删除
	public function unDelete()
	{
		$grade = GradeModel::onlyTrashed()->find();
    	$grade->restore();
	}
}



 ?>