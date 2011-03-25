$(document).ready(function() {
	$("#query").focus();
	$("#getdata").validate({
		submitHandler: function(form) {
			
			$("#returndata").fadeIn();
			$("#getdata").hide();
			
			$(form).ajaxSubmit({
				success : function(resp) {
					$("#returndata").html(resp);
				},
				error: function(resp) {
	                $("#returndata").html(resp);
	            }
			});
			return false;
		}
	});
});