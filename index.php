<!DOCTYPE HTML>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>HealthyRecipe</title>
  <meta name="description" content="awesome">
  <meta name="author" content="wts34">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="stylesheet" href="css/style.css?v=3">
  <script src="js/libs/modernizr-1.7.min.js"></script>

</head>

<body>
	<h2>HealthyRecipe</h2>
	<form id="getdata" action="fetch_initial.php" method="GET">
		<fieldset><!--<label for="ingredient">Ingredients (comma separated list): </label><input type="text" name="ingredient" id="ingredient" /><br />-->
		<input type="text" name="query" id="query" class="required" placeholder="query..." /><br />
<!--			<input type="submit" value="Fetch" />-->
		</fieldset>
	</form>
	<div id="returndata">
		Loading...
	</div>

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript"></script>
  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
  <!-- scripts concatenated and minified via ant build script-->
  <script src="js/plugins.js?v=2"></script>
  <script src="js/script.js"></script>

	<script type="text/javascript">
		
	</script>  

</body>
</html>