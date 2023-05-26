<?php
namespace app\front\controller;
use think\Request;
use think\Exception;
use app\common\model\CollegeModel;
use app\common\model\TeacherModel;

class Teacher extends Base {
    protected $collegeModel;
    protected $teacherModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->collegeModel = new CollegeModel();
        $this->teacherModel = new TeacherModel();
    }

    /*添加教师信息*/
    public function frontAdd(){
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $teacher = $this->getTeacherForm(true);
            $this->uploadPhoto($teacher,"teacherPhoto","teacherPhotoFile"); //处理教师照片上传
            try {
                $this->teacherModel->addTeacher($teacher);
                $message = "教师添加成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "教师添加失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            return $this->fetch('teacher/teacher_frontAdd');
        }
    }

    /*前台修改教师信息*/
    public function frontModify() {
        $this->assign("teacherNumber",input("teacherNumber"));
        return $this->fetch("teacher/teacher_frontModify");
    }

    /*前台按照查询条件分页查询教师信息*/
    public function frontlist() {
        if($this->request->param("currentPage") != null)
            $this->currentPage = $this->request->param("currentPage");
        $teacherNumber = input("teacherNumber")==null?"":input("teacherNumber");
        $collegeObj["collegeNumber"] = input("collegeObj_collegeNumber")==null?"":input("collegeObj_collegeNumber");
        $teacherName = input("teacherName")==null?"":input("teacherName");
        $teacherBirthday = input("teacherBirthday")==null?"":input("teacherBirthday");
        $teacherArriveDate = input("teacherArriveDate")==null?"":input("teacherArriveDate");
        $teacherRs = $this->teacherModel->queryTeacher($teacherNumber, $collegeObj, $teacherName, $teacherBirthday, $teacherArriveDate, $this->currentPage);
        $this->assign("teacherRs",$teacherRs);
        /*获取到总的页码数目*/
        $this->assign("totalPage",$this->teacherModel->totalPage);
        /*当前查询条件下总记录数*/
        $this->assign("recordNumber",$this->teacherModel->recordNumber);
        $this->assign("currentPage",$this->currentPage);
        $this->assign("rows",$this->teacherModel->rows);
        $this->assign("teacherNumber",$teacherNumber);
        $this->assign("collegeObj",$collegeObj);
        $this->assign("collegeList",$this->collegeModel->queryAllCollege());
        $this->assign("teacherName",$teacherName);
        $this->assign("teacherBirthday",$teacherBirthday);
        $this->assign("teacherArriveDate",$teacherArriveDate);
        return $this->fetch('teacher/teacher_frontlist');
    }

    /*ajax方式查询教师信息*/
    public function listAll() {
        $teacherRs = $this->teacherModel->queryAllTeacher();
        echo json_encode($teacherRs);
    }
    /*前台查询根据主键查询一条教师信息*/
    public function frontshow() {
        $teacherNumber = input("teacherNumber");
        $teacher = $this->teacherModel->getTeacher($teacherNumber);
       $this->assign("teacher",$teacher);
        return $this->fetch("teacher/teacher_frontshow");
    }

    /*更新教师信息*/
    public function update() {
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $teacher = $this->getTeacherForm(false);
            $this->uploadPhoto($teacher,"teacherPhoto","teacherPhotoFile"); //处理教师照片上传
            try {
                $this->teacherModel->updateTeacher($teacher);
                $message = "教师更新成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "教师更新失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            /*根据主键获取教师对象*/
            $teacherNumber = input("teacherNumber");
            $teacher = $this->teacherModel->getTeacher($teacherNumber);
            echo json_encode($teacher);
        }
    }

    /*删除多条教师记录*/
    public function deletes() {
        $message = "";
        $success = false;
        $teacherNumbers = input("teacherNumbers");
        try {
            $count = $this->teacherModel->deleteTeachers($teacherNumbers);
            $success = true;
            $message = $count."条记录删除成功";
            $this->writeJsonResponse($success, $message);
        } catch (Exception $ex) {
            $message = "有记录存在外键约束,删除失败";
            $this->writeJsonResponse($success, $message);
        }
    }

}

