<?php

class CUserExt
{

    public static function getBonuses($bPrint = false)
    {
        $bonuses = 2500;

        if ($bPrint)
        {
            $bonuses .= " " . wordPlural($bonuses, array("бонус", "бонуса", "бонусов"));
        }

        return $bonuses;
    }

    public static function getCard()
    {
        $card = "VK 1234567890";

        return $card;
    }

    /**
     * Получаем профили текущего юзера
     * @global type $USER
     * @return type
     */
    public static function getProfile()
    {
        global $USER;

        $USER_ID = $USER->GetID();

        $USER_PROFILES = array();

        $obListProps = \CSaleOrderUserProps::GetList(array("DATE_UPDATE" => "DESC"), array("USER_ID" => $USER_ID));
        while ($arFetch     = $obListProps->Fetch())
        {
            $USER_PROFILES[] = $arFetch;
            break;
        }

        foreach ($USER_PROFILES as &$USER_PROFILE)
        {
            $obListPropsValue = \CSaleOrderUserPropsValue::GetList(array(), Array("USER_PROPS_ID" => $USER_PROFILE["ID"]));
            while ($arPropVals       = $obListPropsValue->Fetch())
            {
                $USER_PROFILE["PROPS_VALUE"][$arPropVals["PROP_CODE"]] = $arPropVals;
            }
        }

        return $USER_PROFILES[0];
    }

    public static function getName($USER_ID = null, $short = false)
    {
        if ($USER_ID === null)
        {
            global $USER;
            $USER_ID = $USER->GetID();
        }

        if (empty($USER_ID))
        {
            return false;
        }

        $name = $USER->GetFullName();

        if (empty($name))
        {
            $name = $USER->GetLogin();
        }

        if ($short)
        {
            $arName = explode(" ", $name);
            $name   = $arName[0];
        }

        return $name;
    }

    /**
     * Возвращает телефон пользователя. Если телефон не установлен, то возвращает 
     * телефон из последнего профиля покупателя, иначе false
     * @return type
     */
    public static function getPhone($USER_ID = null)
    {
        if ($USER_ID === null)
        {
            global $USER;
            $USER_ID = $USER->GetID();
        }

        if (empty($USER_ID))
        {
            return false;
        }

        $obUser = \CUser::GetByID($USER_ID);

        if (empty($obUser))
        {
            return false;
        }

        $arUser = $obUser->Fetch();

        if (!empty($arUser['PERSONAL_PHONE']))
        {
            return fixPhoneNumber($arUser['PERSONAL_PHONE']);
        }

        $USER_PROFILE = self::getProfile();

        if (empty($USER_PROFILE) || empty($USER_PROFILE["PROPS_VALUE"]["PHONE"]["VALUE"]))
        {
            return false;
        }

        return fixPhoneNumber($USER_PROFILE["PROPS_VALUE"]["PHONE"]["VALUE"]);
    }

    public static function getUserProps($PERSON_TYPE_ID)
    {
        $arFullProps = array();

        $arFilter = array(
            "ACTIVE"         => "Y",
            "PERSON_TYPE_ID" => $PERSON_TYPE_ID,
            "!CODE"          => array("PREPAY", "CITY", "NOTIFICATION")
        );

        $obLists = \CSaleOrderProps::GetList(array("SORT" => "ASC"), $arFilter, false, false, array("*"));
        while ($arFetch = $obLists->Fetch())
        {
            //if (in_array($arFetch['CODE'], array("PREPAY"))) continue;
            $arFullProps[$arFetch['CODE']] = array(
                "ID"          => $arFetch['ID'],
                "NAME"        => $arFetch['CODE'],
                "FIELD_TYPE"  => $arFetch['CODE'] == "SUBSCRIBE" ? "hidden" : "text",
                "REQUIRED"    => $arFetch['CODE'] == "SUBSCRIBE" ? "N" : "Y",
                "CAPTION"     => $arFetch['NAME'],
                "VALUE"       => $arFetch['CODE'] == "SUBSCRIBE" ? "EMAIL;" : "",
                "DESCRIPTION" => "",
            );

            $db_vars = \CSaleOrderPropsVariant::GetList(($by      = "SORT"), ($order   = "ASC"), Array("ORDER_PROPS_ID" => $arFetch["ID"]));
            while ($vars    = $db_vars->Fetch())
            {
                $arFullProps[$arFetch['CODE']]['OPTIONS'][] = $vars;
            }
        }

        return $arFullProps;
    }

