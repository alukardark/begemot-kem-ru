<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock"))return;?>
<script type="text/javascript" src="/bitrix/components/altasib/altasib.simplevotelist/js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="/bitrix/components/altasib/altasib.simplevotelist/js/iColorPicker.js"></script>
<div style="background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E;  margin-bottom: 15px;"><div style="background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff">
        <a href="http://www.is-market.ru?param=cl" target="_blank"><img src="/bitrix/components/altasib/altasib.simplevotelist/images/is-market.gif" style="float: left; margin-right: 15px;" border="0" /></a>
        <div style="margin: 13px 0px 0px 0px">
                <a href="http://www.is-market.ru?param=cl" target="_blank" style="color: #fff; font-size: 10px; text-decoration: none"><?=GetMessage("ALTASIB_IS")?></a>
        </div>
</div></div>
<?
$rsIBlock = CIBlock::GetList(Array(), Array("CODE" => "altasib_simplevote"));
if($arr=$rsIBlock->Fetch()) $defaultIBid = $arr["ID"];
  else $defaultIBid = "";

if($arCurrentValues["IBLOCK_ID"]<1)
   $arCurrentValues["IBLOCK_ID"] = $defaultIBid;

$arSection[]=GetMessage("ALL_SECTION");
$arIBlock[]="";
$rsIBlockType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
while ($arr=$rsIBlockType->Fetch())
{
	if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
		$arIBlockType[$arr["ID"]] = "[".$arr["ID"]."] ".$ar["NAME"];
}
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
$arFilter = Array('IBLOCK_ID'=>$arCurrentValues["IBLOCK_ID"], 'IBLOCK_TYPE'=>$arCurrentValues["IBLOCK_TYPE"], 'GLOBAL_ACTIVE'=>'Y');
$db_list = CIBlockSection::GetList(Array("sort" => "asc"), $arFilter);
while($arr = $db_list->Fetch())
    $arSection[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
$width=100;
$position= array (GetMessage("VOTE_LIST"),GetMessage("VOTE_COLUMNS"));
$showimage=array (GetMessage("NO"),GetMessage("YES"));
$show_var = array (GetMessage("NO"),GetMessage("YES"));
$string_res=array();
$variant_image_position = array(GetMessage("DONT_SHOW"),GetMessage("TOP"),GetMessage("RIGHT"));
$arComponentParameters = array(
	"GROUPS" => array(
			"BASE" => array("NAME" => GetMessage("BASE")),
			"PAGER_SETTINGS" => array(
			"NAME" => GetMessage("PAGER_SETTINGS"),
			),
	),
	"PARAMETERS" => array(
						  
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"DEFAULT" => "altasib_simplevote",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"DEFAULT" => $defaultIBid,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SECTION"),
			"TYPE" => "LIST",
			"VALUES" => $arSection,
			"REFRESH" => "Y",
		),
		"AJAX_MODE" => array(),
		"STRING_RES" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("STRING_RES"),
			"TYPE" => "STRING",
			"VALUES" => $string_res,
			"REFRESH" => "N",
		),
		"COLOR" => Array(
            "NAME" => GetMessage("COLOR"),
            "TYPE" => 'CUSTOM',
            "JS_FILE" => '/bitrix/components/altasib/altasib.simplevotelist/js/script.js',
            "JS_EVENT" => 'alxColor',
            "PARENT" => "BASE",
            "DEFAULT" => "#acacac"
		),
		"VOTE_WIDTH" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("WIDTH"),
			"TYPE" => "STRING",
			"VALUES" => $width,
			"REFRESH" => "N",
		),
		"SCALE_HEIGHT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("SCALE_HEIGHT"),
            "TYPE" => "STRING",
            "VALUES" => 20,
        ),
		"POSITION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("VOTE_POSITION"),
			"TYPE" => "LIST",
			"VALUES" => $position,
			"REFRESH" => "N",
		),
		"SHOW_IMAGE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHOW_IMAGE"),
			"TYPE" => "LIST",
			"VALUES" => $showimage,
			"REFRESH" => "N",
		),
		"SHOW_VAR" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SHOW_VAR"),
			"TYPE" => "LIST",
			"VALUES" => $show_var,
			"DEFAULT" => "1",
                ),
		"VARIANT_IMAGE_POSITION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("VARIANT_IMAGE_POSITION"),
			"TYPE" => "LIST",
			"VALUES" => $variant_image_position,
			"REFRESH" => "N",
		),
		"NEWS_COUNT" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"DISPLAY_TOP_PAGER" => array(
			"PARENT" => "PAGER_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_TOP_PAGER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"DISPLAY_BOTTOM_PAGER" => array(
			"PARENT" => "PAGER_SETTINGS",
			"NAME" => GetMessage("T_IBLOCK_DESC_BOTTOM_PAGER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PAGER_SHOW_ALL" => array(
			"PARENT" => "PAGER_SETTINGS",
			"NAME" => GetMessage("CP_BN_PAGER_SHOW_ALL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>86400),
	)
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), true, true);
?>