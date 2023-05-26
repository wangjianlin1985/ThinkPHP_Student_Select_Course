<?php
namespace app\front\controller;
use think\Request;
use think\Exception;
use app\common\model\CollegeModel;

class College extends Base {
    protected $collegeModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->collegeModel = new CollegeModel();
    }

    /*添加学院信息*/
    public function frontAdd(){
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $college = $this->getCollegeForm(true);
            try {
                $this->collegeModel->addCollege($college);
                $message = "学院添加成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "学院添加失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            return $this->fetch('college/college_frontAdd');
        }
    }

    /*前台修改学院信息*/
    public function frontModify() {
        $this->assign("collegeNumber",input("collegeNumber"));
        return $this->fetch("college/college_frontModify");
    }

    /*前台按照查询条件分页查询学院信息*/
    public function frontlist() {
        if($this->request->param("currentPage") != null)
            $this->currentPage = $this->request->param("currentPage");
        $collegeRs = $this->collegeModel->queryCollege($this->currentPage);
        $this->assign("collegeRs",$collegeRs);
        /*获取到总的页码数目*/
        $this->assign("totalPage",$this->collegeModel->totalPage);
        /*当前查询条件下总记录数*/
        $this->assign("recordNumber",$this->collegeModel->recordNumber);
        $this->assign("currentPage",$this->currentPage);
        $this->assign("rows",$this->collegeModel->rows);
        return $this->fetch('college/college_frontlist');
    }

    /*ajax方式查询学院信息*/
    public function listAll() {
        $collegeRs = $this->collegeModel->queryAllCollege();
        echo json_encode($collegeRs);
    }
    /*前台查询根据主键查询一条学院信息*/
    public function frontshow() {
        $collegeNumber = input("collegeNumber");
        $college = $this->collegeModel->getCollege($collegeNumber);
       $this->assign("college",$college);
        return $this->fetch("college/college_frontshow");
    }

    /*更新学院信息*/
    public function update() {
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $college = $this->getCollegeForm(false);
            try {
                $this->collegeModel->updateCollege($college);
                $message = "学院更新成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "学院更新失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            /*根据主键获取学院对象*/
            $collegeNumber = input("collegeNumber");
            $college = $this->collegeModel->getCollege($collegeNumber);
            echo json_encode($college);
        }
    }

    /*删除多条学院记录*/
    public function deletes() {
        $message = "";
        $success = false;
        $collegeNumbers = input("collegeNumbers");
        try {
            $count = $this->collegeModel->deleteColleges($collegeNumbers);
            $success = true;
            $message = $count."条记录删除成功";
            $this->writeJsonResponse($success, $message);
        } catch (Exception $ex) {
            $message = "有记录存在外键约束,删除失败";
            $this->writeJsonResponse($success, $message);
        }
    }

}

