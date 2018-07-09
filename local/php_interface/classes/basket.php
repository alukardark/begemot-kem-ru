<?php

/**
 * Product - это товар из инфоблока каталога
 * Record - это запись в корзине
 */
class CBasketExt
{

    public static function GetOptimalPrice($PRODUCT_ID, $QUANTITY = 1, $arCoupons = false)
    {
        $BASE_PRICE_ID = basePrice();

        $arPriceTable = CatalogGetPriceTableEx($PRODUCT_ID, 0, array($BASE_PRICE_ID), "Y");
        $price        = $arPriceTable["MATRIX"][$BASE_PRICE_ID][0]["PRICE"];

        $arPrices       = array(
            array(
                'ID'               => $BASE_PRICE_ID,
                'PRICE'            => $price,
                'CURRENCY'         => BASE_CURRENCY,
                'CATALOG_GROUP_ID' => $BASE_PRICE_ID,
            ),
        );
        $arOptimalPrice = \CCatalogProduct::GetOptimalPrice($PRODUCT_ID, $QUANTITY, false, "N", $arPrices, SITE_ID, $arCoupons);

        return $arOptimalPrice;
    }

    public static function getDiscountList2($PRODUCT_ID = null)
    {
        $arCoupons = array();

        $couponIterator = \Bitrix\Sale\Internals\DiscountTable::getList(array(
                    'filter' => array()
        ));
        while ($coupon         = $couponIterator->fetch())
        {
            //$coupon["DATA"] = \Bitrix\Sale\DiscountCouponsManager::getData($coupon["COUPON"]);
            //$coupon["CHECKED"] = \Bitrix\Sale\DiscountCouponsManager::isEntered($coupon["COUPON"]);
            $arCoupons[] = $coupon;
        }

        return $arCoupons;
    }

    public static function getDiscountList($PRODUCT_ID)
    {

        $arPrice = self::GetOptimalPrice($PRODUCT_ID);

        $arDiscountList = array();

        if (empty($arPrice['DISCOUNT_LIST']) && !empty($arPrice['DISCOUNT']) && is_array($arPrice['DISCOUNT']))
        {
            $arPrice['DISCOUNT_LIST'] = array($arPrice['DISCOUNT']);
        }

        if (!empty($arPrice['DISCOUNT_LIST']))
        {
            foreach ($arPrice['DISCOUNT_LIST'] as &$arOneDiscount)
            {
                $arDiscountList[] = \CCatalogDiscount::getDiscountDescription($arOneDiscount);
            }
            unset($arOneDiscount);
        }

        return $arDiscountList;
    }

