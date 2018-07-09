<?php

class CFormCustomValidatorEmail {

    function GetDescription()
    {
        return array(
            "NAME"            => "email_validator", // идентификатор
            "DESCRIPTION"     => "E-mail", // наименование
            "TYPES"           => array("text", "textarea", "hidden"), // типы полей
            "SETTINGS"        => array("CFormCustomValidatorEmail", "GetSettings"), // метод, возвращающий массив настроек
            "CONVERT_TO_DB"   => array("CFormCustomValidatorEmail", "ToDB"), // метод, конвертирующий массив настроек в строку
            "CONVERT_FROM_DB" => array("CFormCustomValidatorEmail", "FromDB"), // метод, конвертирующий строку настроек в массив
            "HANDLER"         => array("CFormCustomValidatorEmail", "DoValidate")   // валидатор
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

            if (!filter_var($value, FILTER_VALIDATE_EMAIL))
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
AddEventHandler("form", "onFormValidatorBuildList", array("CFormCustomValidatorEmail", "GetDescription"));
