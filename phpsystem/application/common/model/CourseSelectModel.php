<?php
namespace app\common\model;
use think\Model;

class CourseSelectModel extends Model {
    /*关联表名*/
    protected $table  = 't_courseSelect';
    /*每页显示记录数目*/
    public $rows = 8;
    /*保存查询后总的页数*/
    public $totalPage;
    /*保存查询到的总记录数*/
    public $recordNumber;

    public function setRows($rows) {
        $this->rows = $rows;
    }

    //学生复合属性的获取: 多对一关系
    public function studentNumberF(){
        return $this->belongsTo('StudentModel','studentNumber');
    }

    //课程复合属性的获取: 多对一关系
    public function courseNumberF(){
        return $this->belongsTo('CourseModel','courseNumber');
    }

    /*添加选课记录*/
    public function addCourseSelect($courseSelect) {
        $this->insert($courseSelect);
    }

    /*更新选课记录*/
    public function updateCourseSelect($courseSelect) {
        $this->update($courseSelect);
    }

    /*删除多条选课信息*/
    public function deleteCourseSelects($selectIds){
        $selectIdArray = explode(",",$selectIds);
        foreach ($selectIdArray as $selectId) {
            $this->selectId = $selectId;
            $this->delete();
        }
        return count($selectIdArray);
    }
    /*根据主键获取选课记录*/
    public function getCourseSelect($selectId) {
        $courseSelect = CourseSelectModel::where("selectId",$selectId)->find();
        return $courseSelect;
    }

    /*按照查询条件分页查询选课信息*/
    public function queryCourseSelect($selectTime, $studentNumber, $courseNumber, $currentPage) {
        $startIndex = ($currentPage-1) * $this->rows;
        $where = null;
        if($selectTime != "") $where['selectTime'] = array('like','%'.$selectTime.'%');
        if($studentNumber['studentNumber'] != "") $where['studentNumber'] = $studentNumber['studentNumber'];
        if($courseNumber['courseNumber'] != "") $where['courseNumber'] = $courseNumber['courseNumber'];
        $courseSelectRs = CourseSelectModel::where($where)->limit($startIndex,$this->rows)->select();
        /*计算总的页数和总的记录数*/
        $this->recordNumber = CourseSelectModel::where($where)->count();
        $mod = $this->recordNumber % $this->rows;
        $this->totalPage = (int)($this->recordNumber / $this->rows);
        if($mod != 0) $this->totalPage++;
        return $courseSelectRs;
    }

    /*按照查询条件查询所有选课记录*/
  public function queryOutputCourseSelect( $selectTime,  $studentNumber,  $courseNumber) {
        $where = null;
        if($selectTime != "") $where['selectTime'] = array('like','%'.$selectTime.'%');
        if($studentNumber['studentNumber'] != "") $where['studentNumber'] = $studentNumber['studentNumber'];
        if($courseNumber['courseNumber'] != "") $where['courseNumber'] = $courseNumber['courseNumber'];
        $courseSelectRs = CourseSelectModel::where($where)->select();
        return $courseSelectRs;
    }

    /*查询所有选课记录*/
    public function queryAllCourseSelect(){
        $courseSelectRs = CourseSelectModel::where(null)->select();
        return $courseSelectRs;

    }

}
