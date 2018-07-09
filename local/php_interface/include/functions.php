<?php

function dmp($var, $die = false)
{
    echo "<pre style='font-family: Verdana; background: black; color: white; white-space: pre-wrap; position: relative; z-index: 1000;'>";
    var_dump($var);
    echo "</pre>";
    if ($die) {
        die();
    }
}

function getSiteInfo($siteId = SITE_ID)
{
    $key = "getSiteInfo" . $siteId;
    $result = cache($key, 1440, function () use ($siteId) {
        return \CSite::GetByID($siteId)->Fetch();
    });

    return $result;
}

function getCitiesList()
{
    $rawResult = ShopsModel::select('PROPERTY_CITY')->filter(['ACTIVE' => 'Y'])
        ->cache(30)
        ->sort('NAME')->getList()->toArray();
    $arCities = array_column($rawResult, 'PROPERTY_CITY_VALUE');
    asort($arCities);

    return array_values(array_unique($arCities));
}

function axi_mb_ucfirst($str, $addText = null)
{
    if (!$str) return false;

    $str = mb_strtolower($str);
    $fc = mb_strtoupper(mb_substr($str, 0, 1));

    $result = $fc . mb_substr($str, 1);
    if ($addText) $result .= ' ' . $addText;

    return $result;
}


function startsWith($haystack, $needle)
{
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

function GetRelativePath($sPath)
{
    return substr_count($sPath, $_SERVER["DOCUMENT_ROOT"]) ? str_replace($_SERVER["DOCUMENT_ROOT"], "", $sPath) : $sPath;
}

function ruDate()
{
    $translation = array(
        "am" => "дп",
        "pm" => "пп",
        "AM" => "ДП",
        "PM" => "ПП",
        "Monday" => "Понедельник",
        "Mon" => "Пн",
        "Tuesday" => "Вторник",
        "Tue" => "Вт",
        "Wednesday" => "Среда",
        "Wed" => "Ср",
        "Thursday" => "Четверг",
        "Thu" => "Чт",
        "Friday" => "Пятница",
        "Fri" => "Пт",
        "Saturday" => "Суббота",
        "Sat" => "Сб",
        "Sunday" => "Воскресенье",
        "Sun" => "Вс",
        "January" => "Января",
        "Jan" => "Янв",
        "February" => "Февраля",
        "Feb" => "Фев",
        "March" => "Марта",
        "Mar" => "Мар",
        "April" => "Апреля",
        "Apr" => "Апр",
        "May" => "Мая",
        "May" => "Мая",
        "June" => "Июня",
        "Jun" => "Июн",
        "July" => "Июля",
        "Jul" => "Июл",
        "August" => "Августа",
        "Aug" => "Авг",
        "September" => "Сентября",
        "Sep" => "Сен",
        "October" => "Октября",
        "Oct" => "Окт",
        "November" => "Ноября",
        "Nov" => "Ноя",
        "December" => "Декабря",
        "Dec" => "Дек",
        "st" => "ое",
        "nd" => "ое",
        "rd" => "е",
        "th" => "ое",
        "01" => 'Января',
        "02" => 'Февраля',
        "03" => 'Марта',
        "04" => 'Апреля',
        "05" => 'Мая',
        "06" => 'Июня',
        "07" => 'Июля',
        "08" => 'Августа',
        "09" => 'Сентября',
        "10" => 'Октября',
        "11" => 'Ноября',
        "12" => 'Декабря',
    );
    if (func_num_args() > 1) {
        $timestamp = func_get_arg(1);
        return strtr(date(func_get_arg(0), $timestamp), $translation);
    } else {
        return strtr(date(func_get_arg(0)), $translation);
    }
}

/**
 * @param mixed $value
 * @param array $texts array("день", "дня", "дней")
 * @return string
 */
function wordPlural($value, $texts)
{
    $value = intval($value);

    if ($value % 10 === 1 && ($value < 10 || $value > 20)) return $texts[0];
    if (($value % 10 === 2 || $value % 10 === 3 || $value % 10 === 4) && ($value < 10 || $value > 20)) return $texts[1];
    return $texts[2];
}

/**
 * Определяет юзерагент
 * @return string
 */
function getUA()
{
    $keyname_ua_arr = array(
        'HTTP_X_ORIGINAL_USER_AGENT',
        'HTTP_X_DEVICE_USER_AGENT',
        'HTTP_X_OPERAMINI_PHONE_UA',
        'HTTP_X_BOLT_PHONE_UA',
        'HTTP_X_MOBILE_UA',
        'HTTP_USER_AGENT');
    foreach ($keyname_ua_arr as $keyname_ua) {
        if (!empty($_SERVER[$keyname_ua])) {
            return $_SERVER[$keyname_ua];
        }
    }
    return 'Unknown';
}

function isBot()
{
    $user_agent = getUA();
    $mobile_agents = array("bot", "spider", "archiver", "php", "python", "perl", "wordpress", "crawl", "vkexport");
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            return true;
        }
    }
    return false;
}

