<?php

class CSaleExt
{

    private static $request;
    private static $server;
    private static $isPost;

    /**
     * удаляет все оплаты для заказа
     */
    public static function clearOrderPayments($iOrderId)
    {
        $order             = \Bitrix\Sale\Order::load($iOrderId);
        $paymentCollection = $order->getPaymentCollection();

        foreach ($paymentCollection as $payment)
        {
            $payment->delete();
        }

        $order->save();
    }

    /**
     * Разделяет оплату заказа на две части
     * 
     * @see /bitrix/modules/sale/admin/order_payment_edit.php:83
     * @see /bitrix/modules/sale/lib/helpers/admin/blocks/orderpayment.php:900
     * @see https://mrcappuccino.ru/blog/post/work-with-order-bitrix-d7
     * 
     */
    public static function splitOrderPayments($iOrderId)
    {
        $order       = \Bitrix\Sale\Order::load($iOrderId);
        $prePayKoeff = self::getOrderPrepayKoef($iOrderId);

        $price       = $order->getPrice(); // Сумма заказа
        $pricePrePay = ceil($price * $prePayKoeff / 100); //сумма предоплаты
        $sumPaid     = $order->getSumPaid(); // Оплаченная сумма

        $isPaid     = $order->isPaid(); // true, если оплачен
        $isCanceled = $order->isCanceled();

        $status = $order->getField('STATUS_ID');

        if ($isPaid || $isCanceled || $prePayKoeff == 100)
        {
            return false;
        }

        if ($sumPaid == $pricePrePay)
        {
            //предоплата уже оплачена
            return false;
        }

        if ($status != "N")
        {
            //статус должен быть "Принят, ожидается оплата" (N)
            return false;
        }

        $result          = new \Bitrix\Sale\Result();
        $data['PAYMENT'] = array();

        $payment['PAID']            = 'N';
        $payment['IS_RETURN']       = 'N';
        $payment['ORDER_STATUS_ID'] = 'N';

        //сперва необходимо удалить все текущие оплаты для заказа
        self::clearOrderPayments($iOrderId);

        //теперь необходимо создать две оплаты - предоплата и остаток
        $psServiceE = \Bitrix\Sale\PaySystem\Manager::getObjectById(EPAYMENT_ID); //предоплата (онлайн)
        $psServiceP = \Bitrix\Sale\PaySystem\Manager::getObjectById(CPAYMENT_ID); //оплата остатка (банк)

        $arPayments = array(
            array(
                //предоплата
                'PAY_SYSTEM_ID'   => EPAYMENT_ID,
                'COMMENTS'        => 'prepay',
                'PAY_SYSTEM_NAME' => $psServiceE->getField('NAME'),
                'SUM'             => $pricePrePay,
                'DATE_BILL'       => new \Bitrix\Main\Type\DateTime(),
            ),
            array(
                //оплата остатка
                'PAY_SYSTEM_ID'   => CPAYMENT_ID,
                'COMMENTS'        => 'mainpay',
                'PAY_SYSTEM_NAME' => $psServiceP->getField('NAME'),
                'SUM'             => $price - $pricePrePay,
                'DATE_BILL'       => new \Bitrix\Main\Type\DateTime(),
            )
        );

        $paymentCollection = $order->getPaymentCollection();

        foreach ($arPayments as $paymentFields)
        {
            $paymentItem = $paymentCollection->createItem();

            if ($result->isSuccess())
            {
                $paymentItem->setFields($paymentFields);

                $data['PAYMENT'][] = $paymentItem;
                $result->setData($data);
            }

            if ($result->isSuccess())
            {
                //сохраням
                $order->save();
            }
        }

        if ($result->isSuccess())
        {
            //ставим статус "Принят, ожидается предооплата" (M)
            $order->setField('STATUS_ID', 'M');

            //сохраням
            $order->save();
        }
    }

    public static function getOrderPoperties($iOrderId)
    {
        $arProps  = array();
        $db_props = \CSaleOrderPropsValue::GetOrderProps($iOrderId);
        while ($arFetch  = $db_props->Fetch())
        {
            $arProps[$arFetch['CODE']] = $arFetch;
        }

        return $arProps;
    }

    public static function getOrderPrepayKoef($iOrderId)
    {
        $prePay      = false;
        $prePayKoeff = 100;

        $arProps = self::getOrderPoperties($iOrderId);

        if (!empty($arProps['PREPAY']['VALUE']))
        {
            $arPrepayValue = @unserialize($arProps['PREPAY']['VALUE']);

            if (!empty($arPrepayValue) && is_array($arPrepayValue))
            {
                $prePay = $arPrepayValue[0];
            }
        }

        if (!empty($prePay))
        {
            $prePayKoeff = (int) str_replace("PREPAY", "", $prePay);
            if (empty($prePayKoeff)) $prePayKoeff = 100;
        }

        return $prePayKoeff;
    }

    public static function getLocationPopertyId($PERSON_TYPE_ID)
    {
        $obList = \CSaleOrderProps::GetList(
                        array("SORT" => "ASC"), array(
                    "PERSON_TYPE_ID" => $PERSON_TYPE_ID,
                    "ACTIVE"         => "Y",
                    "IS_LOCATION"    => "Y"
                        ), false, false, array()
        );

        if ($arFetch = $obList->Fetch())
        {
            return $arFetch["ID"];
        }

        return false;
    }
    
    public static function getPropertyArrayByCode($PERSON_TYPE_ID, $CODE)
    {
        $obList = \CSaleOrderProps::GetList(
                        array("SORT" => "ASC"), array(
                    "=PERSON_TYPE_ID" => $PERSON_TYPE_ID,
                    "=CODE"           => $CODE,
                    "ACTIVE"          => "Y",
                        ), false, false, array()
        );

        if ($arFetch = $obList->Fetch())
        {
            return $arFetch;
        }

        return false;
    }

    public static function getPropertyIdByCode($PERSON_TYPE_ID, $CODE)
    {
        $obList = \CSaleOrderProps::GetList(
                        array("SORT" => "ASC"), array(
                    "=PERSON_TYPE_ID" => $PERSON_TYPE_ID,
                    "=CODE"           => $CODE,
                    "ACTIVE"          => "Y",
                        ), false, false, array()
        );

        if ($arFetch = $obList->Fetch())
        {
            return $arFetch["ID"];
        }

        return false;
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
        $context       = \Bitrix\Main\Application::getInstance()->getContext();
        self::$request = $context->getRequest();
        self::$server  = $context->getServer();
        self::$isPost  = self::$request->isPost();
    }

}
