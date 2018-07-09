<?php

class SenSMS
{
    public $_phone;
    public $_tpl;
    public $_data;

    public function __construct($phone, $tpl, $data)
    {
        $this->_phone = $phone;
        $this->_tpl = $tpl;
        $this->_data = $data;
    }

    public function send_sms()
    {
        require_once("sms/transport.php");
        $phone = str_replace(array(" ", "-", "+", "(", ")"), "", trim($this->_phone));
        $logsms = $_SERVER["DOCUMENT_ROOT"] . "/log.sms";

        if ($phone) {

            $params = array(
                "source" => "Kvantor",
                "action" => "send", //send or check
                "onlydelivery" => 1,
                "text" => trim($this->{$this->_tpl}())
            );
            //$log    = $params["text"] . " / " . $phone;
            file_put_contents($logsms, $params["text"] . " / " . $phone . "\n", FILE_APPEND);
            $api = new Transport();
            $api->send($params, explode(",", $phone));
            return true;
        }
        file_put_contents($logsms, "Error: " . $params["text"] . " / " . $phone . "\n", FILE_APPEND);
        return false;
    }

    private function smsMessage_fos_toadmin()
    {
        if ($this->_data['msg_owner'] != '') return $this->_data['msg_owner'];
        return "Заявка на http://www.kvantor42.ru Форма: {$this->_data['form_name']}. Тел.: {$this->_data['phone']}";
    }

    private function smsMessage_order_toadmin()
    {
        if ($this->_data['msg_owner'] != '') return $this->_data['msg_owner'];
        return "Заказ на http://www.kvantor42.ru ID:{$this->_data['order_id']}. Тел.: {$this->_data['phone']}";
    }

    private function smsMessage_client()
    {
        if ($this->_data['msg_client'] != '') return $this->_data['msg_client'];
        return "Заявка принята. «БизнесИнсайдер» http://www.busins.ru Телефон: +7 (495) 669-05-78";
    }
}