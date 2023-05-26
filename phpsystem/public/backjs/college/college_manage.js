var college_manage_tool = null; 
$(function () { 
	initCollegeManageTool(); //建立College管理对象
	college_manage_tool.init(); //如果需要通过下拉框查询，首先初始化下拉框的值
	$("#college_manage").datagrid({
		url : backURL + getVisitPath("College") + '/backList',
		fit : true,
		fitColumns : true,
		striped : true,
		rownumbers : true,
		border : false,
		pagination : true,
		pageSize : 5,
		pageList : [5, 10, 15, 20, 25],
		pageNumber : 1,
		sortName : "collegeNumber",
		sortOrder : "desc",
		toolbar : "#college_manage_tool",
		columns : [[
			{
				field : "collegeNumber",
				title : "学院编号",
				width : 140,
			},
			{
				field : "collegeName",
				title : "学院名称",
				width : 140,
			},
			{
				field : "collegeBirthDate",
				title : "成立日期",
				width : 140,
			},
			{
				field : "collegeMan",
				title : "院长姓名",
				width : 140,
			},
			{
				field : "collegeTelephone",
				title : "联系电话",
				width : 140,
			},
		]],
	});

	$("#collegeEditDiv").dialog({
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
				if ($("#collegeEditForm").form("validate")) {
					//验证表单 
					if(!$("#collegeEditForm").form("validate")) {
						$.messager.alert("错误提示","你输入的信息还有错误！","warning");
					} else {
						$("#collegeEditForm").form({
						    url: backURL + getVisitPath("College") + "/update",
						    onSubmit: function(){
								if($("#collegeEditForm").form("validate"))  {
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
			                        $("#collegeEditDiv").dialog("close");
			                        college_manage_tool.reload();
			                    }else{
			                        $.messager.alert("消息",obj.message);
			                    } 
						    }
						});
						//提交表单
						$("#collegeEditForm").submit();
					}
				}
			},
		},{
			text : "取消",
			iconCls : "icon-redo",
			handler : function () {
				$("#collegeEditDiv").dialog("close");
				$("#collegeEditForm").form("reset"); 
			},
		}],
	});
});

function initCollegeManageTool() {
	college_manage_tool = {
		init: function() {
		},
		reload : function () {
			$("#college_manage").datagrid("reload");
		},
		redo : function () {
			$("#college_manage").datagrid("unselectAll");
		},
		search: function() {
			$("#college_manage").datagrid("load");
		},
		exportExcel: function() {
			$("#collegeQueryForm").form({
			    url: backURL + getVisitPath("College") + "/outToExcel",
			});
			//提交表单
			$("#collegeQueryForm").submit();
		},
		remove : function () {
			var rows = $("#college_manage").datagrid("getSelections");
			if (rows.length > 0) {
				$.messager.confirm("确定操作", "您正在要删除所选的记录吗？", function (flag) {
					if (flag) {
						var collegeNumbers = [];
						for (var i = 0; i < rows.length; i ++) {
							collegeNumbers.push(rows[i].collegeNumber);
						}
						$.ajax({
							type : "POST",
							url :  backURL + getVisitPath("College") + "/deletes",
							data : {
								collegeNumbers : collegeNumbers.join(","),
							},
							dataType: "json",
							beforeSend : function () {
								$("#college_manage").datagrid("loading");
							},
							success : function (data) {
								if (data.success) {
									$("#college_manage").datagrid("loaded");
									$("#college_manage").datagrid("load");
									$("#college_manage").datagrid("unselectAll");
									$.messager.show({
										title : "提示",
										msg : data.message
									});
								} else {
									$("#college_manage").datagrid("loaded");
									$("#college_manage").datagrid("load");
									$("#college_manage").datagrid("unselectAll");
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
			var rows = $("#college_manage").datagrid("getSelections");
			if (rows.length > 1) {
				$.messager.alert("警告操作！", "编辑记录只能选定一条数据！", "warning");
			} else if (rows.length == 1) {
				$.ajax({
					url : backURL + getVisitPath("College") + "/update",
					type : "get",
					data : {
						collegeNumber : rows[0].collegeNumber,
					},
					dataType: "json",
					beforeSend : function () {
						$.messager.progress({
							text : "正在获取中...",
						});
					},
					success : function (college, response, status) {
						$.messager.progress("close");
						if (college) { 
							$("#collegeEditDiv").dialog("open");
							$("#college_collegeNumber_edit").val(college.collegeNumber);
							$("#college_collegeNumber_edit").validatebox({
								required : true,
								missingMessage : "请输入学院编号",
								editable: false
							});
							$("#college_collegeName_edit").val(college.collegeName);
							$("#college_collegeName_edit").validatebox({
								required : true,
								missingMessage : "请输入学院名称",
							});
							$("#college_collegeBirthDate_edit").datebox({
								value: college.collegeBirthDate,
							    required: true,
							    showSeconds: true,
							});
							$("#college_collegeMan_edit").val(college.collegeMan);
							$("#college_collegeTelephone_edit").val(college.collegeTelephone);
							$("#college_collegeMemo_edit").val(college.collegeMemo);
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
