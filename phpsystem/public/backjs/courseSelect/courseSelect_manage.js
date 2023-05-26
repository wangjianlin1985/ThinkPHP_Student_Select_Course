var courseSelect_manage_tool = null; 
$(function () { 
	initCourseSelectManageTool(); //建立CourseSelect管理对象
	courseSelect_manage_tool.init(); //如果需要通过下拉框查询，首先初始化下拉框的值
	$("#courseSelect_manage").datagrid({
		url : backURL + getVisitPath("CourseSelect") + '/backList',
		fit : true,
		fitColumns : true,
		striped : true,
		rownumbers : true,
		border : false,
		pagination : true,
		pageSize : 5,
		pageList : [5, 10, 15, 20, 25],
		pageNumber : 1,
		sortName : "selectId",
		sortOrder : "desc",
		toolbar : "#courseSelect_manage_tool",
		columns : [[
			{
				field : "selectId",
				title : "记录编号",
				width : 70,
			},
			{
				field : "studentNumber",
				title : "学生",
				width : 140,
			},
			{
				field : "courseNumber",
				title : "课程",
				width : 140,
			},
			{
				field : "selectTime",
				title : "选课时间",
				width : 140,
			},
		]],
	});

	$("#courseSelectEditDiv").dialog({
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
				if ($("#courseSelectEditForm").form("validate")) {
					//验证表单 
					if(!$("#courseSelectEditForm").form("validate")) {
						$.messager.alert("错误提示","你输入的信息还有错误！","warning");
					} else {
						$("#courseSelectEditForm").form({
						    url: backURL + getVisitPath("CourseSelect") + "/update",
						    onSubmit: function(){
								if($("#courseSelectEditForm").form("validate"))  {
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
			                        $("#courseSelectEditDiv").dialog("close");
			                        courseSelect_manage_tool.reload();
			                    }else{
			                        $.messager.alert("消息",obj.message);
			                    } 
						    }
						});
						//提交表单
						$("#courseSelectEditForm").submit();
					}
				}
			},
		},{
			text : "取消",
			iconCls : "icon-redo",
			handler : function () {
				$("#courseSelectEditDiv").dialog("close");
				$("#courseSelectEditForm").form("reset"); 
			},
		}],
	});
});

