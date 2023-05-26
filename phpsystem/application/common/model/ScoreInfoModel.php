<?php
namespace app\common\model;
use think\Model;

class ScoreInfoModel extends Model {
    /*关联表名*/
    protected $table  = 't_scoreInfo';
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
    public function studentObjF(){
        return $this->belongsTo('StudentModel','studentObj');
    }

    //课程复合属性的获取: 多对一关系
    public function courseObjF(){
        return $this->belongsTo('CourseModel','courseObj');
    }

    /*添加成绩记录*/
    public function addScoreInfo($scoreInfo) {
        $this->insert($scoreInfo);
    }

    /*更新成绩记录*/
    public function updateScoreInfo($scoreInfo) {
        $this->update($scoreInfo);
    }

    /*删除多条成绩信息*/
    public function deleteScoreInfos($scoreIds){
        $scoreIdArray = explode(",",$scoreIds);
        foreach ($scoreIdArray as $scoreId) {
            $this->scoreId = $scoreId;
            $this->delete();
        }
        return count($scoreIdArray);
    }
    /*根据主键获取成绩记录*/
    public function getScoreInfo($scoreId) {
        $scoreInfo = ScoreInfoModel::where("scoreId",$scoreId)->find();
        return $scoreInfo;
    }

    /*按照查询条件分页查询成绩信息*/
    public function queryScoreInfo($studentObj, $courseObj, $currentPage) {
        $startIndex = ($currentPage-1) * $this->rows;
        $where = null;
        if($studentObj['studentNumber'] != "") $where['studentObj'] = $studentObj['studentNumber'];
        if($courseObj['courseNumber'] != "") $where['courseObj'] = $courseObj['courseNumber'];
        $scoreInfoRs = ScoreInfoModel::where($where)->limit($startIndex,$this->rows)->select();
        /*计算总的页数和总的记录数*/
        $this->recordNumber = ScoreInfoModel::where($where)->count();
        $mod = $this->recordNumber % $this->rows;
        $this->totalPage = (int)($this->recordNumber / $this->rows);
        if($mod != 0) $this->totalPage++;
        return $scoreInfoRs;
    }

    /*按照查询条件查询所有成绩记录*/
  public function queryOutputScoreInfo( $studentObj,  $courseObj) {
        $where = null;
        if($studentObj['studentNumber'] != "") $where['studentObj'] = $studentObj['studentNumber'];
        if($courseObj['courseNumber'] != "") $where['courseObj'] = $courseObj['courseNumber'];
        $scoreInfoRs = ScoreInfoModel::where($where)->select();
        return $scoreInfoRs;
    }

    /*查询所有成绩记录*/
    public function queryAllScoreInfo(){
        $scoreInfoRs = ScoreInfoModel::where(null)->select();
        return $scoreInfoRs;

    }

}
