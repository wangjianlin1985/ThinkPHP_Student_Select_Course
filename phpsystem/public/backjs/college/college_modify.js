$(function () {
	$.ajax({
		url :  backURL + getVisitPath("College") + "/update",
		type : "get",
		data : {
			collegeNumber : $("#college_collegeNumber_edit").val(),
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
				$(".messager-window").css("z-index",10000);
			}
		}
	});

	$("#collegeModifyButton").click(function(){ 
		if ($("#collegeEditForm").form("validate")) {
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
			$("#collegeEditForm").submit();
		} else {
			$.messager.alert("错误提示","你输入的信息还有错误！","warning");
			$(".messager-window").css("z-index",10000);
		}
	});
});