function initCourseSelectManageTool() {
	courseSelect_manage_tool = {
		init: function() {
			$.ajax({
				url : backURL + getVisitPath("Student") + "/listAll",
				type : "post",
				dataType: "json",
				success : function (data, response, status) {
					$("#studentNumber_studentNumber_query").combobox({ 
					    valueField:"studentNumber",
					    textField:"studentName",
					    panelHeight: "200px",
				        editable: false, //不允许手动输入 
					});
					data.splice(0,0,{studentNumber:"",studentName:"不限制"});
					$("#studentNumber_studentNumber_query").combobox("loadData",data); 
				}
			});
			$.ajax({
				url : backURL + getVisitPath("Course") + "/listAll",
				type : "post",
				dataType: "json",
				success : function (data, response, status) {
					$("#courseNumber_courseNumber_query").combobox({ 
					    valueField:"courseNumber",
					    textField:"courseName",
					    panelHeight: "200px",
				        editable: false, //不允许手动输入 
					});
					data.splice(0,0,{courseNumber:"",courseName:"不限制"});
					$("#courseNumber_courseNumber_query").combobox("loadData",data); 
				}
			});
		},
		reload : function () {
			$("#courseSelect_manage").datagrid("reload");
		},
		redo : function () {
			$("#courseSelect_manage").datagrid("unselectAll");
		},
		search: function() {
			var queryParams = $("#courseSelect_manage").datagrid("options").queryParams;
			queryParams["selectTime"] = $("#selectTime").datebox("getValue"); 
			queryParams["studentNumber.studentNumber"] = $("#studentNumber_studentNumber_query").combobox("getValue");
			queryParams["courseNumber.courseNumber"] = $("#courseNumber_courseNumber_query").combobox("getValue");
			$("#courseSelect_manage").datagrid("options").queryParams=queryParams; 
			$("#courseSelect_manage").datagrid("load");
		},
		exportExcel: function() {
			$("#courseSelectQueryForm").form({
			    url: backURL + getVisitPath("CourseSelect") + "/outToExcel",
			});
			//提交表单
			$("#courseSelectQueryForm").submit();
		},
		remove : function () {
			var rows = $("#courseSelect_manage").datagrid("getSelections");
			if (rows.length > 0) {
				$.messager.confirm("确定操作", "您正在要删除所选的记录吗？", function (flag) {
					if (flag) {
						var selectIds = [];
						for (var i = 0; i < rows.length; i ++) {
							selectIds.push(rows[i].selectId);
						}
						$.ajax({
							type : "POST",
							url :  backURL + getVisitPath("CourseSelect") + "/deletes",
							data : {
								selectIds : selectIds.join(","),
							},
							dataType: "json",
							beforeSend : function () {
								$("#courseSelect_manage").datagrid("loading");
							},
							success : function (data) {
								if (data.success) {
									$("#courseSelect_manage").datagrid("loaded");
									$("#courseSelect_manage").datagrid("load");
									$("#courseSelect_manage").datagrid("unselectAll");
									$.messager.show({
										title : "提示",
										msg : data.message
									});
								} else {
									$("#courseSelect_manage").datagrid("loaded");
									$("#courseSelect_manage").datagrid("load");
									$("#courseSelect_manage").datagrid("unselectAll");
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
			var rows = $("#courseSelect_manage").datagrid("getSelections");
			if (rows.length > 1) {
				$.messager.alert("警告操作！", "编辑记录只能选定一条数据！", "warning");
			} else if (rows.length == 1) {
				$.ajax({
					url : backURL + getVisitPath("CourseSelect") + "/update",
					type : "get",
					data : {
						selectId : rows[0].selectId,
					},
					dataType: "json",
					beforeSend : function () {
						$.messager.progress({
							text : "正在获取中...",
						});
					},
					success : function (courseSelect, response, status) {
						$.messager.progress("close");
						if (courseSelect) { 
							$("#courseSelectEditDiv").dialog("open");
							$("#courseSelect_selectId_edit").val(courseSelect.selectId);
							$("#courseSelect_selectId_edit").validatebox({
								required : true,
								missingMessage : "请输入记录编号",
								editable: false
							});
							$("#courseSelect_studentNumber_studentNumber_edit").combobox({
							    url: backURL + getVisitPath("Student") + "/listAll",
							    dataType: "json",
							    valueField:"studentNumber",
							    textField:"studentName",
							    panelHeight: "auto",
						        editable: false, //不允许手动输入 
						        onLoadSuccess: function () { //数据加载完毕事件
									$("#courseSelect_studentNumber_studentNumber_edit").combobox("select", courseSelect.studentNumber);
									//var data = $("#courseSelect_studentNumber_studentNumber_edit").combobox("getData"); 
						            //if (data.length > 0) {
						                //$("#courseSelect_studentNumber_studentNumber_edit").combobox("select", data[0].studentNumber);
						            //}
								}
							});
							$("#courseSelect_courseNumber_courseNumber_edit").combobox({
							    url: backURL + getVisitPath("Course") + "/listAll",
							    dataType: "json",
							    valueField:"courseNumber",
							    textField:"courseName",
							    panelHeight: "auto",
						        editable: false, //不允许手动输入 
						        onLoadSuccess: function () { //数据加载完毕事件
									$("#courseSelect_courseNumber_courseNumber_edit").combobox("select", courseSelect.courseNumber);
									//var data = $("#courseSelect_courseNumber_courseNumber_edit").combobox("getData"); 
						            //if (data.length > 0) {
						                //$("#courseSelect_courseNumber_courseNumber_edit").combobox("select", data[0].courseNumber);
						            //}
								}
							});
							$("#courseSelect_selectTime_edit").datetimebox({
								value: courseSelect.selectTime,
							    required: true,
							    showSeconds: true,
							});
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
