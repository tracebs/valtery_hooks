<html>
<head>
</head>
<body>
<?php
	echo "Start - ".date("h:i:s")."<br>";
	@include_once 'amoCRM_include.php';
	
	$echostr = "";
	$strhookmanager = "";
	
	$login = "rsdim1";
	$email = "rsdim2@mail.ru";
	$telefon = "355555";
	$lname = "Растегаев1";
	$fname = "Дмитрий1";
	$name3 = "Петрович1";
	$fullname = $lname."".$fname."".$name3;			
	//$strEmail, $strLogin, $strName, $strPhone, $strInn, $strCmp_name, $strCmp_name_full, $strCmp_regplace, $strCmp_fio, $strCmp_site, $strCookieFile, $strFwritePath, $arrIdsResponsible
	//
	if ( function_exists('fncAmocrmTextiloptomRegForm') ){
			$var = fncAmocrmTextiloptomRegForm(
					$email,
					$login,
					$fullname,
					$telefon,
					"p1",
					$fullname,
					"p6",
					"p7",
					"p20",
					"p14",
					$strAmocrmCookieFile,
					$strAmocrmFwritePath,
					$arrAmocrmIdsResponsible
			);
			//var_dump($var);
			echo "<br>Answer:".$var."<br>";
		}
		
	echo "<br>------------------------------";	
	echo "<br>fncAmocrmFaqForm:<br>";
	$login = "rsdim4";
	$email = "rsdim4@mail.ru";
	$telefon = "455555";
	$lname = "Растегаев4";
	$fname = "Дмитрий4";
	$name3 = "Петрович4";
	$fullname = $lname."".$fname."".$name3;	
	//$strName, $strQstn, $strEmail, $strCookieFile, $strFwritePath, $arrIdsResponsible
	if ( function_exists('fncAmocrmFaqForm') ){
			$var = fncAmocrmFaqForm(										
					$fullname,
					"0",					
					$email,					
					$strAmocrmCookieFile,
					$strAmocrmFwritePath,
					$arrAmocrmIdsResponsible
			);
			//var_dump($var);
		}
	//fncAmocrmApiForm
	//$strEmail, $strLogin, $strName, $strSiteurl, $strCookieFile, $strFwritePath, $arrIdsResponsible
	echo "<br>------------------------------";	
	echo "<br>fncfncAmocrmApiForm:<br>";	
	$login = "rsdim5";
	$email = "rsdim5@mail.ru";
	$telefon = "555555";
	$lname = "Растегаев5";
	$fname = "Дмитрий5";
	$name3 = "Петрович5";
	$fullname = $lname."".$fname."".$name3;	
	if ( function_exists('fncAmocrmApiForm') ){
			$var = fncAmocrmApiForm(	
					$email,
					$login,
					$fullname,
					'www.google.ru',			
					$strAmocrmCookieFile,
					$strAmocrmFwritePath,
					$arrAmocrmIdsResponsible
			);
			//var_dump($var);
		}
	$homepage = file_get_contents($strAmocrmCookieFile);
	
		
	echo "Finish - ".date("h:i:s")."<br>";
	
	
	//mail("rsdim@rambler.ru","Subj hook started","json:"."!".$var);
?>
</body>
</html>