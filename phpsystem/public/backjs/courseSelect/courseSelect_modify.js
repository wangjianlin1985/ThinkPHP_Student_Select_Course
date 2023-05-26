$(function () {
	$.ajax({
		url :  backURL + getVisitPath("CourseSelect") + "/update",
		type : "get",
		data : {
			selectId : $("#courseSelect_selectId_edit").val(),
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
				$("#courseSelect_selectId_edit").val(courseSelect.selectId);
				$("#courseSelect_selectId_edit").validatebox({
					required : true,
					missingMessage : "请输入记录编号",
					editable: false
				});
				$("#courseSelect_studentNumber_studentNumber_edit").combobox({
					url: backURL + getVisitPath("Student") + "/listAll",
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
				$(".messager-window").css("z-index",10000);
			}
		}
	});

	$("#courseSelectModifyButton").click(function(){ 
		if ($("#courseSelectEditForm").form("validate")) {
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
                	var obj = jQuery.parseJSON(data);
                    if(obj.success){
                        $.messager.alert("消息","信息修改成功！");
                        $(".messager-window").css("z-index",10000);
                        //location.href="frontlist";
                    }else{
                        $.messager.alert("消息",obj.message);
                        $(".messager-window").css("z-index",10000);
                    } 
			    }
			});
			//提交表单
			$("#courseSelectEditForm").submit();
		} else {
			$.messager.alert("错误提示","你输入的信息还有错误！","warning");
			$(".messager-window").css("z-index",10000);
		}
	});
});
