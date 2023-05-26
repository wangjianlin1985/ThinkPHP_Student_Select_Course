<?php
namespace app\back\controller;
use app\common\model\ScoreInfoModel;
use think\Request;
use think\Exception;
use app\common\model\TeacherModel;
use app\common\model\CourseModel;

class Course extends BackBase {
    protected $teacherModel;
    protected $courseModel;
    protected $scoreInfoModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->teacherModel = new TeacherModel();
        $this->courseModel = new CourseModel();
        $this->scoreInfoModel = new ScoreInfoModel();
    }

    /*添加课程信息*/
    public function add(){
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
            return view('course/course_add');
        }
    }

    /*跳转到更新界面*/
    public function modifyView() {
        $this->assign("courseNumber",input("courseNumber"));
        return view("course/course_modify");
    }

    /*ajax方式按照查询条件分页查询课程信息*/
    public function backList() {
        if($this->request->isPost()) {
            if($this->request->param("page") != null)
                $this->currentPage = $this->request->param("page");
            if($this->request->param("rows") != null)
                $this->courseModel->setRows($this->request->param("rows"));
            $courseNumber = input("courseNumber")==null?"":input("courseNumber");
            $courseName = input("courseName")==null?"":input("courseName");
            $courseTeacher["teacherNumber"] = input("courseTeacher_teacherNumber")==null?"":input("courseTeacher_teacherNumber");
            $courseTime = input("courseTime")==null?"":input("courseTime");
            $courseRs = $this->courseModel->queryCourse($courseNumber, $courseName, $courseTeacher, $courseTime, $this->currentPage);
            $expTableData = [];
            foreach($courseRs as $courseRow) {
                $courseRow["courseTeacher"] = $this->teacherModel->getTeacher($courseRow["courseTeacher"])["teacherName"];
                $expTableData[] = $courseRow;
            }
            $data["rows"] = $courseRs;
            /*当前查询条件下总记录数*/
            $data["total"] = $this->courseModel->recordNumber;
            echo json_encode($data);
        } else {
            return view("course/course_query");
        }
    }


    /*ajax方式按照查询条件分页查询课程信息*/
    public function backTeacherList() {
        if($this->request->isPost()) {
            if($this->request->param("page") != null)
                $this->currentPage = $this->request->param("page");
            if($this->request->param("rows") != null)
                $this->courseModel->setRows($this->request->param("rows"));
            $courseNumber = input("courseNumber")==null?"":input("courseNumber");
            $courseName = input("courseName")==null?"":input("courseName");
            $courseTeacher["teacherNumber"] =  session("username");
            $courseTime = input("courseTime")==null?"":input("courseTime");
            $courseRs = $this->courseModel->queryCourse($courseNumber, $courseName, $courseTeacher, $courseTime, $this->currentPage);
            $expTableData = [];
            foreach($courseRs as $courseRow) {
                $courseRow["courseTeacher"] = $this->teacherModel->getTeacher($courseRow["courseTeacher"])["teacherName"];
                $expTableData[] = $courseRow;
            }
            $data["rows"] = $courseRs;
            /*当前查询条件下总记录数*/
            $data["total"] = $this->courseModel->recordNumber;
            echo json_encode($data);
        } else {
            return view("course/course_teacherquery");
        }
    }

    /**成绩统计*/
    public function  tongji() {
        $data["xData"] = ["60分以下","60~80分","80分以上"];
        $courseObj['courseNumber']  = input("courseNumber");
        $scoreRs = $this->scoreInfoModel->queryOutputScoreInfo(null,$courseObj);
        $yData = [0,0,0];
        foreach($scoreRs as $score) {
            if($score["scoreValue"] >= 80) $yData[2] ++;
            else if($score["scoreValue"] >= 60) $yData[1] ++;
            else $yData[0]++;
        }
        $data["yData"] = $yData;
        echo json_encode($data);
    }

    /*ajax方式查询课程信息*/
    public function listAll() {
        $courseRs = $this->courseModel->queryAllCourse();
        echo json_encode($courseRs);
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

    /*按照查询条件导出课程信息到Excel*/
    public function outToExcel() {
        $courseNumber = input("courseNumber")==null?"":input("courseNumber");
        $courseName = input("courseName")==null?"":input("courseName");
        $courseTeacher["teacherNumber"] = input("courseTeacher_teacherNumber")==null?"":input("courseTeacher_teacherNumber");
        $courseTime = input("courseTime")==null?"":input("courseTime");
        $courseRs = $this->courseModel->queryOutputCourse($courseNumber,$courseName,$courseTeacher,$courseTime);
        $expTableData = [];
        foreach($courseRs as $courseRow) {
            $courseRow["courseTeacher"] = $this->teacherModel->getTeacher($courseRow["courseTeacher"])["teacherName"];
            $expTableData[] = $courseRow;
        }
        $xlsName = "Course";
        $xlsCell = array(
            array('courseNumber','课程编号','string'),
            array('courseName','课程名称','string'),
            array('courseTeacher','开课老师','string'),
            array('courseTime','上课时间','string'),
            array('coursePlace','上课地点','string'),
            array('courseScore','课程学分','float'),
        );//查出字段输出对应Excel对应的列名
        //公共方法调用
        $this->export_excel($xlsName,$xlsCell,$expTableData);
    }

}

