<?php

/**
 * Данный класс применим на странице офрмления заказа, то есть тогда, <b>когда ORDER_ID еще не известен</b>.<br>
 * Поэтому на странице оплаты или в персональном разделе его не стоит использовать<br>
 * <br>
 * Product - это товар из инфоблока каталога<br>
 * Record - это запись в корзине<br>
 */
class COrderExt
{

    private static $request;
    private static $server;
    private static $isPost;
    private static $arPostedValues;
    private static $arOrderProps;
    private static $arTotal;
    private static $arBasketItems;
    private static $prepayValue;

    public function getPrice($type = 'total', $print = true)
    {
        $iPrice = self::$arTotal['ORDER_TOTAL_PRICE'];

        if ($type == 'prepay')
        {
            $koeff  = (int) str_replace("PREPAY", "", self::$prepayValue);
            if (empty($koeff)) $koeff  = 100;
            $iPrice = $iPrice * $koeff / 100;
        }

        return $print ? printPrice($iPrice) : $iPrice;
    }

    public function getQuantity()
    {
        $iQuantity = 0;

        foreach (self::$arBasketItems as $arBasketItem)
        {
            $iQuantity += $arBasketItem['QUANTITY'];
        }

        return $iQuantity;
    }

    public function getDeliveryCost($addQuantity = false)
    {
        $arItems    = $this->getItems(true);
        $tiersCount = 0;
        foreach ($arItems as $item)
        {
            if ($item['IBLOCK_CODE'] == TIRES_IB_CODE)
            {
                $tiersCount += $item['QUANTITY'];
            }
        }
        $delivery = $tiersCount >= 4 ? 0 : 500;
        return $addQuantity ? array(
            'delivery'       => $delivery,
            'quantity_tiers' => $tiersCount
                ) : $delivery;
    }

    public function getItems($includeExtraData = false)
    {
        $arItems = self::$arBasketItems;

        if ($includeExtraData)
        {
            foreach ($arItems as &$item)
            {
                $tree                      = $this->getTreeToRoot($item['PRODUCT_ID']);
                $item['ROOT_SECTION_ID']   = $tree[0]['ID'];
                $item['ROOT_SECTION_CODE'] = $tree[0]['CODE'];
                $item['IBLOCK_CODE']       = $tree[0]['IBLOCK_CODE'];
            }
        }

        return $arItems;
    }

    private function getTreeToRoot($id)
    {
        $tree = array();
        if ($e    = CIBlockElement::GetByID($id)->Fetch())
        {
            $sId = $e['IBLOCK_SECTION_ID'];
        }
        else
        {
            $sId = $id;
        }

        while ($sId)
        {
            $s      = CIBlockSection::GetByID($sId)->Fetch();
            $tree[] = $s;
            $sId    = $s['IBLOCK_SECTION_ID'];
        }

        return array_reverse($tree);
    }

    public function getDeliveryDate($iBuyerStore)
    {
        if (isBot())
        {
            return '';
        }

        $BASKET_DATA = \CBasketExt::getBasket(false, false, false, $iBuyerStore);
        $arRecords   = $BASKET_DATA["RECORDS"];

        //printrau($arRecords);

        $maxDeliveryDate = null;
        foreach ($arRecords as $arRecord)
        {
            $deliveryDate = $arRecord['DELIVERY_DATE_BASKET'];

            if ($deliveryDate === null || $deliveryDate === false)
            {
                $maxDeliveryDate = null;
                break;
            }

            if ($maxDeliveryDate === null || $maxDeliveryDate < $deliveryDate)
            {
                $maxDeliveryDate = $deliveryDate;
            }
        }

        if ($maxDeliveryDate === 0)
        {
            return 'Готов к выдаче';
        }
        elseif ($maxDeliveryDate !== null)
        {
            return FormatDateFromDB(date('d.m.Y', strtotime("+$maxDeliveryDate days")), "DD MMMM YYYY");
        }
        else
        {
            return 'Неизвестно';
        }
    }

