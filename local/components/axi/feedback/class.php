<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/*
<?$APPLICATION->IncludeComponent(
    "axi:feedback", 
    "", 
    array(
        "EVENT_ALIAS" => EVENT_ALIAS,
        "STORE_IBLOCK_ID" => STORE_IBLOCK_ID,
        "FIELDS" => array(
            "ANSWER_NAME" => "Имя",
            "ANSWER_PHONE" => "Телефон",
            "ANSWER_EMAIL" => "Электронная почта",
            "ANSWER_TEXT" => "Сообщение",
            ),
        "REQUIRED_FIELDS" => array(
            "ANSWER_NAME",
            "ANSWER_PHONE",
            "ANSWER_EMAIL",
            "ANSWER_TEXT",
            ),
        "UPLOAD_FILE" => "Прикрепить файл",
        "OK_MESSAGE" => "Спасибо, ваше сообщение принято!"
    ),
    false,
    array(
       "HIDE_ICONS" =>  "Y"
    )
);?>
*/

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;

class AxiBSFeedbackComponent extends CBitrixComponent
{
    protected function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException(Loc::getMessage('IES_MODULE_NOT_INSTALLED', array('MODULE_ID' => 'iblock')));
        }
    }

    protected function SanitizeData($data)
    {
        return trim(strip_tags(htmlspecialcharsbx($data)));
    }

    protected function PrepareFileArray($file)
    {
        $arFile = $file;
        $arFile["MODULE_ID"] = "main";
        $arFile["del"] = ${"ANSWER_FILE_del"};

        return $arFile;
    }

    public function onPrepareComponentParams($params)
    {
        $request = Context::getCurrent()->getRequest();

        $params['IS_AJAX'] = false;
        if ($request->isAjaxRequest() || $request->getPost('AJAX') == 'Y') {
            define('STOP_STATISTICS', true);
            define('NO_AGENT_CHECK', true);
            define('DisableEventsCheck', true);
            define('BX_SECURITY_SHOW_MESSAGE', true);
            $params['IS_AJAX'] = true;

            foreach ($params['FIELDS'] as $field => $field_value) {
                $params[$field] = $this->SanitizeData($request->getPost($field));
            }
            $params['ANSWER_FILE'] = $this->PrepareFileArray($request->getFile('ANSWER_FILE'));

            $params['CURRENT_PAGE_URL'] = $this->SanitizeData($request->getPost('CURRENT_PAGE_URL'));
            $params['CURRENT_PAGE'] = $this->SanitizeData($request->getPost('CURRENT_PAGE'));
            $params['FORM_TITLE'] = $this->SanitizeData($request->getPost('FORM_TITLE'));

            $params['ANSWER_PARAMS_HASH'] = $this->SanitizeData($request->getPost('PARAMS_HASH'));
            $params['ANSWER_EMPTY'] = $this->SanitizeData($request->getPost('EMPTY'));
        }

        $params['EVENT_ALIAS'] = trim($params['EVENT_ALIAS']);
        $params['STORE_IBLOCK_ID'] = intval($params['STORE_IBLOCK_ID']);
        $params['OK_MESSAGE'] = trim($params['OK_MESSAGE']);
        $params["PARAMS_HASH"] = md5($this->GetTemplateName() . $params['EVENT_ALIAS']);

        foreach ($params['REQUIRED_FIELDS'] as &$r_field) {
            $r_field = trim($r_field);
        }

        return $params;
    }

    protected function checkRequiredParams()
    {
        $listRequiredParams = array('PARAMS_HASH', 'EVENT_ALIAS', 'STORE_IBLOCK_ID');
        foreach ($listRequiredParams as $requiredParam) {
            if (empty($this->arParams[$requiredParam])) {
                throw new SystemException("Обязательный параметр отсутствует - " . $requiredParam);
            }
        }

        if ($this->arParams["IS_AJAX"]) {
            if ($this->arParams["PARAMS_HASH"] != $this->arParams["ANSWER_PARAMS_HASH"]) {
                throw new SystemException("Форма отправленна не с сайта");
            }
            if (!empty($this->arParams["ANSWER_EMPTY"])) {
                throw new SystemException("Бот");
            }
            if (!check_bitrix_sessid()) {
                throw new SystemException("Сессия истекла");
            }
            foreach ($this->arParams['REQUIRED_FIELDS'] as $r_field) {
                if (empty($r_field)) {
                    throw new SystemException("Не заполнено обязательное поле");
                }
            }
            if ($this->arParams['ANSWER_FILE']["size"] > UPLOAD_MAX_FILE_SIZE) {
                $maxFileSize = UPLOAD_MAX_FILE_SIZE / 1024 / 1024;
                throw new SystemException("Максимальный размер загружаемого файла " . $maxFileSize . "мб");
            }
        }
    }

    protected function fillResult()
    {
        $this->arResult['PARAMS_HASH'] = $this->arParams['PARAMS_HASH'];
        $this->arResult['TEMPLATE'] = $this->arParams['EVENT_ALIAS'];

        $this->arResult['FIELDS'] = $this->arParams['FIELDS'];
        $this->arResult['UPLOAD_FILE'] = $this->arParams['UPLOAD_FILE'];
    }

    protected function saveResult()
    {
        if (!$this->arParams['IS_AJAX']) {
            return false;
        } else {
            $fid = CFile::SaveFile($this->arParams['ANSWER_FILE'], "user_files");

            $element = new CIBlockElement();
            $PROPERTY_VALUES = [
                'PHONE' => $this->arParams['ANSWER_PHONE'],
                'EMAIL' => $this->arParams['ANSWER_EMAIL'],
                'PAGE' => $this->arParams['CURRENT_PAGE'] . ' (' . $this->arParams['CURRENT_PAGE_URL'] . ')',
                'FILE' => $fid
            ];

            $arMessage = [
                "MODIFIED_BY" => $GLOBALS['USER']->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID" => $this->arParams['STORE_IBLOCK_ID'],
                "NAME" => $this->arParams['FORM_TITLE'] . ' — ' . $this->arParams['ANSWER_NAME'],
                "PREVIEW_TEXT" => $this->arParams['ANSWER_TEXT'],
                "ACTIVE" => "Y",
                "ACTIVE_FROM" => date('d.m.Y'),
                "PROPERTY_VALUES" => $PROPERTY_VALUES
            ];
            if ($elementId = $element->add($arMessage)) {
                return true;
            } else {
                throw new SystemException($element->LAST_ERROR);
                return false;
            }
        }
    }

    protected function createEvent()
    {
        $CEventType = new CEventType;
        $EventTypeID = $CEventType->Add(
            [
                "LID" => "ru",
                "EVENT_NAME" => $this->arParams["EVENT_ALIAS"],
                "NAME" => $this->arParams["EVENT_ALIAS"],
                "DESCRIPTION" => "
                    #EMAIL_TO# - E-Mail администратора сайта (отправитель по умолчанию)
                    #EMAIL_ADD# - E-Mail (или список через запятую), на который будут дублироваться исходящие сообщения
                    #MESSAGE_NAME# - Поле ФИО
                    #MESSAGE_PHONE# - Поле ТЕЛЕФОН
                    #MESSAGE_EMAIL# - Поле EMAIL
                    #MESSAGE_TEXT# - Поле СООБЩЕНИЕ/ОТЗЫВ/ВОПРОС
                    #FORM_TITLE# - Название формы
                    #PAGE# - Название страницы с которой отправлена форма
                    #PAGE_URL# - URL страницы с которой отправлена форма
                    #MESSAGE# - Все заполненые поля в одном макросе
                    "
            ]
        );
        if ($EventTypeID > 0) {
            $CEventMessage = new CEventMessage;
            $CEventMessage->Add(
                [
                    "ACTIVE" => "Y",
                    "EVENT_NAME" => $this->arParams["EVENT_ALIAS"],
                    "LID" => SITE_LID,
                    "EMAIL_FROM" => "message@#SERVER_NAME#",
                    "EMAIL_TO" => "#EMAIL_TO#",
                    "BCC" => "#EMAIL_ADD#",
                    "SUBJECT" => "#FORM_TITLE#",
                    "BODY_TYPE" => "text",
                    "MESSAGE" => "#MESSAGE#"
                ]
            );
        }
    }

    protected function sendAdminMail()
    {
        $arEventFields = [
            'EMAIL_TO' => Option::get('main', 'email_from'),
            'EMAIL_ADD' => Option::get('main', 'all_bcc'),
            'MESSAGE_NAME' => $this->arParams['ANSWER_NAME'],
            'MESSAGE_PHONE' => $this->arParams['ANSWER_PHONE'],
            'MESSAGE_EMAIL' => $this->arParams['ANSWER_EMAIL'],
            'MESSAGE_TEXT' => $this->arParams['ANSWER_TEXT'],
            'FORM_TITLE' => $this->arParams['FORM_TITLE'],
            'PAGE' => $this->arParams['CURRENT_PAGE'],
            'PAGE_URL' => $this->arParams['CURRENT_PAGE_URL']
        ];
        $MESSAGE = "";
        foreach ($arEventFields as $fKey => $fValue) {
            if (empty($fValue)) continue;
            $MESSAGE .= $fKey . " — " . $fValue . PHP_EOL;
        }
        $arEventFields['MESSAGE'] = $MESSAGE;

        $sendParams = [
            "EVENT_NAME" => $this->arParams['EVENT_ALIAS'],
            "LID" => SITE_ID,
            "C_FIELDS" => $arEventFields
        ];

        $check = Event::send($sendParams);
    }

    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->checkRequiredParams();

            $dbET = CEventType::GetList(["TYPE_ID" => $this->arParams["EVENT_ALIAS"], "LID" => "ru"]);
            $arET = $dbET->Fetch();
            if (!$arET) {
                $this->createEvent();
            }

            $this->fillResult();
            if ($this->saveResult()) {
                $this->sendAdminMail();
            }
        } catch (SystemException $exception) {
            if ($this->arParams['IS_AJAX']) {
                header('Content-Type: application/json; charset=utf-8');
                $result = [
                    'status' => 'exception',
                    'message' => $exception->getMessage()
                ];
                echo Json::encode($result);
                die;
            } else {
                ShowError($exception->getMessage());
            }
        }

        if ($this->arParams['IS_AJAX']) {
            $GLOBALS['APPLICATION']->RestartBuffer();
        }

        ob_start();
        $this->includeComponentTemplate();
        $componentResult = ob_get_clean();

        if ($this->arParams['IS_AJAX']) {
            header('Content-Type: application/json; charset=utf-8');
            $result = array(
                'status' => 'OK',
                'message' => $this->arParams['OK_MESSAGE'],
                'result' => $this->arResult,
                'HTML' => $componentResult,
            );
            echo Json::encode($result);
            die;
        } else {
            $result = $componentResult;
            echo $result;
        }
    }
}