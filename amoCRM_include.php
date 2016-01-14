<?php

# настройки (((

define('AMOCRM_LOGIN', 'nikolaev@textiloptom.net');
define('AMOCRM_SUBDOMAIN', 'domitex');
define('AMOCRM_API_KEY', '7a3aeaabf4501e6d6545140e5600b079');

define('AMOCRM_CONTACT_PHONE_CSTFID', 600364); # id поля Телефон у контакта
define('AMOCRM_CONTACT_PHONE_CSTFTYPE', 'WORK'); # тип поля Телефон у контакта

define('AMOCRM_CONTACT_EMAIL_CSTFID', 600366); # id поля Email у контакта
define('AMOCRM_CONTACT_EMAIL_CSTFTYPE', 'WORK'); # тип поля Email у контакта

define('AMOCRM_CONTACT_LGNt_CSTFID', 651536); # id поля "Логин textiloptom" у контакта
define('AMOCRM_CONTACT_LGNs_CSTFID', 651538); # id поля "Логин sailid" у контакта
define('AMOCRM_CONTACT_LGNa_CSTFID', 651540); # id поля "Логин API" у контакта

define('AMOCRM_COMPANY_INN_CSTFID', 652037); # id поля "ИНН" у компании
define('AMOCRM_COMPANY_FULLN_CSTFID', 652039); # id поля "Полное наименование" у компании
define('AMOCRM_COMPANY_YURADDR_CSTFID', 648716); # id поля "Юридический адрес" у компании
define('AMOCRM_COMPANY_DFIO_CSTFID', 652041); # id поля "ФИО руководителя" у компании
define('AMOCRM_COMPANY_SITE_CSTFID', 600368); # id поля "Web" у компании

define('AMOCRM_TASKTYPECALL_ID', 1); # id типа задачи "Звонок"
define('AMOCRM_LOG_FILE', 'amolog.txt'); # запись логов
define('AMOCRM_LOG_FILE2', 'amolog2.txt'); # запись логов

# ответственные (((
define('AMOCRM_ID_FIXED_RESPONSIBLE', 594474);
//id ответственных 624678 - Пованов
$arrAmocrmIdsResponsible = array (591174, 591192, 594459, 594462, 594480, 594486, 594489, 591168);
//исключения ответсвенных из 594492 - Николаев, Селезнев - 628743 
$arrAmocrmIdsResponsibleDisable = array (594492, 628743);
# ))) ответственные

$strAmocrmFwritePath = $_SERVER['DOCUMENT_ROOT']. '';

$strAmocrmCookieFile = $strAmocrmFwritePath . '\\cookies.txt';

# ))) настройки

# ниже - функции

#-----------
function fncGetIdNextResponsible($arrIdsRspnsible, $strFwrtPath) {	
	//функция выполняет получение следующего ответственого
    $strFileFullName = $strFwrtPath . 'idLatestResp.txt';
	
	if (is_writable($strFileFullName)) {
		//если файл доступен для записи - то берем случайным образом нового менеджера не равного предыдущему и возвращаем в результат нового совсемстно с записью в файл
		$idNextResp = NULL;

		if (
			count($arrIdsRspnsible)
		) {
			if (
				1 == count($arrIdsRspnsible)
			) {
				$idNextResp = $arrIdsRspnsible[0];
			} # if
			else {
			
				$idLatestResp = NULL;
				//получаем ID последнего менеджера из файла
				if (
					is_file($strFileFullName)
				) {
					$strTmp = @file_get_contents($strFileFullName);
					if (
						$strTmp !== FALSE
					) {
						$idLatestResp = $strTmp;
					} # if
				} # if
				
				if (
					! isset ($idLatestResp)
				) {
					$idNextResp = $arrIdsRspnsible[array_rand($arrIdsRspnsible)];
				} # if
				else {
					$kFound = NULL;
					foreach ( $arrIdsRspnsible as $k => $v ) {
						if (
							$v == $idLatestResp
						) {
							$kFound = $k;
						} # if
					} # foreach

					if (
						! isset ($kFound)
					) {
						$idNextResp = $arrIdsRspnsible[array_rand($arrIdsRspnsible)];
					} # if
					else {
						if (
							! isset ($arrIdsRspnsible[1+$kFound])
						) {
							$idNextResp = $arrIdsRspnsible[0];
						} # if
						else {
							$idNextResp = $arrIdsRspnsible[1+$kFound];
						} # else
					} # else
	
				} # else
	
			} # else
		} # if

		if (
			isset ($idNextResp)
		) {	
			//пишем в файл последнего
			@file_put_contents($strFileFullName, $idNextResp);		
		} # if
	} else {
		//если нельзя записать в файл последнего менеджера - то генерим случайного из массива
		$idNextResp = NULL;

		if (
			count($arrIdsRspnsible)
		) {
			if (
				1 == count($arrIdsRspnsible)
			) {
				$idNextResp = $arrIdsRspnsible[0];
			} else {
				$idNextResp = $arrIdsRspnsible[array_rand($arrIdsRspnsible)];
			}	
		}	
		//иначе просто генерим случайный номер менеджера 
	} //else    

    return $idNextResp;

} # function
#-----------

#-----------
function fncAmocrmAuth($strLogin, $strSubdomain, $strApiKey, $strCookieFileName) {

    # почти copy-paste из документации (((

    #Массив с параметрами, которые нужно передать методом POST к API системы
    $user=array(
      'USER_LOGIN'=>$strLogin, #Ваш логин (электронная почта)
      'USER_HASH'=>$strApiKey #Хэш для доступа к API (смотрите в профиле пользователя)
    );
     
    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
     
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
    curl_close($curl); #Завершаем сеанс cURL

    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    $Response=$Response['response'];
    if(isset($Response['auth'])) #Флаг авторизации доступен в свойстве "auth"
        return array (
          'boolOk' => TRUE,
        );
    return array (
      'boolOk' => FALSE,
      'strErrDevelopUtf8' => 'AmoCRM error: ' . 'Авторизация не удалась',
    );

    # ))) почти copy-paste из документации

} # function
#-----------

#-----------
function fncAmocrmContactsSet(
    $strSubdomain,
    $strCookieFileName,
    $arrContactsSet,
    $addORupdate # 'add' или 'update'
) {

    # почти copy-paste из документации (((

    $contacts['request']['contacts'][$addORupdate] = $arrContactsSet;

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($contacts));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------

