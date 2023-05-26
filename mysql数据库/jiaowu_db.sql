/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50051
Source Host           : localhost:3306
Source Database       : jiaowu_db

Target Server Type    : MYSQL
Target Server Version : 50051
File Encoding         : 65001

Date: 2018-11-27 23:08:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `t_admin`
-- ----------------------------
DROP TABLE IF EXISTS `t_admin`;
CREATE TABLE `t_admin` (
  `username` varchar(20) NOT NULL default '',
  `password` varchar(32) default NULL,
  PRIMARY KEY  (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_admin
-- ----------------------------
INSERT INTO `t_admin` VALUES ('a', 'a');

-- ----------------------------
-- Table structure for `t_classinfo`
-- ----------------------------
DROP TABLE IF EXISTS `t_classinfo`;
CREATE TABLE `t_classinfo` (
  `classNumber` varchar(20) NOT NULL COMMENT 'classNumber',
  `className` varchar(20) NOT NULL COMMENT '班级名称',
  `classSpecialFieldNumber` varchar(20) NOT NULL COMMENT '所属专业',
  `classBirthDate` varchar(20) default NULL COMMENT '成立日期',
  `classTeacherCharge` varchar(12) default NULL COMMENT '班主任',
  `classTelephone` varchar(20) default NULL COMMENT '联系电话',
  `classMemo` varchar(500) default NULL COMMENT '附加信息',
  PRIMARY KEY  (`classNumber`),
  KEY `classSpecialFieldNumber` (`classSpecialFieldNumber`),
  CONSTRAINT `t_classinfo_ibfk_1` FOREIGN KEY (`classSpecialFieldNumber`) REFERENCES `t_specialinfo` (`specialFieldNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_classinfo
-- ----------------------------
INSERT INTO `t_classinfo` VALUES ('BJ001', '计算机3班', 'ZY001', '2018-11-08', '王刚', '13958084034', '新开班级');
INSERT INTO `t_classinfo` VALUES ('BJ002', '电子技术1班', 'ZY002', '2018-11-08', '张明涛', '13950830843', '电子技术班');

-- ----------------------------
-- Table structure for `t_college`
-- ----------------------------
DROP TABLE IF EXISTS `t_college`;
CREATE TABLE `t_college` (
  `collegeNumber` varchar(20) NOT NULL COMMENT 'collegeNumber',
  `collegeName` varchar(20) NOT NULL COMMENT '学院名称',
  `collegeBirthDate` varchar(20) default NULL COMMENT '成立日期',
  `collegeMan` varchar(10) default NULL COMMENT '院长姓名',
  `collegeTelephone` varchar(20) default NULL COMMENT '联系电话',
  `collegeMemo` varchar(500) default NULL COMMENT '附加信息',
  PRIMARY KEY  (`collegeNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_college
-- ----------------------------
INSERT INTO `t_college` VALUES ('XY001', '信息工程学院', '2018-11-07', '王建杰', '028-82980834', '特色学院');
INSERT INTO `t_college` VALUES ('XY002', '外国语学院', '2018-11-16', '牛小明', '028-82908034', '测试');

-- ----------------------------
-- Table structure for `t_course`
-- ----------------------------
DROP TABLE IF EXISTS `t_course`;
CREATE TABLE `t_course` (
  `courseNumber` varchar(20) NOT NULL COMMENT 'courseNumber',
  `courseName` varchar(20) NOT NULL COMMENT '课程名称',
  `courseTeacher` varchar(20) NOT NULL COMMENT '开课老师',
  `courseTime` varchar(40) default NULL COMMENT '上课时间',
  `coursePlace` varchar(40) default NULL COMMENT '上课地点',
  `courseScore` float NOT NULL COMMENT '课程学分',
  `courseMemo` varchar(100) default NULL COMMENT '附加信息',
  PRIMARY KEY  (`courseNumber`),
  KEY `courseTeacher` (`courseTeacher`),
  CONSTRAINT `t_course_ibfk_1` FOREIGN KEY (`courseTeacher`) REFERENCES `t_teacher` (`teacherNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_course
-- ----------------------------
INSERT INTO `t_course` VALUES ('KC001', 'php网站开发编程', 'TH001', '每周2和周四下午', '6A-203', '4', 'ceshi');
INSERT INTO `t_course` VALUES ('KC002', 'Java基础编程', 'TH002', '周一上午和周5下午1-2节', '7B-302', '3.5', 'aaa');
INSERT INTO `t_course` VALUES ('KC003', '安卓手机app开发', 'TH001', '周一下午和晚上', '6C-101', '3.5', '测试课程');

-- ----------------------------
-- Table structure for `t_courseselect`
-- ----------------------------
DROP TABLE IF EXISTS `t_courseselect`;
CREATE TABLE `t_courseselect` (
  `selectId` int(11) NOT NULL auto_increment COMMENT '记录编号',
  `studentNumber` varchar(30) NOT NULL COMMENT '学生',
  `courseNumber` varchar(20) NOT NULL COMMENT '课程',
  `selectTime` varchar(20) default NULL COMMENT '选课时间',
  PRIMARY KEY  (`selectId`),
  KEY `studentNumber` (`studentNumber`),
  KEY `courseNumber` (`courseNumber`),
  CONSTRAINT `t_courseselect_ibfk_2` FOREIGN KEY (`courseNumber`) REFERENCES `t_course` (`courseNumber`),
  CONSTRAINT `t_courseselect_ibfk_1` FOREIGN KEY (`studentNumber`) REFERENCES `t_student` (`studentNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_courseselect
-- ----------------------------
INSERT INTO `t_courseselect` VALUES ('4', 'xs001', 'KC001', '2018-11-27 20:31:48');
INSERT INTO `t_courseselect` VALUES ('5', 'xs002', 'KC002', '2018-11-27 21:34:05');
INSERT INTO `t_courseselect` VALUES ('6', 'xs002', 'KC001', '2018-11-27 22:40:43');
INSERT INTO `t_courseselect` VALUES ('7', 'xs003', 'KC001', '2018-11-27 23:05:40');
INSERT INTO `t_courseselect` VALUES ('8', 'xs004', 'KC001', '2018-11-27 23:05:55');
INSERT INTO `t_courseselect` VALUES ('9', 'xs001', 'KC002', '2018-11-27 23:06:16');

-- ----------------------------
-- Table structure for `t_news`
-- ----------------------------
DROP TABLE IF EXISTS `t_news`;
CREATE TABLE `t_news` (
  `newsId` int(11) NOT NULL auto_increment COMMENT '记录编号',
  `newsTitle` varchar(50) NOT NULL COMMENT '新闻标题',
  `newsPhoto` varchar(60) NOT NULL COMMENT '新闻图片',
  `newsContent` varchar(500) NOT NULL COMMENT '新闻内容',
  `newsDate` varchar(20) default NULL COMMENT '发布日期',
  PRIMARY KEY  (`newsId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_news
-- ----------------------------
INSERT INTO `t_news` VALUES ('1', 'PHP教务选课系统网站开通', 'upload/NoImage.jpg', '同学们赶快来这里选课吧！', '2018-11-26');

-- ----------------------------
-- Table structure for `t_scoreinfo`
-- ----------------------------
DROP TABLE IF EXISTS `t_scoreinfo`;
CREATE TABLE `t_scoreinfo` (
  `scoreId` int(11) NOT NULL auto_increment COMMENT '记录编号',
  `studentObj` varchar(30) NOT NULL COMMENT '学生',
  `courseObj` varchar(20) NOT NULL COMMENT '课程',
  `scoreValue` float NOT NULL COMMENT '成绩得分',
  `studentEvaluate` varchar(100) default NULL COMMENT '学生评价',
  PRIMARY KEY  (`scoreId`),
  KEY `studentObj` (`studentObj`),
  KEY `courseObj` (`courseObj`),
  CONSTRAINT `t_scoreinfo_ibfk_2` FOREIGN KEY (`courseObj`) REFERENCES `t_course` (`courseNumber`),
  CONSTRAINT `t_scoreinfo_ibfk_1` FOREIGN KEY (`studentObj`) REFERENCES `t_student` (`studentNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_scoreinfo
-- ----------------------------
INSERT INTO `t_scoreinfo` VALUES ('1', 'xs001', 'KC001', '93', '还不错');
INSERT INTO `t_scoreinfo` VALUES ('3', 'xs002', 'KC002', '89', '比较好了');
INSERT INTO `t_scoreinfo` VALUES ('4', 'xs002', 'KC001', '68', '一般般');
INSERT INTO `t_scoreinfo` VALUES ('5', 'xs003', 'KC001', '52', '太差了');
INSERT INTO `t_scoreinfo` VALUES ('6', 'xs004', 'KC001', '75', '综合水平');

-- ----------------------------
-- Table structure for `t_specialinfo`
-- ----------------------------
DROP TABLE IF EXISTS `t_specialinfo`;
CREATE TABLE `t_specialinfo` (
  `specialFieldNumber` varchar(20) NOT NULL COMMENT 'specialFieldNumber',
  `specialFieldName` varchar(20) NOT NULL COMMENT '专业名称',
  `specialCollegeNumber` varchar(20) NOT NULL COMMENT '所在学院',
  `specialBirthDate` varchar(20) default NULL COMMENT '成立日期',
  `specialMan` varchar(10) default NULL COMMENT '联系人',
  `specialTelephone` varchar(20) default NULL COMMENT '联系电话',
  `specialMemo` varchar(500) default NULL COMMENT '附加信息',
  PRIMARY KEY  (`specialFieldNumber`),
  KEY `specialCollegeNumber` (`specialCollegeNumber`),
  CONSTRAINT `t_specialinfo_ibfk_1` FOREIGN KEY (`specialCollegeNumber`) REFERENCES `t_college` (`collegeNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_specialinfo
-- ----------------------------
INSERT INTO `t_specialinfo` VALUES ('ZY001', '计算机科学', 'XY001', '2018-11-08', '王斌', '028-89840835', '前沿专业，就业率高');
INSERT INTO `t_specialinfo` VALUES ('ZY002', '电子信息工程', 'XY001', '2018-11-13', '张国斌', '028-89809843', '搞电子的');

-- ----------------------------
-- Table structure for `t_student`
-- ----------------------------
DROP TABLE IF EXISTS `t_student`;
CREATE TABLE `t_student` (
  `studentNumber` varchar(30) NOT NULL COMMENT 'studentNumber',
  `studentName` varchar(12) NOT NULL COMMENT '姓名',
  `studentPassword` varchar(30) NOT NULL COMMENT '登录密码',
  `studentSex` varchar(2) NOT NULL COMMENT '性别',
  `studentClassNumber` varchar(20) NOT NULL COMMENT '所在班级',
  `studentBirthday` varchar(20) default NULL COMMENT '出生日期',
  `studentState` varchar(20) default NULL COMMENT '政治面貌',
  `studentPhoto` varchar(60) NOT NULL COMMENT '学生照片',
  `studentTelephone` varchar(20) default NULL COMMENT '联系电话',
  `studentEmail` varchar(30) default NULL COMMENT '学生邮箱',
  `studentQQ` varchar(20) default NULL COMMENT '联系qq',
  `studentAddress` varchar(100) default NULL COMMENT '家庭地址',
  `studentMemo` varchar(100) default NULL COMMENT '附加信息',
  PRIMARY KEY  (`studentNumber`),
  KEY `studentClassNumber` (`studentClassNumber`),
  CONSTRAINT `t_student_ibfk_1` FOREIGN KEY (`studentClassNumber`) REFERENCES `t_classinfo` (`classNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_student
-- ----------------------------
INSERT INTO `t_student` VALUES ('xs001', '张晓芬', '123', '女', 'BJ001', '2018-11-07', '团员', 'upload/de8aa60242b8da8beb5a9f448abe05d0.jpg', '13939509343', 'xiaofen@163.com', '13058105', '四川成都红星路', '9');
INSERT INTO `t_student` VALUES ('xs002', '王文琦', '123', '女', 'BJ001', '2018-11-14', '党员', 'upload/4c9c3575b43db68bf24a99764572fb92.jpg', '13508503543', 'wenqi@163.com', '50181058', '四川南充市滨江路10号', '11');
INSERT INTO `t_student` VALUES ('xs003', '学生3', '123', '女', 'BJ001', '2018-11-07', '团员', 'upload/NoImage.jpg', '13508503543', 'xuesheng3@163.com', '5018560141', '地址3', '测试');
INSERT INTO `t_student` VALUES ('xs004', '学生4', '123', '男', 'BJ002', '2018-11-14', '党员', 'upload/NoImage.jpg', '13985080843', 'xuesheng4@163.com', '98341058', '地址4', '44444');

-- ----------------------------
-- Table structure for `t_teacher`
-- ----------------------------
DROP TABLE IF EXISTS `t_teacher`;
CREATE TABLE `t_teacher` (
  `teacherNumber` varchar(20) NOT NULL COMMENT 'teacherNumber',
  `collegeObj` varchar(20) NOT NULL COMMENT '所在学院',
  `teacherName` varchar(12) NOT NULL COMMENT '教师姓名',
  `teacherPassword` varchar(30) default NULL COMMENT '登录密码',
  `teacherSex` varchar(2) NOT NULL COMMENT '性别',
  `teacherPhoto` varchar(60) NOT NULL COMMENT '教师照片',
  `teacherBirthday` varchar(20) default NULL COMMENT '出生日期',
  `teacherArriveDate` varchar(20) default NULL COMMENT '入职日期',
  `teacherPhone` varchar(20) default NULL COMMENT '联系电话',
  `teacherMail` varchar(50) NOT NULL COMMENT '教师邮箱',
  `teacherDesc` varchar(8000) NOT NULL COMMENT '教师简介',
  PRIMARY KEY  (`teacherNumber`),
  KEY `collegeObj` (`collegeObj`),
  CONSTRAINT `t_teacher_ibfk_1` FOREIGN KEY (`collegeObj`) REFERENCES `t_college` (`collegeNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_teacher
-- ----------------------------
INSERT INTO `t_teacher` VALUES ('TH001', 'XY001', '张明涛', '123', '男', 'upload/7f9c3830f014987a8a3dc8b5a96187de.jpg', '2018-11-08', '2018-11-05', '13950804352', 'mingtao@163.com', '<p>老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,老师技术好，教学认真，讲课清晰,</p>');
INSERT INTO `t_teacher` VALUES ('TH002', 'XY001', '李晓明', '123', '女', 'upload/bdb8b2c8b12d376caf465be5e33674c5.jpg', '2018-11-08', '2018-11-14', '13058080853', 'xiaoming@163.com', '<p>老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，老师勤劳认真，讲课生动形象，对学生耐心辅导，很负责，</p>');
