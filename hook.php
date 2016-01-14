<?php
	@include_once 'amoCRM_include.php';
	
	$echostr = "";
	$strhookmanager = "";
	if(isset($_POST)) {
		if (isset($_POST['leads'])) {			
			$strpost = json_encode($_POST);
			@file_put_contents("hook.txt",$strpost);
			$arrpost = $_POST;
			$arrLeads = $_POST['leads'];
			foreach($arrLeads as $key1=>$value1) {
				
				if ($key1=="add") {
					$strhookmanager = $arrpost['leads']['add'][0]['responsible_user_id'];
					$strhookLeadId = $arrpost['leads']['add'][0]['id'];	
					//проверка тэгов в сделке для назначения на Пованова
					
				}
				if ($key1=="update") {
					//$strhookmanager = $arrpost['leads']['update'][0]['responsible_user_id'];
					//$strhookLeadId = $arrpost['leads']['update'][0]['id'];					
				}
				
			}
			if ($strhookmanager!="") {
				//проверяем наличие тэга в сделке				
				$povanovvar = "";
				$straTag = "импорт domiteks.spb@gmail.com";
				if ( function_exists('fncAmocrmCheckTag') ){
					$povanovvar = fncAmocrmCheckTag($straTag,$strhookLeadId,$arrAmocrmIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strAmocrmCookieFile);				
					// вернет "1" - если тэг есть и "0" - если тэга нет
					
				}
				if ($povanovvar == "1") {	
					//если тэг есть 
					//то назначаем и сделку и контакт на Пованова
					//id Пованова - 624678
					//mail("rsdim@rambler.ru","Subj hook started - add","json:".$strpost);
					$strPovanovId = '624678';
					if ( function_exists('fncAmocrmUpdateLeadContactTo') ){
							$var = fncAmocrmUpdateLeadContactTo($strhookLeadId,$strPovanovId,$arrAmocrmIdsResponsibleDisable,$strAmocrmCookieFile);				
					}
					
					//if	(in_array($strhookmanager,$arrAmocrmIdsResponsibleDisable)) {
					//	if ( function_exists('fncAmocrmUpdateLeadContact') ){
					//		$var = fncAmocrmUpdateLeadContact($strhookLeadId,$arrAmocrmIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strAmocrmCookieFile);				
					//	}
					//}				
				} else {
					//Назначаем по карусели
					//mail("rsdim@rambler.ru","Subj hook started - add","json:".$strpost);
					if	(in_array($strhookmanager,$arrAmocrmIdsResponsibleDisable)) {
						if ( function_exists('fncAmocrmUpdateLeadContact') ){
							$var = fncAmocrmUpdateLeadContact($strhookLeadId,$arrAmocrmIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strAmocrmCookieFile);				
						}
					}				
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
