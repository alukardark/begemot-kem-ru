<?php

class CFormCustomValidatorStrLen {

    function GetDescription()
    {
        return array(
            "NAME"            => "strlen_validator", // идентификатор
            "DESCRIPTION"     => "Длина строки", // наименование
            "TYPES"           => array("text", "textarea", "hidden"), // типы полей
            "SETTINGS"        => array("CFormCustomValidatorStrLen", "GetSettings"), // метод, возвращающий массив настроек
            "CONVERT_TO_DB"   => array("CFormCustomValidatorStrLen", "ToDB"), // метод, конвертирующий массив настроек в строку
            "CONVERT_FROM_DB" => array("CFormCustomValidatorStrLen", "FromDB"), // метод, конвертирующий строку настроек в массив
            "HANDLER"         => array("CFormCustomValidatorStrLen", "DoValidate")   // валидатор
        );
    }

    function GetSettings()
    {
        return array(
            "LENGTH_MIN" => array(
                "TITLE"   => "Миниальная длина строки",
                "TYPE"    => "TEXT",
                "DEFAULT" => "3",
            ),
            "LENGTH_MAX" => array(
                "TITLE"   => "Максимальная длина строки",
                "TYPE"    => "TEXT",
                "DEFAULT" => "30",
            )
        );
    }

    function ToDB($arParams)
    {
        // перестановка значений порогов, если требуется
        if ($arParams["LENGTH_MIN"] > $arParams["LENGTH_MAX"])
        {
            $tmp                    = $arParams["LENGTH_MIN"];
            $arParams["LENGTH_MIN"] = $arParams["LENGTH_MAX"];
            $arParams["LENGTH_MAX"] = $tmp;
        }

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

            if (mb_strlen($value) < $arParams["LENGTH_MIN"])
            {
                $APPLICATION->ThrowException("#FIELD_NAME#: минимальная длина " . $arParams["LENGTH_MIN"] . " символов");
                return false;
            }

            if (mb_strlen($value) > $arParams["LENGTH_MAX"])
            {
                $APPLICATION->ThrowException("#FIELD_NAME#: максимальная длина " . $arParams["LENGTH_MAX"] . " символов");
                return false;
            }
        }

        // все значения прошли валидацию, вернем true
        return true;
    }

}

// установим метод CFormCustomValidatorNumberEx в качестве обработчика события
AddEventHandler("form", "onFormValidatorBuildList", array("CFormCustomValidatorStrLen", "GetDescription"));
