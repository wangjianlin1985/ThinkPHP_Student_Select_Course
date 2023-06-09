var scoreInfo_manage_tool = null; 
$(function () { 
	initScoreInfoManageTool(); //建立ScoreInfo管理对象
	scoreInfo_manage_tool.init(); //如果需要通过下拉框查询，首先初始化下拉框的值
	$("#scoreInfo_manage").datagrid({
		url : backURL + getVisitPath("ScoreInfo") + '/backTeacherList',
		fit : true,
		fitColumns : true,
		striped : true,
		rownumbers : true,
		border : false,
		pagination : true,
		pageSize : 5,
		pageList : [5, 10, 15, 20, 25],
		pageNumber : 1,
		sortName : "scoreId",
		sortOrder : "desc",
		toolbar : "#scoreInfo_manage_tool",
		columns : [[
			{
				field : "scoreId",
				title : "记录编号",
				width : 70,
			},
			{
				field : "studentObj",
				title : "学生",
				width : 140,
			},
			{
				field : "courseObj",
				title : "课程",
				width : 140,
			},
			{
				field : "scoreValue",
				title : "成绩得分",
				width : 70,
			},
		]],
	});

	$("#scoreInfoEditDiv").dialog({
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
				if ($("#scoreInfoEditForm").form("validate")) {
					//验证表单 
					if(!$("#scoreInfoEditForm").form("validate")) {
						$.messager.alert("错误提示","你输入的信息还有错误！","warning");
					} else {
						$("#scoreInfoEditForm").form({
						    url: backURL + getVisitPath("ScoreInfo") + "/teacherUpdate",
						    onSubmit: function(){
								if($("#scoreInfoEditForm").form("validate"))  {
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
			                        $("#scoreInfoEditDiv").dialog("close");
			                        scoreInfo_manage_tool.reload();
			                    }else{
			                        $.messager.alert("消息",obj.message);
			                    } 
						    }
						});
						//提交表单
						$("#scoreInfoEditForm").submit();
					}
				}
			},
		},{
			text : "取消",
			iconCls : "icon-redo",
			handler : function () {
				$("#scoreInfoEditDiv").dialog("close");
				$("#scoreInfoEditForm").form("reset"); 
			},
		}],
	});
});

