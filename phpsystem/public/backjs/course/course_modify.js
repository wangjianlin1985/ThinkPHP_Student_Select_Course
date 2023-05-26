$(function () {
	$.ajax({
		url :  backURL + getVisitPath("Course") + "/update",
		type : "get",
		data : {
			courseNumber : $("#course_courseNumber_edit").val(),
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
				$(".messager-window").css("z-index",10000);
			}
		}
	});

	$("#courseModifyButton").click(function(){ 
		if ($("#courseEditForm").form("validate")) {
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
			$("#courseEditForm").submit();
		} else {
			$.messager.alert("错误提示","你输入的信息还有错误！","warning");
			$(".messager-window").css("z-index",10000);
		}
	});
});
