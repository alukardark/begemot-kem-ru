<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="alx_vote">
<?global $APPLICATION;
$rand = $arResult["RAND"];?>
<?if ($arResult["VOTE_PROP"]["VOTE_FLAG"] == 1 && $arParams["SHOW_RESULTS"] == 1):?>
        <p class="alx_vote_res_title"><b><?=$arParams["STRING_RES"]?></b></p>
        <?if ($arParams["SHOW_IMAGE"]==1):// result *******************?>
                        <table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0" class="alx_vote_res" cellpadding="4">
                                        <tr>
                                        <?if (strlen($arResult["PREVIEW_PICTURE"]) > 0):?>
                                                <td align="left" valign="top" width="1%"><img src="<?=$arResult["PREVIEW_PICTURE"]?>" /></td>
                                        <?endif;?>
                                                <td align="left" valign="top" class="alx_preview_text"><?=$arResult["PREVIEW_TEXT"]?></td>
                                        </tr>
                        </table>
        <?endif;?>
        <p class="alx_vote_name"><b><?=$arResult['NAME']?></b></p>
        <?if ($arParams["VARIANT_IMAGE_POSITION"]==1):?>
                        <table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0"><tr>
                                        <?foreach ($arResult["VAR"] as $key => $value){?>
                                        <?if (strlen($value["PIC"]) > 0):?>
                                                <td align="center" valign="bottom"><img src="<?=$value["PIC"]?>" /><p><?=$value["PIC_DESCRIPTION"]?></p></td>
                                        <?endif;?>
                                        <?}?></tr>
                        </table><br />
        <?endif;?>
        <?if ($arParams["POSITION"]==1):?>
                        <table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0" class="alx_vote_res">
                                        <?foreach ($arResult["VAR"] as $key => $value){?>
                                                        <tr>
                                                        <td class="alx_line_container"><div style="width:<?=$value["PERSENT"]?>%; height:<?=$arParams["SCALE_HEIGHT"]?>px; background:<?=$arParams["COLOR"]?>">&nbsp</div></td>
                                                        <td align="left">&nbsp <?if ($arParams["SHOW_VAR"] == 1):?><span class="alx_vote_ask_txt"><?=$value["VALUE"]?></span> | <?endif;?><span class="alx_vote_ask_num">  <?=declOfNum($value["NUMBER_OF_VOTES"], array(GetMessage("GOLOS"), GetMessage("GOLOSA"), GetMessage("GOLOSOV")))?></span></td>
                                                        <?if (strlen($value["PIC"])>0 && $arParams["VARIANT_IMAGE_POSITION"]==2):?>
                                                                <td style="padding: 0px 5px 5px 15px" valign="center" align="center"><img src="<?=$value["PIC"]?>" /><p><?=$value["PIC_DESCRIPTION"]?></p></td>
                                                        <?endif;?>
                                                        </tr>
                                        <?}?>
                        </table>
        <?else:?>
                        <table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0" class="alx_vote_res">
                                        <?foreach ($arResult["VAR"] as $key => $value){?>
                                                        <tr>
                                                                <td align="left">&nbsp <?if ($arParams["SHOW_VAR"] == 1):?><span class="alx_vote_ask_txt"><?=$value["VALUE"]?></span> | <?endif;?><span class="alx_vote_ask_num"><?=declOfNum($value["NUMBER_OF_VOTES"], array(GetMessage("GOLOS"), GetMessage("GOLOSA"), GetMessage("GOLOSOV")))?></span></td>
                                                        </tr>
                                                        <tr>
                                                                <td class="alx_line_container"><div style="width:<?=$value["PERSENT"]?>%; height:<?=$arParams["SCALE_HEIGHT"]?>px; background:<?=$arParams["COLOR"]?>">&nbsp</div></td>
                                                                <?if (strlen($value["PIC"])>0 && $arParams["VARIANT_IMAGE_POSITION"]==2):?>
                                                                        <td style="padding: 0px 5px 5px 15px" valign="center" align="center"><img src="<?=$value["PIC"]?>" /><p><?=$value["PIC_DESCRIPTION"]?></p></td>
                                                                <?endif;?>
                                                        </tr>
                                        <?}?>
                        </table>
        <?endif;?>
        <p class="alx_vote_total"><?=GetMessage("TOTAL_VOTES")?><b><?=$arResult["VOTE_PROP"]["TOTALNUMBEROFVOTES"]?></b></p>
