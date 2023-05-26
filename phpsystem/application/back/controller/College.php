<?php
namespace app\back\controller;
use think\Request;
use think\Exception;
use app\common\model\CollegeModel;

class College extends BackBase {
    protected $collegeModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->collegeModel = new CollegeModel();
    }

    /*添加学院信息*/
    public function add(){
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
            return view('college/college_add');
        }
    }

    /*跳转到更新界面*/
    public function modifyView() {
        $this->assign("collegeNumber",input("collegeNumber"));
        return view("college/college_modify");
    }

    /*ajax方式按照查询条件分页查询学院信息*/
    public function backList() {
        if($this->request->isPost()) {
            if($this->request->param("page") != null)
                $this->currentPage = $this->request->param("page");
            if($this->request->param("rows") != null)
                $this->collegeModel->setRows($this->request->param("rows"));
            $collegeRs = $this->collegeModel->queryCollege($this->currentPage);
            $expTableData = [];
            foreach($collegeRs as $collegeRow) {
                $expTableData[] = $collegeRow;
            }
            $data["rows"] = $collegeRs;
            /*当前查询条件下总记录数*/
            $data["total"] = $this->collegeModel->recordNumber;
            echo json_encode($data);
        } else {
            return view("college/college_query");
        }
    }

    /*ajax方式查询学院信息*/
    public function listAll() {
        $collegeRs = $this->collegeModel->queryAllCollege();
        echo json_encode($collegeRs);
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

    /*按照查询条件导出学院信息到Excel*/
    public function outToExcel() {
        $collegeRs = $this->collegeModel->queryOutputCollege();
        $expTableData = [];
        foreach($collegeRs as $collegeRow) {
            $expTableData[] = $collegeRow;
        }
        $xlsName = "College";
        $xlsCell = array(
            array('collegeNumber','学院编号','string'),
            array('collegeName','学院名称','string'),
            array('collegeBirthDate','成立日期','string'),
            array('collegeMan','院长姓名','string'),
            array('collegeTelephone','联系电话','string'),
        );//查出字段输出对应Excel对应的列名
        //公共方法调用
        $this->export_excel($xlsName,$xlsCell,$expTableData);
    }

}

