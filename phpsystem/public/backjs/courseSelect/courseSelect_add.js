$(function () {
	$("#courseSelect_studentNumber_studentNumber").combobox({
	    url: backURL + getVisitPath("Student") + '/listAll',
	    valueField: "studentNumber",
	    textField: "studentName",
	    panelHeight: "auto",
        editable: false, //不允许手动输入
        required : true,
        onLoadSuccess: function () { //数据加载完毕事件
            var data = $("#courseSelect_studentNumber_studentNumber").combobox("getData"); 
            if (data.length > 0) {
                $("#courseSelect_studentNumber_studentNumber").combobox("select", data[0].studentNumber);
            }
        }
	});
	$("#courseSelect_courseNumber_courseNumber").combobox({
	    url: backURL + getVisitPath("Course") + '/listAll',
	    valueField: "courseNumber",
	    textField: "courseName",
	    panelHeight: "auto",
        editable: false, //不允许手动输入
        required : true,
        onLoadSuccess: function () { //数据加载完毕事件
            var data = $("#courseSelect_courseNumber_courseNumber").combobox("getData"); 
            if (data.length > 0) {
                $("#courseSelect_courseNumber_courseNumber").combobox("select", data[0].courseNumber);
            }
        }
	});
	$("#courseSelect_selectTime").datetimebox({
	    required : true, 
	    showSeconds: true,
	    editable: false
	});

	//单击添加按钮
	$("#courseSelectAddButton").click(function () {
		//验证表单 
		if(!$("#courseSelectAddForm").form("validate")) {
			$.messager.alert("错误提示","你输入的信息还有错误！","warning");
			$(".messager-window").css("z-index",10000);
		} else {
			$("#courseSelectAddForm").form({
			    url: backURL + getVisitPath("CourseSelect") + "/add",
			    onSubmit: function(){
					if($("#courseSelectAddForm").form("validate"))  { 
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
                        $("#courseSelectAddForm").form("clear");
                    }else{
                        $.messager.alert("消息",obj.message);
                        $(".messager-window").css("z-index",10000);
                    }
			    }
			});
			//提交表单
			$("#courseSelectAddForm").submit();
		}
	});

	//单击清空按钮
	$("#courseSelectClearButton").click(function () { 
		$("#courseSelectAddForm").form("clear"); 
	});
});