<hr />
<?
// Anwer *******************
elseif ($arResult["VOTE_PROP"]["VOTE_FLAG"] != 1):?>
<?$WidthTD = (intval(100/count($arResult["VAR"])))."%";?>
        <p class="alx_vote_title"><b><?=$arParams["STRING_VOTE"]?></b></p>
                <?if ($arParams["SHOW_IMAGE"]==1):?>
                        <table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0" class="alx_vote_res" cellpadding="4">
                                <tr>
                                        <?if (strlen($arResult["PREVIEW_PICTURE"]) > 0):?>
                                                <td align="left" valign="top" width="1%"><img src="<?=$arResult["PREVIEW_PICTURE"]?>" /></td>
                                        <?endif;?>
                                        <td align="left" valign="top" class="alx_preview_text"><?if ($arParams["SHOW_IMAGE"]==1) echo $arResult["PREVIEW_TEXT"]?></td>
                                </tr>
                        </table>
                <?endif;?>
        <p class="alx_vote_name"><b><?=$arResult['NAME']?></b></p>
        <?if ($arParams["VARIANT_IMAGE_POSITION"]==1):?>
                        <form name="vote_Form[<?=$rand?>]" method="post" action="">
                                        <input type="hidden" name="ok_vote" value="<?=$arResult["RAND"]?>" />
                                        <table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0"><tr>
                                                        <?foreach ($arResult["VAR"] as $key => $value){?>
                                                                        <?if (strlen($value["PIC"]) > 0):?>
                                                                                <td align="center" valign="bottom" width="<?=$WidthTD?>"><label for="<?=($rand.$key)?>"><img src="<?=$value["PIC"]?>" /></label>
                                                                                <p><label for="<?=($rand.$key)?>"><?=$value["PIC_DESCRIPTION"]?></label></p>
                                                                                </td>
                                                                        <?else:?><td width="<?=$WidthTD?>" &nbsp </td>
                                                                        <?endif;?>
                                                        <?}?></tr>
                                                        <tr>
                                                        <?foreach ($arResult["VAR"] as $key => $value):?>
                                                                        <td align="center">
                                                                                        <?if($arResult["MULTI"] == "Y"):?>
                                                                                                        <input type="checkbox" name="mood_<?=$arResult["RAND"]?>[<?=$key?>]" value="1" id="<?=($rand.$key)?>" /> <label for="<?=($rand.$key)?>"><br /><?=$value["VALUE"]?></label>
                                                                                                        </td>
                                                                                        <?else:?>
                                                                                                        <input type="radio" name="mood_<?=$arResult["RAND"]?>" value="<?=$key?>" id="<?=($rand.$key)?>" /> <label for="<?=($rand.$key)?>"><br /><?=$value["VALUE"]?></label>
                                                                                                        </td>
                                                                                        <?endif;?>
                                                        <?endforeach;?></tr>
                                        </table>
                                        <input type="hidden" name="rand" value="<?=$arResult["RAND"]?>" />
                                        <p><input type="submit" value="<?=GetMessage("DO_VOTE")?>" /></p>
                                </form>
        <?elseif($arParams["VARIANT_IMAGE_POSITION"]==2):?>
                <form name="vote_Form[<?=$rand?>]" method="post" action="">
                                        <input type="hidden" name="ok_vote" value="<?=$arResult["RAND"]?>" />
                                        <table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0">
                                                <?foreach ($arResult["VAR"] as $key => $value):?>
                                                <tr><td>
                                                                <?if($arResult["MULTI"] == "Y"):?>
                                                                                <input type="checkbox" name="mood_<?=$arResult["RAND"]?>[<?=$key?>]" value="1" id="<?=($rand.$key)?>" /> <label for="<?=($rand.$key)?>"><?=$value["VALUE"]?></label>
                                                                                </td>
                                                                                <?if (strlen($value["PIC"]) > 0):?>
                                                                                                <td align="center" valign="bottom"><label for="<?=($rand.$key)?>"><img src="<?=$value["PIC"]?>" /></label>
                                                                                                <p><label for="<?=($rand.$key)?>"><?=$value["PIC_DESCRIPTION"]?></label></p><br />
                                                                                                </td>
                                                                                <?endif;?>
                                                                <?else:?>
                                                                                <input type="radio" name="mood_<?=$arResult["RAND"]?>" value="<?=$key?>" id="<?=($rand.$key)?>" /> <label for="<?=($rand.$key)?>"><?=$value["VALUE"]?><br /></label>
                                                                                </td>
                                                                                <?if (strlen($value["PIC"]) > 0):?>
                                                                                                <td align="center" valign="bottom"><label for="<?=($rand.$key)?>"><img src="<?=$value["PIC"]?>" /></label>
                                                                                                <p><label for="<?=($rand.$key)?>"><?=$value["PIC_DESCRIPTION"]?></label></p><br />
                                                                                                </td>
                                                                                <?endif;?>
                                                                <?endif;?>
                                                </tr>
                                                <?endforeach;?>
                                        </table>
                                        <input type="hidden" name="rand" value="<?=$arResult["RAND"]?>" />
                                        <p><input type="submit" value="<?=GetMessage("DO_VOTE")?>" /></p>
                </form>
                <hr />
        <?elseif ($arParams["VARIANT_IMAGE_POSITION"]==0):?>
                <form name="vote_Form[<?=$rand?>]" method="post" action="">
                                        <input type="hidden" name="ok_vote" value="<?=$arResult["RAND"]?>" />
                                        <table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0">
                                                <?foreach ($arResult["VAR"] as $key => $value):?>
                                                        <tr><td>
                                                        <?if ($arResult["MULTI"] == "Y"):?>
                                                                        <input type="checkbox" name="mood_<?=$arResult["RAND"]?>[<?=$key?>]" value="1" id="<?=($rand.$key)?>" /> <label for="<?=($rand.$key)?>"><?=$value["VALUE"]?></label>
                                                                        </td>
                                                        <?else:?>
                                                                        <input type="radio" name="mood_<?=$arResult["RAND"]?>" value="<?=$key?>" id="<?=($rand.$key)?>" /> <label for="<?=($rand.$key)?>"><?=$value["VALUE"]?><br /></label>
                                                                        </td>
                                                        <?endif;?>
                                                <?endforeach;?>
                                        </table>
                                        <input type="hidden" name="rand" value="<?=$arResult["RAND"]?>" />
                                        <p><input type="submit" value="<?=GetMessage("DO_VOTE")?>" /></p>
                </form>
                <hr />
        <?endif;?>
<?elseif ($arResult["VOTE_PROP"]["VOTE_FLAG"] == 1 && $arParams["SHOW_RESULTS"] != 1):?>
<?=GetMessage("VOTED")?>
<?endif;?>
</div>
</div>