    public function showProps($arExcludeGroups = array(), $arIncludeGroups = array(), $description = false)
    {
        $arProps = self::$arOrderProps['properties'];

        $arGroupedProperties = self::getGroupedProperties($arExcludeGroups, $arIncludeGroups);
        $arGroupedProps      = $arGroupedProperties[0];
        $arPropsId           = $arGroupedProperties[1];
        $arFullProps         = self::getFullProperies($arPropsId);

        foreach ($arGroupedProps as $arOrderProp):
            $arGroup = $arOrderProp['GROUP'];
            $arProps = $arOrderProp['PROPS'];
            if (count($arProps)):
                ?>
                <div class="order-block order-props-block" data-property-group="<?= $arGroup['ID'] ?>">
                    <div class="order-block-title"><?= $arGroup['NAME'] ?></div>

                    <?
                    foreach ($arProps as $arProp):
                        if ($arProp['PROPS_GROUP_ID'] != $arGroup['ID']) continue;

                        if ($arProp['CODE'] == 'NOTIFICATION')
                        {
                            $arProp['TYPE']     = 'ENUM';
                            $arProp['MULTIPLE'] = 'Y';

                            $arFullProps[$arProp['ID']]['OPTIONS'] = array(
                                array('NAME' => 'по SMS', 'VALUE' => 'SMS',),
                                array('NAME' => 'по E-mail', 'VALUE' => 'EMAIL',),
                            );
                        }

                        if ($arProp['CODE'] == 'SUBSCRIBE')
                        {
                            $arProp['TYPE']     = 'ENUM';
                            $arProp['MULTIPLE'] = 'Y';

                            $arFullProps[$arProp['ID']]['OPTIONS'] = array(
                                array('NAME' => 'Согласие на SMS рассылку', 'VALUE' => 'SMS',),
                                array('NAME' => 'Согласие на E-mail рассылку', 'VALUE' => 'EMAIL',),
                            );
                        }
                        ?>

                        <? if ($arProp['TYPE'] == 'LOCATION'): ?>

                        <? endif; ?>

                        <? if ($arProp['TYPE'] == 'STRING'): ?>
                            <? self::showPropsString($arProp) ?>
                        <? endif; ?>

                        <? if ($arProp['TYPE'] == 'ENUM' && $arProp['MULTIPLE'] == 'Y'): ?>
                            <? self::showPropsCheckBox($arProp, $arFullProps[$arProp['ID']]) ?>
                        <? endif; ?>

                        <? if ($arProp['TYPE'] == 'ENUM' && $arProp['MULTIPLE'] != 'Y'): ?>
                            <? self::showPropsRadio($arProp, $arFullProps[$arProp['ID']]) ?>
                        <? endif; ?>
                    <? endforeach; ?>

                    <? if ($arGroup['ID'] == 1 || $arGroup['ID'] == 4): ?>
                        <div class="order-subscribe">
                            <?
                            //группы 9 и 10 - Хотите получать новости и акции от вк сервис?
                            $this->showProps(array(), array(9, 10));
                            ?>
                        </div>
                    <? endif; ?>

                    <? if (!empty($description)): ?>
                        <div class="order-block-description2"><?= $description ?></div>
                    <? endif; ?>
                </div>
                <?
            endif;
        endforeach;
    }

    private static function showPropsString($arProp)
    {
        global $USER;
        $CODE = $arProp['CODE'];

        if ($CODE == "ZIP" || $CODE == "CITY" || $CODE == "ADDRESS")
        {
            return;
        }

        $CAPTION = $arProp['NAME'];
        if ($arProp['REQUIRED'] == "Y") $CAPTION .= "*";
        $VALUE   = self::$isPost && !empty(self::$arPostedValues[$arProp['ID']]) ? self::$arPostedValues[$arProp['ID']] : $arProp['VALUE'][0];
        ?>
        <div class="order-props-block-input order-props-block-input--<?= $CODE ?>">
            <span
                class="<?= !empty($VALUE) ? "active" : "" ?> "
                onclick="Order.onClickPlaceholder(this)"
                ><?= $CAPTION ?></span>
            <input
                type="text"
                data-property-code="<?= $CODE ?>"
                name="ORDER_PROP_<?= $arProp['ID'] ?>"
                id="<?= $CODE ?>_PROP"
                placeholder="<?= $CAPTION ?>"
                title="<?= $arProp['NAME'] ?>"
                value="<?= htmlspecialcharsbx($VALUE) ?>"
                onfocus="Order.onInputFocus(this)"
                onblur="Order.onInputBlur(this)"
                <?= $arProp['REQUIRED'] == "Y" ? 'required="required"' : '' ?>
                <?= $CODE == "PHONE" ? 'data-type="phone"' : '' ?>
                <?= $CODE == "EMAIL" ? 'data-type="email"' : '' ?>
                />

            <? if ($CODE == "PHONE" || $CODE == "EMAIL"): ?>
                <div class="order-props-block-input-enter js-enter-link">
                    <div>Этот <?= $arProp['CODE'] == "PHONE" ? "номер" : "e-mail" ?> уже зарегистрирован.</div>
                    <? if (!$USER->IsAuthorized()): ?>
                        <a href="<?= PATH_AUTH ?>" title="Войти">Войти на сайт</a>
                    <? endif; ?>
                </div>
            <? endif; ?>
        </div>
        <?
    }

