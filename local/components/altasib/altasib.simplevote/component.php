<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (!CModule::IncludeModule("iblock")) die();
if (!CModule::IncludeModule("altasib.simplevote")) die();
global $APPLICATION, $USER;
$rand = CAjax::GetComponentID($this->componentName, $this->componentTemplate, NULL);// unic ID

/************** CACHE **********************************************/
$arParams["CACHE_TIME"] = (isset($arParams["CACHE_TIME"]) && strlen($arParams["CACHE_TIME"]) > 0 ?
    $arParams["CACHE_TIME"] : 300);
$arParams["ADDITIONAL_CACHE_ID"] = (isset($arParams["ADDITIONAL_CACHE_ID"]) && strlen($arParams["ADDITIONAL_CACHE_ID"]) > 0 ?
    $arParams["ADDITIONAL_CACHE_ID"] : $USER->GetGroups());
$arParamsCache = array($arParams["ADDITIONAL_CACHE_ID"], $arParams["CACHE_TYPE"]);
/************** *---* **********************************************/
$_POST["mood_" . $rand] = htmlspecialcharsEx($_POST["mood_" . $rand]);
$arParams["IP_STOP"] = intval(trim($arParams["IP_STOP"]));
if ($arParams["IP_STOP"] == 0) $arParams["IP_STOP"] = 10;
$arParams["VOTE_WIDTH"] = intval(str_replace("%", "", trim($arParams["VOTE_WIDTH"])));
if ($arParams["VOTE_WIDTH"] < 1) $arParams["VOTE_WIDTH"] = 100;
$arParams["SCALE_HEIGHT"] = intval(str_replace("px", "", trim($arParams["SCALE_HEIGHT"])));
if ($arParams["SCALE_HEIGHT"] < 1) $arParams["SCALE_HEIGHT"] = 15;
if (!function_exists("declOfNum")) {
    function declOfNum($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);
        return $number . " " . $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
}

$ob = FALSE;

