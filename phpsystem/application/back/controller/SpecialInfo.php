<?php
namespace app\back\controller;
use think\Request;
use think\Exception;
use app\common\model\CollegeModel;
use app\common\model\SpecialInfoModel;

class SpecialInfo extends BackBase {
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
    public function add(){
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
            return view('specialInfo/specialInfo_add');
        }
    }

    /*跳转到更新界面*/
    public function modifyView() {
        $this->assign("specialFieldNumber",input("specialFieldNumber"));
        return view("specialInfo/specialInfo_modify");
    }

    /*ajax方式按照查询条件分页查询专业信息*/
    public function backList() {
        if($this->request->isPost()) {
            if($this->request->param("page") != null)
                $this->currentPage = $this->request->param("page");
            if($this->request->param("rows") != null)
                $this->specialInfoModel->setRows($this->request->param("rows"));
            $specialFieldNumber = input("specialFieldNumber")==null?"":input("specialFieldNumber");
            $specialFieldName = input("specialFieldName")==null?"":input("specialFieldName");
            $specialCollegeNumber["collegeNumber"] = input("specialCollegeNumber_collegeNumber")==null?"":input("specialCollegeNumber_collegeNumber");
            $specialBirthDate = input("specialBirthDate")==null?"":input("specialBirthDate");
            $specialInfoRs = $this->specialInfoModel->querySpecialInfo($specialFieldNumber, $specialFieldName, $specialCollegeNumber, $specialBirthDate, $this->currentPage);
            $expTableData = [];
            foreach($specialInfoRs as $specialInfoRow) {
                $specialInfoRow["specialCollegeNumber"] = $this->collegeModel->getCollege($specialInfoRow["specialCollegeNumber"])["collegeName"];
                $expTableData[] = $specialInfoRow;
            }
            $data["rows"] = $specialInfoRs;
            /*当前查询条件下总记录数*/
            $data["total"] = $this->specialInfoModel->recordNumber;
            echo json_encode($data);
        } else {
            return view("specialInfo/specialInfo_query");
        }
    }

    /*ajax方式查询专业信息*/
    public function listAll() {
        $specialInfoRs = $this->specialInfoModel->queryAllSpecialInfo();
        echo json_encode($specialInfoRs);
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

    /*按照查询条件导出专业信息到Excel*/
    public function outToExcel() {
        $specialFieldNumber = input("specialFieldNumber")==null?"":input("specialFieldNumber");
        $specialFieldName = input("specialFieldName")==null?"":input("specialFieldName");
        $specialCollegeNumber["collegeNumber"] = input("specialCollegeNumber_collegeNumber")==null?"":input("specialCollegeNumber_collegeNumber");
        $specialBirthDate = input("specialBirthDate")==null?"":input("specialBirthDate");
        $specialInfoRs = $this->specialInfoModel->queryOutputSpecialInfo($specialFieldNumber,$specialFieldName,$specialCollegeNumber,$specialBirthDate);
        $expTableData = [];
        foreach($specialInfoRs as $specialInfoRow) {
            $specialInfoRow["specialCollegeNumber"] = $this->collegeModel->getCollege($specialInfoRow["specialCollegeNumber"])["collegeName"];
            $expTableData[] = $specialInfoRow;
        }
        $xlsName = "SpecialInfo";
        $xlsCell = array(
            array('specialFieldNumber','专业编号','string'),
            array('specialFieldName','专业名称','string'),
            array('specialCollegeNumber','所在学院','string'),
            array('specialBirthDate','成立日期','string'),
            array('specialMan','联系人','string'),
            array('specialTelephone','联系电话','string'),
        );//查出字段输出对应Excel对应的列名
        //公共方法调用
        $this->export_excel($xlsName,$xlsCell,$expTableData);
    }

}

