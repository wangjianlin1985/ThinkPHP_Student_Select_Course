<?php
namespace app\back\controller;
use think\Request;
use think\Exception;
use app\common\model\SpecialInfoModel;
use app\common\model\ClassInfoModel;

class ClassInfo extends BackBase {
    protected $specialInfoModel;
    protected $classInfoModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->specialInfoModel = new SpecialInfoModel();
        $this->classInfoModel = new ClassInfoModel();
    }

    /*添加班级信息*/
    public function add(){
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $classInfo = $this->getClassInfoForm(true);
            try {
                $this->classInfoModel->addClassInfo($classInfo);
                $message = "班级添加成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "班级添加失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            return view('classInfo/classInfo_add');
        }
    }

    /*跳转到更新界面*/
    public function modifyView() {
        $this->assign("classNumber",input("classNumber"));
        return view("classInfo/classInfo_modify");
    }

    /*ajax方式按照查询条件分页查询班级信息*/
    public function backList() {
        if($this->request->isPost()) {
            if($this->request->param("page") != null)
                $this->currentPage = $this->request->param("page");
            if($this->request->param("rows") != null)
                $this->classInfoModel->setRows($this->request->param("rows"));
            $classNumber = input("classNumber")==null?"":input("classNumber");
            $className = input("className")==null?"":input("className");
            $classSpecialFieldNumber["specialFieldNumber"] = input("classSpecialFieldNumber_specialFieldNumber")==null?"":input("classSpecialFieldNumber_specialFieldNumber");
            $classBirthDate = input("classBirthDate")==null?"":input("classBirthDate");
            $classInfoRs = $this->classInfoModel->queryClassInfo($classNumber, $className, $classSpecialFieldNumber, $classBirthDate, $this->currentPage);
            $expTableData = [];
            foreach($classInfoRs as $classInfoRow) {
                $classInfoRow["classSpecialFieldNumber"] = $this->specialInfoModel->getSpecialInfo($classInfoRow["classSpecialFieldNumber"])["specialFieldName"];
                $expTableData[] = $classInfoRow;
            }
            $data["rows"] = $classInfoRs;
            /*当前查询条件下总记录数*/
            $data["total"] = $this->classInfoModel->recordNumber;
            echo json_encode($data);
        } else {
            return view("classInfo/classInfo_query");
        }
    }

    /*ajax方式按照查询条件分页查询班级信息*/
    public function backTeacherList() {
        if($this->request->isPost()) {
            if($this->request->param("page") != null)
                $this->currentPage = $this->request->param("page");
            if($this->request->param("rows") != null)
                $this->classInfoModel->setRows($this->request->param("rows"));
            $classNumber = input("classNumber")==null?"":input("classNumber");
            $className = input("className")==null?"":input("className");
            $classSpecialFieldNumber["specialFieldNumber"] = input("classSpecialFieldNumber_specialFieldNumber")==null?"":input("classSpecialFieldNumber_specialFieldNumber");
            $classBirthDate = input("classBirthDate")==null?"":input("classBirthDate");
            $classInfoRs = $this->classInfoModel->queryClassInfo($classNumber, $className, $classSpecialFieldNumber, $classBirthDate, $this->currentPage);
            $expTableData = [];
            foreach($classInfoRs as $classInfoRow) {
                $classInfoRow["classSpecialFieldNumber"] = $this->specialInfoModel->getSpecialInfo($classInfoRow["classSpecialFieldNumber"])["specialFieldName"];
                $expTableData[] = $classInfoRow;
            }
            $data["rows"] = $classInfoRs;
            /*当前查询条件下总记录数*/
            $data["total"] = $this->classInfoModel->recordNumber;
            echo json_encode($data);
        } else {
            return view("classInfo/classInfo_teacherquery");
        }
    }


    /*ajax方式查询班级信息*/
    public function listAll() {
        $classInfoRs = $this->classInfoModel->queryAllClassInfo();
        echo json_encode($classInfoRs);
    }
    /*更新班级信息*/
    public function update() {
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $classInfo = $this->getClassInfoForm(false);
            try {
                $this->classInfoModel->updateClassInfo($classInfo);
                $message = "班级更新成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "班级更新失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            /*根据主键获取班级对象*/
            $classNumber = input("classNumber");
            $classInfo = $this->classInfoModel->getClassInfo($classNumber);
            echo json_encode($classInfo);
        }
    }

    /*删除多条班级记录*/
    public function deletes() {
        $message = "";
        $success = false;
        $classNumbers = input("classNumbers");
        try {
            $count = $this->classInfoModel->deleteClassInfos($classNumbers);
            $success = true;
            $message = $count."条记录删除成功";
            $this->writeJsonResponse($success, $message);
        } catch (Exception $ex) {
            $message = "有记录存在外键约束,删除失败";
            $this->writeJsonResponse($success, $message);
        }
    }

    /*按照查询条件导出班级信息到Excel*/
    public function outToExcel() {
        $classNumber = input("classNumber")==null?"":input("classNumber");
        $className = input("className")==null?"":input("className");
        $classSpecialFieldNumber["specialFieldNumber"] = input("classSpecialFieldNumber_specialFieldNumber")==null?"":input("classSpecialFieldNumber_specialFieldNumber");
        $classBirthDate = input("classBirthDate")==null?"":input("classBirthDate");
        $classInfoRs = $this->classInfoModel->queryOutputClassInfo($classNumber,$className,$classSpecialFieldNumber,$classBirthDate);
        $expTableData = [];
        foreach($classInfoRs as $classInfoRow) {
            $classInfoRow["classSpecialFieldNumber"] = $this->specialInfoModel->getSpecialInfo($classInfoRow["classSpecialFieldNumber"])["specialFieldName"];
            $expTableData[] = $classInfoRow;
        }
        $xlsName = "ClassInfo";
        $xlsCell = array(
            array('classNumber','班级编号','string'),
            array('className','班级名称','string'),
            array('classSpecialFieldNumber','所属专业','string'),
            array('classBirthDate','成立日期','string'),
            array('classTeacherCharge','班主任','string'),
            array('classTelephone','联系电话','string'),
        );//查出字段输出对应Excel对应的列名
        //公共方法调用
        $this->export_excel($xlsName,$xlsCell,$expTableData);
    }

}

