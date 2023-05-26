$(function () {
	$.ajax({
		url :  backURL + getVisitPath("SpecialInfo") + "/update",
		type : "get",
		data : {
			specialFieldNumber : $("#specialInfo_specialFieldNumber_edit").val(),
		},
		dataType: "json",
		beforeSend : function () {
			$.messager.progress({
				text : "正在获取中...",
			});
		},
		success : function (specialInfo, response, status) {
			$.messager.progress("close");
			if (specialInfo) { 
				$("#specialInfo_specialFieldNumber_edit").val(specialInfo.specialFieldNumber);
				$("#specialInfo_specialFieldNumber_edit").validatebox({
					required : true,
					missingMessage : "请输入专业编号",
					editable: false
				});
				$("#specialInfo_specialFieldName_edit").val(specialInfo.specialFieldName);
				$("#specialInfo_specialFieldName_edit").validatebox({
					required : true,
					missingMessage : "请输入专业名称",
				});
				$("#specialInfo_specialCollegeNumber_collegeNumber_edit").combobox({
					url: backURL + getVisitPath("College") + "/listAll",
					valueField:"collegeNumber",
					textField:"collegeName",
					panelHeight: "auto",
					editable: false, //不允许手动输入 
					onLoadSuccess: function () { //数据加载完毕事件
						$("#specialInfo_specialCollegeNumber_collegeNumber_edit").combobox("select", specialInfo.specialCollegeNumber);
						//var data = $("#specialInfo_specialCollegeNumber_collegeNumber_edit").combobox("getData"); 
						//if (data.length > 0) {
							//$("#specialInfo_specialCollegeNumber_collegeNumber_edit").combobox("select", data[0].collegeNumber);
						//}
					}
				});
				$("#specialInfo_specialBirthDate_edit").datebox({
					value: specialInfo.specialBirthDate,
					required: true,
					showSeconds: true,
				});
				$("#specialInfo_specialMan_edit").val(specialInfo.specialMan);
				$("#specialInfo_specialTelephone_edit").val(specialInfo.specialTelephone);
				$("#specialInfo_specialMemo_edit").val(specialInfo.specialMemo);
			} else {
				$.messager.alert("获取失败！", "未知错误导致失败，请重试！", "warning");
				$(".messager-window").css("z-index",10000);
			}
		}
	});

	$("#specialInfoModifyButton").click(function(){ 
		if ($("#specialInfoEditForm").form("validate")) {
			$("#specialInfoEditForm").form({
			    url: backURL + getVisitPath("SpecialInfo") + "/update",
			    onSubmit: function(){
					if($("#specialInfoEditForm").form("validate"))  {
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
			$("#specialInfoEditForm").submit();
		} else {
			$.messager.alert("错误提示","你输入的信息还有错误！","warning");
			$(".messager-window").css("z-index",10000);
		}
	});
});
