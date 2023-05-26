<?php
namespace app\back\controller;
use think\Controller;

class BackBase extends Controller
{
    protected $currentPage = 1;
    protected $request = null;

    public function _initialize(){
        if(!session('username')){
            $this->error('请先登录系统！','Login/index');
        }
    }

    public function writeJsonResponse($success, $message) {
        $response = array(
            "success" => $success,
            "message" => $message,
        );
        echo json_encode($response);
    }

    /**
     * @param $obj:  保存图片路径的对象
     * @param $indexName 索引名称
     * @param $photoFiledName 提交的图片表单名称
     */
    public function uploadPhoto(&$obj,$indexName,$photoFiledName) {
        if($_FILES[$photoFiledName]['tmp_name']){
            $file = $this->request->file($photoFiledName);
            //控制上传的文件类型，大小
            if(!(($_FILES[$photoFiledName]["type"]=="image/jpeg"
                    || $_FILES[$photoFiledName]["type"]=="image/jpg"
                    || $_FILES[$photoFiledName]["type"]=="image/png") && $_FILES[$photoFiledName]["size"] < 1024000)) {
                $message = "图书图片请选择jpg或png格式的图片!";
                $this->writeJsonResponse(false,$message);
                exit;
            }
            $file->setRule("short"); //文件路径采用简短方式
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            $obj[$indexName]='upload/'.$info->getSaveName();
        }
    }

    /**
     * @param $obj:  保存文件路径的对象
     * @param $indexName 索引名称
     * @param $resourceFiledName 提交的文件表单名称
     */
    public function uploadFile(&$obj,$indexName,$resourceFiledName) {
        if($_FILES[$resourceFiledName]['tmp_name']){
            $file = $this->request->file($resourceFiledName);
            $file->setRule("short"); //文件路径采用简短方式
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            $obj[$indexName]='upload/'.$info->getSaveName();
        }
    }

    //接收提交的College信息参数
    public function getCollegeForm($insertFlag) {
        $college = [
            'collegeNumber'=> input("college_collegeNumber"), //学院编号
            'collegeName'=> input("college_collegeName"), //学院名称
            'collegeBirthDate'=> input("college_collegeBirthDate"), //成立日期
            'collegeMan'=> input("college_collegeMan"), //院长姓名
            'collegeTelephone'=> input("college_collegeTelephone"), //联系电话
            'collegeMemo'=> input("college_collegeMemo"), //附加信息
        ];
        return $college;
    }

    //接收提交的SpecialInfo信息参数
    public function getSpecialInfoForm($insertFlag) {
        $specialInfo = [
            'specialFieldNumber'=> input("specialInfo_specialFieldNumber"), //专业编号
            'specialFieldName'=> input("specialInfo_specialFieldName"), //专业名称
            'specialCollegeNumber'=> input("specialInfo_specialCollegeNumber_collegeNumber"), //所在学院
            'specialBirthDate'=> input("specialInfo_specialBirthDate"), //成立日期
            'specialMan'=> input("specialInfo_specialMan"), //联系人
            'specialTelephone'=> input("specialInfo_specialTelephone"), //联系电话
            'specialMemo'=> input("specialInfo_specialMemo"), //附加信息
        ];
        return $specialInfo;
    }

    //接收提交的ClassInfo信息参数
    public function getClassInfoForm($insertFlag) {
        $classInfo = [
            'classNumber'=> input("classInfo_classNumber"), //班级编号
            'className'=> input("classInfo_className"), //班级名称
            'classSpecialFieldNumber'=> input("classInfo_classSpecialFieldNumber_specialFieldNumber"), //所属专业
            'classBirthDate'=> input("classInfo_classBirthDate"), //成立日期
            'classTeacherCharge'=> input("classInfo_classTeacherCharge"), //班主任
            'classTelephone'=> input("classInfo_classTelephone"), //联系电话
            'classMemo'=> input("classInfo_classMemo"), //附加信息
        ];
        return $classInfo;
    }

    //接收提交的Student信息参数
    public function getStudentForm($insertFlag) {
        $student = [
            'studentNumber'=> input("student_studentNumber"), //学号
            'studentName'=> input("student_studentName"), //姓名
            'studentPassword'=> input("student_studentPassword"), //登录密码
            'studentSex'=> input("student_studentSex"), //性别
            'studentClassNumber'=> input("student_studentClassNumber_classNumber"), //所在班级
            'studentBirthday'=> input("student_studentBirthday"), //出生日期
            'studentState'=> input("student_studentState"), //政治面貌
            'studentPhoto' => $insertFlag==true?"upload/NoImage.jpg":input("student_studentPhoto"), //学生照片
            'studentTelephone'=> input("student_studentTelephone"), //联系电话
            'studentEmail'=> input("student_studentEmail"), //学生邮箱
            'studentQQ'=> input("student_studentQQ"), //联系qq
            'studentAddress'=> input("student_studentAddress"), //家庭地址
            'studentMemo'=> input("student_studentMemo"), //附加信息
        ];
        return $student;
    }

    //接收提交的Teacher信息参数
    public function getTeacherForm($insertFlag) {
        $teacher = [
            'teacherNumber'=> input("teacher_teacherNumber"), //教师工号
            'collegeObj'=> input("teacher_collegeObj_collegeNumber"), //所在学院
            'teacherName'=> input("teacher_teacherName"), //教师姓名
            'teacherPassword'=> input("teacher_teacherPassword"), //登录密码
            'teacherSex'=> input("teacher_teacherSex"), //性别
            'teacherPhoto' => $insertFlag==true?"upload/NoImage.jpg":input("teacher_teacherPhoto"), //教师照片
            'teacherBirthday'=> input("teacher_teacherBirthday"), //出生日期
            'teacherArriveDate'=> input("teacher_teacherArriveDate"), //入职日期
            'teacherPhone'=> input("teacher_teacherPhone"), //联系电话
            'teacherMail'=> input("teacher_teacherMail"), //教师邮箱
            'teacherDesc'=> input("teacher_teacherDesc"), //教师简介
        ];
        return $teacher;
    }