    public static function addRecord($PRODUCT_ID)
    {
        $arBasePrice    = \CPrice::GetBasePrice($PRODUCT_ID);
        $arProductInfo  = self::getProductInfo($PRODUCT_ID);
        $arDiscountList = self::getDiscountList($PRODUCT_ID);

        //printrau($arDiscountList);

        $arFields = array(
            "PRODUCT_ID"       => $PRODUCT_ID,
            "PRODUCT_PRICE_ID" => $arBasePrice['ID'],
            "PRICE"            => $arBasePrice['PRICE'],
            "DISCOUNT_LIST"    => $arDiscountList,
            "CURRENCY"         => $arBasePrice['CURRENCY'],
            "QUANTITY"         => 1,
            "LID"              => LANG,
            "DELAY"            => "N",
            "CAN_BUY"          => "Y",
            "NAME"             => $arProductInfo['FIELDS']['NAME'],
            "MODULE"           => "catalog",
            "NOTES"            => "",
            "DETAIL_PAGE_URL"  => \CCatalogExt::getProductUrl($arProductInfo['FIELDS']),
            "PRODUCT_XML_ID"   => $arProductInfo['FIELDS']['XML_ID'],
            "CATALOG_XML_ID"   => $arProductInfo['FIELDS']['IBLOCK_EXTERNAL_ID'],
        );

        if (\CSaleBasket::Add($arFields))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function setRecordQuantity($PRODUCT_ID, $QUANTITY)
    {
        $BASKET_DATA = \CBasketExt::getBasket();
        $arRecord    = $BASKET_DATA["RECORDS"][$PRODUCT_ID];

        if (empty($arRecord)) return false;

        return \CSaleBasket::Update($arRecord['ID'], array("QUANTITY" => $QUANTITY));
    }

    public static function deleteRecord($PRODUCT_ID)
    {
        $BASKET_DATA = \CBasketExt::getBasket();
        $arRecord    = $BASKET_DATA["RECORDS"][$PRODUCT_ID];

        if (empty($arRecord)) return false;

        return \CSaleBasket::Delete($arRecord['ID']);
    }

    public static function deleteAllRecords()
    {
        $obRecordList = \CSaleBasket::GetList(array(), array(
                    "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
                    "LID"      => SITE_ID,
                    "ORDER_ID" => "NULL",
                        ), false, false, array("ID"));
        while ($arRecord     = $obRecordList->Fetch())
        {
            \CSaleBasket::Delete($arRecord['ID']);
        }
    }

    /**
     * Список всех активных купонов
     * @return type
     */
    public static function getCoupons()
    {
        $arCoupons = array();

        $couponIterator = \Bitrix\Sale\Internals\DiscountCouponTable::getList(array(
                    'filter' => array("ACTIVE" => "Y", "TYPE" => 4)
        ));
        while ($coupon         = $couponIterator->fetch())
        {
            $coupon["DATA"] = \Bitrix\Sale\DiscountCouponsManager::getData($coupon["COUPON"]);
            //$coupon["CHECKED"] = \Bitrix\Sale\DiscountCouponsManager::isEntered($coupon["COUPON"]);
            $arCoupons[]    = $coupon;
        }

        return $arCoupons;
    }

    public static function getBasket($clear = false, $coupon = false, $delete_coupon = false, $iBuyerStore = false)
    {
        //$coupon = "SL-FM54E-2EQJD7S";
        //$coupon = "SL-FM54E-2EQJD7S";

        if ($clear)
        {
            \Bitrix\Sale\DiscountCouponsManager::clear(true);
        }

        if (!empty($coupon))
        {
            $_POST["coupon"] = $coupon;
        }

        if (!empty($delete_coupon))
        {
            $_POST["delete_coupon"] = $delete_coupon;
        }

        $arPropsValues  = isset($_POST["props"]) ? $_POST["props"] : array();
        $strColumns     = isset($_POST["select_props"]) ? $_POST["select_props"] : "";
        $arColumns      = explode(",", $strColumns);
        $strOffersProps = isset($_POST["offers_props"]) ? $_POST["offers_props"] : "";
        $strOffersProps = explode(",", $strOffersProps);

        \CBitrixComponent::includeComponentClass("bitrix:sale.basket.basket");

        $basket = new \CBitrixBasketComponent();
        $basket->onIncludeComponentLang();

        $basket->weightKoef  = htmlspecialcharsbx(\COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));
        $basket->weightUnit  = htmlspecialcharsbx(\COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
        $basket->columns     = $arColumns;
        $basket->offersProps = $strOffersProps;

        $basket->quantityFloat             = (isset($_POST["quantity_float"]) && $_POST["quantity_float"] == "Y") ? "Y" : "N";
        $basket->countDiscount4AllQuantity = (isset($_POST["count_discount_4_all_quantity"]) && $_POST["count_discount_4_all_quantity"] == "Y") ? "Y" : "N";
        $basket->priceVatShowValue         = (isset($_POST["price_vat_show_value"]) && $_POST["price_vat_show_value"] == "Y") ? "Y" : "N";
        $basket->hideCoupon                = (isset($_POST["hide_coupon"]) && $_POST["hide_coupon"] == "Y") ? "Y" : "N";
        $basket->usePrepayment             = (isset($_POST["use_prepayment"]) && $_POST["use_prepayment"] == "Y") ? "Y" : "N";

        $res = $basket->recalculateBasket($_POST);
        foreach ($res as $key => $value)
        {
            $arRes[$key] = $value;
        }

        $arRes["BASKET_DATA"]                    = $basket->getBasketItems();
        $arRes["BASKET_DATA"]["GRID"]["HEADERS"] = $basket->getCustomColumns();
        $arRes["COLUMNS"]                        = $strColumns;
        $arRes["CODE"]                           = "SUCCESS";

        if (!empty($_POST["coupon"]) && $arRes['VALID_COUPON'] === true)
        {
            if (!empty($arRes['BASKET_DATA']['FULL_DISCOUNT_LIST']))
            {
                global $USER;
                $userId      = $USER instanceof CAllUser ? $USER->getId() : null;
                $giftManager = \Bitrix\Sale\Discount\Gift\Manager::getInstance()->setUserId($userId);

                \Bitrix\Sale\Compatible\DiscountCompatibility::stopUsageCompatible();
                $collections = $giftManager->getCollectionsByBasket(
                        \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), SITE_ID), $arRes['BASKET_DATA']['FULL_DISCOUNT_LIST'], $arRes['BASKET_DATA']['APPLIED_DISCOUNT_LIST']
                );
                \Bitrix\Sale\Compatible\DiscountCompatibility::revertUsageCompatible();
                if (count($collections))
                {
                    $arRes['BASKET_DATA']['NEED_TO_RELOAD_FOR_GETTING_GIFTS'] = true;
                }
            }
        }

