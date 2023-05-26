<?php
namespace app\back\controller;
use think\Request;
use think\Exception;
use app\common\model\CollegeModel;
use app\common\model\TeacherModel;

class Teacher extends BackBase {
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
    public function add(){
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
            return view('teacher/teacher_add');
        }
    }

    /*跳转到更新界面*/
    public function modifyView() {
        $this->assign("teacherNumber",input("teacherNumber"));
        return view("teacher/teacher_modify");
    }


    /*跳转到更新界面*/
    public function modifySelfView() {
        $this->assign("teacherNumber",session("username"));
        return view("teacher/teacher_modify");
    }



    /*ajax方式按照查询条件分页查询教师信息*/
    public function backList() {
        if($this->request->isPost()) {
            if($this->request->param("page") != null)
                $this->currentPage = $this->request->param("page");
            if($this->request->param("rows") != null)
                $this->teacherModel->setRows($this->request->param("rows"));
            $teacherNumber = input("teacherNumber")==null?"":input("teacherNumber");
            $collegeObj["collegeNumber"] = input("collegeObj_collegeNumber")==null?"":input("collegeObj_collegeNumber");
            $teacherName = input("teacherName")==null?"":input("teacherName");
            $teacherBirthday = input("teacherBirthday")==null?"":input("teacherBirthday");
            $teacherArriveDate = input("teacherArriveDate")==null?"":input("teacherArriveDate");
            $teacherRs = $this->teacherModel->queryTeacher($teacherNumber, $collegeObj, $teacherName, $teacherBirthday, $teacherArriveDate, $this->currentPage);
            $expTableData = [];
            foreach($teacherRs as $teacherRow) {
                $teacherRow["collegeObj"] = $this->collegeModel->getCollege($teacherRow["collegeObj"])["collegeName"];
                $expTableData[] = $teacherRow;
            }
            $data["rows"] = $teacherRs;
            /*当前查询条件下总记录数*/
            $data["total"] = $this->teacherModel->recordNumber;
            echo json_encode($data);
        } else {
            return view("teacher/teacher_query");
        }
    }

    /*ajax方式查询教师信息*/
    public function listAll() {
        $teacherRs = $this->teacherModel->queryAllTeacher();
        echo json_encode($teacherRs);
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

    /*按照查询条件导出教师信息到Excel*/
    public function outToExcel() {
        $teacherNumber = input("teacherNumber")==null?"":input("teacherNumber");
        $collegeObj["collegeNumber"] = input("collegeObj_collegeNumber")==null?"":input("collegeObj_collegeNumber");
        $teacherName = input("teacherName")==null?"":input("teacherName");
        $teacherBirthday = input("teacherBirthday")==null?"":input("teacherBirthday");
        $teacherArriveDate = input("teacherArriveDate")==null?"":input("teacherArriveDate");
        $teacherRs = $this->teacherModel->queryOutputTeacher($teacherNumber,$collegeObj,$teacherName,$teacherBirthday,$teacherArriveDate);
        $expTableData = [];
        foreach($teacherRs as $teacherRow) {
            $teacherRow["collegeObj"] = $this->collegeModel->getCollege($teacherRow["collegeObj"])["collegeName"];
            $expTableData[] = $teacherRow;
        }
        $xlsName = "Teacher";
        $xlsCell = array(
            array('teacherNumber','教师工号','string'),
            array('collegeObj','所在学院','string'),
            array('teacherName','教师姓名','string'),
            array('teacherSex','性别','string'),
            array('teacherPhoto','教师照片','photo'),
            array('teacherBirthday','出生日期','string'),
            array('teacherArriveDate','入职日期','string'),
            array('teacherPhone','联系电话','string'),
            array('teacherMail','教师邮箱','string'),
        );//查出字段输出对应Excel对应的列名
        //公共方法调用
        $this->export_excel($xlsName,$xlsCell,$expTableData);
    }

}

