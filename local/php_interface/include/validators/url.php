<?php

class CFormCustomValidatorUrl {

    function GetDescription()
    {
        return array(
            "NAME"            => "url_validator", // идентификатор
            "DESCRIPTION"     => "URL", // наименование
            "TYPES"           => array("text", "textarea", "hidden"), // типы полей
            "SETTINGS"        => array("CFormCustomValidatorUrl", "GetSettings"), // метод, возвращающий массив настроек
            "CONVERT_TO_DB"   => array("CFormCustomValidatorUrl", "ToDB"), // метод, конвертирующий массив настроек в строку
            "CONVERT_FROM_DB" => array("CFormCustomValidatorUrl", "FromDB"), // метод, конвертирующий строку настроек в массив
            "HANDLER"         => array("CFormCustomValidatorUrl", "DoValidate")   // валидатор
        );
    }

    function ToDB($arParams)
    {
        // возвращаем сериализованную строку
        return serialize($arParams);
    }

    function FromDB($strParams)
    {
        // никаких преобразований не требуется, просто вернем десериализованный массив
        return unserialize($strParams);
    }

    function DoValidate($arParams, $arQuestion, $arAnswers, $arValues)
    {
        global $APPLICATION;

        foreach ($arValues as $value)
        {
            $value = trim($value);

            // пустые значения
            if (mb_strlen($value) <= 0) continue;

            if (!filter_var($value, FILTER_VALIDATE_URL))
            {
                // вернем ошибку
                $APPLICATION->ThrowException("#FIELD_NAME#: неверный формат");
                return false;
            }
        }

        // все значения прошли валидацию, вернем true
        return true;
    }

}

// установим метод CFormCustomValidatorNumberEx в качестве обработчика события
AddEventHandler("form", "onFormValidatorBuildList", array("CFormCustomValidatorUrl", "GetDescription"));
