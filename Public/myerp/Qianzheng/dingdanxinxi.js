﻿
function save(){
	scroll(0,0);
	ThinkAjax.sendForm('form1',SITE_INDEX+'Xiaoshou/dopostdingdanxinxi/',doComplete,'resultdiv');
}
function doComplete(data,status){
	if(status == 1){
		jQuery(window).unbind('beforeunload');
		location.reload();
	}
}
		 
function checktable()
{
	if(CheckForm('form2','resultdiv_2'))
	{
		if(checktitle())
		{
			ThinkAjax.sendForm('form2',SITE_INDEX+'Xiaoshou/dopostdingdanxinxi/',doComplete,'resultdiv');
		}
	}
	return false;
}


function checktitle(){
	datas = user;
	var title = jQuery("#owner").val();
	var ishas = 0;
	for (var i=0;i<datas.length;i++) { 
		if(title == datas[i]['title']){
			ishas = 1;
			break;
		}
	} 
	if(!ishas){
		alert("收客人填写错误");
	}
	else{
		var title = jQuery("#fuzeren").val();
		var ishas = 0;
		for (var i=0;i<datas.length;i++) { 
			if(title == datas[i]['title']){
				ishas = 1;
				break;
			}
		} 
		if(!ishas){
			alert("负责人填写错误");
		}
		else{
			return true;
		}
	}
}
	function doshenhe(dotype){
		ThinkAjax.myloading('resultdiv');
		var dataID = chanpinID;
		var datatype = '订单';
		jQuery.ajax({
			type:	"POST",
			url:	SITE_INDEX+"Chanpin/doshenhe",
			data:	"dataID="+dataID+"&dotype="+dotype+"&datatype="+datatype+"&title="+title,
			success:function(msg){
				ThinkAjax.myAjaxResponse(msg,'resultdiv');
			}
		});
	}


jQuery().ready(function() {
	  myautocomplete("#owner",'用户');
	  myautocomplete("#fuzeren",'用户');
});
		
 function myautocomplete(target,parenttype)
{
		if(parenttype == '用户')
		datas = user;
		jQuery(target).unautocomplete().autocomplete(datas, {
		   max: 50,    //列表里的条目数
		   minChars: 0,    //自动完成激活之前填入的最小字符
		   width: 150,     //提示的宽度，溢出隐藏
		   scroll:false,
		   matchContains: true,    //包含匹配，就是data参数里的数据，是否只要包含文本框里的数据就显示
		   autoFill: true,    //自动填充
		   formatItem: function(data, i, num) {//多选显示
			   return data.title;
		   },
		   formatMatch: function(data, i, num) {//匹配格式
			   return data.title;
		   },
		   formatResult: function(data) {//选定显示
			   return data.title;
		   }
		})
}

	
 
function TravelerDetail(id)
{
	if(id == ''){
	ajaxalert("请填写团员基本信息，并保存后刷新后重试！！");
	return ;
	}
	save();//保存
    var url=SITE_INDEX+"Xiaoshou/tuanyuanxinxi/id/"+id;
    window.open(url,'newwin','width=900,height=700,left=240,status=no,resizable=yes,scrollbars=yes');
}


function dofukuan(id){
	is_pay = '已支付';
	pay_method = jQuery("#pay_method").val();
	ThinkAjax.sendForm('form1',SITE_INDEX+'Xiaoshou/dingdan_daokuanqueren/dingdanID/'+id+'/is_pay/'+is_pay+'/pay_method/'+pay_method,doComplete,'resultdiv');
}

function lingduiortuanyuan(id){
	if(!id)
		alert("不存在团员信息，请保存信息后操作！！！");
	else{
		if(jQuery("#is_leader"+id).val() == '领队')
		jQuery("#is_leader"+id).val('团员');
		else
		jQuery("#is_leader"+id).val('领队');
		scroll(0,0);
		ThinkAjax.sendForm('form1',SITE_INDEX+'Xiaoshou/dopostdingdanxinxi/daokuanqueren/1',doComplete,'resultdiv');
	}
}

function quxiaocantuan(id){
	if(!id)
		alert("不存在团员信息，请取消订单并重新提交订单！！！");
	else	
	jQuery.ajax({
		type:	"POST",
		url:	SITE_INDEX+"Xiaoshou/quxiaocantuan",
		data:	"tuanyuanID="+id,
		success:function(msg){
			ThinkAjax.myAjaxResponse(msg,'resultdiv_2',quxiaocantuan_after);
		}
	});
}
function quxiaocantuan_after(data,status){
	if(status == 1){
		location.reload();
	}
}

function querendingdan(id){
	jQuery("#status").val('确认');
	ThinkAjax.sendForm('form2',SITE_INDEX+'Xiaoshou/dopostdingdanxinxi/',doComplete,'resultdiv');
}

