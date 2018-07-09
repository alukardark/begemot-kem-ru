<?php

class CFormCustomValidatorPhone {

    function GetDescription()
    {
        return array(
            "NAME"            => "phone_validator", // идентификатор
            "DESCRIPTION"     => "Телефон", // наименование
            "TYPES"           => array("text", "textarea", "hidden"), // типы полей
            "SETTINGS"        => array("CFormCustomValidatorPhone", "GetSettings"), // метод, возвращающий массив настроек
            "CONVERT_TO_DB"   => array("CFormCustomValidatorPhone", "ToDB"), // метод, конвертирующий массив настроек в строку
            "CONVERT_FROM_DB" => array("CFormCustomValidatorPhone", "FromDB"), // метод, конвертирующий строку настроек в массив
            "HANDLER"         => array("CFormCustomValidatorPhone", "DoValidate")   // валидатор
        );
    }

    function GetSettings()
    {
        return array(
            "PHONE_MASK" => array(
                "TITLE"   => "Маска",
                "TYPE"    => "TEXT",
                "DEFAULT" => "+7-(912)-345-67-89",
            )
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

            if (mb_strlen($value) != mb_strlen($arParams["PHONE_MASK"]))
            {
                // вернем ошибку
                $APPLICATION->ThrowException("#FIELD_NAME#: неверный формат");
                return false;
            }

            $phone_arr = str_split($value);
            $mask_arr  = str_split($arParams["PHONE_MASK"]);
            foreach ($mask_arr as $n => $symbol)
            {
                if ((is_numeric($symbol) && !is_numeric($phone_arr[$n])) ||
                        (!is_numeric($symbol) && $symbol != $phone_arr[$n]) ||
                        ($symbol == " " && $phone_arr[$n] != " "))
                {
                    $APPLICATION->ThrowException("#FIELD_NAME#: неверный формат");
                    return false;
                }
            }
        }

        // все значения прошли валидацию, вернем true
        return true;
    }

}

// установим метод CFormCustomValidatorNumberEx в качестве обработчика события
AddEventHandler("form", "onFormValidatorBuildList", array("CFormCustomValidatorPhone", "GetDescription"));
