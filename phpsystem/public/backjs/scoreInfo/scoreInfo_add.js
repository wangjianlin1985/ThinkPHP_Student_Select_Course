$(function () {
	$("#scoreInfo_studentObj_studentNumber").combobox({
	    url: backURL + getVisitPath("Student") + '/listAll',
	    valueField: "studentNumber",
	    textField: "studentName",
	    panelHeight: "auto",
        editable: false, //不允许手动输入
        required : true,
        onLoadSuccess: function () { //数据加载完毕事件
            var data = $("#scoreInfo_studentObj_studentNumber").combobox("getData"); 
            if (data.length > 0) {
                $("#scoreInfo_studentObj_studentNumber").combobox("select", data[0].studentNumber);
            }
        }
	});
	$("#scoreInfo_courseObj_courseNumber").combobox({
	    url: backURL + getVisitPath("Course") + '/listAll',
	    valueField: "courseNumber",
	    textField: "courseName",
	    panelHeight: "auto",
        editable: false, //不允许手动输入
        required : true,
        onLoadSuccess: function () { //数据加载完毕事件
            var data = $("#scoreInfo_courseObj_courseNumber").combobox("getData"); 
            if (data.length > 0) {
                $("#scoreInfo_courseObj_courseNumber").combobox("select", data[0].courseNumber);
            }
        }
	});
	$("#scoreInfo_scoreValue").validatebox({
		required : true,
		validType : "number",
		missingMessage : '请输入成绩得分',
		invalidMessage : '成绩得分输入不对',
	});

	//单击添加按钮
	$("#scoreInfoAddButton").click(function () {
		//验证表单 
		if(!$("#scoreInfoAddForm").form("validate")) {
			$.messager.alert("错误提示","你输入的信息还有错误！","warning");
			$(".messager-window").css("z-index",10000);
		} else {
			$("#scoreInfoAddForm").form({
			    url: backURL + getVisitPath("ScoreInfo") + "/add",
			    onSubmit: function(){
					if($("#scoreInfoAddForm").form("validate"))  { 
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
                        $("#scoreInfoAddForm").form("clear");
                    }else{
                        $.messager.alert("消息",obj.message);
                        $(".messager-window").css("z-index",10000);
                    }
			    }
			});
			//提交表单
			$("#scoreInfoAddForm").submit();
		}
	});

	//单击清空按钮
	$("#scoreInfoClearButton").click(function () { 
		$("#scoreInfoAddForm").form("clear"); 
	});
});