if (intval($arParams["IBLOCK_ID"]) > 0) {
    if (isset($_POST["ok_vote"]) && $_POST["ok_vote"] == $rand)
        $this->ClearResultCache($arParamsCache);
    if ($this->StartResultCache(false, $arParamsCache)) {
        $arSelect = Array();
        $arFilter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
        if (intval($arParams["SECTION_ID"]) > 0)
            $arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
        $res = CIBlockElement::GetList(Array("date_active_to" => "desc"), $arFilter, false, Array("nPageSize" => 1), $arSelect);
        if ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
            $arFields["PREVIEW_PICTURE"] = CFile::GetPath((int)$arFields["PREVIEW_PICTURE"]);
            $arResult = $arFields;
            $arResult["PROP"] = $arProps;

            $this->EndResultCache();
        } else
            $this->AbortResultCache();
    }
}
if (isset($arResult["ID"])) {


    $tempval = $arResult["PROP"]["VARIANT"]["VALUE"];
    $tempdescr = $arResult["PROP"]["VARIANT"]["DESCRIPTION"];
    $total_val = count($tempval);
    $arResult["VOTE_PROP"]["VOTE_FLAG"] = $APPLICATION->get_cookie("ALTASIB_VOTE_ID_" . $rand);
    $ELEMENT_ID = $arResult["ID"];
    if (isset($_POST["ok_vote"]) && $_POST["ok_vote"] == $rand && $_POST["mood_" . $rand] != "" && $arResult["PROP"]["MULTI"]["VALUE"] != "Y" && $arResult["VOTE_PROP"]["VOTE_FLAG"] != 1) {

        $TIMES[$rand] = CAltasib_simplevote::GetLastIpTime($ELEMENT_ID);
        $a = time() - intval($TIMES[$rand]['TIMES']);
        if ($a < 60 * $arParams["IP_STOP"]) {
            ShowError(GetMessage("IP_STOP_TXT"));
        } else {
            $USER_ID = "";
            if ($USER->IsAuthorized())
                $USER_ID = $USER->GetID();
            CAltasib_simplevote::Add(array("VOTE_ID" => $ELEMENT_ID, "VOTE_RES" => $_POST["mood_" . $rand], "USER_ID" => $USER_ID));

            echo GetMessage("ALL_OK");
            $i = $_POST["mood_" . $rand];
            $tempdescr[$i] = intval($tempdescr[$i]) + 1;
            $PROPERTY_CODE = "VARIANT";
            for ($a = 0; $a < $total_val; $a++) // adding vote
                $PROPERTY_VALUE ['n' . $a] = array('VALUE' => $tempval[$a], 'DESCRIPTION' => intval($tempdescr[$a]));
            CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, array($PROPERTY_CODE => $PROPERTY_VALUE));
            $APPLICATION->set_cookie("ALTASIB_VOTE_ID_" . $rand, 1, time() + 60 * $arParams["IP_STOP"]);
            $arResult["VOTE_PROP"]["VOTE_FLAG"] = 1;
            $this->ClearResultCache($arParamsCache);
        }
    } else if (isset($_POST["ok_vote"]) && $_POST["ok_vote"] == $rand && $arResult["PROP"]["MULTI"]["VALUE"] == "Y" && $_POST["mood_" . $rand] != "" && $arResult["VOTE_PROP"]["VOTE_FLAG"] != 1) {

        $TIMES[$rand] = CAltasib_simplevote::GetLastIpTime($ELEMENT_ID);
        $a = time() - intval($TIMES[$rand]['TIMES']);
        if ($a < 60 * $arParams["IP_STOP"])
            ShowError(GetMessage("IP_STOP_TXT"));
        else {
            $mood = "";
            foreach ($_POST["mood_" . $rand] as $num => $val) {
                $tempdescr[$num] = intval($tempdescr[$num]) + 1;
                $mood .= $num . ", ";
            }

            $USER_ID = "";
            if ($USER->IsAuthorized())
                $USER_ID = $USER->GetID();
            CAltasib_simplevote::Add(array("VOTE_ID" => $ELEMENT_ID, "VOTE_RES" => $mood, "USER_ID" => $USER_ID));

            echo GetMessage("ALL_OK");
            $PROPERTY_CODE = "VARIANT";
            for ($a = 0; $a < $total_val; $a++) // adding vote
                $PROPERTY_VALUE ['n' . $a] = array('VALUE' => $tempval[$a], 'DESCRIPTION' => intval($tempdescr[$a]));
            CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, array($PROPERTY_CODE => $PROPERTY_VALUE));
            $APPLICATION->set_cookie("ALTASIB_VOTE_ID_" . $rand, 1, time() + 60 * $arParams["IP_STOP"]);
            $arResult["VOTE_PROP"]["VOTE_FLAG"] = 1;
            $this->ClearResultCache($arParamsCache);
        }
    } elseif (isset($_POST["ok_vote"]) && $_POST["ok_vote"] == $rand && $_POST["mood_" . $rand] == "")
        showerror(GetMessage("CHOOSE_YOUR_VARIANT"));
    $max = 0;
    for ($k = 0; $k < $total_val; $k++) {
        if (intval($tempdescr[$k]) > $max)
            $max = intval($tempdescr[$k]);
    }

    $arResult["VOTE_PROP"]["TOTALNUMBEROFVOTES"] = 0;

    foreach ($tempdescr as $key => $value) {
        $tempdescr[$key] = intval($value);
        $arResult["VOTE_PROP"]["PERSENT"][$key] = 100 / $max * $tempdescr[$key];
        $arResult["VOTE_PROP"]["TOTALNUMBEROFVOTES"] += $tempdescr[$key];
    }

    $arResult["VOTE_PROP"]["NUMBEROFVOTES"] = $tempdescr;
    $arResult["TOTAL_VAL"] = $total_val;

    if (isset($arResult["ID"])) {
        $arTitleOptions = null;
        if (CModule::IncludeModule("iblock")) {
            CIBlockElement::CounterInc($arParams["ELEMENT_ID"]);
            if ($USER->IsAuthorized()) {
                if (
                    $APPLICATION->GetShowIncludeAreas()
                    || $arParams["SET_TITLE"]
                    || isset($arResult[$arParams["BROWSER_TITLE"]])
                ) {
                    $arReturnUrl = array(
                        "add_element" => CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "DETAIL_PAGE_URL"),
                        "delete_element" => (
                        empty($arResult["SECTION_URL"]) ?
                            $arResult["LIST_PAGE_URL"] :
                            $arResult["SECTION_URL"]
                        ),
                    );
                    $arButtons = CIBlock::GetPanelButtons(
                        $arResult["IBLOCK_ID"],
                        $arResult["ID"],
                        $arResult["IBLOCK_SECTION_ID"],
                        Array(
                            "RETURN_URL" => $arReturnUrl,
                            "SECTION_BUTTONS" => false,
                        )
                    );
                    if ($APPLICATION->GetShowIncludeAreas())
                        $this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
                    if ($arParams["SET_TITLE"] || isset($arResult[$arParams["BROWSER_TITLE"]])) {
                        $arTitleOptions = array(
                            'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_element"]["ACTION"],
                            'PUBLIC_EDIT_LINK' => $arButtons["edit"]["edit_element"]["ACTION"],
                            'COMPONENT_NAME' => $this->GetName(),
                        );
                    }
                }
            }
        }
    }
    for ($f = 0; $f < $arResult["TOTAL_VAL"]; $f++) {
        $arResult["PROP"]["PIC"]["VALUE"][$f] = CFile::GetPath((int)$arResult["PROP"]["PIC"]["VALUE"][$f]);
    }
    foreach ($arResult["PROP"]["VARIANT"]["VALUE"] as $key => $value):
        $VAR = array();
        $VAR["VALUE"] = $value;
        $VAR["NUMBER_OF_VOTES"] = intval($arResult["VOTE_PROP"]["NUMBEROFVOTES"][$key]);
        $VAR["PIC"] = $arResult["PROP"]["PIC"]["VALUE"][$key];
        $VAR["PIC_DESCRIPTION"] = $arResult["PROP"]["PIC"]["DESCRIPTION"][$key];
        $VAR["PERSENT"] = $arResult["VOTE_PROP"]["PERSENT"][$key];
        $arResult["VAR"][] = $VAR;
    endforeach;
    $arResult["MULTI"] = $arResult["PROP"]["MULTI"]["VALUE"];
    unset ($arResult["PROP"]);


    if ($arParams["UNSORT_VARIANT"] == 1) {
        shuffle($arResult["VAR"]);
    };
    $arResult["RAND"] = $rand;
    $this->IncludeComponentTemplate();
} else {
//$this->AbortResultCache();
    if ($USER->IsAdmin())
        ShowError(GetMessage("NONE"));
}
?>