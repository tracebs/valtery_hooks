<html>
<head>
</head>
<body>
<?php
	echo "Start - ".date("h:i:s")."<br>";
	@include_once 'amoCRM_include.php';
	
	$echostr = "";
	$strhookmanager = "";
			
	
	if ( function_exists('fncAmocrmUpdateAllContacts') ){
		$var = fncAmocrmUpdateAllContacts($arrAmocrmIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strAmocrmCookieFile);				
		echo "Answer:".$var."<br>";
	}
	
		
	echo "Finish - ".date("h:i:s")."<br>";
	
	
	//mail("rsdim@rambler.ru","Subj hook started","json:"."!".$var);
?>
</body>
</html>