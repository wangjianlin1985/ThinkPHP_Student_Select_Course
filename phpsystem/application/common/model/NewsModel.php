<?php
namespace app\common\model;
use think\Model;

class NewsModel extends Model {
    /*关联表名*/
    protected $table  = 't_news';
    /*每页显示记录数目*/
    public $rows = 8;
    /*保存查询后总的页数*/
    public $totalPage;
    /*保存查询到的总记录数*/
    public $recordNumber;

    public function setRows($rows) {
        $this->rows = $rows;
    }

    /*添加新闻记录*/
    public function addNews($news) {
        $this->insert($news);
    }

    /*更新新闻记录*/
    public function updateNews($news) {
        $this->update($news);
    }

    /*删除多条新闻信息*/
    public function deleteNewss($newsIds){
        $newsIdArray = explode(",",$newsIds);
        foreach ($newsIdArray as $newsId) {
            $this->newsId = $newsId;
            $this->delete();
        }
        return count($newsIdArray);
    }
    /*根据主键获取新闻记录*/
    public function getNews($newsId) {
        $news = NewsModel::where("newsId",$newsId)->find();
        return $news;
    }

    /*按照查询条件分页查询新闻信息*/
    public function queryNews($newsTitle, $newsDate, $currentPage) {
        $startIndex = ($currentPage-1) * $this->rows;
        $where = null;
        if($newsTitle != "") $where['newsTitle'] = array('like','%'.$newsTitle.'%');
        if($newsDate != "") $where['newsDate'] = array('like','%'.$newsDate.'%');
        $newsRs = NewsModel::where($where)->limit($startIndex,$this->rows)->select();
        /*计算总的页数和总的记录数*/
        $this->recordNumber = NewsModel::where($where)->count();
        $mod = $this->recordNumber % $this->rows;
        $this->totalPage = (int)($this->recordNumber / $this->rows);
        if($mod != 0) $this->totalPage++;
        return $newsRs;
    }

    /*按照查询条件查询所有新闻记录*/
  public function queryOutputNews( $newsTitle,  $newsDate) {
        $where = null;
        if($newsTitle != "") $where['newsTitle'] = array('like','%'.$newsTitle.'%');
        if($newsDate != "") $where['newsDate'] = array('like','%'.$newsDate.'%');
        $newsRs = NewsModel::where($where)->select();
        return $newsRs;
    }

    /*查询所有新闻记录*/
    public function queryAllNews(){
        $newsRs = NewsModel::where(null)->select();
        return $newsRs;

    }

}