#-----------
function fncAmocrmContactsListByResponsibleID(
    $strSubdomain,
    $strCookieFileName,
    $strresponsibleid = ''
) {	
	//example - domitex.amocrm.ru/private/api/v2/json/contacts/list?responsible_user_id=628743
    # почти copy-paste из документации (((

    $subdomain = $strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list';
    if (
        $strresponsibleid != ''
    ) {
        $link .= '?responsible_user_id=' . urlencode($strresponsibleid);
    } # if
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------
#-----------
function fncAmocrmContactsList(
    $strSubdomain,
    $strCookieFileName,
    $query = ''
) {

    # почти copy-paste из документации (((

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list';
    if (
        $query != ''
    ) {
        $link .= '?query=' . urlencode($query);
    } # if
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------
#-----------Получает связь между контактами и сделками
function fncAmocrmContactsGet(
    $strSubdomain,
    $strCookieFileName,
    $query11512 = ''
) {
	//mail("rsdim@rambler.ru","Subj hook started","query11512".$query11512);
    # почти copy-paste из документации (((

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/links';
    if (
        $query11512 != ''
    ) {
        $link .= '?deals_link=' . urlencode($query11512);
    } # if
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------
//Получает контакт по id контакта
function fncAmocrmContactsListById(
    $strSubdomain,
    $strCookieFileName,
    $query1512 = ''
) {

    # почти copy-paste из документации (((
	
    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list';
    if (
        $query1512 != ''
    ) {
        $link .= '?id=' . urlencode($query1512);
    } # if
    mail("rsdim@rambler.ru","Subj hook started  3.3 fncAmocrmContactsListById","query1512: ".$query1512." link:".$link);
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $strout=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    mail("rsdim@rambler.ru","Subj hook started  3.4 fncAmocrmContactsListById","query1512: ".$query1512." link:!".$link."! code:".$code);
    $code=(int)$code;
	//$resout = gettype($out);
	@file_put_contents("curl.txt",$strout);
	//$out2 = quotemeta($out);
	// --- 628 - 600,500,525 -> 530
	$outpos = strpos($strout,'"custom_fields":[{');
	$outpos = $outpos - 1;
	$out2 = substr($strout, 0, $outpos);	
	$out2 .= '}]}}';
	//$out2 = strlen($out);	
	mail("rsdim@rambler.ru","Subj hook started  3.5 fncAmocrmContactsListById","query1512: ".$query1512." link:!".$link."! Out:".$out2); 
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
	
	
    $Response=json_decode($out2,true);
	
    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );
	
    # ))) почти copy-paste из документации

} # function
#-----------
#-----------
function fncAmocrmLeadsCreate(
    $strSubdomain,
    $strCookieFileName,
    $arrLeadsCreate
) {

    # почти copy-paste из документации (((	
    $leads['request']['leads']['add'] = $arrLeadsCreate;

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------

#-----------
function fncAmocrmLeadsUpdate(
    $strSubdomain,
    $strCookieFileName,
    $arrLeadsCreate
) {

    # почти copy-paste из документации (((	
    $leads['request']['leads']['update'] = $arrLeadsCreate;

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------
function fncAmocrmLeadsGetById(
    $strSubdomain,
    $strCookieFileName,
    $leadid1712
) {

    # почти copy-paste из документации (((

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list';
    if (
        $leadid1712 != ''
    ) {
        $link .= '?id=' . urlencode($leadid1712);
    } # if
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

} # function
#-----------

#-----------
function fncAmocrmTasksCreate(
    $strSubdomain,
    $strCookieFileName,
    $arrTasksCreate
) {

    # почти copy-paste из документации (((

    $tasks['request']['tasks']['add'] = $arrTasksCreate;

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/tasks/set';
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($tasks));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------

#-----------
function fncAmocrmNotesCreate(
    $strSubdomain,
    $strCookieFileName,
    $arrNotesCreate
) {

    # почти copy-paste из документации (((

    $notes['request']['notes']['add']= $arrNotesCreate;

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/notes/set';
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($notes));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------

#-----------
function fncAmocrmCompaniesSet(
    $strSubdomain,
    $strCookieFileName,
    $arrCompaniesSet,
    $addORupdate # 'add' или 'update'
) {

    # почти copy-paste из документации (((

    $companies['request']['contacts'][$addORupdate] = $arrCompaniesSet;

    $subdomain=$strSubdomain; #Наш аккаунт - поддомен
    #Формируем ссылку для запроса
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/company/set';
    
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($companies));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    
    $code=(int)$code;
    $errors=array(
      301=>'Moved permanently',
      400=>'Bad request',
      401=>'Unauthorized',
      403=>'Forbidden',
      404=>'Not found',
      500=>'Internal server error',
      502=>'Bad gateway',
      503=>'Service unavailable'
    );
    try
    {
      #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
      if($code!=200 && $code!=204)
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
        );
    }
    catch(Exception $E)
    {
        return array (
          'boolOk' => FALSE,
          'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode(),
        );
    }
     
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response=json_decode($out,true);

    return array (
      'boolOk' => TRUE,
      'arrResponse' => $Response['response'],
    );

    # ))) почти copy-paste из документации

} # function
#-----------

//$strQstn - это вопрос
function fncAmocrmFaqForm($strName, $strQstn, $strEmail, $strCookieFile, $strFwritePath, $arrIdsResponsible) {
	echo "start fncAmocrmFaqForm";
	//используемые функции
	// fncAmocrmAuth - авторизация
	// fncGetIdNextResponsible - 
	// fncAmocrmContactsList - получение списка контактов
	// fncAmocrmContactsSet - !!!создание\update сущности Контакт - ответственный нужен	
	// fncAmocrmTasksCreate - !!!создание сущности Задача - ответственный нужен
	// fncAmocrmNotesCreate - !!!создание сущности Примечание - ответственный НЕ нужен
	
    # пытаемся авторизироваться в amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        //return array("error"=>"not_auth");
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
		//проверяем наличие контакта и update если нужно
		$arrContUpdate = fncUpdateContactAmo($strEmail, "", $idNextResponsible, "", $strCookieFile);
		if ($arrContUpdate[2]!="") {
			$idNextResponsible = $arrContUpdate[2];
		}
        # пытаемся поискать
        $arrAmocrmContactsList = fncAmocrmContactsList(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            $strEmail
        );

        if (
        ! $arrAmocrmContactsList['boolOk']
        ) {
            # $arrAmocrmContactsList['strErrDevelopUtf8']
        } # if
        else {

            $idContactExists = NULL;

            if (
                isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                &&
                count($arrAmocrmContactsList['arrResponse']['contacts'])
            ) {
                # для каждого найденного контакта
                # есть break!
                foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                    # отбираем его emailы
                    $arrCntEmails = array ();
                    if (
                        isset ($arrCntct['custom_fields'])
                        &&
                        count($arrCntct['custom_fields'])
                    ) {
                        foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                            if (
                                AMOCRM_CONTACT_EMAIL_CSTFID == $arrCF['id']
                            ) {
                                # это email
                                if (
                                    isset ($arrCF['values'])
                                    &&
                                    count($arrCF['values'])
                                ) {
                                    foreach ( $arrCF['values'] as $arrV ) {
                                        if (
                                            isset ($arrV['value'])
                                            &&
                                            trim($arrV['value']) != ''
                                        ) {
                                            $arrCntEmails[] = trim($arrV['value']);
                                        } # if
                                    } # foreach
                                } # if
                            } # if
                        } # foreach
                    } # if

                    if (
                    in_array($strEmail, $arrCntEmails)
                    ) {

                        $idContactExists = $arrCntct['id'];

                        break; # !!!

                    } # if

                } # foreach
            } # if

            if (
            ! isset ($idContactExists)
            ) {
				
                # пытаемся создать контакт
                $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => $strName,
							'tags' => 'Лид',
							'responsible_user_id' => $idNextResponsible,
                            'custom_fields' => array (
                                array (
                                    'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                    'values' => array (
                                        array (
                                            'value' => $strEmail,
                                            'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                        ),
                                    ),
                                ),
                            ),
                        )
                    ),
                    'add'
                );

                if (
                ! $arrAmocrmContactsSetAdd['boolOk']
                ) {
                    # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                } # if
                else {
                    $idContactExists = $arrAmocrmContactsSetAdd['arrResponse']['contacts']['add'][0]['id'];
                } # else

            } # if

            # пытаемся создать задачу
            $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                array (
                    array (
                        'element_id' => $idContactExists, # id контакта
                        'responsible_user_id' => $idNextResponsible,
                        'element_type' => 1, # 1 значит, что в element_id - контакт
                        'task_type' => AMOCRM_TASKTYPECALL_ID,
                        'text' => 'Новый вопрос с сайта textiloptom.ru',
                        'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                    ),
                )
            );

            if (
            ! $arrAmocrmTasksCreate['boolOk']
            ) {
                # $arrAmocrmTasksCreate['strErrDevelopUtf8']
            } # if

            if (
                $strQstn != ''
            ) {

                # пытаемся создать примечание
                $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $idContactExists,
                            'element_type' => 1, # 1 == контакт
                            'note_type' => 4, # 4 == обычное примечание https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                            'text' => $strQstn,
                        )
                    )
                );

                if (
                ! $arrAmocrmNotesCreate['boolOk']
                ) {
                    # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                } # if

            } # if

        } # else

    } # else

}
#-----------
function fncAmocrmFaqForm0($strName, $strQstn, $strEmail, $strCookieFile, $strFwritePath, $arrIdsResponsible) {

    # пытаемся авторизироваться в amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);

        # пытаемся создать сделку
        $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            array (
                array (
                    'name' => 'Новый вопрос. ' . date('d.m.Y'),
                    'responsible_user_id' => $idNextResponsible,
                )
            )
        );

        if (
            ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # пытаемся поискать
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strEmail
            );

            if (
                ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # для каждого найденного контакта
                    # есть break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # отбираем его emailы
                        $arrCntEmails = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                        &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_EMAIL_CSTFID == $arrCF['id']
                                ) {
                                    # это email
                                    if (
                                        isset ($arrCF['values'])
                                    &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                            &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntEmails[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                            in_array($strEmail, $arrCntEmails)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                                isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                    ! isset ($idContactExists)
                ) {

                    # пытаемся создать контакт
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
								'responsible_user_id' => $idNextResponsible,
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                        ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {

                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # пытаемся обновить контакт
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                        ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # пытаемся создать задачу
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id сделки
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 значит, что в element_id - сделка
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => 'Перезвонить',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                    ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

                if (
                    $strQstn != ''
                ) {

                    # пытаемся создать примечание
                    $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
                                'element_type' => 2, # 2 == сделка
                                'note_type' => 4, # 4 == обычное примечание https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                                'text' => $strQstn,
                            )
                        )
                    );

                    if (
                        ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # if

            } # else

        } # else

    } # else

} # function
#-----------

