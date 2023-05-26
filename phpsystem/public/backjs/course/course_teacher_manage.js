var course_manage_tool = null; 
$(function () { 
	initCourseManageTool(); //建立Course管理对象
	course_manage_tool.init(); //如果需要通过下拉框查询，首先初始化下拉框的值
	$("#course_manage").datagrid({
		url : backURL + getVisitPath("Course") + '/backTeacherList',
		fit : true,
		fitColumns : true,
		striped : true,
		rownumbers : true,
		border : false,
		pagination : true,
		pageSize : 5,
		pageList : [5, 10, 15, 20, 25],
		pageNumber : 1,
		sortName : "courseNumber",
		sortOrder : "desc",
		toolbar : "#course_manage_tool",
		columns : [[
			{
				field : "courseNumber",
				title : "课程编号",
				width : 140,
			},
			{
				field : "courseName",
				title : "课程名称",
				width : 140,
			},
			{
				field : "courseTeacher",
				title : "开课老师",
				width : 140,
			},
			{
				field : "courseTime",
				title : "上课时间",
				width : 140,
			},
			{
				field : "coursePlace",
				title : "上课地点",
				width : 140,
			},
			{
				field : "courseScore",
				title : "课程学分",
				width : 70,
			},
		]],
	});

	$("#courseEditDiv").dialog({
		title : "修改管理",
		top: "50px",
		width : 700,
		height : 515,
		modal : true,
		closed : true,
		iconCls : "icon-edit-new",
		buttons : [{
			text : "提交",
			iconCls : "icon-edit-new",
			handler : function () {
				if ($("#courseEditForm").form("validate")) {
					//验证表单 
					if(!$("#courseEditForm").form("validate")) {
						$.messager.alert("错误提示","你输入的信息还有错误！","warning");
					} else {
						$("#courseEditForm").form({
						    url: backURL + getVisitPath("Course") + "/update",
						    onSubmit: function(){
								if($("#courseEditForm").form("validate"))  {
				                	$.messager.progress({
										text : "正在提交数据中...",
									});
				                	return true;
				                } else { 
				                    return false; 
				                }
						    },
						    success:function(data){
						    	$.messager.progress("close");
						    	console.log(data);
			                	var obj = jQuery.parseJSON(data);
			                    if(obj.success){
			                        $.messager.alert("消息","信息修改成功！");
			                        $("#courseEditDiv").dialog("close");
			                        course_manage_tool.reload();
			                    }else{
			                        $.messager.alert("消息",obj.message);
			                    } 
						    }
						});
						//提交表单
						$("#courseEditForm").submit();
					}
				}
			},
		},{
			text : "取消",
			iconCls : "icon-redo",
			handler : function () {
				$("#courseEditDiv").dialog("close");
				$("#courseEditForm").form("reset"); 
			},
		}],
	});


    $("#tongjiDiv").dialog({
        title : "成绩统计",
        top: "50px",
        width : 800,
        height : 545,
        modal : true,
        closed : true,
        iconCls : "icon-edit-new",
        buttons : [{
            text : "关闭",
            iconCls : "icon-redo",
            handler : function () {
                $("#tongjiDiv").dialog("close");
            },
        }],
    });

});




