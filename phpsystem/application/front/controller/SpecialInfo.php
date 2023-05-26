<?php
namespace app\front\controller;
use think\Request;
use think\Exception;
use app\common\model\CollegeModel;
use app\common\model\SpecialInfoModel;

class SpecialInfo extends Base {
    protected $collegeModel;
    protected $specialInfoModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->collegeModel = new CollegeModel();
        $this->specialInfoModel = new SpecialInfoModel();
    }

    /*添加专业信息*/
    public function frontAdd(){
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $specialInfo = $this->getSpecialInfoForm(true);
            try {
                $this->specialInfoModel->addSpecialInfo($specialInfo);
                $message = "专业添加成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "专业添加失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            return $this->fetch('specialInfo/specialInfo_frontAdd');
        }
    }

    /*前台修改专业信息*/
    public function frontModify() {
        $this->assign("specialFieldNumber",input("specialFieldNumber"));
        return $this->fetch("specialInfo/specialInfo_frontModify");
    }

    /*前台按照查询条件分页查询专业信息*/
    public function frontlist() {
        if($this->request->param("currentPage") != null)
            $this->currentPage = $this->request->param("currentPage");
        $specialFieldNumber = input("specialFieldNumber")==null?"":input("specialFieldNumber");
        $specialFieldName = input("specialFieldName")==null?"":input("specialFieldName");
        $specialCollegeNumber["collegeNumber"] = input("specialCollegeNumber_collegeNumber")==null?"":input("specialCollegeNumber_collegeNumber");
        $specialBirthDate = input("specialBirthDate")==null?"":input("specialBirthDate");
        $specialInfoRs = $this->specialInfoModel->querySpecialInfo($specialFieldNumber, $specialFieldName, $specialCollegeNumber, $specialBirthDate, $this->currentPage);
        $this->assign("specialInfoRs",$specialInfoRs);
        /*获取到总的页码数目*/
        $this->assign("totalPage",$this->specialInfoModel->totalPage);
        /*当前查询条件下总记录数*/
        $this->assign("recordNumber",$this->specialInfoModel->recordNumber);
        $this->assign("currentPage",$this->currentPage);
        $this->assign("rows",$this->specialInfoModel->rows);
        $this->assign("specialFieldNumber",$specialFieldNumber);
        $this->assign("specialFieldName",$specialFieldName);
        $this->assign("specialCollegeNumber",$specialCollegeNumber);
        $this->assign("collegeList",$this->collegeModel->queryAllCollege());
        $this->assign("specialBirthDate",$specialBirthDate);
        return $this->fetch('specialInfo/specialInfo_frontlist');
    }

    /*ajax方式查询专业信息*/
    public function listAll() {
        $specialInfoRs = $this->specialInfoModel->queryAllSpecialInfo();
        echo json_encode($specialInfoRs);
    }
    /*前台查询根据主键查询一条专业信息*/
    public function frontshow() {
        $specialFieldNumber = input("specialFieldNumber");
        $specialInfo = $this->specialInfoModel->getSpecialInfo($specialFieldNumber);
       $this->assign("specialInfo",$specialInfo);
        return $this->fetch("specialInfo/specialInfo_frontshow");
    }

    /*更新专业信息*/
    public function update() {
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $specialInfo = $this->getSpecialInfoForm(false);
            try {
                $this->specialInfoModel->updateSpecialInfo($specialInfo);
                $message = "专业更新成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "专业更新失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            /*根据主键获取专业对象*/
            $specialFieldNumber = input("specialFieldNumber");
            $specialInfo = $this->specialInfoModel->getSpecialInfo($specialFieldNumber);
            echo json_encode($specialInfo);
        }
    }

    /*删除多条专业记录*/
    public function deletes() {
        $message = "";
        $success = false;
        $specialFieldNumbers = input("specialFieldNumbers");
        try {
            $count = $this->specialInfoModel->deleteSpecialInfos($specialFieldNumbers);
            $success = true;
            $message = $count."条记录删除成功";
            $this->writeJsonResponse($success, $message);
        } catch (Exception $ex) {
            $message = "有记录存在外键约束,删除失败";
            $this->writeJsonResponse($success, $message);
        }
    }

}

