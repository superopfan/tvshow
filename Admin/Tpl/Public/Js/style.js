$(function() {
	/*
	 * 编辑类型
	 */
	$('.editbtn').click(function() {
		var styleid = '#' + $(this).attr('forid');
		var thisbtn = $(this);
		if ($(this).html() == '编辑') {
			$(this).html("保存");
			$(styleid).removeAttr("disabled");
		} else {
			var stylename = $(styleid).val();
			$.ajax({
				url: editurl,
				data: {
					styleid: $(this).attr('forid'),
					stylename: stylename
				},
				type: 'post',
				success: function(data) {
					if (data == 1) {

						thisbtn.html("编辑");
						$(styleid).attr("disabled", "disabled");
					} else {
						alert("修改失败");
					}
				}
			});
		}
	});
	/*
	 * 新增类型
	 */

	$('#addbtn').click(function() {
		$('#addstyle').show();

	});
	$('#save').click(function() {
		var stylename=$('#newstyle').val();
		
		
			$.ajax({
				url: addurl,
				data: {
					 
					stylename: stylename
				},
				type: 'post',
				success: function(data) {
					if (data == 1) {
						location.reload();
					} else {
						alert("保存失败");
					}
				}
			});
		

	});
	
	
});