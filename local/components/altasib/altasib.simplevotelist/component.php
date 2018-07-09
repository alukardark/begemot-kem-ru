<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!CModule::IncludeModule("iblock"))die();
$arParams["VOTE_WIDTH"] = intval(str_replace("%", "", trim($arParams["VOTE_WIDTH"])));
global $USER;
if ($arParams["VOTE_WIDTH"]<1)
	$arParams["VOTE_WIDTH"] = 100;

$arParams["SCALE_HEIGHT"] = intval(str_replace("px", "", trim($arParams["SCALE_HEIGHT"])));
if ($arParams["SCALE_HEIGHT"]<1)$arParams["SCALE_HEIGHT"] = 15;
if (!function_exists("declOfNum"))
	{
	function declOfNum($number, $titles)
		{
		$cases = array (2, 0, 1, 1, 1, 2);
		return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
		}
	}
$arSelect = Array();
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";
if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
	{
		$arNavParams = array(
			"nPageSize" => $arParams["NEWS_COUNT"],
			"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
			"bShowAll" => $arParams["PAGER_SHOW_ALL"],
		);
		$arNavigation = CDBResult::GetNavParams($arNavParams);
		if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
			$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
	}
else
	{
		$arNavParams = array(
			"nTopCount" => $arParams["NEWS_COUNT"],
			"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		);
		$arNavigation = false;
	}
$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
    $arParams["GROUP_PERMISSIONS"] = array(1);
$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
        $arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
        foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
        {
                if(in_array($PERM, $arUserGroupArray))
                {
                        $bUSER_HAVE_ACCESS = true;
                        break;
                }
        }
}
if($this->StartResultCache(false, array($arNavigation)))
{
	$arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y");
	if($arParams["SECTION_ID"]>0)
		$arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
	$res = CIBlockElement::GetList(Array(), $arFilter, false, $arNavParams, $arSelect);
	while($ob = $res->GetNextElement())
	{
		$arFields = $ob->GetFields();
		$arProps[] = $ob->GetProperties();
		$arElements[] = $arFields;
	}
	if($arElements){
		$arResult["NAV_STRING"] = $res->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
		$arResult["VOTE_PROP"]["TOTALNUMBEROFVOTES"] =0;
		$max = $arrayy[0];
		foreach ($arProps as $key => $value)
		{
			$arResult["VOTE"][$key]["VALUE"]=$arProps[$key]["VARIANT"]["VALUE"];
			$arResult["VOTE"][$key]["DESCRIPTION"]=$arProps[$key]["VARIANT"]["DESCRIPTION"];
		}
		unset($value);
		foreach ($arResult["VOTE"] as $key => $value)
		{
			foreach ($arProps[$key]["PIC"]["VALUE"] as $key1 => $value1)
			{
			$arResult["VOTE"][$key]["PIC"][$key1] = CFile::GetPath((int)$arProps[$key]["PIC"]["VALUE"][$key1]);
			$arResult["VOTE"][$key]["PIC_DESC"][$key1] = $arProps[$key]["PIC"]["DESCRIPTION"][$key1];
			}
			foreach ($arProps[$key]["VARIANT"]["VALUE"] as $key1 => $value1)
			{
				if ($arProps[$key]["VARIANT"]["DESCRIPTION"][$key1]>$arResult["VOTE"][$key]["MAX_RESULT"])
					{
					$arResult["VOTE"][$key]["MAX_RESULT"] = $arProps[$key]["VARIANT"]["DESCRIPTION"][$key1];
					}
			}unset($value1);
			foreach ($arProps[$key]["VARIANT"]["VALUE"] as $key1 => $value1)
			{
				$arResult["VOTE"][$key]["PERSENT"][$key1] = 100/$arResult["VOTE"][$key]["MAX_RESULT"]*$arResult["VOTE"][$key]["DESCRIPTION"][$key1];
				$arResult["VOTE"][$key]["TOTALNUMBEROFVOTES"] += $arResult["VOTE"][$key]["DESCRIPTION"][$key1];
			}unset($value1);
		$arResult["VOTE_PROP"]["TOTALNUMBEROFVOTES"] += $arResult["VOTE"][$key]["TOTALNUMBEROFVOTES"];	
		$arResult["VOTE"][$key]["NAME"]=$arElements[$key]["NAME"];
		$arResult["VOTE"][$key]["PREVIEW_PICTURE"] = CFile::GetPath((int)$arElements[$key]["PREVIEW_PICTURE"]);
		$arResult["VOTE"][$key]["PREVIEW_TEXT"]=$arElements[$key]["PREVIEW_TEXT"];
		}
		unset($value);
		
		$this->IncludeComponentTemplate();
	}
	else{
		if($USER->IsAdmin()){
			$this->AbortResultCache();
			ShowError(GetMessage("NONE"));
		}
	}
}
?>