    private static function showPropsCheckBox($arProp, $arFullProp)
    {
        if (!empty($arProp['DESCRIPTION'])):
            ?>
            <div class="order-block-description"><?= $arProp['DESCRIPTION'] ?></div>
            <?
        endif;

        foreach ($arFullProp['OPTIONS'] as $arOption):
            $sOptionCode        = $arOption['VALUE'];
            $sOptionName        = $arOption['NAME'];
            $sOptionDescription = $arOption['DESCRIPTION'];

            $selected = false;

            if (self::$isPost && !empty(self::$arPostedValues[$arProp['ID']]))
            {
                if ($sOptionCode == "SMS" || $sOptionCode == "EMAIL")
                {
                    $selected = strstr(self::$arPostedValues[$arProp['ID']], $sOptionCode);
                }
                else
                {
                    $selected = in_array($sOptionCode, self::$arPostedValues[$arProp['ID']]);
                }
            }
            else
            {
                if ($sOptionCode == "SMS" || $sOptionCode == "EMAIL")
                {
                    $selected = strstr($arProp['VALUE'][0], $sOptionCode);
                }
                else
                {
                    $selected = in_array($sOptionCode, $arProp['VALUE']);
                }
            }
            ?>
            <button
                class="order-checkbox <?= $selected == true ? "selected" : "" ?>"
                data-multiple="<?= $arProp['MULTIPLE'] ?>"
                data-property-code="<?= $sOptionCode ?>"
                data-property-value="<?= $sOptionName ?>"
                name="ORDER_PROP_<?= $arProp['ID'] ?>"
                onclick="Order.setCheckbox(event, this)"
                >
                <i></i><span>
                    <?= $sOptionName ?>
                </span>

                <? if (!empty($sOptionDescription)): ?>
                    <div class="order-checkbox-description"><?= $sOptionDescription ?></div>
                <? endif; ?>
            </button>
            <?
        endforeach;
    }

    private static function showPropsRadio($arProp, $arFullProp)
    {
        if (!empty($arProp['DESCRIPTION'])):
            ?>
            <div class="order-block-description"><?= $arProp['DESCRIPTION'] ?></div>
            <?
        endif;

        foreach ($arFullProp['OPTIONS'] as $arOption):
            $sOptionCode        = $arOption['VALUE'];
            $sOptionName        = $arOption['NAME'];
            $sOptionDescription = $arOption['DESCRIPTION'];

            $selected = self::$isPost && !empty(self::$arPostedValues[$arProp['ID']]) ?
                    in_array($sOptionCode, self::$arPostedValues[$arProp['ID']]) :
                    in_array($sOptionCode, $arProp['VALUE']);
            ?>
            <button
                class="order-radio <?= $selected ? "selected" : "" ?>"
                data-multiple="<?= $arProp['MULTIPLE'] ?>"
                data-property-code="<?= $sOptionCode ?>"
                data-property-value="<?= $sOptionName ?>"
                name="ORDER_PROP_<?= $arProp['ID'] ?>"
                onclick="Order.setRadio(event, this)"
                >
                <i></i><span>
                    <?= $sOptionName ?>
                </span>

                <? if (!empty($sOptionDescription)): ?>
                    <mark class="order-radio-description"><?= $sOptionDescription ?></mark>
                    <? endif; ?>
            </button>
            <?
        endforeach;
    }

