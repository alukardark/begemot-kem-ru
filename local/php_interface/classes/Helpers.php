<?php

namespace Axi;

class Helpers
{
    private static $sIncludePath = "_include/";
    private static $sTextsPath = "_text/";
    private static $sSvgPath = "_svg/";
    private static $_instance, $curSection, $curSubSection, $sectionName, $curAlias;

    public static function getInstance()
    {
        if (!is_object(self::$_instance)) {
            self::$_instance = new self;
            self::init();
        }
        return self::$_instance;
    }

    private static function setAlias()
    {
        global $APPLICATION;

        $curPage = $APPLICATION->GetCurPage(true);
        switch ($curPage) {
            case $curPage == "/" || $curPage == "/index.php":
                self::$curAlias = "indexpage";
                break;
            default:
                self::$curAlias = "innerpage";
                break;
        }
    }

    public function getAlias()
    {
        return self::$curAlias;
    }

    private static function setSection()
    {
        global $APPLICATION;

        $sSectionName = null;
        $curSection = $curSubSection = $APPLICATION->GetCurDir();
        include($_SERVER["DOCUMENT_ROOT"] . $curSection . '.section.php');
        self::$sectionName = $sSectionName;

        self::$curSection = preg_replace('/\/([0-9A-Za-z?-]*)\/(.*)/', '$1', $curSection);
        self::$curSubSection = preg_replace('/\/([0-9A-Za-z?-]*)\/([0-9A-Za-z?-]*)\/(.*)/', '$2', $curSubSection);
    }

    public function getSection($sub = false)
    {
        if ($sub == 'sub') {
            return self::$curSubSection;
        } else {
            return self::$curSection;
        }
    }

    public function getSectionName()
    {
        return self::$sectionName;
    }

    /**
     * Выводит $filename из папки $sIncludePath
     * @global $APPLICATION
     * @param string $filename Имя файла без расширения (возможно с указанием папки)
     * @param string $sTitle Тайтл кнопки
     * @param bool $bHideIcons запретить/разрешить редактирование в режиме правки
     * @param string $sEditMode html|text|php
     * @param string $sExtension Расширение файла
     * @param bool $bCache
     */
    public static function GF($filename, $sTitle = "Редактирование включаемой области раздела", $bHideIcons = true, $sEditMode = "html", $sExtension = ".php", $bCache = true)
    {
        global $APPLICATION;

        if ($bCache) {
            $APPLICATION->IncludeFile(SITE_DIR . self::$sIncludePath . $filename . $sExtension, Array(), Array(
                "MODE" => $sEditMode,
                "NAME" => $sTitle,
                "SHOW_BORDER" => !$bHideIcons
            ));
        } else {
            $APPLICATION->IncludeComponent("bitrix:main.include", "", array(
                "AREA_FILE_SHOW" => "file",
                "EDIT_MODE" => $sEditMode,
                "NAME" => $sTitle,
                "PATH" => SITE_DIR . self::$sIncludePath . $filename . $sExtension,
            ), false, array("HIDE_ICONS" => $bHideIcons ? "Y" : "N"));
        }
    }

    /**
     * @see Axi::GF()
     */
    public static function GT($filename, $sTitle = "текстовую область", $bHideIcons = false, $sEditMode = "html")
    {
        self::GF(self::$sTextsPath . $filename, $sTitle, $bHideIcons, $sEditMode, ".php");
    }

    /**
     * @see Axi::GF()
     */
    public static function GSVG($filename, $sTitle = "SVG-файл")
    {
        self::GF(self::$sSvgPath . $filename, $sTitle, true, "text", ".svg");
    }

    /**
     * Обертка для плагина "Настройки проекта" (https://marketplace.1c-bitrix.ru/solutions/ws.projectsettings/)
     * @param mixed $code символьный код элемента настройки
     * @return string значение элемента настройки
     */
    public static function GS($code)
    {
        if (class_exists('WS_PSettings')) {
            $result = \WS_PSettings::getFieldValue($code, false);
        } else {
            $result = 'Для использования этого метода установите модуль ws.projectsettings';
        }
        return $result;
    }

    private static function init()
    {
        self::setAlias();
        self::setSection();
    }

    public static function SetGlobalCity()
    {
        global $CURRENT_CITY;
        $defaultCity = 'Кемерово';

        $currentCityCookie = $_COOKIE['CURRENT_CITY'];
        if (!$currentCityCookie) {
            $geo = new \Geo(['ip' => '46.149.225.204']);
            $city = $geo->get_value('city');

            if (!in_array($city, CITIES_LIST)) {
                $city = $defaultCity;
            }

            $CURRENT_CITY = $city;
            setcookie('CURRENT_CITY', $CURRENT_CITY, time() + 60 * 60 * 24, '/');
        } else {
            $CURRENT_CITY = $currentCityCookie;
        }
    }


    public static function isDomainAvailible($url)
    {
        $agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $page = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode >= 200 && $httpcode < 400)
            return true;
        else
            return false;
    }

}