$(function() {
	jQuery.validator.addMethod("user", function(value, element) {   
		    var tel = /^[a-zA-Z][\w]{4,16}$/;
		    return this.optional(element) || (tel.test(value));
		}, "以字母开头，5-17 字母、数字、下划线'_'");
	$('form[name=base]').validate({
		rules: {
			username: {
				required: true,
				user : true,
				rangelength: [2, 10],
				remote: {
					url: checkUname,
					type: 'post',
					dataType: 'json',
					data: {
						uname: function() {
							return $('#uname').val();
						}
					}
				}
			}
		},
		messages: {
			username: {
				required: '请填写要修改的昵称',
				rangelength: '昵称在2-10个字之间',
				remote: '昵称已存在'
			}
		}
	});
	
	$('form[name=psw]').validate({

		rules: {
			old: {
				required: true,
			},
			pwd: {
				required: true,
				user : true
			},
			pwded: {
				required: true,			
				equalTo: "#pwd"
			}
		},
		messages: {
			old: {
				required: '请填写旧密码'
			},
			pwd: {
				required: '密码不能为空'
			},
			pwded: {
				required: '请确认密码',
				equalTo: '两次密码不一致'
			}
		}
	});

	/*
	 * 加载性别
	 */
	var sex = $('#sexinput').attr('sex');
	$("#sexinput input").each(function() {
		if ($(this).val() == sex) {
			$(this).attr("checked", "checked");
		}

	});

	/*
	 * 	头像上传 Uploadify 插件
	 */

	$('#face').uploadify({
		swf: PUBLIC + '/Uploadify/uploadify.swf', //引入Uploadify核心Flash文件
		uploader: uploadUrl, //PHP处理脚本地址
		width: 120, //上传按钮宽度
		height: 30, //上传按钮高度
		buttonImage: PUBLIC + '/Uploadify/browse-btn.png', //上传按钮背景图地址
		fileTypeDesc: 'Image File', //选择文件提示文字
		fileTypeExts: '*.jpeg; *.jpg; *.png; *.gif', //允许选择的文件类型
		formData: {
			'session_id': sid
		}, //添加session
		//上传成功后的回调函数
		onUploadSuccess: function(file, data, response) {
			eval('var data = ' + data);
			if (data.status) {
				$('#face-img').attr('src', ROOT + '/Uploads/Face/' + data.path.max);
				$('input[name=facemax]').val(data.path.max);
				$('input[name=facemini]').val(data.path.mini);
			} else {
				alert(data.msg);
			}
		}
	});

	/*
	 * 标签页切换
	 */

	$('.nav-tabs li').click(
		function() {
			var act = $(this).attr('act');
			$('.nav-tabs li').each(function() {
				if ($(this).attr('act') == act) {
					$(this).addClass("activeli");
				} else {
					$(this).removeClass("activeli");

				}
			});
			$(".page form").each(function() {
				if ($(this).attr('act') == act) {
					$(this).removeClass("nodisplay")
						.addClass("display");
				} else {
					$(this).removeClass("display")
						.addClass("nodisplay");
				}
			});
		});
});