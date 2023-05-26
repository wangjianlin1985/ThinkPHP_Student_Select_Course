$(function () {
	$("#specialInfo_specialFieldNumber").validatebox({
		required : true, 
		missingMessage : '请输入专业编号',
	});

	$("#specialInfo_specialFieldName").validatebox({
		required : true, 
		missingMessage : '请输入专业名称',
	});

	$("#specialInfo_specialCollegeNumber_collegeNumber").combobox({
	    url: backURL + getVisitPath("College") + '/listAll',
	    valueField: "collegeNumber",
	    textField: "collegeName",
	    panelHeight: "auto",
        editable: false, //不允许手动输入
        required : true,
        onLoadSuccess: function () { //数据加载完毕事件
            var data = $("#specialInfo_specialCollegeNumber_collegeNumber").combobox("getData"); 
            if (data.length > 0) {
                $("#specialInfo_specialCollegeNumber_collegeNumber").combobox("select", data[0].collegeNumber);
            }
        }
	});
	$("#specialInfo_specialBirthDate").datebox({
	    required : true, 
	    showSeconds: true,
	    editable: false
	});

	//单击添加按钮
	$("#specialInfoAddButton").click(function () {
		//验证表单 
		if(!$("#specialInfoAddForm").form("validate")) {
			$.messager.alert("错误提示","你输入的信息还有错误！","warning");
			$(".messager-window").css("z-index",10000);
		} else {
			$("#specialInfoAddForm").form({
			    url: backURL + getVisitPath("SpecialInfo") + "/add",
			    onSubmit: function(){
					if($("#specialInfoAddForm").form("validate"))  { 
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
                    //此处data={"Success":true}是字符串
                	var obj = jQuery.parseJSON(data); 
                    if(obj.success){ 
                        $.messager.alert("消息","保存成功！");
                        $(".messager-window").css("z-index",10000);
                        $("#specialInfoAddForm").form("clear");
                    }else{
                        $.messager.alert("消息",obj.message);
                        $(".messager-window").css("z-index",10000);
                    }
			    }
			});
			//提交表单
			$("#specialInfoAddForm").submit();
		}
	});

	//单击清空按钮
	$("#specialInfoClearButton").click(function () { 
		$("#specialInfoAddForm").form("clear"); 
	});
});