function initScoreInfoManageTool() {
	scoreInfo_manage_tool = {
		init: function() {
			$.ajax({
				url : backURL + getVisitPath("Student") + "/listAll",
				type : "post",
				dataType: "json",
				success : function (data, response, status) {
					$("#studentObj_studentNumber_query").combobox({ 
					    valueField:"studentNumber",
					    textField:"studentName",
					    panelHeight: "200px",
				        editable: false, //不允许手动输入 
					});
					data.splice(0,0,{studentNumber:"",studentName:"不限制"});
					$("#studentObj_studentNumber_query").combobox("loadData",data); 
				}
			});
			$.ajax({
				url : backURL + getVisitPath("Course") + "/listAll",
				type : "post",
				dataType: "json",
				success : function (data, response, status) {
					$("#courseObj_courseNumber_query").combobox({ 
					    valueField:"courseNumber",
					    textField:"courseName",
					    panelHeight: "200px",
				        editable: false, //不允许手动输入 
					});
					data.splice(0,0,{courseNumber:"",courseName:"不限制"});
					$("#courseObj_courseNumber_query").combobox("loadData",data); 
				}
			});
		},
		reload : function () {
			$("#scoreInfo_manage").datagrid("reload");
		},
		redo : function () {
			$("#scoreInfo_manage").datagrid("unselectAll");
		},
		search: function() {
			var queryParams = $("#scoreInfo_manage").datagrid("options").queryParams;
			queryParams["studentObj.studentNumber"] = $("#studentObj_studentNumber_query").combobox("getValue");
			queryParams["courseObj.courseNumber"] = $("#courseObj_courseNumber_query").combobox("getValue");
			$("#scoreInfo_manage").datagrid("options").queryParams=queryParams; 
			$("#scoreInfo_manage").datagrid("load");
		},
		exportExcel: function() {
			$("#scoreInfoQueryForm").form({
			    url: backURL + getVisitPath("ScoreInfo") + "/outToExcel",
			});
			//提交表单
			$("#scoreInfoQueryForm").submit();
		},
		remove : function () {
			var rows = $("#scoreInfo_manage").datagrid("getSelections");
			if (rows.length > 0) {
				$.messager.confirm("确定操作", "您正在要删除所选的记录吗？", function (flag) {
					if (flag) {
						var scoreIds = [];
						for (var i = 0; i < rows.length; i ++) {
							scoreIds.push(rows[i].scoreId);
						}
						$.ajax({
							type : "POST",
							url :  backURL + getVisitPath("ScoreInfo") + "/deletes",
							data : {
								scoreIds : scoreIds.join(","),
							},
							dataType: "json",
							beforeSend : function () {
								$("#scoreInfo_manage").datagrid("loading");
							},
							success : function (data) {
								if (data.success) {
									$("#scoreInfo_manage").datagrid("loaded");
									$("#scoreInfo_manage").datagrid("load");
									$("#scoreInfo_manage").datagrid("unselectAll");
									$.messager.show({
										title : "提示",
										msg : data.message
									});
								} else {
									$("#scoreInfo_manage").datagrid("loaded");
									$("#scoreInfo_manage").datagrid("load");
									$("#scoreInfo_manage").datagrid("unselectAll");
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
		edit : function () {
			var rows = $("#scoreInfo_manage").datagrid("getSelections");
			if (rows.length > 1) {
				$.messager.alert("警告操作！", "编辑记录只能选定一条数据！", "warning");
			} else if (rows.length == 1) {
				$.ajax({
					url : backURL + getVisitPath("ScoreInfo") + "/update",
					type : "get",
					data : {
						scoreId : rows[0].scoreId,
					},
					dataType: "json",
					beforeSend : function () {
						$.messager.progress({
							text : "正在获取中...",
						});
					},
					success : function (scoreInfo, response, status) {
						$.messager.progress("close");
						if (scoreInfo) { 
							$("#scoreInfoEditDiv").dialog("open");
							$("#scoreInfo_scoreId_edit").val(scoreInfo.scoreId);
							$("#scoreInfo_scoreId_edit").validatebox({
								required : true,
								missingMessage : "请输入记录编号",
								editable: false
							});
							$("#scoreInfo_studentObj_studentNumber_edit").combobox({
							    url: backURL + getVisitPath("Student") + "/listAll",
							    dataType: "json",
							    valueField:"studentNumber",
							    textField:"studentName",
							    panelHeight: "auto",
						        editable: false, //不允许手动输入 
						        onLoadSuccess: function () { //数据加载完毕事件
									$("#scoreInfo_studentObj_studentNumber_edit").combobox("select", scoreInfo.studentObj);
									//var data = $("#scoreInfo_studentObj_studentNumber_edit").combobox("getData"); 
						            //if (data.length > 0) {
						                //$("#scoreInfo_studentObj_studentNumber_edit").combobox("select", data[0].studentNumber);
						            //}
								}
							});
							$("#scoreInfo_courseObj_courseNumber_edit").combobox({
							    url: backURL + getVisitPath("Course") + "/listAll",
							    dataType: "json",
							    valueField:"courseNumber",
							    textField:"courseName",
							    panelHeight: "auto",
						        editable: false, //不允许手动输入 
						        onLoadSuccess: function () { //数据加载完毕事件
									$("#scoreInfo_courseObj_courseNumber_edit").combobox("select", scoreInfo.courseObj);
									//var data = $("#scoreInfo_courseObj_courseNumber_edit").combobox("getData"); 
						            //if (data.length > 0) {
						                //$("#scoreInfo_courseObj_courseNumber_edit").combobox("select", data[0].courseNumber);
						            //}
								}
							});
							$("#scoreInfo_scoreValue_edit").val(scoreInfo.scoreValue);
							$("#scoreInfo_scoreValue_edit").validatebox({
								required : true,
								validType : "number",
								missingMessage : "请输入成绩得分",
								invalidMessage : "成绩得分输入不对",
							});
							$("#scoreInfo_studentEvaluate_edit").val(scoreInfo.studentEvaluate);
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