    /**
     * @see https://dev.1c-bitrix.ru/user_help/store/sale/components_2/order/sale_order_ajax.php
     * Если пользователь не зарегистрирован на сайте, то при отмеченной опции он будет автоматически
     * зарегистрирован для оформления заказа.
     * Если флаг с данной опции снят, то при оформлении заказа будет отображена форма регистрации и незарегистрированный пользователь
     * должен будет зарегистрироваться самостоятельно.
     * Поле работает при условии, что в ядре включена самостоятельная регистрация 
     * и отключено подтверждение регистрации по E-mail (поле ниже).
     * @global type $USER
     * @param type $VALUES
     * @return type
     */
    public static function register($VALUES)
    {
        global $USER;

        if ($USER->IsAuthorized())
        {
            return array('success' => false, 'message' => '<p>Вы ведь уже вошли! :)</p>');
        }

        $PERSON_TYPE_ID = (int) $VALUES['PERSON_TYPE_ID'];

        if ($PERSON_TYPE_ID != FIZ_LICO && $PERSON_TYPE_ID != UR_LICO)
        {
            return array('success' => false, 'message' => '<p>Неверный тип плательщика</p>');
        }

        $PROPS = self::getUserProps($PERSON_TYPE_ID);

        $QUESTIONS = $PROPS + array(
            "PASSWORD"  => array(
                "NAME"        => "PASSWORD",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "Y",
                "CAPTION"     => "Пароль",
                "VALUE"       => "",
                "DESCRIPTION" => "",
            ),
            "PASSWORD2" => array(
                "NAME"        => "PASSWORD2",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "Y",
                "CAPTION"     => "Повторите пароль",
                "VALUE"       => "",
                "DESCRIPTION" => "",
            ),
        );

        $errors = false;
        //быстрая проверка на заполненность обязательных полей
        foreach ($QUESTIONS as $code => $question)
        {
            $value = trim($VALUES[$code]);
            if ($question['REQUIRED'] == "Y" && empty($value))
            {
                $errors .= '<p>Поле ' . $question['CAPTION'] . ' должно быть заполнено</p>';
            }
        }

        if (!empty($errors))
        {
            return array('success' => false, 'message' => $errors);
        }

        //поля, обязательные для любого типа плательщика
        $sUserName  = trim($VALUES['FIO']);
        $sPhone     = trim($VALUES['PHONE']);
        $sUserEmail = trim($VALUES['EMAIL']);
        $sPassword  = $VALUES['PASSWORD'];
        $sPassword2 = $VALUES['PASSWORD2'];

        if ($sPassword != $sPassword2)
        {
            $errors .= '<p>пароли не совпадают</p>';
        }

        if (!self::isUniquePhone($sPhone))
        {
            $errors .= '<p>Телефон ' . $sPhone . ' уже зарегистрирован</p>';
        }

        if (!self::isUniqueEmail($sUserEmail))
        {
            $errors .= '<p>E-mail ' . $sUserEmail . ' уже зарегистрирован</p>';
        }

        if (!empty($errors))
        {
            return array('success' => false, 'message' => $errors);
        }


        $regResult = $USER->Register($sUserEmail, $sUserName, "", $sPassword, $sPassword2, $sUserEmail);
        $USER_ID   = $USER->GetID();

        if (empty($USER_ID))
        {
            return array('success' => false, 'message' => '<p>' . $regResult['MESSAGE'] . '</p>');
        }

        $arUserFields = array(
            "NAME"           => $sUserName,
            "EMAIL"          => $sUserEmail,
            "PERSONAL_PHONE" => $sPhone,
        );

        if (!empty($VALUES['UF']))
        {
            foreach ($VALUES['UF'] as $UF_KEY => $UF_VALUE)
            {
                $arUserFields[$UF_KEY] = $UF_VALUE;
            }
        }
        $USER->Update($USER_ID, $arUserFields);

        //создаем профиль покупателя
        $arFields      = array(
            "NAME"           => $sUserName,
            "USER_ID"        => $USER_ID,
            "PERSON_TYPE_ID" => $PERSON_TYPE_ID
        );
        $USER_PROPS_ID = \CSaleOrderUserProps::Add($arFields);

        //свойства заказа для созданног опрофиля покупателя
        $arProfileFields = array();
        foreach ($PROPS as $code => $prop)
        {
            $arProfileFields[$prop['ID']] = $VALUES[$code];
        }
        \CSaleOrderUserProps::DoSaveUserProfile($USER_ID, $USER_PROPS_ID, $sUserName, $PERSON_TYPE_ID, $arProfileFields, $arResult);

        return array('success' => true);
    }

