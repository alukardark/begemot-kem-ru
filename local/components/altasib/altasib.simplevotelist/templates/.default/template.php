<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="vote-result">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<p class="alx_vote_res_title"><b><?=$arParams["STRING_RES"]?></b></p>
<?foreach ($arResult["VOTE"] as $key => $value)
	{?>
		<br /><br />
		<?if ($arParams["SHOW_IMAGE"]==1):?>
		<table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0" class="alx_vote_res" cellpadding="4">
			<tr>
			<?if (strlen($arResult["VOTE"][$key]["PREVIEW_PICTURE"]) > 0):?>
			<td align="left" valign="top" width="1%"> <img src="<?=$arResult["VOTE"][$key]["PREVIEW_PICTURE"]?>" /></td>
			<?endif;?>
			<td align="left" valign="top" class="alx_preview_text"><?=$arResult["VOTE"][$key]["PREVIEW_TEXT"]?></td>
			</tr>
		</table>
		<?endif;?>
	<p class="alx_vote_name"><b><?=$arResult["VOTE"][$key]["NAME"]?></b></p>
	<?if ($arParams["VARIANT_IMAGE_POSITION"] == 1):?>
		<table width="<?=$arParams["VOTE_WIDTH"]?>%">
		<tr>
		<?foreach ($arResult["VOTE"][$key]["PIC"] as $key2 => $value2){?>
			<td align="center" valign="center"><img src="<?=$arResult["VOTE"][$key]["PIC"][$key2]?>" /></td>
		<?}?><?unset($value2);?>
		</tr>
		<tr>
		<?foreach ($arResult["VOTE"][$key]["PIC_DESC"] as $key2 => $value2){?>
			<td align="center" valign="center"><?=$arResult["VOTE"][$key]["PIC_DESC"][$key2]?></td>
		<?}?><?unset($value2);?>
		</tr>
		</table>
		<br />
	<?endif;?>
<?if ($arParams["POSITION"]==1):?> 
	<table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0" class="alx_vote_res">
	<?foreach ($arResult["VOTE"][$key]["VALUE"] as $key1 => $value1)
		{?>
			<tr>
			<td class="alx_line_container"><div style="width:<?=$arResult["VOTE"][$key]["PERSENT"][$key1]?>%; height:<?=$arParams["SCALE_HEIGHT"]?>px; background:<?=$arParams["COLOR"]?>">&nbsp</div></td>
			<td align="left" class="alx_vote_ask" >&nbsp <span class="alx_vote_ask_txt"><?if ($arParams["SHOW_VAR"] == 1):?><?=$arResult["VOTE"][$key]["VALUE"][$key1]?></span> | <?endif;?><span class="alx_vote_ask_num">  <?=declOfNum(intval($arResult["VOTE"][$key]["DESCRIPTION"][$key1]), array(GetMessage("GOLOS"), GetMessage("GOLOSA"), GetMessage("GOLOSOV")))?></span></td>
			<?if ($arParams["VARIANT_IMAGE_POSITION"] == 2 && strlen($arResult["VOTE"][$key]["PIC"][$key1]) > 0):?>
				<td valign="center" align="center" style="padding: 0px 5px 5px 15px"><img src="<?=$arResult["VOTE"][$key]["PIC"][$key1]?>" />
				<?if (strlen($arResult["VOTE"][$key]["PIC_DESC"][$key1]) > 0):?>
					<p><?=$arResult["VOTE"][$key]["PIC_DESC"][$key1]?></p>
				<?endif;?>
				</td>
			<?endif;?>
			</tr>
		<?}?>
		<?unset($value1);?>
	</table>
<?elseif ($arParams["POSITION"]==0):?>	
	<table width="<?=$arParams["VOTE_WIDTH"]?>%" border="0" class="alx_vote_res">
	<?foreach ($arResult["VOTE"][$key]["VALUE"] as $key1 => $value1)
		{?>
			<tr>
			<td align="left" class="alx_vote_ask" >&nbsp <span class="alx_vote_ask_txt"><?if ($arParams["SHOW_VAR"] == 1):?><?=$arResult["VOTE"][$key]["VALUE"][$key1]?></span> | <?endif;?><span class="alx_vote_ask_num"><?=declOfNum(intval($arResult["VOTE"][$key]["DESCRIPTION"][$key1]), array(GetMessage("GOLOS"), GetMessage("GOLOSA"), GetMessage("GOLOSOV")))?></span></td>
			</tr>
			<tr>
			<td class="alx_line_container"><div style="width:<?=$arResult["VOTE"][$key]["PERSENT"][$key1]?>%; height:<?=$arParams["SCALE_HEIGHT"]?>px; background:<?=$arParams["COLOR"]?>">&nbsp</div></td>
			<?if ($arParams["VARIANT_IMAGE_POSITION"] == 2 && strlen($arResult["VOTE"][$key]["PIC"][$key1]) > 0):?>
				<td valign="center" align="center" style="padding: 0px 5px 5px 15px"><img src="<?=$arResult["VOTE"][$key]["PIC"][$key1]?>" />
				<?if (strlen($arResult["VOTE"][$key]["PIC_DESC"][$key1]) > 0):?>
					<p><?=$arResult["VOTE"][$key]["PIC_DESC"][$key1]?></p>
				<?endif;?>
				</td>
			<?endif;?>
			</tr>
		<?}?>
		<?unset($value1);?>
	</table>
<?endif;?>
<p><?=GetMessage("TOTAL_VOTES")?><b><?=$arResult["VOTE"][$key]["TOTALNUMBEROFVOTES"]?></b></p>
<div style="width:<?=$arParams["VOTE_WIDTH"]?>%"><hr /></div>
<?}?>
<?unset($value);?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
</div>