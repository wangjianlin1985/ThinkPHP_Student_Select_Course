<?php
namespace app\back\controller;
use think\Request;
use think\Exception;
use app\common\model\NewsModel;

class News extends BackBase {
    protected $newsModel;

    //控制层对象初始化：注入业务逻辑层对象等
    public function _initialize() {
        parent::_initialize();
        $this->request = Request::instance();
        $this->newsModel = new NewsModel();
    }

    /*添加新闻信息*/
    public function add(){
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
            return view('news/news_add');
        }
    }

    /*跳转到更新界面*/
    public function modifyView() {
        $this->assign("newsId",input("newsId"));
        return view("news/news_modify");
    }

    /*ajax方式按照查询条件分页查询新闻信息*/
    public function backList() {
        if($this->request->isPost()) {
            if($this->request->param("page") != null)
                $this->currentPage = $this->request->param("page");
            if($this->request->param("rows") != null)
                $this->newsModel->setRows($this->request->param("rows"));
            $newsTitle = input("newsTitle")==null?"":input("newsTitle");
            $newsDate = input("newsDate")==null?"":input("newsDate");
            $newsRs = $this->newsModel->queryNews($newsTitle, $newsDate, $this->currentPage);
            $expTableData = [];
            foreach($newsRs as $newsRow) {
                $expTableData[] = $newsRow;
            }
            $data["rows"] = $newsRs;
            /*当前查询条件下总记录数*/
            $data["total"] = $this->newsModel->recordNumber;
            echo json_encode($data);
        } else {
            return view("news/news_query");
        }
    }

    /*ajax方式查询新闻信息*/
    public function listAll() {
        $newsRs = $this->newsModel->queryAllNews();
        echo json_encode($newsRs);
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

    /*按照查询条件导出新闻信息到Excel*/
    public function outToExcel() {
        $newsTitle = input("newsTitle")==null?"":input("newsTitle");
        $newsDate = input("newsDate")==null?"":input("newsDate");
        $newsRs = $this->newsModel->queryOutputNews($newsTitle,$newsDate);
        $expTableData = [];
        foreach($newsRs as $newsRow) {
            $expTableData[] = $newsRow;
        }
        $xlsName = "News";
        $xlsCell = array(
            array('newsId','记录编号','int'),
            array('newsTitle','新闻标题','string'),
            array('newsPhoto','新闻图片','photo'),
            array('newsContent','新闻内容','string'),
            array('newsDate','发布日期','string'),
        );//查出字段输出对应Excel对应的列名
        //公共方法调用
        $this->export_excel($xlsName,$xlsCell,$expTableData);
    }

}