#-----------
function fncAmocrmPartnerForm($strName, $strPhone, $strEmail, $strCookieFile, $strFwritePath, $arrIdsResponsible) {
	//используемые функции
	// fncAmocrmAuth - авторизация
	// fncGetIdNextResponsible - 
	// fncAmocrmLeadsCreate - !!!создание сущности Сделка - ответственный нужен
	// fncAmocrmContactsList - получение списка контактов
	// fncAmocrmContactsSet - !!!создание сущности Контакт - ответственный нужен
	// fncAmocrmTasksCreate - !!!создание сущности Задача - ответственный нужен
	
    # пытаемся авторизироваться в amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
		//проверяем наличие контакта и update если нужно
		$arrContUpdate = fncUpdateContactAmo($strEmail, AMOCRM_LOGIN, $idNextResponsible, $strPhone, $strCookieFile);
		//
		# пытаемся создать сделку и поместить ее в нужную воронку
        if ($arrContUpdate[0] == "") {
			if ($arrContUpdate[1] == "1") {
				//для клиента c тэгом Лид в воронке Лид 'pipeline_id'=>40290
				$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					array (
						array (
							'name' => 'Новый партнёр c textiloptom.ru. ' . date('d.m.Y'),
							'pipeline_id'=>40290,
							'responsible_user_id' => $idNextResponsible,
						)
					)
				);
			} else {
				//для старого клиента в воронке клиент 'pipeline_id'=>10476
				$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					array (
						array (
							'name' => 'Новый партнёр c textiloptom.ru. ' . date('d.m.Y'),
							'pipeline_id'=>10476,
							'responsible_user_id' => $idNextResponsible,
						)
					)
				);
			}
		} else {			
			//для нового клиента в воронке Лид 'pipeline_id'=>40290
			$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
				AMOCRM_SUBDOMAIN,
				$strCookieFile,
				array (
					array (
						'name' => 'Новый партнёр c textiloptom.ru. ' . date('d.m.Y'),
						'pipeline_id'=>40290,
						'responsible_user_id' => $idNextResponsible,
					)
				)
			);
		}	      
		//$arrContUpdate[0]!="" - новый клиент
		//$arrContUpdate[1]=="1" - существующий клиент с тэгом Лид
		//===================
       

        if (
            ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # пытаемся поискать
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strPhone
            );

            if (
                ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # для каждого найденного контакта
                    # есть break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # отбираем его телефоны
                        $arrCntPhones = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                        &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_PHONE_CSTFID == $arrCF['id']
                                ) {
                                    # это телефон
                                    if (
                                        isset ($arrCF['values'])
                                    &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                            &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntPhones[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                            in_array($strPhone, $arrCntPhones)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                                isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                    ! isset ($idContactExists)
                ) {

                    # пытаемся создать контакт
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
								'tags' => "Лид",
								'responsible_user_id' => $idNextResponsible,
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strPhone,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            ),
                                        ),
                                    )
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                        ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {

                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # пытаемся обновить контакт
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                        ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # пытаемся создать задачу
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id сделки
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 значит, что в element_id - сделка
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => 'Обработать (textiloptom.ru)',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                    ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

            } # else

        } # else

    } # else

} # function !
#-----------

#-----------
#-----------
function fncAmocrmApiForm($strEmail, $strLogin, $strName, $strSiteurl, $strCookieFile, $strFwritePath, $arrIdsResponsible) {

    # пытаемся авторизироваться в amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
		//проверяем наличие контакта и update если нужно
		$arrContUpdate = fncUpdateContactAmo($strEmail, $strLogin, $idNextResponsible, $strPhone, $strCookieFile);
		if ($arrContUpdate[2]!="") {
			$idNextResponsible = $arrContUpdate[2];
		}
		# пытаемся создать сделку и поместить ее в нужную воронку
        if ($arrContUpdate[0] == "") {
			if ($arrContUpdate[1] == "1") {
				//для клиента c тэгом Лид в воронке Лид 'pipeline_id'=>40290
				$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					array (
						array (
							'name' => 'Новый пользователь API. ' . date('d.m.Y'),
							'pipeline_id'=>40290,
							'responsible_user_id' => $idNextResponsible,
						)
					)
				);
			} else {
				//для старого клиента в воронке клиент 'pipeline_id'=>10476
				$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					array (
						array (
							'name' => 'Новый пользователь API. ' . date('d.m.Y'),
							'pipeline_id'=>10476,
							'responsible_user_id' => $idNextResponsible,
						)
					)
				);
			}
		} else {			
			//для нового клиента в воронке Лид 'pipeline_id'=>40290
			$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
				AMOCRM_SUBDOMAIN,
				$strCookieFile,
				array (
					array (
						'name' => 'Новый пользователь API. ' . date('d.m.Y'),
						'pipeline_id'=>40290,
						'responsible_user_id' => $idNextResponsible,
					)
				)
			);
		}	      
		//$arrContUpdate[0]!="" - новый клиент
		//$arrContUpdate[1]=="1" - существующий клиент с тэгом Лид
		//================================================================
       
        if (
            ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # пытаемся поискать
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strEmail
            );

            if (
                ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # для каждого найденного контакта
                    # есть break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # отбираем его emailы
                        $arrCntEmails = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                        &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_EMAIL_CSTFID == $arrCF['id']
                                ) {
                                    # это email
                                    if (
                                        isset ($arrCF['values'])
                                    &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                            &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntEmails[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                            in_array($strEmail, $arrCntEmails)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                                isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                    ! isset ($idContactExists)
                ) {

                    # пытаемся создать контакт
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
								'tags' => "Лид",
								'responsible_user_id' => $idNextResponsible,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_LGNa_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strLogin,
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                        ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {

                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # пытаемся обновить контакт
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,								
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                        ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # пытаемся создать задачу
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id сделки
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 значит, что в element_id - сделка
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => 'Перезвонить',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                    ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

                if (
                    $strSiteurl != ''
                ) {

                    # пытаемся создать примечание
                    $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
                                'element_type' => 2, # 2 == сделка
                                'note_type' => 4, # 4 == обычное примечание https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                                'text' => 'Адрес сайта: ' . $strSiteurl,
                            )
                        )
                    );

                    if (
                        ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # if

            } # else

        } # else

    } # else

} # function
#-----------

#-----------
function fncAmocrmSailidRegForm($strLogin, $strEmail, $strPhone, $strSurname, $strName, $strMiddlename, $strCookieFile) {

    # пытаемся авторизироваться в amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = AMOCRM_ID_FIXED_RESPONSIBLE;

        # пытаемся создать сделку
        $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            array (
                array (
                    'name' => 'sailid регистрация. ' . date('d.m.Y'),
                    'responsible_user_id' => $idNextResponsible,
                )
            )
        );

        if (
            ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # пытаемся поискать
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strPhone
            );

            if (
                ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # для каждого найденного контакта
                    # есть break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # отбираем его телефоны
                        $arrCntPhones = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                        &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_PHONE_CSTFID == $arrCF['id']
                                ) {
                                    # это телефон
                                    if (
                                        isset ($arrCF['values'])
                                    &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                            &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntPhones[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                            in_array($strPhone, $arrCntPhones)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                                isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                    ! isset ($idContactExists)
                ) {

                    # пытаемся создать контакт
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strSurname . ( $strSurname != '' && ( $strName != '' || $strMiddlename != '' ) ? ' ' : '' ) . $strName . ( ( $strSurname != '' || $strName != '' ) && $strMiddlename != '' ? ' ' : '' ) . $strMiddlename,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
								'responsible_user_id' => $idNextResponsible,
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strPhone,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_LGNs_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strLogin,
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                        ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {

                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # пытаемся обновить контакт
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                        ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # пытаемся создать задачу
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id сделки
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 значит, что в element_id - сделка
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => 'Перезвонить',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                    ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

            } # else

        } # else

    } # else

} # function
#-----------

