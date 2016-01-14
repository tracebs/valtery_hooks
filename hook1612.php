<?php
	@include_once 'amoCRM_include.php';
	
	$echostr = "";
	$strhookstatus = "";
	if(isset($_POST)) {
		if (isset($_POST['leads'])) {			
			$strpost = json_encode($_POST);			
			mail("rsdim@rambler.ru","Subj hook started 1612","json:".$strpost);
			$arrpost = $_POST;
			$arrLeads = $_POST['leads'];
			
			foreach($arrLeads as $key1=>$value1) {				
				if ($key1=="status") {
					$strhookstatus = $arrpost['leads']['status'][0]['status_id'];
					$strhookLeadId = $arrpost['leads']['status'][0]['id'];					
				}
			}
			//142 - признак Успешно завершено
			if ($strhookstatus=="142") {	
				mail("rsdim@rambler.ru","Subj hook started 1612 - status142","json:".$strpost);
				if ( function_exists('fncAmocrmUpdateContactTag') ){
					$var = fncAmocrmUpdateContactTag($strhookLeadId,$arrAmocrmIdsResponsible,$strAmocrmCookieFile);				
				}				
			} else {
				//mail("rsdim@rambler.ru","Subj hook started - update","json:".$strpost);
			}
			
		} else {
			//какойто неправильный POST
		}		
	} else {
		//какойто неправильный POST
	}
	
	
	
	//mail("rsdim@rambler.ru","Subj hook started","json:"."!".$var);
?>
