<?php

class COAuthExt
{

    public static function getNets()
    {
        return array(
            'VK' => array(
                'redirect_uri'  => SITE_PROTOCOL . SITE_URL . '/kabinet/auth/oauth.php',
                'client_id'     => SITE_TEST ? '3396240' : '6028259',
                'client_secret' => SITE_TEST ? 'm8B16nmykZ0ChGPQRkkM' : 'UBRORjFXl1Mu8MrHfAsH',
                'display'       => 'popup',
                'response_type' => 'code',
                'v'             => '5.63',
                'state'         => 'vkauth',
                'fields'        => 'bdate,city,has_mobile,contacts',
                'scope'         => 'email',
                'auth_url'      => 'https://oauth.vk.com/authorize',
                'token_url'     => 'https://oauth.vk.com/access_token',
                'user_url'      => 'https://api.vk.com/method/users.get',
            ),
            'FB' => array(
                'redirect_uri'  => SITE_PROTOCOL . SITE_URL . '/kabinet/auth/oauth.php',
                'client_id'     => SITE_TEST ? '996945340386558' : '',
                'client_secret' => SITE_TEST ? 'e7dcf5e1848b57b646a9d0b23565461c' : '',
                'display'       => 'popup',
                'response_type' => 'code',
                'v'             => '2.8',
                'state'         => 'fbauth',
                'fields'        => 'id,name,email,first_name,last_name',
                'scope'         => 'email',
                'auth_url'      => 'https://www.facebook.com/v2.8/dialog/oauth',
                'token_url'     => 'https://graph.facebook.com/v2.8/oauth/access_token',
                'user_url'      => 'https://graph.facebook.com/me',
            ),
            'OK' => array(
                'redirect_uri'      => SITE_PROTOCOL . SITE_URL . '/kabinet/auth/oauth.php',
                'client_id'         => SITE_TEST ? '1250519552' : '',
                'client_secret'     => SITE_TEST ? 'F47FE76ED06E45DDB7994AA2' : '',
                'client_public_key' => SITE_TEST ? 'CBALGKILEBABABABA' : '',
                'layout'            => 'popup',
                'response_type'     => 'code',
                'grant_type'        => 'authorization_code', //Тип выдаваемых прав
                'v'                 => '',
                'state'             => 'okauth',
                'fields'            => 'UID;NAME;EMAIL;FIRST_NAME;LAST_NAME',
                'scope'             => 'VALUABLE_ACCESS;GET_EMAIL',
                'auth_url'          => 'https://connect.ok.ru/oauth/authorize',
                'token_url'         => 'https://api.ok.ru/oauth/token.do',
                'user_url'          => 'http://api.odnoklassniki.ru/fb.do',
            ),
        );
    }

    public static function getNetsCodes()
    {
        return array_keys(self::getNets());
    }

    public static function getState($NET)
    {
        $NETS = self::getNets();
        return $NETS[$NET]['state'];
    }

    public static function getResponse($sUrl, $arParams = false)
    {
        //$request = $sUrl . '?' . http_build_query($arParams);
        $result = json_decode(\CURL::sendPost($sUrl, $arParams), true);
        return $result;
    }

    public static function getToken($code, $NET)
    {
        $NETS = self::getNets();

        $arParams = array(
            'code'          => htmlspecialcharsbx($code),
            'client_id'     => $NETS[$NET]['client_id'],
            'client_secret' => $NETS[$NET]['client_secret'],
            'redirect_uri'  => $NETS[$NET]['redirect_uri'],
            'grant_type'    => $NETS[$NET]['grant_type'],
            'v'             => $NETS[$NET]['v'],
        );

        $result = self::getResponse($NETS[$NET]['token_url'], $arParams);
        return $result;
    }

    public static function getUser($arToken, $NET)
    {
        $result = false;

        $NETS = self::getNets();

        if (!empty($arToken['access_token']))
        {
            $arParams = array(
                'user_id'      => $arToken['user_id'],
                'access_token' => $arToken['access_token'],
                'fields'       => $NETS[$NET]['fields'],
                'v'            => $NETS[$NET]['v']
            );

            if ($NET == "OK")
            {
                $public_key    = $NETS[$NET]['client_public_key'];
                $client_secret = $NETS[$NET]['client_secret'];
                $access_token  = $arToken['access_token'];

                $sign     = md5("application_key={$public_key}format=jsonmethod=users.getCurrentUser" . md5("{$access_token}{$client_secret}"));
                $arParams = array(
                    'method'          => 'users.getCurrentUser',
                    'access_token'    => $access_token,
                    'application_key' => $public_key,
                    'format'          => 'json',
                    'sig'             => $sign
                );
            }

            $response = self::getResponse($NETS[$NET]['user_url'], $arParams);

            if ($NET == "VK")
            {
                $result['user_id']    = $arToken['user_id'];
                $result['email']      = $arToken['email'];
                $result['first_name'] = $response['response'][0]['first_name'];
                $result['last_name']  = $response['response'][0]['last_name'];
                $result['user_name']  = $response['response'][0]['first_name'] . " " . $response['response'][0]['last_name'];
            }

            if ($NET == "FB")
            {
                $result['user_id']    = $response['id'];
                $result['email']      = $response['email'];
                $result['first_name'] = $response['first_name'];
                $result['last_name']  = $response['last_name'];
                $result['user_name']  = $response['name'];
            }

            if ($NET == "OK")
            {
                $result['user_id']    = $response['uid'];
                $result['email']      = $response['email'];
                $result['first_name'] = $response['first_name'];
                $result['last_name']  = $response['last_name'];
                $result['user_name']  = $response['name'];
            }
        }

        return $result;
    }

    public static function getAuthUrls()
    {
        $arResult = array();

        $NETS = self::getNets();

        foreach ($NETS as $NET => $arParams)
        {
            $arNETParams = array(
                'client_id'     => $arParams['client_id'],
                'display'       => $arParams['display'],
                'redirect_uri'  => $arParams['redirect_uri'],
                'scope'         => $arParams['scope'],
                'response_type' => $arParams['response_type'],
                'v'             => $arParams['v'],
                'state'         => $arParams['state'],
            );

            $arResult[$NET] = $arParams['auth_url'] . "?" . http_build_query($arNETParams);
        }

        return $arResult;
    }

}