#-----------
function fncAmocrmCartForm($strName, $Strbudget, $strEmail, $strPhone, $txtOtherForLeadNote, $strCookieFile, $strFwritePath, $arrIdsResponsible) {
	//используемые функции
	// fncAmocrmAuth - авторизация
	// fncGetIdNextResponsible - 
	// fncAmocrmLeadsCreate - !!!создание сущности Сделка - ответственный нужен
	// fncAmocrmContactsList - получение списка контактов
	// fncAmocrmContactsSet - !!!создание сущности Контакт - ответственный нужен	
	// fncAmocrmTasksCreate - !!!создание сущности Задача - ответственный нужен
	// fncAmocrmNotesCreate - !!!создание сущности Примечание - ответственный НЕ нужен
	
    # пытаемся авторизироваться в amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
		//проверяем наличие контакта и update если нужно
		$arrContUpdate = fncUpdateContactAmo($strEmail, $strLogin, $idNextResponsible, $strPhone, $strCookieFile);
		# пытаемся создать сделку и поместить ее в нужную воронку
        if ($arrContUpdate[0] == "") {
			if ($arrContUpdate[1] == "1") {
				//для клиента c тэгом Лид в воронке Лид 'pipeline_id'=>40290
				$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					array (
						array (
							'name' => 'Новый заказ (textiloptom.ru). ' . date('d.m.Y'),
							'price' => $Strbudget,
							'pipeline_id'=>40290,
							'responsible_user_id' => $idNextResponsible,
						)
					)
				);
			} else {
				//для старого клиента в воронке клиент 'pipeline_id'=>10476
				$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					array (
						array (
							'name' => 'Новый заказ (textiloptom.ru). ' . date('d.m.Y'),
							'price' => $Strbudget,
							'pipeline_id'=>10476,
							'responsible_user_id' => $idNextResponsible,
						)
					)
				);
			}
		} else {			
			//для нового клиента в воронке Лид 'pipeline_id'=>40290
			$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
				AMOCRM_SUBDOMAIN,
				$strCookieFile,
				array (
					array (
						'name' => 'Новый заказ (textiloptom.ru). ' . date('d.m.Y'),
						'price' => $Strbudget,
						'pipeline_id'=>40290,
						'responsible_user_id' => $idNextResponsible,
					)
				)
			);
		}	      
		//$arrContUpdate[0]!="" - новый клиент
		//$arrContUpdate[1]=="1" - существующий клиент с тэгом Лид
		
		//===============
        if (
            ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # пытаемся поискать
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strPhone
            );

            if (
                ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # для каждого найденного контакта
                    # есть break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # отбираем его телефоны
                        $arrCntPhones = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                        &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_PHONE_CSTFID == $arrCF['id']
                                ) {
                                    # это телефон
                                    if (
                                        isset ($arrCF['values'])
                                    &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                            &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntPhones[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                            in_array($strPhone, $arrCntPhones)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                                isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                    ! isset ($idContactExists)
                ) {
					//если контакта нет
                    # пытаемся создать контакт
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
								'tags' => 'Лид',
								'responsible_user_id' => $idNextResponsible,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strPhone,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            ),
                                        ),
                                    )
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                        ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {
					//контакт есть
                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # пытаемся обновить контакт
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                        ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # пытаемся создать задачу
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id сделки
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 значит, что в element_id - сделка
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => 'Обработать новый заказ (textiloptom.ru).',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                    ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

                if (
                    $txtOtherForLeadNote != ''
                ) {

                    # пытаемся создать примечание
                    $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
                                'element_type' => 2, # 2 == сделка
                                'note_type' => 4, # 4 == обычное примечание https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                                'text' => $txtOtherForLeadNote,
                            )
                        )
                    );

                    if (
                        ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # if

            } # else

        } # else

    } # else

} # function

