$(document).ready(function() {
	$("#query").focus();
	$("#getdata").validate({
		submitHandler: function(form) {
			
			$("#returndata").fadeIn();
			$("#getdata").hide();
			
			$(form).ajaxSubmit({
				success : function(resp) {
					parseXml(resp);
				},
				error: function(resp) {
	                $("#returndata").html(resp);
	            }
			});
			return false;
		}
	});
});

function parseXml(xmlDoc) {
	var html = "";
	var $xml = $(xmlDoc);
	
	$xml.find("recipe").each(function() {
		html += "<div class='recipe'>";
			html += "<a class='recipetitle' href='"+$(this).find("href").text()+"'>"+$(this).find("name").text()+"</a><br />";
			$(this).find("ingredient").each(function() {
				html += "<div class='ingredient'>"+$(this).attr("name");
					$(this).find("nutrient").each(function() {
						html += "<div class='nutrient'>"+$(this).attr("name")+" : "+$(this).text()+"</div>";
					});
				html += "</div>";
			});
		html += "</div>";
	});
	
	$("#returndata").html(html);
}