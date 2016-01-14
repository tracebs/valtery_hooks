<html>
<head>
</head>
<body>
<?php
	echo "Start - ".date("h:i:s")."<br>";
	@include_once 'amoCRM_include.php';
	
	$echostr = "";
	$strhookmanager = "";
			
	$straTag = "импорт domiteks.spb@gmail.com";
	$strhookLeadId = "8766180"; //domitex.amocrm.ru/private/api/v2/json/leads/list?id=8766180
	if ( function_exists('fncAmocrmCheckTag') ){
		$povanovvar = fncAmocrmCheckTag($straTag,$strhookLeadId,$arrAmocrmIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strAmocrmCookieFile);				
		echo "povanovvar:".$povanovvar."<br>";
	}
	if ($povanovvar == "1") {	
		echo "Start:".$povanovvar."<br>";
			//если тэг есть 
			//то назначаем и сделку и контакт на Пованова
			//id Пованова - 624678
			//mail("rsdim@rambler.ru","Subj hook started - add","json:".$strpost); 
			$strPovanovId = '624678';
			if ( function_exists('fncAmocrmUpdateLeadContactTo') ){
				$var = fncAmocrmUpdateLeadContactTo($strhookLeadId,$strPovanovId,$arrAmocrmIdsResponsibleDisable,$strAmocrmCookieFile);				
			}
	}	
	echo "Finish - ".date("h:i:s")."<br>";
	
	
	//mail("rsdim@rambler.ru","Subj hook started","json:"."!".$var);
?>
</body>
</html>