function initCourseManageTool() {
	course_manage_tool = {
		init: function() {
			$.ajax({
				url : backURL + getVisitPath("Teacher") + "/listAll",
				type : "post",
				dataType: "json",
				success : function (data, response, status) {
					$("#courseTeacher_teacherNumber_query").combobox({ 
					    valueField:"teacherNumber",
					    textField:"teacherName",
					    panelHeight: "200px",
				        editable: false, //不允许手动输入 
					});
					data.splice(0,0,{teacherNumber:"",teacherName:"不限制"});
					$("#courseTeacher_teacherNumber_query").combobox("loadData",data); 
				}
			});
		},
		reload : function () {
			$("#course_manage").datagrid("reload");
		},
		redo : function () {
			$("#course_manage").datagrid("unselectAll");
		},
		search: function() {
			var queryParams = $("#course_manage").datagrid("options").queryParams;
			queryParams["courseNumber"] = $("#courseNumber").val();
			queryParams["courseName"] = $("#courseName").val();
			queryParams["courseTeacher.teacherNumber"] = $("#courseTeacher_teacherNumber_query").combobox("getValue");
			queryParams["courseTime"] = $("#courseTime").val();
			$("#course_manage").datagrid("options").queryParams=queryParams; 
			$("#course_manage").datagrid("load");
		},
		exportExcel: function() {
			$("#courseQueryForm").form({
			    url: backURL + getVisitPath("Course") + "/outToExcel",
			});
			//提交表单
			$("#courseQueryForm").submit();
		},
		remove : function () {
			var rows = $("#course_manage").datagrid("getSelections");
			if (rows.length > 0) {
				$.messager.confirm("确定操作", "您正在要删除所选的记录吗？", function (flag) {
					if (flag) {
						var courseNumbers = [];
						for (var i = 0; i < rows.length; i ++) {
							courseNumbers.push(rows[i].courseNumber);
						}
						$.ajax({
							type : "POST",
							url :  backURL + getVisitPath("Course") + "/deletes",
							data : {
								courseNumbers : courseNumbers.join(","),
							},
							dataType: "json",
							beforeSend : function () {
								$("#course_manage").datagrid("loading");
							},
							success : function (data) {
								if (data.success) {
									$("#course_manage").datagrid("loaded");
									$("#course_manage").datagrid("load");
									$("#course_manage").datagrid("unselectAll");
									$.messager.show({
										title : "提示",
										msg : data.message
									});
								} else {
									$("#course_manage").datagrid("loaded");
									$("#course_manage").datagrid("load");
									$("#course_manage").datagrid("unselectAll");
									$.messager.alert("消息",data.message);
								}
							},
						});
					}
				});
			} else {
				$.messager.alert("提示", "请选择要删除的记录！", "info");
			}
		},
		tongji: function() {
            var rows = $("#course_manage").datagrid("getSelections");
            if (rows.length > 1) {
                $.messager.alert("警告操作！", "编辑记录只能选定一条数据！", "warning");
            } else if (rows.length == 1) {
                $("#tongjiDiv").dialog("open");
                var xData = null;
                var yData = null;
                $.ajax({
                    url: backURL + getVisitPath("Course") + "/tongji",
                    type: "post",
                    dataType: "json",
                    data : {
                        courseNumber : rows[0].courseNumber,
                    },
                    beforeSend : function () {
                        $.messager.progress({
                            text : "正在获取中...",
                        });
                    },
                    success: function(data) {
                        $.messager.progress("close");

                        obj = data;
                        xData = obj.xData;
                        yData = obj.yData;

                        // 初始化图表标签
                        var myChart = echarts.init(document.getElementById('chart'));
                        var options={
                            //定义一个标题
                            title:{
                                text: '课程成绩分数段统计图'
                            },
                            legend:{
                                data:['成绩段人数统计']
                            },
                            tooltip : {
                                trigger: 'axis',
                                formatter: "{c}人"
                            },
                            //X轴设置
                            xAxis:{
                                data:xData
                            },
                            yAxis:{
                            },
                            //name=legend.data的时候才能显示图例
                            series:[{
                                name:'人数',
                                type:'bar',
                                barWidth: 30,//固定柱子宽度
                                data:yData
                            }]
                        };
                        myChart.setOption(options);
                    }
                });
            } else {
                $.messager.alert("警告操作！", "请选择一门课程进行统计！", "warning");
			}
		},
		edit : function () {
			var rows = $("#course_manage").datagrid("getSelections");
			if (rows.length > 1) {
				$.messager.alert("警告操作！", "编辑记录只能选定一条数据！", "warning");
			} else if (rows.length == 1) {
				$.ajax({
					url : backURL + getVisitPath("Course") + "/update",
					type : "get",
					data : {
						courseNumber : rows[0].courseNumber,
					},
					dataType: "json",
					beforeSend : function () {
						$.messager.progress({
							text : "正在获取中...",
						});
					},
					success : function (course, response, status) {
						$.messager.progress("close");
						if (course) { 
							$("#courseEditDiv").dialog("open");
							$("#course_courseNumber_edit").val(course.courseNumber);
							$("#course_courseNumber_edit").validatebox({
								required : true,
								missingMessage : "请输入课程编号",
								editable: false
							});
							$("#course_courseName_edit").val(course.courseName);
							$("#course_courseName_edit").validatebox({
								required : true,
								missingMessage : "请输入课程名称",
							});
							$("#course_courseTeacher_teacherNumber_edit").combobox({
							    url: backURL + getVisitPath("Teacher") + "/listAll",
							    dataType: "json",
							    valueField:"teacherNumber",
							    textField:"teacherName",
							    panelHeight: "auto",
						        editable: false, //不允许手动输入 
						        onLoadSuccess: function () { //数据加载完毕事件
									$("#course_courseTeacher_teacherNumber_edit").combobox("select", course.courseTeacher);
									//var data = $("#course_courseTeacher_teacherNumber_edit").combobox("getData"); 
						            //if (data.length > 0) {
						                //$("#course_courseTeacher_teacherNumber_edit").combobox("select", data[0].teacherNumber);
						            //}
								}
							});
							$("#course_courseTime_edit").val(course.courseTime);
							$("#course_coursePlace_edit").val(course.coursePlace);
							$("#course_courseScore_edit").val(course.courseScore);
							$("#course_courseScore_edit").validatebox({
								required : true,
								validType : "number",
								missingMessage : "请输入课程学分",
								invalidMessage : "课程学分输入不对",
							});
							$("#course_courseMemo_edit").val(course.courseMemo);
						} else {
							$.messager.alert("获取失败！", "未知错误导致失败，请重试！", "warning");
						}
					}
				});
			} else if (rows.length == 0) {
				$.messager.alert("警告操作！", "编辑记录至少选定一条数据！", "warning");
			}
		},
	};
}