    function getFullProperies($arPropsId = false)
    {
        //из компонента приходят не все данные о свойствах. Поэтому здесь получаем полные данные о свойствах
        $arFullProps = array();
        $obLists     = \CSaleOrderProps::GetList(array(), array('ID' => $arPropsId), false, false, array("*"));
        while ($arFetch     = $obLists->Fetch())
        {
            $arFullProps[$arFetch['ID']] = $arFetch;

            if (!self::$isPost)
            {
                if ($arFetch['ID'] == PREPAY_PROP_ID_FIZ_LICO || $arFetch['ID'] == PREPAY_PROP_ID_UR_LICO)
                {
                    if (!empty($arFetch['DEFAULT_VALUE']))
                    {
                        self::$prepayValue = $arFetch['DEFAULT_VALUE']; //PREPAY100 || PREPAY10 || etc.
                    }
                    elseif (!empty($arFetch['DEFAULT_VALUE_ORIG']))
                    {
                        self::$prepayValue = $arFetch['DEFAULT_VALUE_ORIG']; //PREPAY100 || PREPAY10 || etc.
                    }
                }
            }

            $db_vars = \CSaleOrderPropsVariant::GetList(($by      = "SORT"), ($order   = "ASC"), Array("ORDER_PROPS_ID" => $arFetch["ID"]));
            while ($vars    = $db_vars->Fetch())
            {
                $arFullProps[$arFetch['ID']]['OPTIONS'][] = $vars;
            }
        }

        return $arFullProps;
    }

    private function getGroupedProperties($arExcludeGroups, $arIncludeGroups)
    {
        $arGroups = self::$arOrderProps['groups'];
        $arProps  = self::$arOrderProps['properties'];

        $arResult  = array();
        $arPropsId = array();
        //формируем список свойств заказа
        foreach ($arGroups as $arGroup)
        {
            if (!empty($arExcludeGroups) && in_array($arGroup['ID'], $arExcludeGroups)) continue;
            if (!empty($arIncludeGroups) && !in_array($arGroup['ID'], $arIncludeGroups)) continue;

            $arResult[$arGroup['ID']]['GROUP'] = $arGroup;

            foreach ($arProps as $arProp)
            {
                if ($arProp['PROPS_GROUP_ID'] != $arGroup['ID']) continue;

                $arResult[$arGroup['ID']]['PROPS'][] = $arProp;
                $arPropsId[]                         = $arProp['ID'];
            }
        }

        return array($arResult, $arPropsId);
    }

    private static $_instance;

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    public static function get($arResult)
    {
        if (!is_object(self::$_instance))
        {
            self::$_instance = new self;
            self::init($arResult);
        }
        return self::$_instance;
    }

    private static function init($arResult)
    {
        //printra($arResult);
        $context              = \Bitrix\Main\Application::getInstance()->getContext();
        self::$request        = $context->getRequest();
        self::$server         = $context->getServer();
        self::$isPost         = self::$request->isPost();
        self::$arPostedValues = self::getPropertyValuesFromRequest();
        self::$arOrderProps   = $arResult['JS_DATA']['ORDER_PROP'];
        self::$arTotal        = $arResult['JS_DATA']['TOTAL'];
        self::$arBasketItems  = $arResult['BASKET_ITEMS'];
    }

    /**
     * Значения свойств заказа на странице фофрмления заказа
     * @return type
     */
    private static function getPropertyValuesFromRequest()
    {
        $postedProperties = array();
        //dmp(self::$request);

        foreach (self::$request as $k => $v)
        {
            if (strpos($k, "ORDER_PROP_") !== false)
            {
                if (strpos($k, "[]") !== false)
                        $orderPropId = intval(substr($k, strlen("ORDER_PROP_"), strlen($k) - 2));
                else $orderPropId = intval(substr($k, strlen("ORDER_PROP_")));

                if ($orderPropId > 0) $postedProperties[$orderPropId] = $v;

                if (self::$isPost)
                {
                    if ($orderPropId == PREPAY_PROP_ID_FIZ_LICO || $orderPropId == PREPAY_PROP_ID_UR_LICO)
                    {
                        if (!empty($v[0]))
                        {
                            self::$prepayValue = $v[0]; //PREPAY100 || PREPAY10 || etc.
                        }
                    }
                }
            }
        }

        return $postedProperties;
    }

    /**
     * возвращает массив целей для метрики, если в заказе есть соответствубщие акционные товары
     */
    public function getActionGoals()
    {
        $arGoals       = array();
        $arBasketItems = \CBasketExt::getBasket();

        foreach ($arBasketItems["RECORDS"] as $arItem)
        {
            if (!empty($arItem["ACTION_GOAL_BUY"]) && !in_array($arItem["ACTION_GOAL_BUY"], $arGoals))
            {
                $arGoals[] = $arItem["ACTION_GOAL_BUY"];
            }
        }

        return $arGoals;
    }

}
