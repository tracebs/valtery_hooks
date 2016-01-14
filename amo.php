<html>
<head>
<title>hook</title>
</head>
<body>
<?php
	@include_once 'amoCRM_include.php';
	if ( function_exists('fncAmocrmTextiloptomRegForm') ){
		$var = fncGetIdNextResponsible($arrAmocrmIdsResponsible, "");
	}	
	echo "varr: ".$var;
?>
</body>
</html>