        $BASKET_DATA = $arRes["BASKET_DATA"];

        $curCityKey = \Axi::getCityKey();

        $BASKET_DATA["QUANTITY"] = 0;
        $BASKET_DATA["RECORDS"]  = array();

        $BASKET_DISCOUNT_PRICE = 0;
        $BASKET_DISCOUNT       = 0;
        $BASKET_BASE_PRICE     = 0;

        $HIDDEN_DISCOUNTS_IBLOCK_ID = \WS_PSettings::getFieldValue("HIDDEN_DISCOUNTS_IBLOCK_ID", false);

        if (!empty($BASKET_DATA["GRID"]["ROWS"]))
        {
            foreach ($BASKET_DATA["GRID"]["ROWS"] as $row)
            {
                $PRODUCT_ID = $row["PRODUCT_ID"];
                $QUANTITY   = $row["QUANTITY"];
                $IBLOCK_ID  = getIBlockByElement($PRODUCT_ID);

                //проверяем есть ли у товара акция, получаем ее XML_ID и цели
                $arProductActionsGoals  = \CBasketExt::getProductActionsGoals($IBLOCK_ID, $PRODUCT_ID);
                $row["ACTION_XML_ID"]   = $arProductActionsGoals["ACTION_XML_ID"];
                $row["ACTION_GOAL_ADD"] = $arProductActionsGoals["ACTION_GOAL_ADD"];
                $row["ACTION_GOAL_BUY"] = $arProductActionsGoals["ACTION_GOAL_BUY"];

                //$BASE_PRICE     = $row["BASE_PRICE"]; //цена без скидки
                //$DISCOUNT_PRICE = $row["PRICE"]; //цена со скидкой
                //$DISCOUNT       = $row["DISCOUNT_PRICE"]; //сумма скидки

                $arPriceTable   = CatalogGetPriceTableEx($PRODUCT_ID, 0, array(), "Y");
                $BASE_PRICE     = $arPriceTable[MATRIX][RETAIL_PRICE_ID][0]["PRICE"]; //цена без скидки
                $DISCOUNT_PRICE = $arPriceTable[MATRIX][CATALOG_PRICE_ID][0]["PRICE"]; //цена со скидкой
                $DISCOUNT       = $BASE_PRICE - $DISCOUNT_PRICE; //сумма скидки

                if (in_array($IBLOCK_ID, $HIDDEN_DISCOUNTS_IBLOCK_ID))
                {
                    $DISCOUNT   = 0;
                    $BASE_PRICE = $DISCOUNT_PRICE;
                }

                $TOTAL_BASE_PRICE     = $BASE_PRICE * $QUANTITY; //общая цена без скидки
                $TOTAL_DISCOUNT_PRICE = $DISCOUNT_PRICE * $QUANTITY; //общая цена со скидкой
                $TOTAL_DISCOUNT       = $DISCOUNT * $QUANTITY; //общая сумма скидки

                $BASKET_BASE_PRICE     += $TOTAL_BASE_PRICE;
                $BASKET_DISCOUNT_PRICE += $TOTAL_DISCOUNT_PRICE;
                $BASKET_DISCOUNT       += $TOTAL_DISCOUNT;

                $BASKET_DATA["QUANTITY"] += $row["QUANTITY"];

                $row["PRICES"] = array(
                    'BASE_PRICE'           => $BASE_PRICE,
                    'DISCOUNT_PRICE'       => $DISCOUNT_PRICE,
                    'DISCOUNT'             => $DISCOUNT,
                    'TOTAL_BASE_PRICE'     => $TOTAL_BASE_PRICE,
                    'TOTAL_DISCOUNT_PRICE' => $TOTAL_DISCOUNT_PRICE,
                    'TOTAL_DISCOUNT'       => $TOTAL_DISCOUNT,
                    'PRINT'                => array(
                        'BASE_PRICE'           => printPrice($BASE_PRICE),
                        'DISCOUNT_PRICE'       => printPrice($DISCOUNT_PRICE),
                        'DISCOUNT'             => printPrice($DISCOUNT),
                        'TOTAL_BASE_PRICE'     => printPrice($TOTAL_BASE_PRICE),
                        'TOTAL_DISCOUNT_PRICE' => printPrice($TOTAL_DISCOUNT_PRICE),
                        'TOTAL_DISCOUNT'       => printPrice($TOTAL_DISCOUNT),
                    ),
                );

                $row["MAX_QUANTITY"]         = \CCatalogExt::getProductAmountInStores($PRODUCT_ID);
                $row["DELIVERY_DATE"]        = \CCatalogExt::getProductDeliveryDate($PRODUCT_ID, $QUANTITY, $curCityKey, false);
                $row["DELIVERY_DATE_PRINT"]  = \CCatalogExt::getProductDeliveryDate($PRODUCT_ID, $QUANTITY, $curCityKey, true);
                $row["DELIVERY_DATE_BASKET"] = \CCatalogExt::getProductDeliveryDate($PRODUCT_ID, $QUANTITY, $curCityKey, false, $iBuyerStore);

                $BASKET_DATA["RECORDS"][$PRODUCT_ID] = $row;
            }
        }

