$(function() {
	//头像上传 Uploadify 插件
	var coverstyle=$('#face').attr('coverstyle');
	
	$('#face').uploadify({
		swf: PUBLIC + '/Uploadify/uploadify.swf', //引入Uploadify核心Flash文件
		uploader: uploadUrl, //PHP处理脚本地址
		width: 120, //上传按钮宽度
		height: 30, //上传按钮高度
		buttonImage: PUBLIC + '/Uploadify/browse-btn.png', //上传按钮背景图地址
		fileTypeDesc: 'Image File', //选择文件提示文字
		fileTypeExts: '*.jpeg; *.jpg; *.png; *.gif', //允许选择的文件类型
		formData: {
			'session_id': sid,
			'coverstyle':coverstyle
		}, //添加session
		//上传成功后的回调函数
		onUploadSuccess: function(file, data, response) {			
			eval('var data = ' + data);
			 			if (data.status) {
				$('#cover-img').attr('src', ROOT + '/Uploads/Cover/' + data.path.mini);
				$('input[name=covermax]').val(data.path.max);
				$('input[name=covermini]').val(data.path.mini);
			} else {
				alert(data.msg);
			}
		}
	});
});