    public static function auth($VALUES)
    {
        global $APPLICATION, $USER;

        if ($USER->GetID())
        {
            return array('success' => false, 'message' => '<p>Вы ведь уже вошли! :)</p>');
        }

        $LOGIN    = trim($VALUES['LOGIN']);
        $PASSWORD = $VALUES['PASSWORD'];

        if (empty($LOGIN) || empty($PASSWORD))
        {
            return array('success' => false, 'message' => '<p>Все поля должны быть заполнены</p>');
        }

        $arUsers = array();
        $arUsers += self::getByPhone(getPhoneFromString($LOGIN));
        $arUsers += self::getByEmail(array($LOGIN));

        //printra($arUsers);

        if (empty($arUsers))
        {
            return array('success' => false, 'message' => '<p>Введенные данные не найдены</p>');
        }

        $arAuthResult = false;
        foreach ($arUsers as $arUser)
        {
            $arAuthResult = $USER->Login($arUser['LOGIN'], $PASSWORD, "Y", "Y");

            if ($arAuthResult === true)
            {
                break;
            }
        }

        $APPLICATION->arAuthResult = $arAuthResult;

        if ($arAuthResult === true) return array('success' => true);
        else return array('success' => false, 'message' => '<p>Введенные данные не найдены</p>');
    }

    public static function authByCard($VALUES)
    {
        global $USER;

        if ($USER->GetID())
        {
            return array('success' => false, 'message' => '<p>Вы ведь уже вошли! :)</p>');
        }

        $CARD  = trim(htmlspecialcharsbx($VALUES["CARD"]));
        $FIO   = trim(htmlspecialcharsbx($VALUES["FIO"]));
        $PHONE = trim(htmlspecialcharsbx($VALUES["PHONE"]));

        if (empty($CARD) || empty($FIO) || empty($PHONE))
        {
            return array('success' => false, 'message' => '<p>Необходимо заполнить все поля, отмеченные звездочкой.</p>');
        }

        //ищем юзера в 1С
        $arData = array(
            "CARD"  => $CARD,
            "FIO"   => $FIO,
            "PHONE" => $PHONE,
        );

        $arReplay = \CURL::getReplay("getUserByCard", $arData, true, false, true);

        if (empty($arReplay))
        {
            return array('success' => false, 'message' => '<p>Пользователь не найден</p>');
        }

        //напутал Векшин местами ID и XML_ID
        $userXML_ID = $arReplay["ID"]; //это XML_ID юзера. Он должен быть всегда заполнен, если юзер в 1С найден.
        $userID     = $arReplay["XML_ID"]; //это внутренний ID юзера на сайте. Он заполнен, если такой юзер есть на сайте.

        if (empty($userXML_ID))
        {
            return array('success' => false, 'message' => '<p>Пользователь не найден.</p>');
        }
        elseif (!empty($userID))
        {
            //1С нам заявляет, что такой юзер на сайте уже есть. Проверяем.
            //ищем юзера на сайте
            $userIDByCard  = self::getByField("UF_CARD_CODE", $CARD);
            $userIDByXmlId = self::getByXMLId($userXML_ID, array("Y", "N"));

            if ($userID == $userIDByCard && $userIDByCard == $userIDByXmlId)
            {
                //все ок, авторизируем юзера
                self::authByUserId($userID);
                $redirect = PATH_PERSONAL . "?FROM=BYCARD";
                return array('success' => true, 'redirect' => $redirect);
            }
            else
            {
                //что то пошло не так
                return array('success' => false, 'message' => '<p>Не удалось произвести авторизацию.</p>');
            }
        }
        elseif (empty($userID))
        {
            //в 1С такой юзер есть, но на сайте его нету. Производим регистрацию на сайте
            //$arData["XML_ID"] = $userXML_ID;
            $encrypted_hash = getHash('encrypt', $userXML_ID);

            $redirect = PATH_REGISTER . "?FROM=BYCARD&HASH=" . urlencode($encrypted_hash);
            return array('success' => true, 'redirect' => $redirect);
        }

        return array('success' => false, 'message' => '<p>Ошибка</p>');
    }

    public static function authByUserId($USER_ID)
    {
        global $USER;
        $USER->Authorize($USER_ID);
    }

