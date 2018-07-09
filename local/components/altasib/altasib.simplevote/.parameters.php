<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock"))return;
?>
<script type="text/javascript" src="/bitrix/components/altasib/altasib.simplevote/js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="/bitrix/components/altasib/altasib.simplevote/js/iColorPicker.js"></script>
<div style="background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E;  margin-bottom: 15px;"><div style="background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff">
        <a href="http://www.is-market.ru?param=cl" target="_blank"><img src="/bitrix/components/altasib/altasib.simplevote/images/is-market.gif" style="float: left; margin-right: 15px;" border="0" /></a>
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

$arSection[] = GetMessage("ALL");
$arIBlock[] = GetMessage("CHOOSE_IB");
$rsIBlockType = CIBlockType::GetList(array("sort"=>"asc"), array("ACTIVE"=>"Y"));
while ($arr=$rsIBlockType->Fetch())
{
        if($ar=CIBlockType::GetByIDLang($arr["ID"], LANGUAGE_ID))
                $arIBlockType[$arr["ID"]] = "[".$arr["ID"]."] ".$ar["NAME"];
}

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
        $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$rsSection = CIBlockSection::GetList(Array("sort" => "asc"), Array('IBLOCK_ID'=>$arCurrentValues["IBLOCK_ID"], 'GLOBAL_ACTIVE'=>'Y'));
while($arr = $rsSection->Fetch())
        $arSection[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
$position = array (GetMessage("VOTE_LIST"),GetMessage("VOTE_COLUMNS"));
$showimage = array (GetMessage("NO"),GetMessage("YES"));
$string_res = array();
$string_vote = array();
$show_var = array (GetMessage("NO"),GetMessage("YES"));

$variant_image_position = array(GetMessage("DONT_SHOW"),GetMessage("TOP"),GetMessage("RIGHT"));
$unsort_variant = array (GetMessage("NO"),GetMessage("YES"));
$show_results = array (GetMessage("NO"),GetMessage("YES"));

$arComponentParameters = array(
        "GROUPS" => array(
                        "BASE" => array("NAME" => GetMessage("BASE")),
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
                        "DEFAULT" => "",
                ),
                "AJAX_MODE" => array(),
                "STRING_VOTE" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("STRING_VOTE"),
                        "TYPE" => "STRING",
                        "VALUES" => $string_vote,
                ),
                "STRING_RES" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("STRING_RES"),
                        "TYPE" => "STRING",
                        "VALUES" => $string_res,
                ),
                "COLOR" => Array(
                        "NAME" => GetMessage("COLOR"),
                        "TYPE" => 'CUSTOM',
                        "JS_FILE" => '/bitrix/components/altasib/altasib.simplevote/js/script.js',
                        "JS_EVENT" => 'alxColor',
                        "PARENT" => "BASE",
                        "DEFAULT" => "#acacac"
               ),
                "VOTE_WIDTH" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("WIDTH"),
                        "TYPE" => "STRING",
                        "VALUES" => 100,
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
                ),
                "SHOW_IMAGE" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("SHOW_IMAGE"),
                        "TYPE" => "LIST",
                        "VALUES" => $showimage,
                        "DEFAULT" => "1",
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
                ),
                "UNSORT_VARIANT" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("UNSORT_VARIANT"),
                        "TYPE" => "LIST",
                        "VALUES" => $unsort_variant,
                ),
                "IP_STOP" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("IP_STOP"),
                        "TYPE" => "STRING",
                        "DEFAULT" => 10,
                ),
                "SHOW_RESULTS" => array(
                        "PARENT" => "BASE",
                        "NAME" => GetMessage("SHOW_RESULTS"),
                        "TYPE" => "LIST",
                        "VALUES" => $show_results,
                        "DEFAULT" => "1",
                ),
				"CACHE_TIME"  =>  Array("DEFAULT"=>60),
		)
)
?>