        $BASKET_DATA["PRICES"] = array(
            'BASE_PRICE'     => $BASKET_BASE_PRICE,
            'DISCOUNT_PRICE' => $BASKET_DISCOUNT_PRICE,
            'DISCOUNT'       => $BASKET_DISCOUNT,
            'PRINT'          => array(
                'BASE_PRICE'     => printPrice($BASKET_BASE_PRICE),
                'DISCOUNT_PRICE' => printPrice($BASKET_DISCOUNT_PRICE),
                'DISCOUNT'       => printPrice($BASKET_DISCOUNT),
            ),
        );

        //unset($BASKET_DATA["COUPON_LIST"]);
        //unset($BASKET_DATA["APPLIED_DISCOUNT_LIST"]);
        //unset($BASKET_DATA["FULL_DISCOUNT_LIST"]);
        //unset($BASKET_DATA["ITEMS"]);
        //unset($BASKET_DATA["GRID"]);
        //printrau($BASKET_DATA);

        return $BASKET_DATA;
    }

    /**
     * для акционных товаров возвращает идентификаторы целей GOAL_ADD и GOAL_BUY для акции и XML_ID акции
     */
    public static function getProductActionsGoals($IBLOCK_ID, $PRODUCT_ID)
    {
        $arResult["ACTION_XML_ID"]   = false;
        $arResult["ACTION_GOAL_ADD"] = false;
        $arResult["ACTION_GOAL_BUY"] = false;

        $obProperty = \CIBlockElement::GetProperty($IBLOCK_ID, $PRODUCT_ID, array(), Array("CODE" => AKTSIYA));
        if ($arNext     = $obProperty->GetNext())
        {
            if (!empty($arNext))
            {
                $arResult["ACTION_XML_ID"] = $arNext["VALUE"];
            }
        }

        if (!empty($arResult["ACTION_XML_ID"]))
        {
            //проверим, есть ли у этой акцйии значения метрик
            $arFilter = array("IBLOCK_ID" => ACTIONS_IB, "ACTIVE" => "Y", "PROPERTY_REF_ACTION" => $arResult["ACTION_XML_ID"]);

            $obList = \CIBlockElement::GetList(array(), $arFilter, false, false, array("PROPERTY_GOAL_ADD", "PROPERTY_GOAL_BUY"));
            while ($arItem = $obList->Fetch())
            {
                if (!empty($arItem["PROPERTY_GOAL_ADD_VALUE"]))
                {
                    $arResult["ACTION_GOAL_ADD"] = $arItem["PROPERTY_GOAL_ADD_VALUE"];
                }

                if (!empty($arItem["PROPERTY_GOAL_BUY_VALUE"]))
                {
                    $arResult["ACTION_GOAL_BUY"] = $arItem["PROPERTY_GOAL_BUY_VALUE"];
                }
            }
        }

        return $arResult;
    }