    public static function recovery($VALUES)
    {
        global $USER;

        if ($USER->GetID())
        {
            return array('success' => false, 'message' => '<p>Вы ведь уже вошли! :)</p>');
        }

        $LOGIN = trim($VALUES['LOGIN']);

        if (empty($LOGIN))
        {
            return array('success' => false, 'message' => '<p>Все поля должны быть заполнены</p>');
        }

        $arUsers = array();
        $arUsers += self::getByPhone(getPhoneFromString($LOGIN));
        $arUsers += self::getByEmail(array($LOGIN));

        if (empty($arUsers))
        {
            return array('success' => false, 'message' => '<p>Введенные данные не найдены</p>');
        }


        foreach ($arUsers as $arUser)
        {
            $arResult = $USER->SendPassword($arUser['LOGIN'], $arUser['EMAIL']);
        }

        if ($arResult["TYPE"] == "OK") return array('success' => true, 'message' => '<p>success</p>');
        else return array('success' => false, 'message' => '<p>Введенные данные не найдены</p>');
    }

    public static function change($VALUES)
    {
        global $USER;

        if ($USER->GetID())
        {
            return array('success' => false, 'message' => '<p>Вы ведь уже вошли! :)</p>');
        }

        $LOGIN      = trim($VALUES['LOGIN']);
        $WORD       = trim($VALUES['WORD']);
        $sPassword  = $VALUES['PASSWORD'];
        $sPassword2 = $VALUES['PASSWORD2'];

        if (empty($LOGIN) || empty($WORD))
        {
            return array('success' => false, 'message' => '<p>Ошибка безопасности. Попробуйте еще раз.</p>');
        }

        if (empty($sPassword) || empty($sPassword2))
        {
            return array('success' => false, 'message' => '<p>Все поля должны быть заполнены</p>');
        }

        if ($sPassword != $sPassword2)
        {
            return array('success' => false, 'message' => '<p>Пароли не совпадают</p>');
        }

        $arResult = $USER->ChangePassword($LOGIN, $WORD, $sPassword, $sPassword2);

        if ($arResult["TYPE"] == "OK") return array('success' => true, 'message' => '<p>success</p>');
        else return array('success' => false, 'message' => '<p>' . $arResult['MESSAGE'] . '</p>');
    }

    public static function saveUser($VALUES)
    {
        global $USER;

        if (!$USER->IsAuthorized())
        {
            return array('success' => false, 'message' => '<p>Ошибка авторизации</p>');
        }

        $USER_ID      = $USER->GetId();
        $USER_PROFILE = self::getProfile();

        $PERSON_TYPE_ID = $USER_PROFILE["PERSON_TYPE_ID"];

        $PROPS = self::getUserProps($PERSON_TYPE_ID);

        $QUESTIONS = $PROPS + array(
            "PASSWORD"  => array(
                "NAME"        => "PASSWORD",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "N",
                "CAPTION"     => "Пароль",
                "VALUE"       => "",
                "DESCRIPTION" => "",
            ),
            "PASSWORD2" => array(
                "NAME"        => "PASSWORD2",
                "FIELD_TYPE"  => "password",
                "REQUIRED"    => "N",
                "CAPTION"     => "Повторите пароль",
                "VALUE"       => "",
                "DESCRIPTION" => "",
            ),
        );

        $errors = false;
        //быстрая проверка на заполненность обязательных полей
        foreach ($QUESTIONS as $code => $question)
        {
            $value = trim($VALUES[$code]);
            if ($question['REQUIRED'] == "Y" && empty($value))
            {
                $errors .= '<p>Поле ' . $question['CAPTION'] . ' должно быть заполнено</p>';
            }
        }

        if (!empty($errors))
        {
            return array('success' => false, 'message' => $errors);
        }

        //поля, обязательные для любого типа плательщика
        $sUserName  = trim($VALUES['FIO']);
        $sPhone     = trim($VALUES['PHONE']);
        $sUserEmail = trim($VALUES['EMAIL']);
        $sPassword  = $VALUES['PASSWORD'];
        $sPassword2 = $VALUES['PASSWORD2'];

        if (!empty($sPassword) && $sPassword != $sPassword2)
        {
            $errors .= '<p>пароли не совпадают</p>';
        }

        if (!self::isUniquePhone($sPhone, $USER_ID))
        {
            $errors .= '<p>Телефон ' . $sPhone . ' уже зарегистрирован</p>';
        }

        if (!self::isUniqueEmail($sUserEmail, $USER_ID))
        {
            $errors .= '<p>E-mail ' . $sUserEmail . ' уже зарегистрирован</p>';
        }

        if (!empty($errors))
        {
            return array('success' => false, 'message' => $errors);
        }

        $arUserFields = array(
            "LOGIN"          => $sUserEmail,
            "NAME"           => $sUserName,
            //"LAST_NAME"        => "Иванов",
            "EMAIL"          => $sUserEmail,
            "PERSONAL_PHONE" => $sPhone,
            "ACTIVE"         => "Y",
        );

        if (!empty($sPassword))
        {
            $arUserFields["PASSWORD"]         = $sPassword;
            $arUserFields["CONFIRM_PASSWORD"] = $sPassword2;
        }

        $USER->Update($USER_ID, $arUserFields);

        //свойства заказа для созданног опрофиля покупателя
        $arProfileFields = array();
        foreach ($PROPS as $code => $prop)
        {
            $arProfileFields[$prop['ID']] = $VALUES[$code];
        }
        \CSaleOrderUserProps::DoSaveUserProfile($USER_ID, $USER_PROFILE["ID"], $sUserName, $PERSON_TYPE_ID, $arProfileFields, $arResult);

        return array('success' => true, 'message' => "<p>Данные сохранены</p>");
    }

