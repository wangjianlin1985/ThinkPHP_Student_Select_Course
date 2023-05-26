<?php
namespace app\front\controller;
use think\Request;
use think\Exception;
use app\common\model\TeacherModel;
use app\common\model\CourseModel;

class Course extends Base {
    protected $teacherModel;
    protected $courseModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->teacherModel = new TeacherModel();
        $this->courseModel = new CourseModel();
    }

    /*添加课程信息*/
    public function frontAdd(){
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $course = $this->getCourseForm(true);
            try {
                $this->courseModel->addCourse($course);
                $message = "课程添加成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "课程添加失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            return $this->fetch('course/course_frontAdd');
        }
    }

    /*前台修改课程信息*/
    public function frontModify() {
        $this->assign("courseNumber",input("courseNumber"));
        return $this->fetch("course/course_frontModify");
    }

    /*前台按照查询条件分页查询课程信息*/
    public function frontlist() {
        if($this->request->param("currentPage") != null)
            $this->currentPage = $this->request->param("currentPage");
        $courseNumber = input("courseNumber")==null?"":input("courseNumber");
        $courseName = input("courseName")==null?"":input("courseName");
        $courseTeacher["teacherNumber"] = input("courseTeacher_teacherNumber")==null?"":input("courseTeacher_teacherNumber");
        $courseTime = input("courseTime")==null?"":input("courseTime");
        $courseRs = $this->courseModel->queryCourse($courseNumber, $courseName, $courseTeacher, $courseTime, $this->currentPage);
        $this->assign("courseRs",$courseRs);
        /*获取到总的页码数目*/
        $this->assign("totalPage",$this->courseModel->totalPage);
        /*当前查询条件下总记录数*/
        $this->assign("recordNumber",$this->courseModel->recordNumber);
        $this->assign("currentPage",$this->currentPage);
        $this->assign("rows",$this->courseModel->rows);
        $this->assign("courseNumber",$courseNumber);
        $this->assign("courseName",$courseName);
        $this->assign("courseTeacher",$courseTeacher);
        $this->assign("teacherList",$this->teacherModel->queryAllTeacher());
        $this->assign("courseTime",$courseTime);
        return $this->fetch('course/course_frontlist');
    }


    /*ajax方式查询课程信息*/
    public function listAll() {
        $courseRs = $this->courseModel->queryAllCourse();
        echo json_encode($courseRs);
    }
    /*前台查询根据主键查询一条课程信息*/
    public function frontshow() {
        $courseNumber = input("courseNumber");
        $course = $this->courseModel->getCourse($courseNumber);
       $this->assign("course",$course);
        return $this->fetch("course/course_frontshow");
    }

    /*更新课程信息*/
    public function update() {
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $course = $this->getCourseForm(false);
            try {
                $this->courseModel->updateCourse($course);
                $message = "课程更新成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "课程更新失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            /*根据主键获取课程对象*/
            $courseNumber = input("courseNumber");
            $course = $this->courseModel->getCourse($courseNumber);
            echo json_encode($course);
        }
    }

    /*删除多条课程记录*/
    public function deletes() {
        $message = "";
        $success = false;
        $courseNumbers = input("courseNumbers");
        try {
            $count = $this->courseModel->deleteCourses($courseNumbers);
            $success = true;
            $message = $count."条记录删除成功";
            $this->writeJsonResponse($success, $message);
        } catch (Exception $ex) {
            $message = "有记录存在外键约束,删除失败";
            $this->writeJsonResponse($success, $message);
        }
    }

}

