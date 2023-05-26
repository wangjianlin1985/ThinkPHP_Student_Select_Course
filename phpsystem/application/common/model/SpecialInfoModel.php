<?php
namespace app\common\model;
use think\Model;

class SpecialInfoModel extends Model {
    /*关联表名*/
    protected $table  = 't_specialInfo';
    /*每页显示记录数目*/
    public $rows = 8;
    /*保存查询后总的页数*/
    public $totalPage;
    /*保存查询到的总记录数*/
    public $recordNumber;

    public function setRows($rows) {
        $this->rows = $rows;
    }

    //所在学院复合属性的获取: 多对一关系
    public function specialCollegeNumberF(){
        return $this->belongsTo('CollegeModel','specialCollegeNumber');
    }

    /*添加专业记录*/
    public function addSpecialInfo($specialInfo) {
        $this->insert($specialInfo);
    }

    /*更新专业记录*/
    public function updateSpecialInfo($specialInfo) {
        $this->update($specialInfo);
    }

    /*删除多条专业信息*/
    public function deleteSpecialInfos($specialFieldNumbers){
        $specialFieldNumberArray = explode(",",$specialFieldNumbers);
        foreach ($specialFieldNumberArray as $specialFieldNumber) {
            $this->specialFieldNumber = $specialFieldNumber;
            $this->delete();
        }
        return count($specialFieldNumberArray);
    }
    /*根据主键获取专业记录*/
    public function getSpecialInfo($specialFieldNumber) {
        $specialInfo = SpecialInfoModel::where("specialFieldNumber",$specialFieldNumber)->find();
        return $specialInfo;
    }

    /*按照查询条件分页查询专业信息*/
    public function querySpecialInfo($specialFieldNumber, $specialFieldName, $specialCollegeNumber, $specialBirthDate, $currentPage) {
        $startIndex = ($currentPage-1) * $this->rows;
        $where = null;
        if($specialFieldNumber != "") $where['specialFieldNumber'] = array('like','%'.$specialFieldNumber.'%');
        if($specialFieldName != "") $where['specialFieldName'] = array('like','%'.$specialFieldName.'%');
        if($specialCollegeNumber['collegeNumber'] != "") $where['specialCollegeNumber'] = $specialCollegeNumber['collegeNumber'];
        if($specialBirthDate != "") $where['specialBirthDate'] = array('like','%'.$specialBirthDate.'%');
        $specialInfoRs = SpecialInfoModel::where($where)->limit($startIndex,$this->rows)->select();
        /*计算总的页数和总的记录数*/
        $this->recordNumber = SpecialInfoModel::where($where)->count();
        $mod = $this->recordNumber % $this->rows;
        $this->totalPage = (int)($this->recordNumber / $this->rows);
        if($mod != 0) $this->totalPage++;
        return $specialInfoRs;
    }

    /*按照查询条件查询所有专业记录*/
  public function queryOutputSpecialInfo( $specialFieldNumber,  $specialFieldName,  $specialCollegeNumber,  $specialBirthDate) {
        $where = null;
        if($specialFieldNumber != "") $where['specialFieldNumber'] = array('like','%'.$specialFieldNumber.'%');
        if($specialFieldName != "") $where['specialFieldName'] = array('like','%'.$specialFieldName.'%');
        if($specialCollegeNumber['collegeNumber'] != "") $where['specialCollegeNumber'] = $specialCollegeNumber['collegeNumber'];
        if($specialBirthDate != "") $where['specialBirthDate'] = array('like','%'.$specialBirthDate.'%');
        $specialInfoRs = SpecialInfoModel::where($where)->select();
        return $specialInfoRs;
    }

    /*查询所有专业记录*/
    public function queryAllSpecialInfo(){
        $specialInfoRs = SpecialInfoModel::where(null)->select();
        return $specialInfoRs;

    }

}