    /**
     * Проверяет на уникальность номер телефона
     * @param string $value номер телефона
     * @param array $exclude ID юзеров, которые надо исключить из проверки
     * @return boolean
     */
    public static function isUniquePhone($value, $exclude = null, $allowEmpty = false)
    {
        $phones = getPhoneFromString($value);

        $result = true;

        if (!$allowEmpty && empty($phones))
        {
            $result = false;
        }

        foreach ($phones as $phone)
        {
            if (!$allowEmpty && empty($phone))
            {
                $result = false;
                continue;
            }

            $arFilter = array(
                "PERSONAL_PHONE" => $phone,
            );

            if (!empty($exclude))
            {
                $arFilter["LOGIC"] = "AND";
                $arFilter["!ID"]   = $exclude;
            }

            $obList  = \CUser::GetList($by      = "", $order   = "", $arFilter);
            if ($arFetch = $obList->Fetch())
            {
                $result = false;
                continue;
            }

            $result = true;
        }

        return $result;
    }

    /**
     * Проверяет на уникальность почту
     * @param string $value адрес почты
     * @param array $exclude ID юзеров, которые надо исключить из проверки
     * @return boolean
     */
    public static function isUniqueEmail($value, $exclude = null, $allowEmpty = false)
    {
        $email = trim(htmlspecialcharsbx($value));

        if (!$allowEmpty && empty($email))
        {
            return false;
        }

        $arFilter = array(
            "=EMAIL" => $email,
        );

        if (!empty($exclude))
        {
            $arFilter["LOGIC"] = "AND";
            $arFilter["!ID"]   = $exclude;
        }

        $obList  = \CUser::GetList($by      = "", $order   = "", $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            return false;
        }

        return true;
    }

    public static function getByPhone($arPhones)
    {
        $arUsers = array();

        foreach ($arPhones as $phone)
        {
            if (empty($phone)) continue;

            $arFilter = Array("ACTIVE" => "Y", "PERSONAL_PHONE" => $phone);

            $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
            while ($arFetch = $obList->Fetch())
            {
                if (!array_key_exists($arFetch['ID'], $arUsers)) $arUsers[$arFetch['ID']] = $arFetch;
            }
        }

        return $arUsers;
    }

    public static function getByEmail($arEmails)
    {
        $arUsers = array();

        foreach ($arEmails as $email)
        {
            if (empty($email)) continue;

            $arFilter = Array("ACTIVE" => "Y", "EMAIL" => $email);

            $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
            while ($arFetch = $obList->Fetch())
            {
                if (!array_key_exists($arFetch['ID'], $arUsers)) $arUsers[$arFetch['ID']] = $arFetch;
            }
        }

        return $arUsers;
    }

    public static function getByLogin($login)
    {
        $arFilter = Array("ACTIVE" => "Y", "LOGIN_EQUAL" => $login);

        $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch['ID'];
        }

        return false;
    }

    public static function getByXMLId($XML_ID, $active = "Y")
    {
        $arFilter = Array("ACTIVE" => $active, "XML_ID" => $XML_ID);

        $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch['ID'];
        }

        return false;
    }

    public static function getByField($field, $value)
    {
        if (empty($field) || empty($value))
        {
            return false;
        }

        $arFilter = Array("ACTIVE" => "Y", "!" . $field => false, "=" . $field => $value);

        $obList  = \CUser::GetList(($by      = "id"), ($order   = "desc"), $arFilter);
        if ($arFetch = $obList->Fetch())
        {
            return $arFetch['ID'];
        }

        return false;
    }

    private static $_instance;

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    public static function get()
    {
        if (!is_object(self::$_instance))
        {
            self::$_instance = new self;
            self::init();
        }
        return self::$_instance;
    }

    private static function init()
    {
        
    }

}