//    public static function getRecords($coupon = false)
//    {
//        $BASKET_DATA = \CBasketExt::getBasket($coupon);
//        return $BASKET_DATA["GRID"]["ROWS"];
//
////        $arRecords = array();
////
////        $arFilter = array(
////            "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
////            "LID"      => SITE_ID,
////            "ORDER_ID" => "NULL",
////        );
////
////        $obRecordList = \CSaleBasket::GetList(array(), $arFilter);
////        while ($arRecord     = $obRecordList->Fetch())
////        {
////            $arProduct   = self::getProductInfo($arRecord['PRODUCT_ID']);
////            $arRecords[] = array(
////                "RECORD"  => $arRecord,
////                "PRODUCT" => $arProduct,
////            );
////        }
////
////        return $arRecords;
//    }

    /**
     * Возвращает информацию о корзине
     * @return array
     */
//    public static function getSummary()
//    {
//        $arResult['quantity'] = 0;
//
//        $arResult['discount_summ']        = 0;
//        $arResult['price']                = 0;
//        $arResult['price_print']          = "";
//        $arResult['price_discount']       = 0;
//        $arResult['price_discount_print'] = "";
//
//        $obRecordList = \CSaleBasket::GetList(
//                        array(), array(
//                    "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
//                    "LID"      => SITE_ID,
//                    "ORDER_ID" => "NULL",
//                        ), false, false, array(
//                    "ID", "PRODUCT_ID", "PRICE", "QUANTITY",
//                        )
//        );
//        while ($arRecord     = $obRecordList->Fetch())
//        {
//            $PRODUCT_ID = $arRecord['PRODUCT_ID'];
//            $QUANTITY   = $arRecord['QUANTITY'];
//            $arPrice    = self::GetOptimalPrice($PRODUCT_ID, $QUANTITY);
//            //printra($arPrice);
//
//            $arResult['quantity']       += $QUANTITY;
//            $arResult['price']          += $QUANTITY * $arPrice["RESULT_PRICE"]["BASE_PRICE"]; //оригинальная цена единицы товара
//            $arResult['price_discount'] += $QUANTITY * $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"]; //цене единицы товара со всеми скидками
//        }
//
//        $arResult['discount_summ']           = $arResult['price'] - $arResult['price_discount'];
//        $arResult['discount_summ_print']     = printPrice($arResult['discount_summ'], BASE_CURRENCY);
//        $arResult['discount_summ_print_alt'] = printPrice($arResult['discount_summ']);
//
//        $arResult['price_print']     = printPrice($arResult['price'], BASE_CURRENCY);
//        $arResult['price_print_alt'] = printPrice($arResult['price']);
//
//        $arResult['price_discount_print']     = printPrice($arResult['price_discount'], BASE_CURRENCY);
//        $arResult['price_discount_print_alt'] = printPrice($arResult['price_discount']);
//
//        return $arResult;
//    }
//    public static function getPrices($PRODUCT_ID, $QUANTITY = 1)
//    {
//        $arPrice = self::GetOptimalPrice($PRODUCT_ID);
//
//        $BASE_PRICE     = $arPrice["RESULT_PRICE"]["BASE_PRICE"]; //цена без скидки
//        $DISCOUNT_PRICE = $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"]; //цена со скидкой
//        $DISCOUNT       = $arPrice["RESULT_PRICE"]["DISCOUNT"]; //сумма скидки
//
//        $TOTAL_BASE_PRICE     = $BASE_PRICE * $QUANTITY; //общая цена без скидки
//        $TOTAL_DISCOUNT_PRICE = $DISCOUNT_PRICE * $QUANTITY; //общая цена со скидкой
//        $TOTAL_DISCOUNT       = $DISCOUNT * $QUANTITY; //общая сумма скидки
//
//        return array(
//            'BASE_PRICE'           => $BASE_PRICE,
//            'DISCOUNT_PRICE'       => $DISCOUNT_PRICE,
//            'DISCOUNT'             => $DISCOUNT,
//            'TOTAL_BASE_PRICE'     => $TOTAL_BASE_PRICE,
//            'TOTAL_DISCOUNT_PRICE' => $TOTAL_DISCOUNT_PRICE,
//            'TOTAL_DISCOUNT'       => $TOTAL_DISCOUNT,
//            'PRINT'                => array(
//                'BASE_PRICE'           => printPrice($BASE_PRICE),
//                'DISCOUNT_PRICE'       => printPrice($DISCOUNT_PRICE),
//                'DISCOUNT'             => printPrice($DISCOUNT),
//                'TOTAL_BASE_PRICE'     => printPrice($TOTAL_BASE_PRICE),
//                'TOTAL_DISCOUNT_PRICE' => printPrice($TOTAL_DISCOUNT_PRICE),
//                'TOTAL_DISCOUNT'       => printPrice($TOTAL_DISCOUNT),
//            ),
//        );
//    }

    /**
     * Возвращает информацию о записи по ID или CODE продукта
     * @param int $PRODUCT_ID ID или CODE товара
     * @return mixed false или array
     */
