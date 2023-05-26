$(function () {
	//实例化编辑器
	//建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
	UE.delEditor('teacher_teacherDesc_edit');
	var teacher_teacherDesc_edit = UE.getEditor('teacher_teacherDesc_edit'); //教师简介编辑器
	teacher_teacherDesc_edit.addListener("ready", function () {
		 // editor准备好之后才可以使用 
		 ajaxModifyQuery();
	}); 
  function ajaxModifyQuery() {	
	$.ajax({
		url :  backURL + getVisitPath("Teacher") + "/update",
		type : "get",
		data : {
			teacherNumber : $("#teacher_teacherNumber_edit").val(),
		},
		dataType: "json",
		beforeSend : function () {
			$.messager.progress({
				text : "正在获取中...",
			});
		},
		success : function (teacher, response, status) {
			$.messager.progress("close");
			if (teacher) { 
				$("#teacher_teacherNumber_edit").val(teacher.teacherNumber);
				$("#teacher_teacherNumber_edit").validatebox({
					required : true,
					missingMessage : "请输入教师工号",
					editable: false
				});
				$("#teacher_collegeObj_collegeNumber_edit").combobox({
					url: backURL + getVisitPath("College") + "/listAll",
					valueField:"collegeNumber",
					textField:"collegeName",
					panelHeight: "auto",
					editable: false, //不允许手动输入 
					onLoadSuccess: function () { //数据加载完毕事件
						$("#teacher_collegeObj_collegeNumber_edit").combobox("select", teacher.collegeObj);
						//var data = $("#teacher_collegeObj_collegeNumber_edit").combobox("getData"); 
						//if (data.length > 0) {
							//$("#teacher_collegeObj_collegeNumber_edit").combobox("select", data[0].collegeNumber);
						//}
					}
				});
				$("#teacher_teacherName_edit").val(teacher.teacherName);
				$("#teacher_teacherName_edit").validatebox({
					required : true,
					missingMessage : "请输入教师姓名",
				});
				$("#teacher_teacherPassword_edit").val(teacher.teacherPassword);
				$("#teacher_teacherSex_edit").val(teacher.teacherSex);
				$("#teacher_teacherSex_edit").validatebox({
					required : true,
					missingMessage : "请输入性别",
				});
				$("#teacher_teacherPhoto").val(teacher.teacherPhoto);
				$("#teacher_teacherPhotoImg").attr("src", publicURL + teacher.teacherPhoto);
				$("#teacher_teacherBirthday_edit").datebox({
					value: teacher.teacherBirthday,
					required: true,
					showSeconds: true,
				});
				$("#teacher_teacherArriveDate_edit").datebox({
					value: teacher.teacherArriveDate,
					required: true,
					showSeconds: true,
				});
				$("#teacher_teacherPhone_edit").val(teacher.teacherPhone);
				$("#teacher_teacherMail_edit").val(teacher.teacherMail);
				$("#teacher_teacherMail_edit").validatebox({
					required : true,
					missingMessage : "请输入教师邮箱",
				});
				teacher_teacherDesc_edit.setContent(teacher.teacherDesc);
			} else {
				$.messager.alert("获取失败！", "未知错误导致失败，请重试！", "warning");
				$(".messager-window").css("z-index",10000);
			}
		}
	});

  }

	$("#teacherModifyButton").click(function(){ 
		if ($("#teacherEditForm").form("validate")) {
			$("#teacherEditForm").form({
			    url: backURL + getVisitPath("Teacher") + "/update",
			    onSubmit: function(){
					if($("#teacherEditForm").form("validate"))  {
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
			$("#teacherEditForm").submit();
		} else {
			$.messager.alert("错误提示","你输入的信息还有错误！","warning");
			$(".messager-window").css("z-index",10000);
		}
	});
});