    //接收提交的Course信息参数
    public function getCourseForm($insertFlag) {
        $course = [
            'courseNumber'=> input("course_courseNumber"), //课程编号
            'courseName'=> input("course_courseName"), //课程名称
            'courseTeacher'=> input("course_courseTeacher_teacherNumber"), //开课老师
            'courseTime'=> input("course_courseTime"), //上课时间
            'coursePlace'=> input("course_coursePlace"), //上课地点
            'courseScore'=> input("course_courseScore"), //课程学分
            'courseMemo'=> input("course_courseMemo"), //附加信息
        ];
        return $course;
    }

    //接收提交的CourseSelect信息参数
    public function getCourseSelectForm($insertFlag) {
        $courseSelect = [
            'selectId'=> input("courseSelect_selectId"), //记录编号
            'studentNumber'=> input("courseSelect_studentNumber_studentNumber"), //学生
            'courseNumber'=> input("courseSelect_courseNumber_courseNumber"), //课程
            'selectTime'=> input("courseSelect_selectTime"), //选课时间
        ];
        return $courseSelect;
    }

    //接收提交的ScoreInfo信息参数
    public function getScoreInfoForm($insertFlag) {
        $scoreInfo = [
            'scoreId'=> input("scoreInfo_scoreId"), //记录编号
            'studentObj'=> input("scoreInfo_studentObj_studentNumber"), //学生
            'courseObj'=> input("scoreInfo_courseObj_courseNumber"), //课程
            'scoreValue'=> input("scoreInfo_scoreValue"), //成绩得分
            'studentEvaluate'=> input("scoreInfo_studentEvaluate"), //学生评价
        ];
        return $scoreInfo;
    }

    //接收提交的News信息参数
    public function getNewsForm($insertFlag) {
        $news = [
            'newsId'=> input("news_newsId"), //记录编号
            'newsTitle'=> input("news_newsTitle"), //新闻标题
            'newsPhoto' => $insertFlag==true?"upload/NoImage.jpg":input("news_newsPhoto"), //新闻图片
            'newsContent'=> input("news_newsContent"), //新闻内容
            'newsDate'=> input("news_newsDate"), //发布日期
        ];
        return $news;
    }

    /** * 公共数据导出实现功能
     * @param $expTitle 导出文件名
     * @param $expCellName 导出文件列名称
     * @param $expTableData 导出数据
     */
    public function export_excel($expTitle,$expCellName,$expTableData) {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle . date('_Ymd');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        //这些文件需要下载phpexcel，然后放在vendor文件里面。具体参考上一篇数据导出。
        vendor("PHPExcel.PHPExcel");
        //vendor("PHPExcel.PHPExcel.Writer.IWriter");
        //vendor("PHPExcel.PHPExcel.Writer.Abstract");
        //vendor("PHPExcel.PHPExcel.Writer.Excel5");
        //vendor("PHPExcel.PHPExcel.Writer.Excel2007");
        vendor("PHPExcel.PHPExcel.IOFactory");
        $objPHPExcel = new \PHPExcel();//方法一
        $cellName = array('A','B', 'C','D', 'E', 'F','G','H','I', 'J', 'K','L','M', 'N', 'O', 'P', 'Q','R','S', 'T','U','V', 'W', 'X','Y', 'Z', 'AA',    'AB', 'AC','AD','AE', 'AF','AG','AH','AI', 'AJ', 'AK', 'AL','AM','AN','AO','AP','AQ','AR', 'AS', 'AT','AU', 'AV','AW', 'AX', 'AY', 'AZ');
        //设置头部导出时间备注
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1');//合并单元格
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . ' 导出时间:' . date('Y-m-d H:i:s'));
        //设置列名称
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
            $objPHPExcel->getActiveSheet()->getColumnDimension($cellName[$i])->setWidth(20); //设置每列宽度
            $objPHPExcel->getActiveSheet()->getStyle($cellName[$i].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($cellName[$i])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中对齐
        }
        //赋值
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet()->getStyle($cellName[$j].($i + 3))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                if($expCellName[$j][2] == 'photo') {
                    try {
                        // 表格高度
                        $objPHPExcel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(80);
                        // 图片生成
                        $objDrawing = new \PHPExcel_Worksheet_Drawing();
                        $objDrawing->setPath(PUBLIC_PATH.$expTableData[$i][$expCellName[$j][0]]);
                        // 设置宽度高度
                        $objDrawing->setHeight(80);//照片高度
                        $objDrawing->setWidth(80); //照片宽度
                        /*设置图片要插入的单元格*/
                        $objDrawing->setCoordinates($cellName[$j].($i + 3));
                        // 图片偏移距离
                        $objDrawing->setOffsetX(5);
                        $objDrawing->setOffsetY(5);
                        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    } catch (\Exception $ex) {}
                } else {
                    $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i + 3), $expTableData[$i][$expCellName[$j][0]]);
                }
            }
        }
        ob_end_clean();//这一步非常关键，用来清除缓冲区防止导出的excel乱码
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//"xls"参考下一条备注
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'  );//"Excel2007"生成2007版本的xlsx，"Excel5"生成2003版本的xls
        $objWriter->save('php://output');
    }
}

