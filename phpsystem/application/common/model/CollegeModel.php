<?php
namespace app\common\model;
use think\Model;

class CollegeModel extends Model {
    /*关联表名*/
    protected $table  = 't_college';
    /*每页显示记录数目*/
    public $rows = 8;
    /*保存查询后总的页数*/
    public $totalPage;
    /*保存查询到的总记录数*/
    public $recordNumber;

    public function setRows($rows) {
        $this->rows = $rows;
    }

    /*添加学院记录*/
    public function addCollege($college) {
        $this->insert($college);
    }

    /*更新学院记录*/
    public function updateCollege($college) {
        $this->update($college);
    }

    /*删除多条学院信息*/
    public function deleteColleges($collegeNumbers){
        $collegeNumberArray = explode(",",$collegeNumbers);
        foreach ($collegeNumberArray as $collegeNumber) {
            $this->collegeNumber = $collegeNumber;
            $this->delete();
        }
        return count($collegeNumberArray);
    }
    /*根据主键获取学院记录*/
    public function getCollege($collegeNumber) {
        $college = CollegeModel::where("collegeNumber",$collegeNumber)->find();
        return $college;
    }

    /*按照查询条件分页查询学院信息*/
    public function queryCollege($currentPage) {
        $startIndex = ($currentPage-1) * $this->rows;
        $where = null;
        $collegeRs = CollegeModel::where($where)->limit($startIndex,$this->rows)->select();
        /*计算总的页数和总的记录数*/
        $this->recordNumber = CollegeModel::where($where)->count();
        $mod = $this->recordNumber % $this->rows;
        $this->totalPage = (int)($this->recordNumber / $this->rows);
        if($mod != 0) $this->totalPage++;
        return $collegeRs;
    }

    /*按照查询条件查询所有学院记录*/
  public function queryOutputCollege() {
        $where = null;
        $collegeRs = CollegeModel::where($where)->select();
        return $collegeRs;
    }

    /*查询所有学院记录*/
    public function queryAllCollege(){
        $collegeRs = CollegeModel::where(null)->select();
        return $collegeRs;

    }

}