//    public static function getRecord($PRODUCT_ID)
//    {
//        $curCityKey = \Axi::getCityKey();
//
//        $arResult['product_id']        = 0; //ID продукта
//        $arResult['record_id']         = 0; //ID записи
//        $arResult['quantity']          = 0; //quantity в корзине
//        $arResult['price']             = 0; //цена за единицу
//        $arResult['total_price']       = 0; //общая цена с учетом quantity в корзине
//        $arResult['price_print']       = "";
//        $arResult['total_price_print'] = "";
//        $arResult['max_quantity']      = 0;
//        $arResult['PRICES']            = array();
//
//        if (!is_numeric($PRODUCT_ID))
//        {
//            $PRODUCT_ID = self::getProductIdByCode($PRODUCT_ID);
//        }
//
//        //доступное количество товара на всех складах
//        $iMaxQuantity = \CCatalogExt::getProductAmountInStores($PRODUCT_ID);
//
//        $arFilter = array(
//            "FUSER_ID"   => \CSaleBasket::GetBasketUserID(),
//            "LID"        => SITE_ID,
//            "ORDER_ID"   => "NULL",
//            "PRODUCT_ID" => intval($PRODUCT_ID),
//        );
//
//        $arSelect     = array("ID", "PRODUCT_ID", "PRICE", "QUANTITY", "DISCOUNT_PRICE", "DISCOUNT_VALUE");
//        $obRecordList = \CSaleBasket::GetList(array(), $arFilter, false, false, $arSelect);
//        if ($arRecord     = $obRecordList->Fetch())
//        {
//            $PRODUCT_ID = $arRecord['PRODUCT_ID'];
//            $QUANTITY   = $arRecord['QUANTITY'];
//
//            $arPrice = self::GetOptimalPrice($PRODUCT_ID);
//
//            $iPrice      = $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
//            $iTotalPrice = $QUANTITY * $iPrice;
//
//            $arResult['PRICES']              = self::getPrices($PRODUCT_ID, $QUANTITY);
//            $arResult['product_id']          = $arRecord['PRODUCT_ID'];
//            $arResult['record_id']           = $arRecord['ID'];
//            $arResult['price']               = $iPrice;
//            $arResult['total_price']         = $iTotalPrice;
//            $arResult['price_print']         = printPrice($iPrice);
//            $arResult['total_price_print']   = printPrice($iTotalPrice);
//            $arResult['max_quantity']        = $iMaxQuantity; //кол-во на складах
//            $arResult['quantity']            = $arRecord['QUANTITY']; //кол-во в корзине
//            $arResult['delivery_date_print'] = \CCatalogExt::getProductDeliveryDate($arRecord['PRODUCT_ID'], $arRecord['QUANTITY'], $curCityKey, true); //срок доставки до текущего города текущего кол-ва товара
//        }
//        else
//        {
//            //такого товара нет в корзине
//            return false;
//        }
//
//        return $arResult;
//    }

    public static function getProductIdByCode($productCode)
    {
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("30day", 0);
        $cachePath = "/ccache_catalog/getProductIdByCode/";
        $cacheID   = "getProductIdByCode" . $productCode;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["productCode"]))
            {
                $productCode = $vars["productCode"];
            }
        }

        if ($lifeTime > 0)
        {
            $arFilter = Array("CODE" => $productCode);
            $arSelect = Array("ID");
            $obList   = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            if ($arFetch  = $obList->Fetch())
            {
                $productCode = $arFetch['ID'];
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "productCode" => $productCode,
            ));
        }

        return $productCode;
    }

    /**
     * Информация о продукте
     * @param int $PRODUCT_ID ID
     * @return mixed false или array
     */
    public static function getProductInfo($PRODUCT_ID)
    {
        $obCache   = \Bitrix\Main\Data\Cache::createInstance();
        $lifeTime  = strtotime("1day", 0);
        $cachePath = "/ccache_catalog/getProductInfo/";
        $cacheID   = "getProductInfo" . $PRODUCT_ID;

        if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
        {
            $vars = $obCache->GetVars();
            if (isset($vars["arResult"]))
            {
                $arResult = $vars["arResult"];
            }
        }

        if ($lifeTime > 0)
        {
            $arResult = false;

            $obElement = \CIBlockElement::GetById($PRODUCT_ID)->GetNextElement(true, false);
            if ($obElement)
            {
                $arFields = $obElement->GetFields();
                $arProps  = $obElement->GetProperties();

                if (!$arFields || !$arProps || $arFields['ACTIVE'] != 'Y')
                {
                    $arResult = false;
                }
                else
                {
                    $arResult = array("FIELDS" => $arFields, "PROPS" => $arProps);
                }
            }

            //кешируем
            $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
            $obCache->EndDataCache(array(
                "arResult" => $arResult,
            ));
        }

        return $arResult;
    }

    private static $_instance;

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    public static function get()
    {
        if (!is_object(self::$_instance))
        {
            self::$_instance = new self;
            self::init();
        }
        return self::$_instance;
    }

    private static function init()
    {
        //self::$sBaseCurrency = CCurrency::GetByID('RUR') ? 'RUR' : 'RUB';
    }

}
