<?php
namespace app\common\model;
use think\Model;

class CourseModel extends Model {
    /*关联表名*/
    protected $table  = 't_course';
    /*每页显示记录数目*/
    public $rows = 8;
    /*保存查询后总的页数*/
    public $totalPage;
    /*保存查询到的总记录数*/
    public $recordNumber;

    public function setRows($rows) {
        $this->rows = $rows;
    }

    //开课老师复合属性的获取: 多对一关系
    public function courseTeacherF(){
        return $this->belongsTo('TeacherModel','courseTeacher');
    }

    /*添加课程记录*/
    public function addCourse($course) {
        $this->insert($course);
    }

    /*更新课程记录*/
    public function updateCourse($course) {
        $this->update($course);
    }

    /*删除多条课程信息*/
    public function deleteCourses($courseNumbers){
        $courseNumberArray = explode(",",$courseNumbers);
        foreach ($courseNumberArray as $courseNumber) {
            $this->courseNumber = $courseNumber;
            $this->delete();
        }
        return count($courseNumberArray);
    }
    /*根据主键获取课程记录*/
    public function getCourse($courseNumber) {
        $course = CourseModel::where("courseNumber",$courseNumber)->find();
        return $course;
    }

    /*按照查询条件分页查询课程信息*/
    public function queryCourse($courseNumber, $courseName, $courseTeacher, $courseTime, $currentPage) {
        $startIndex = ($currentPage-1) * $this->rows;
        $where = null;
        if($courseNumber != "") $where['courseNumber'] = array('like','%'.$courseNumber.'%');
        if($courseName != "") $where['courseName'] = array('like','%'.$courseName.'%');
        if($courseTeacher['teacherNumber'] != "") $where['courseTeacher'] = $courseTeacher['teacherNumber'];
        if($courseTime != "") $where['courseTime'] = array('like','%'.$courseTime.'%');
        $courseRs = CourseModel::where($where)->limit($startIndex,$this->rows)->select();
        /*计算总的页数和总的记录数*/
        $this->recordNumber = CourseModel::where($where)->count();
        $mod = $this->recordNumber % $this->rows;
        $this->totalPage = (int)($this->recordNumber / $this->rows);
        if($mod != 0) $this->totalPage++;
        return $courseRs;
    }

    /*按照查询条件查询所有课程记录*/
  public function queryOutputCourse( $courseNumber,  $courseName,  $courseTeacher,  $courseTime) {
        $where = null;
        if($courseNumber != "") $where['courseNumber'] = array('like','%'.$courseNumber.'%');
        if($courseName != "") $where['courseName'] = array('like','%'.$courseName.'%');
        if($courseTeacher['teacherNumber'] != "") $where['courseTeacher'] = $courseTeacher['teacherNumber'];
        if($courseTime != "") $where['courseTime'] = array('like','%'.$courseTime.'%');
        $courseRs = CourseModel::where($where)->select();
        return $courseRs;
    }

    /*查询所有课程记录*/
    public function queryAllCourse(){
        $courseRs = CourseModel::where(null)->select();
        return $courseRs;

    }

}