function is_iphone()
{
    $user_agent = getUA();
    $mobile_agents = array("iphone", "ipad", "ipod");
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            return true;
        }
    }
    return false;
}

function is_winphone()
{
    $user_agent = getUA();
    $mobile_agents = array("Windows Phone", "IEMobile", "Edge/1");
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            return true;
        }
    }
    return false;
}

function is_android()
{
    $user_agent = getUA();
    $mobile_agents = array("android");
    foreach ($mobile_agents as $device) {
        if (stristr($user_agent, $device)) {
            return true;
        }
    }
    return false;
}

function getViewportMetaTag()
{
    if (is_iphone()) {
        return '<meta name="viewport" content="width=1024" />' . PHP_EOL;
    }

    if (is_winphone()) {
        return;
    }

    if (is_android()) {
        return '<meta name="viewport" content="width=100%" />' . PHP_EOL;
    }

    return '<meta name="viewport" content="width=device-width">' . PHP_EOL;
}

function download($file, $fileName)
{
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Определим IE
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
        $fileName = rawurlencode($fileName);
    }
    $fileName = '"' . $fileName . '"';

    // заставляем браузер показать окно сохранения файла
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream;');
    header('Content-Disposition: attachment; filename=' . $fileName);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));

    // читаем файл и отправляем его пользователю
    readfile($file);
    exit;
}

function json_result($success, $result = null)
{
    die(json_encode(array('success' => $success, 'result' => $result)));
}

function NormalizeLink($sURL = '')
{
    $sNewURL = preg_replace('#\/{2,}#', '/', $sURL);

    return $sNewURL;
}

function isPost($action = false)
{
    if ($action === false) {
        return \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isPost();
    } elseif ($action === true) {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        return $request->isPost() && $request->getPost("AJAX") == "Y";
    } else {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        return $request->isPost() && $request->getPost("AJAX") == "Y" && $request->getPost("ACTION") == $action;
    }
}

function isEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function getPhoneFromString($string, $oneResult = false)
{
    //сперва вырежем из строки все кроме цифр
    $string = preg_replace("/[^0-9]/", "", $string);

    //убираем первую цифру
    if (strlen($string) == 11) {
        $string = substr($string, 1);
    }

    $string = intval($string);

    if ($string > 0 && strlen($string) == 10) {
        //+7 (923) 666-55-44
        $phone1 = "+7 ("
            . substr($string, 0, 3) . ") "
            . substr($string, 3, 3) . "-"
            . substr($string, 6, 2) . "-"
            . substr($string, 8, 2);

        //79236665544
        $phone2 = "7" . $string;

        //89236665544
        $phone3 = "8" . $string;

        return $oneResult ? $phone1 : array($phone1, $phone2, $phone3);
    }

    return false;
}