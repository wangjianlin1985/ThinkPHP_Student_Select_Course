<?php
namespace app\front\controller;
use think\Request;
use think\Exception;
use app\common\model\NewsModel;

class News extends Base {
    protected $newsModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->newsModel = new NewsModel();
    }

    /*添加新闻信息*/
    public function frontAdd(){
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $news = $this->getNewsForm(true);
            $this->uploadPhoto($news,"newsPhoto","newsPhotoFile"); //处理新闻图片上传
            try {
                $this->newsModel->addNews($news);
                $message = "新闻添加成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "新闻添加失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            return $this->fetch('news/news_frontAdd');
        }
    }

    /*前台修改新闻信息*/
    public function frontModify() {
        $this->assign("newsId",input("newsId"));
        return $this->fetch("news/news_frontModify");
    }

    /*前台按照查询条件分页查询新闻信息*/
    public function frontlist() {
        if($this->request->param("currentPage") != null)
            $this->currentPage = $this->request->param("currentPage");
        $newsTitle = input("newsTitle")==null?"":input("newsTitle");
        $newsDate = input("newsDate")==null?"":input("newsDate");
        $newsRs = $this->newsModel->queryNews($newsTitle, $newsDate, $this->currentPage);
        $this->assign("newsRs",$newsRs);
        /*获取到总的页码数目*/
        $this->assign("totalPage",$this->newsModel->totalPage);
        /*当前查询条件下总记录数*/
        $this->assign("recordNumber",$this->newsModel->recordNumber);
        $this->assign("currentPage",$this->currentPage);
        $this->assign("rows",$this->newsModel->rows);
        $this->assign("newsTitle",$newsTitle);
        $this->assign("newsDate",$newsDate);
        return $this->fetch('news/news_frontlist');
    }

    /*ajax方式查询新闻信息*/
    public function listAll() {
        $newsRs = $this->newsModel->queryAllNews();
        echo json_encode($newsRs);
    }
    /*前台查询根据主键查询一条新闻信息*/
    public function frontshow() {
        $newsId = input("newsId");
        $news = $this->newsModel->getNews($newsId);
       $this->assign("news",$news);
        return $this->fetch("news/news_frontshow");
    }

    /*更新新闻信息*/
    public function update() {
        $message = "";
        $success = false;
        if($this->request->isPost()) {
            $news = $this->getNewsForm(false);
            $this->uploadPhoto($news,"newsPhoto","newsPhotoFile"); //处理新闻图片上传
            try {
                $this->newsModel->updateNews($news);
                $message = "新闻更新成功!";
                $success = true;
                $this->writeJsonResponse($success, $message);
            } catch (Exception $ex) {
                $message = "新闻更新失败!";
                $this->writeJsonResponse($success,$message);
            }
        } else {
            /*根据主键获取新闻对象*/
            $newsId = input("newsId");
            $news = $this->newsModel->getNews($newsId);
            echo json_encode($news);
        }
    }

    /*删除多条新闻记录*/
    public function deletes() {
        $message = "";
        $success = false;
        $newsIds = input("newsIds");
        try {
            $count = $this->newsModel->deleteNewss($newsIds);
            $success = true;
            $message = $count."条记录删除成功";
            $this->writeJsonResponse($success, $message);
        } catch (Exception $ex) {
            $message = "有记录存在外键约束,删除失败";
            $this->writeJsonResponse($success, $message);
        }
    }

}