function fncUpdateContactAmo($strEmail, $strLogin, $idNextResponsible2, $strPhone, $strCookieFile) {
		echo "<br>start fncUpdateContactAmo data strEmail:".$strEmail." strPhone".$strPhone;
		//===============Растегаев Дмитрий 18122015===============================
		//		
		//возвращает данные контакта откуда получаем id ответственного сотрудника
		//этот id далее используется в создании сделки
		//========================================================================		
		$newclient = "";
		$leed1012 = "";	//примет отличное значение если в контакте есть тэг Лид				
		$contactid10122015 = "";
		$plen09122015 = strlen($strPhone); //при наличии в email как минимум 6 символов
		if ($plen09122015>=6) {			
			//проверка при наличии в телефоне как минимум 6 символов
			$arrAmocrmContactsList = fncAmocrmContactsList(
				AMOCRM_SUBDOMAIN,
				$strCookieFile,
				$strPhone
			);
			
			if (array_key_exists('arrResponse',$arrAmocrmContactsList)) {
				if ($arrAmocrmContactsList['arrResponse']===null) {
					//если по телефону ничего не нашли ищем по email
					$arrAmocrmContactsListE = fncAmocrmContactsList(
						AMOCRM_SUBDOMAIN,
						$strCookieFile,
						$strEmail
					);
					$contactdata10122015 = $arrAmocrmContactsListE; //массив для поиска и добавления данных 
					if ($arrAmocrmContactsListE['arrResponse']===null) {
						//не нашли по email
						$idNextResponsible = $idNextResponsible2;							
						$newclient = "1";
					}	else {
						//нашли по email
						$idNextResponsible = $arrAmocrmContactsListE['arrResponse']["contacts"][0]['responsible_user_id'];							
						$contactid10122015 = "".$arrAmocrmContactsListE['arrResponse']["contacts"][0]['id'];//id контакта
					}
				} else {
					//нашли по телефону
					$idNextResponsible = $arrAmocrmContactsList['arrResponse']["contacts"][0]['responsible_user_id'];
					$contactdata10122015 = $arrAmocrmContactsList;//массив для поиска и добавления данных 
					$contactid10122015 = "".$arrAmocrmContactsList['arrResponse']["contacts"][0]['id'];//id контакта
				}
			} else {
				$arrAmocrmContactsListE = fncAmocrmContactsList(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					$strEmail
				);
				$contactdata10122015 = $arrAmocrmContactsListE;//массив для поиска и добавления данных 
				if (array_key_exists('arrResponse',$arrAmocrmContactsListE)) {
					if ($arrAmocrmContactsListE['arrResponse']===null) {
						$idNextResponsible = $idNextResponsible2;
						$newclient = "1";
					}	else {
						//нашли по email
						$idNextResponsible = $arrAmocrmContactsListE['arrResponse']["contacts"][0]['responsible_user_id'];
						$contactid10122015 = "".$arrAmocrmContactsListE['arrResponse']["contacts"][0]['id'];//id контакта
					}
				}	else {
					$idNextResponsible = $idNextResponsible2;
					$newclient = "1";
				}
			}
		} else {
			//проверка при наличии в email как минимум 5 символов
			$plen09122015 = strlen($strEmail);
			if ($plen09122015>=5) {
				$arrAmocrmContactsListE = fncAmocrmContactsList(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					$strEmail
				);
				$contactdata10122015 = $arrAmocrmContactsListE;//массив для поиска и добавления данных 
				if (array_key_exists('arrResponse',$arrAmocrmContactsListE)) {
					if ($arrAmocrmContactsListE['arrResponse']===null) {
						$idNextResponsible = $idNextResponsible2;
						$newclient = "1";
					}	else {
						//нашли по email
						$idNextResponsible = $arrAmocrmContactsListE['arrResponse']["contacts"][0]['responsible_user_id'];
						$contactid10122015 = "".$arrAmocrmContactsListE['arrResponse']["contacts"][0]['id'];//id контакта
					}
				}	else {
					$idNextResponsible = $idNextResponsible2;
					$newclient = "1";
				}
			} else {
				//if если недостаточно строк в email и телефоне
				$idNextResponsible = $idNextResponsible2;
			}
		}
		//==============================================		
		//==============================================
		$leed1012 = "";	//примет отличное значение если в контакте есть тэг Лид
		$sdelka1012 = ""; //примет отличное значение если в контакте есть тэг Сделка
		echo "<br>Finish fncUpdateContactAmo point1";
		//если клиент не новый - то возможно нужен Update что и проверяем
		if ($newclient == "") {	
			echo "<br>Finish fncUpdateContactAmo point2";
			//======================update контакта=============================
			if ($contactid10122015!="") {
				echo "<br>Finish fncUpdateContactAmo point3";
				//контакт существует и нужно проверить значения в остальны полях для возможного UPDATE и поиска тэга Лид
				
				//ищем тэги лид и Сделка
				$tags1012 = $contactdata10122015['arrResponse']["contacts"][0]['tags'];							
				foreach($tags1012 as $onetag1012) {
					//ищем тэг Лид
					if($onetag1012['name']=="Лид") {
						$leed1012 = "1";
					}
					//ищем тэг Клиент
					if($onetag1012['name']=="Клиент") {
						$sdelka1012 = "1";
					}
				}
				
				
				
				$needupdate1012 = ""; //флаг указывающий на необходимость UPDATE
				$customfields = $contactdata10122015['arrResponse']["contacts"][0]['custom_fields'];
				$i1012 = 0;
				$flagphone = ""; //если останется пустым - нужен update
				$flagemail = ""; //если останется пустым - нужен update
				$flaglogin = ""; //если останется пустым - нужен update	
				$newemailarr = array();		
				$newphonearr = array();				
				echo "<br>Finish fncUpdateContactAmo point4";
				foreach($customfields as $value1012) {					
					//проверка на телефон
					if($strPhone!="") {	
						echo "<br>Finish fncUpdateContactAmo point5 - strPhone";
						if (array_key_exists('code',$value1012)) {														
							$strtmpcodeval = "".$value1012['code'];
							if($strtmpcodeval==='PHONE') {																
								//сравниваем телефон контакта и телефон из регистрации
							
								$arramovalue = $value1012['values'];								
								foreach($arramovalue as $arrcustomelement1812) {
									$strphonenum = "".$arrcustomelement1812['value'];
									//$strPhone
									if (($strPhone!="") and ($strphonenum!="")) {
										if ($strPhone===$strphonenum) {										
											$flagphone = "1";
										}
									} 
									$arrTmpPhone =  array (
                                                'value' => $strphonenum,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            );
									array_push($newphonearr,$arrTmpPhone);
								}
								//$amovalue = $contactdata10122015['arrResponse']["contacts"][0]['custom_fields'][$i1012]['values'][0]['value'];								
								//if ($amovalue!="") {									
									//НЕ update поля
								//	$flagphone = "1"; //ставим признак что update телефона не нужен
								//}
							}
						} 
					}
					//проверка на email
					if($strEmail!="") {
						echo "<br>Finish fncUpdateContactAmo point5 - strEmail";
						if (array_key_exists('code',$value1012)) {							
							$strtmpcodeval = "".$value1012['code'];
							if($strtmpcodeval==='EMAIL') {		
								//сравниваем email контакта и email из регистрации
								
								$arramovalue = $value1012['values'];								
								
								foreach($arramovalue as $arrcustomelement1812) {									
									$stremailval = "".$arrcustomelement1812['value'];
									//$strEmail
									if (($strEmail!="") and ($stremailval!="")) {
										if ($strEmail===$$stremailval) {												
											$flagemail = "1";
										}
									} 
									$arrTmpEmail =  array (
                                        'value' => $stremailval,
                                        'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                    );
									array_push($newemailarr,$arrTmpEmail);
								}
									
								//$amovalue = $contactdata10122015['arrResponse']["contacts"][0]['custom_fields'][$i1012]['values'][0]['value'];								
								//if ($amovalue!="") {
									//НЕ update поля
								//	$flagemail = "1";									
								//}
							}
						}
					}
					//проверка на логин					
					if($strLogin!="") {
						echo "<br>Finish fncUpdateContactAmo point5 - strLogin";
						if (array_key_exists('id',$value1012)) {
							if($value1012['id']=='651538') {
								//сравниваем логин контакта и логин из регистрации
								$amovalue = $contactdata10122015['arrResponse']["contacts"][0]['custom_fields'][$i1012]['values'][0]['value'];								
								if ($amovalue!="") {
									//НЕ update поля
									$flaglogin = "1";									
								}
							}
						}
					}
					$i1012++;
				}	
				echo "<br>Finish fncUpdateContactAmo check passed";				
				//не самое оптимальное решение по скорости работы - но так быстрее было сделать - задача была срочная
				//update телефона
				
				if( $flagphone != "1") {					
					//делаем update		
					$arrTmpPhone =  array (
										'value' => $strPhone,
                                        'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                        );
					array_push($newphonearr,$arrTmpPhone);					
					$arrAmocrmContactsSetUpdate1012 = fncAmocrmContactsSet(
						AMOCRM_SUBDOMAIN,
						$strCookieFile,
						array (
							array (
								'id' => $contactid10122015,					
								'last_modified' => time(),
								'custom_fields' => array (                                    
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => $newphonearr,
                                    ),
                                ),
							)	
						),
						'update'
					);
				}
				//update email
				if( $flagemail != "1") {
					//делаем update							
					$arrTmpEmail =  array (
										'value' => $strEmail,
                                        'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                        );
					array_push($newemailarr,$arrTmpEmail);										
					$arrAmocrmContactsSetUpdate1012 = fncAmocrmContactsSet(
						AMOCRM_SUBDOMAIN,
						$strCookieFile,
						array (
							array (
								'id' => $contactid10122015,					
								'last_modified' => time(),
								'custom_fields' => array (                                    
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' =>$newemailarr,
                                    ),
                                ),
							)	
						),
						'update'
					);
				}
				
				//update login
				if( $flaglogin != "1") {
					//делаем update									
					$arrAmocrmContactsSetUpdate1012 = fncAmocrmContactsSet(
						AMOCRM_SUBDOMAIN,
						$strCookieFile,
						array (
							array (
								'id' => $contactid10122015,					
								'last_modified' => time(),
								'custom_fields' => array (                                    
                                    array (
                                        'id' => AMOCRM_CONTACT_LGNs_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strLogin,
                                            ),
                                        ),
                                    ),
                                ),
							)	
						),
						'update'
					);
				}
				
			}
		}
		echo "<br>Finish fncUpdateContactAmo data strEmail:".$strEmail." strPhone:".$strPhone." idNextResponsible:".$idNextResponsible;
		//===============Растегаев 18122015===============================
		if($sdelka1012=="1") {
			//то есть тэг клиент есть
			$outTag1812 = "";
		} else {
			$outTag1812 = "1";
		}
		$rarray = array($newclient,$outTag1812,$idNextResponsible);
		return $rarray;
}
//-----------
function fncAmocrmTextiloptomRegForm($strEmail, $strLogin, $strName, $strPhone, $strInn, $strCmp_name, $strCmp_name_full, $strCmp_regplace, $strCmp_fio, $strCmp_site, $strCookieFile, $strFwritePath, $arrIdsResponsible) {
		echo "Start fncAmocrmTextiloptomRegForm";
//используемые функции
// fncAmocrmAuth - авторизация
// fncGetIdNextResponsible - 
// fncAmocrmLeadsCreate - !!!создание сущности Сделка - ответственный нужен
// fncAmocrmContactsList - получение списка контактов
// fncAmocrmContactsSet - !!!создание сущности Контакт - ответственный нужен
// fncAmocrmCompaniesSet - !!!создание сущности Компания - ответственный нужен
// fncAmocrmNotesCreate - !!!создание сущности Примечание - ответственный НЕ нужен
// fncAmocrmTasksCreate - !!!создание сущности Задача - ответственный нужен
    # пытаемся авторизироваться в amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
		
		//проверяем наличие контакта и update если нужно
		$arrContUpdate = fncUpdateContactAmo($strEmail, $strLogin, $idNextResponsible, $strPhone, $strCookieFile);
		echo "<br>Start fncAmocrmTextiloptomRegForm.fncUpdateContactAmo";
		if ($arrContUpdate[2]!="") {
			$idNextResponsible = $arrContUpdate[2];
		}
		# пытаемся создать сделку и поместить ее в нужную воронку
        if ($arrContUpdate[0] == "") {
			if ($arrContUpdate[1] == "1") {
				//для клиента c тэгом Лид в воронке Лид 'pipeline_id'=>40290
				$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					array (
						array (
							'name' => 'textiloptom.ru регистрация. ' . date('d.m.Y'),
							'pipeline_id'=>40290,
							'responsible_user_id' => $idNextResponsible,
						)
					)
				);
			} else {
				//для старого клиента в воронке клиент 'pipeline_id'=>10476
				$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
					AMOCRM_SUBDOMAIN,
					$strCookieFile,
					array (
						array (
							'name' => 'textiloptom.ru регистрация. ' . date('d.m.Y'),
							'pipeline_id'=>10476,
							'responsible_user_id' => $idNextResponsible,
						)
					)
				);
			}
		} else {			
			//для нового клиента в воронке Лид 'pipeline_id'=>40290
			$arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
				AMOCRM_SUBDOMAIN,
				$strCookieFile,
				array (
					array (
						'name' => 'textiloptom.ru регистрация. ' . date('d.m.Y'),
						'pipeline_id'=>40290,
						'responsible_user_id' => $idNextResponsible,
					)
				)
			);
		}	      
		//$arrContUpdate[0]!="" - новый клиент
		//$arrContUpdate[1]=="1" - существующий клиент с тэгом Лид
        if (
            ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {
			//если сделка создана то двигаемся дальше
            # пытаемся поискать
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strPhone
            );

            if (
                ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {
				
                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # для каждого найденного контакта
                    # есть break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # отбираем его телефоны
                        $arrCntPhones = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                        &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_PHONE_CSTFID == $arrCF['id']
                                ) {
                                    # это телефон
                                    if (
                                        isset ($arrCF['values'])
                                    &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                            &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntPhones[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                            in_array($strPhone, $arrCntPhones)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                                isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                    ! isset ($idContactExists)
                ) {
					//если контакт не найден
                    # пытаемся создать контакт
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
								'responsible_user_id' => $idNextResponsible,
								'tags' => 'Лид',
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strPhone,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_LGNt_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strLogin,
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                        ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                    # пытаемся создать компанию
					//$arrContUpdate[0]!="" - новый клиент
					//$arrContUpdate[1]=="1" - существующий клиент с тэгом Лид
					if( $arrContUpdate[0]!="" ) {
						$arrAmocrmCompaniesSetAdd = fncAmocrmCompaniesSet(
							//для нового клиента - создание компании с тэгом ЛИД
							AMOCRM_SUBDOMAIN,
							$strCookieFile,
							array (
								array (
									'name' => $strCmp_name,
									'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
									'responsible_user_id' => $idNextResponsible,	
									'tags' => 'Лид',									
									'custom_fields' => array (
										array (
											'id' => AMOCRM_COMPANY_INN_CSTFID,
											'values' => array (
												array (
													'value' => $strInn,
												),
											),
										),
										array (
											'id' => AMOCRM_COMPANY_FULLN_CSTFID,
											'values' => array (
												array (
													'value' => $strCmp_name_full,
												),
											),
										),
										array (
											'id' => AMOCRM_COMPANY_YURADDR_CSTFID,
											'values' => array (
												array (
													'value' => $strCmp_regplace,
												),
											),
										),
										array (
											'id' => AMOCRM_COMPANY_DFIO_CSTFID,
											'values' => array (
												array (
													'value' => $strCmp_fio,
												),
											),
										),
										array (
											'id' => AMOCRM_COMPANY_SITE_CSTFID,
											'values' => array (
												array (
													'value' => $strCmp_site,
												),
											),
										),
									),
								)
							),
							'add'
						);
					} else {
						//для НЕ нового клиента - создание компании без тэгов
						$arrAmocrmCompaniesSetAdd = fncAmocrmCompaniesSet(
							AMOCRM_SUBDOMAIN,
							$strCookieFile,
							array (
								array (
									'name' => $strCmp_name,
									'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
									'responsible_user_id' => $idNextResponsible,								
									'custom_fields' => array (
										array (
											'id' => AMOCRM_COMPANY_INN_CSTFID,
											'values' => array (
												array (
													'value' => $strInn,
												),
											),
										),
										array (
											'id' => AMOCRM_COMPANY_FULLN_CSTFID,
											'values' => array (
												array (
													'value' => $strCmp_name_full,
												),
											),
										),
										array (
											'id' => AMOCRM_COMPANY_YURADDR_CSTFID,
											'values' => array (
												array (
													'value' => $strCmp_regplace,
												),
											),
										),
										array (
											'id' => AMOCRM_COMPANY_DFIO_CSTFID,
											'values' => array (
												array (
													'value' => $strCmp_fio,
												),
											),
										),
										array (
											'id' => AMOCRM_COMPANY_SITE_CSTFID,
											'values' => array (
												array (
													'value' => $strCmp_site,
												),
											),
										),
									),
								)
							),
							'add'
						);
					}
                    

                    if (
                        ! $arrAmocrmCompaniesSetAdd['boolOk']
                    ) {
                        # $arrAmocrmCompaniesSetAdd['strErrDevelopUtf8']
                    } // if

                } // if для нового контакта
                else {
					//id контакта существует
                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # пытаемся обновить контакт
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                        ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                    # пытаемся создать примечание
					
						$arrAmocrmNotesCreate = fncAmocrmNotesCreate(
							AMOCRM_SUBDOMAIN,
							$strCookieFile,
							array (
								array (
									'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
									'element_type' => 2, # 2 == сделка									
									'note_type' => 4, # 4 == обычное примечание https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
									'text' => 'Зарегистрировался снова.',
								)
							)
						);
						
                    

                    if (
                        ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # else

                # пытаемся создать задачу
				//$arrContUpdate[0]!="" - новый клиент
				//$arrContUpdate[1]=="1" - существующий клиент с тэгом Лид
				
					$arrAmocrmTasksCreate = fncAmocrmTasksCreate(
						AMOCRM_SUBDOMAIN,
						$strCookieFile,
						array (
							array (
								'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id сделки
								'responsible_user_id' => $idNextResponsible,
								'tags' => 'Лид',
								'element_type' => 2, # 2 значит, что в element_id - сделка
								'task_type' => AMOCRM_TASKTYPECALL_ID,
								'text' => 'Обработать регистрацию с textiloptom.ru',
								'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
							),
						)
					);
				
                

                if (
                    ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

                if (
                    $strInn != ''
                ) {

                    # пытаемся создать примечание
					//$arrContUpdate[0]!="" - новый клиент
					//$arrContUpdate[1]=="1" - существующий клиент с тэгом Лид
				
						$arrAmocrmNotesCreate = fncAmocrmNotesCreate(
							AMOCRM_SUBDOMAIN,
							$strCookieFile,
							array (
								array (
									'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
									'element_type' => 2, # 2 == сделка																	
									'note_type' => 4, # 4 == обычное примечание https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
									'text' => 'ИНН: ' . $strInn,
								)
							)
						);
				

                    if (
                        ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # if

            } # else

        } # else

    } # else
	

} # function


//выполняет UPDATE сделки и контакта на ID случайного менеджера кроме менеджеров исключений
//входной параметр - id сделки
function fncAmocrmUpdateLeadContact($strLeadID,$arrIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strCookieFile) {	
	$strnewline = '
	================================
	';
	@file_put_contents(AMOCRM_LOG_FILE2,'Start fncAmocrmUpdateLeadContact'.$strnewline);
	 # пытаемся авторизироваться в amoCRM
	$flagneedupdate1612 = ""; 
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
		$idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);      	
		@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl1 - "." New idNextResponsible:".$idNextResponsible.$strnewline, FILE_APPEND);
		//mail("rsdim@rambler.ru","Subj hook started incl1","1:".$idNextResponsible); 
		// пытаемся из сделки получить данные о контакте
		$arrAmocrmContactsGet = fncAmocrmContactsGet(
			AMOCRM_SUBDOMAIN,
            $strCookieFile,
			$strLeadID
		);
		@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl2 - "."2:".implode("!",$arrAmocrmContactsGet).$strnewline, FILE_APPEND);
		//mail("rsdim@rambler.ru","Subj hook started incl2","2:".implode("!",$arrAmocrmContactsGet));
		if (
        ! $arrAmocrmContactsGet['boolOk']
		) {
			
		} # if
		else {			
			//получаем id контакта связанного с сделкой
			if (isset($arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'])) {
				$contactid1512 = $arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'];							
				$strcontactid512 = "".$contactid1512;
				if ($strcontactid512!="") {
					@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 - "."3:".json_encode($arrAmocrmContactsGet).$strnewline, FILE_APPEND);
					//mail("rsdim@rambler.ru","Subj hook started incl3 yes contact","3:".json_encode($arrAmocrmContactsGet));
					//получаем контакт по id контакта
					$arrAmocrmContactsListById = fncAmocrmContactsListById(
						AMOCRM_SUBDOMAIN,
						$strCookieFile,
						$strcontactid512
					);
					if (
						! $arrAmocrmContactsListById['boolOk']
					) {
				
					} # if
					else {
						//получаем из контакта ответственного за контакт
						//mail("rsdim@rambler.ru","Subj hook started incl4 no contact","4: contactID: ".$strcontactid512." json - ".json_encode($arrAmocrmContactsListById));
						$strcontactRespId = "".$arrAmocrmContactsListById['arrResponse']['contacts'][0]['responsible_user_id'];
						@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.0 - "."From contact id:".$strcontactid512." responsible_user_id = ".$strcontactRespId.$strnewline, FILE_APPEND);
						
						if ($strcontactRespId!="") {							
							if	(in_array($strcontactRespId,$arrAmocrmIdsResponsibleDisable)) {
								//mail("rsdim@rambler.ru","Subj hook started incl5.1","5.1");
								@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.1"."5.1".$strnewline, FILE_APPEND);
								//апдейтим ответственного в контакте если нужно
								$arrAmocrmContactsSetUpdate1512 = fncAmocrmContactsSet(
									AMOCRM_SUBDOMAIN,
									$strCookieFile,
									array (
										array (
											'id' => $contactid1512,
											'responsible_user_id' => $idNextResponsible,
											'last_modified' => time(),
										)
									),
									'update'
								);
								$flagneedupdate1612 = "1"; 
							} else {
								//mail("rsdim@rambler.ru","Subj hook started incl5.2","5.2");
								@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.2 "."5.2".$strnewline, FILE_APPEND);
								//берем из контакта ответственного для апдейта в сделке
								$idNextResponsible = $strcontactRespId;
								$flagneedupdate1612 = "1"; 
							}
						} else {
							@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.3"."5.3".$strnewline, FILE_APPEND);
							//mail("rsdim@rambler.ru","Subj hook started incl5.3","5.3");							
							// idNextResponsible - не меняется т.е. в контакте ерунда							
						}						
					}
				} else {
					//если сделка создана без контакта то и делать с ним ничего не нужно
					//mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
					@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 no contact"."3:".implode("!",$contactid1512).$strnewline, FILE_APPEND);
				}
				
			} else {
				//если сделка создана без контакта то и делать с ним ничего не нужно
				//mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
				@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 no contact"."3:".implode("!",$contactid1512).$strnewline, FILE_APPEND);
			}
				
		}
		if ($flagneedupdate1612 == "1") {
			# пытаемся update сделку
			$arrAmocrmLeadsUpdate = fncAmocrmLeadsUpdate(
				AMOCRM_SUBDOMAIN,
				$strCookieFile,
				array (
					array (
						'id' => $strLeadID,
						'last_modified' => time(),
						'responsible_user_id' => $idNextResponsible,
					)
				)
			);
			@file_put_contents(AMOCRM_LOG_FILE2,"Lead - updated!".$strnewline, FILE_APPEND);
		} else {
			@file_put_contents(AMOCRM_LOG_FILE2,"Lead - not need update!".$strnewline, FILE_APPEND);
		}
		
		//mail("rsdim@rambler.ru","Subj hook started incl10 leadsupdate","idNextResponsible:".$idNextResponsible."  10:".json_encode($arrAmocrmLeadsUpdate));
		@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl10 leadsupdate - "."idNextResponsible:".$idNextResponsible."  10:".json_encode($arrAmocrmLeadsUpdate).$strnewline, FILE_APPEND);
	}	
	return $strLeadID;
}

//выполняет поиск и UPDATE контакта  - тэг Лид заменяется на Клиент
//входной параметр - id сделки
function fncAmocrmUpdateContactTag($strLeadID,$arrIdsResponsible,$strCookieFile) {	
	$strnewline = '
	================================
	';
	@file_put_contents(AMOCRM_LOG_FILE,'Start fncAmocrmUpdateContactTag'.$strnewline);
	
	 # пытаемся авторизироваться в amoCRM
	$flagneedupdate1612 = ""; 
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
		$idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath); 
		@file_put_contents(AMOCRM_LOG_FILE,"Subj hook started hook1612 incl1:"."1:".$idNextResponsible.$strnewline,FILE_APPEND);
		
		// пытаемся из сделки получить данные о контакте
		$arrAmocrmContactsGet = fncAmocrmContactsGet(
			AMOCRM_SUBDOMAIN,
            $strCookieFile,
			$strLeadID
		);
		
		if (
        ! $arrAmocrmContactsGet['boolOk']
		) {
			
		} # if
		else {			
			//получаем id контакта связанного с сделкой
			if (isset($arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'])) {
				$contactid1512 = $arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'];							
				$strcontactid512 = "".$contactid1512;
				if ($strcontactid512!="") {
					@file_put_contents(AMOCRM_LOG_FILE,"Subj hook started Lid-Klient 3.1. Contactid: ".$strcontactid512."  3:".json_encode($arrAmocrmContactsGet).$strnewline,FILE_APPEND);
					//получаем контакт по id контакта
					$arrAmocrmContactsListById = fncAmocrmContactsListById(
						AMOCRM_SUBDOMAIN,
						$strCookieFile,
						$strcontactid512
					);
					@file_put_contents(AMOCRM_LOG_FILE,"Subj hook started Lid-Klient 3.2"."Contactid: ".$strcontactid512."  3:".json_encode($arrAmocrmContactsListById).$strnewline,FILE_APPEND);
					
					if (
						! $arrAmocrmContactsListById['boolOk']
					) {
				
					} # if
					else {
						//получаем из контакта массив тэгов						
						$arrcontactTag = $arrAmocrmContactsListById['arrResponse']['contacts'][0]['tags'];
						@file_put_contents(AMOCRM_LOG_FILE, "Subj hook started Lid-Klient 4.0 - "."4: json: ".json_encode($arrcontactTag).$strnewline,FILE_APPEND);						
						$strnewtags = "";
						$flagnewtags = "";
						$flagnewtags2 = "";
						foreach($arrcontactTag as $arkey => $arrtags1612) {
							foreach($arrtags1612 as $arkey1612 => $val16120) {
								$strnewtags .= "# key2:".$arkey1612." - ".$val16120."#";								
									if($arkey1612=="name") {
										$strtagsearch = "".$val16120;
									if ( $strtagsearch === "Лид" ) {
										$strnewtags2 .= "Клиент,";
										$flagnewtags = "1";
									} elseif($strtagsearch === "Клиент") {
										$flagnewtags2 = "1";
									} else 
									{
										$strnewtags2 .= "".$val16120.",";
									}
								}								
							}
						}
						if (($flagnewtags2 === "") and ($flagnewtags === "")) {
							$strnewtags2 .= "Клиент";
						}
						@file_put_contents(AMOCRM_LOG_FILE,$strnewtags." tags2 = ".$strnewtags2.$strnewline,FILE_APPEND);
						if ($flagnewtags == "1") {							
								@file_put_contents(AMOCRM_LOG_FILE, "Subj hook started Lid-Klient 4.2"."4: contactID: ".$strcontactid512." json - ".json_encode($arrAmocrmContactsListById).$strnewline,FILE_APPEND);
								//mail("rsdim@rambler.ru","Subj hook started incl5.1","5.1");
								//апдейтим ответственного в контакте если нужно
								$arrAmocrmContactsSetUpdate1512 = fncAmocrmContactsSet(
									AMOCRM_SUBDOMAIN,
									$strCookieFile,
									array (
										array (
											'id' => $contactid1512,
											'tags' => $strnewtags2,
											'last_modified' => time(),
										)
									),
									'update'
								);
						} else {
							@file_put_contents(AMOCRM_LOG_FILE, "Subj hook started Lid-Klient 4.3"."4: contactID: ".$strcontactid512." tags - ".$strnewtags.$strnewline,FILE_APPEND);
							
							//mail("rsdim@rambler.ru","Subj hook started incl5.3","5.3");							
							// idNextResponsible - не меняется т.е. в контакте ерунда							
						}						
					}
				} else {
					//если сделка создана без контакта то и делать с ним ничего не нужно
					//mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
				}
				
			} else {
				//если сделка создана без контакта то и делать с ним ничего не нужно
				//mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
			}
				
		}		
		
		//mail("rsdim@rambler.ru","Subj hook started incl10 leadsupdate","idNextResponsible:".$idNextResponsible."  10:".json_encode($arrAmocrmLeadsUpdate));
	}	
	return $strLeadID;
}

function fncAmocrmUpdateAllContacts($arrIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strCookieFile) {	
	$countUpdated = 0; //число update контактов
	$strnewline = '
	================================
	';
	@file_put_contents(AMOCRM_LOG_FILE,'Start fncAmocrmUpdateAllContacts');
	
	 # пытаемся авторизироваться в amoCRM
	$flagneedupdate1612 = ""; 
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
		$idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
		//fncAmocrmContactsListByResponsibleID
		//3й параметр - id сотрудника для которого нужно получить контакты
		$arrAmocrmContactsListByResponsibleID = fncAmocrmContactsListByResponsibleID(
			AMOCRM_SUBDOMAIN,
			$strCookieFile,
			'628743'
		);
		if (
			! $arrAmocrmContactsListByResponsibleID['boolOk']
		) {
		
		} # if
		else {
			//получаем из контакта массив тэгов						
			$arrcontactTag = $arrAmocrmContactsListByResponsibleID['arrResponse']['contacts'];
			$countUpdated = count($arrcontactTag);
		}	
	}
	
	return $countUpdated;
	//return $idNextResponsible;
}	
function fncAmocrmCheckTag($strLeadTag,$strLeadId,$arrIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strCookieFile) {	
	$countUpdated = 0; //число update контактов
	$strnewline = '
	================================
	';
	@file_put_contents(AMOCRM_LOG_FILE,'Start fncAmocrmCheckTag'.$strnewline);
	
	 # пытаемся авторизироваться в amoCRM
	
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
		$idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
		
		$arrLeadData = fncAmocrmLeadsGetById(
			AMOCRM_SUBDOMAIN,
			$strCookieFile,
			$strLeadId
		);
		
		 if (
			! $arrLeadData['boolOk']
		) {
			
		} # if
		else {
			$arrTags = $arrLeadData['arrResponse']['leads'][0]['tags'];
			@file_put_contents(AMOCRM_LOG_FILE,"Ищем тэг:".$strLeadTag.$strnewline,FILE_APPEND);
			foreach($arrTags as $subarrtag) {
				if($subarrtag['name']==$strLeadTag) {					
					//нужный тэг найден
					$countUpdated = 1;
				} else {
					@file_put_contents(AMOCRM_LOG_FILE,"tags:".$subarrtag['name'].$strnewline,FILE_APPEND);
				}
			}			
		}
	}
	
	return $countUpdated;
	//return $idNextResponsible;
}	

//выполняет UPDATE сделки и контакта на ID случайного менеджера кроме менеджеров исключений
//входной параметр strLeadID - id сделки, strManagerId - id того менеджера который должен быть ответсвенным за сделку и контакт
function fncAmocrmUpdateLeadContactTo($strLeadID,$strManagerId,$arrAmocrmIdsResponsibleDisable,$strCookieFile) {	
	$strnewline = '
	================================
	';
	@file_put_contents(AMOCRM_LOG_FILE2,'Start fncAmocrmUpdateLeadContactTo'.$strnewline);
	 # пытаемся авторизироваться в amoCRM
	$flagneedupdate1612 = ""; 
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
        ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
		$idNextResponsible = $strManagerId; 
		@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl1 - "." New idNextResponsible:".$idNextResponsible.$strnewline, FILE_APPEND);
		//mail("rsdim@rambler.ru","Subj hook started incl1","1:".$idNextResponsible); 
		// пытаемся из сделки получить данные о контакте
		$arrAmocrmContactsGet = fncAmocrmContactsGet(
			AMOCRM_SUBDOMAIN,
            $strCookieFile,
			$strLeadID
		);
		@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl2 - "."2:".implode("!",$arrAmocrmContactsGet).$strnewline, FILE_APPEND);
		//mail("rsdim@rambler.ru","Subj hook started incl2","2:".implode("!",$arrAmocrmContactsGet));
		if (
        ! $arrAmocrmContactsGet['boolOk']
		) {
			
		} # if
		else {			
			//получаем id контакта связанного с сделкой
			if (isset($arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'])) {
				$contactid1512 = $arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'];							
				$strcontactid512 = "".$contactid1512;
				if ($strcontactid512!="") {
					@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 - "."3:".json_encode($arrAmocrmContactsGet).$strnewline, FILE_APPEND);
					//mail("rsdim@rambler.ru","Subj hook started incl3 yes contact","3:".json_encode($arrAmocrmContactsGet));
					//получаем контакт по id контакта
					$arrAmocrmContactsListById = fncAmocrmContactsListById(
						AMOCRM_SUBDOMAIN,
						$strCookieFile,
						$strcontactid512
					);
					if (
						! $arrAmocrmContactsListById['boolOk']
					) {
				
					} # if
					else {
						//получаем из контакта ответственного за контакт
						//mail("rsdim@rambler.ru","Subj hook started incl4 no contact","4: contactID: ".$strcontactid512." json - ".json_encode($arrAmocrmContactsListById));
						$strcontactRespId = "".$arrAmocrmContactsListById['arrResponse']['contacts'][0]['responsible_user_id'];
						@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.0 - "."From contact id:".$strcontactid512." responsible_user_id = ".$strcontactRespId.$strnewline, FILE_APPEND);
						
						if ($strcontactRespId!="") {			
							//если ответсвенный в контакте не наш менеджер - то нужно апдейтить ответственного в контакте
							if	($strcontactRespId!=$idNextResponsible) {
								//mail("rsdim@rambler.ru","Subj hook started incl5.1","5.1");
								@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.1"."5.1".$strnewline, FILE_APPEND);
								//апдейтим ответственного в контакте если нужно
								$arrAmocrmContactsSetUpdate1512 = fncAmocrmContactsSet(
									AMOCRM_SUBDOMAIN,
									$strCookieFile,
									array (
										array (
											'id' => $contactid1512,
											'responsible_user_id' => $idNextResponsible,
											'last_modified' => time(),
										)
									),
									'update'
								);
								$flagneedupdate1612 = "1"; 
							} else {
								//если ответственный наш менеджер - то контанкт апдейтить не нужно
							}
						} else {
							@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.3"."5.3".$strnewline, FILE_APPEND);
							//mail("rsdim@rambler.ru","Subj hook started incl5.3","5.3");							
							// idNextResponsible - не меняется т.е. в контакте ерунда							
						}						
					}
				} else {
					//если сделка создана без контакта то и делать с ним ничего не нужно
					//mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
					@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 no contact"."3:".implode("!",$contactid1512).$strnewline, FILE_APPEND);
				}
				
			} else {
				//если сделка создана без контакта то и делать с ним ничего не нужно
				//mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
				@file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 no contact"."3:".implode("!",$contactid1512).$strnewline, FILE_APPEND);
			}
				
		}
		
		// пытаемся update сделку в любом случае 
		$arrAmocrmLeadsUpdate = fncAmocrmLeadsUpdate(
			AMOCRM_SUBDOMAIN,
			$strCookieFile,
			array (
				array (
					'id' => $strLeadID,
					'last_modified' => time(),
					'responsible_user_id' => $idNextResponsible,
				)
			)
		);
		@file_put_contents(AMOCRM_LOG_FILE2,"Lead - updated!".$strnewline, FILE_APPEND);		
		
	}	
	return $strLeadID;
}
?>