<?php

namespace Artamonov\Api\Controllers;


class Star extends BaseAPIController
{
    protected $requireFields = ['name', 'phone', 'star'];

    public function Send()
    {
        $query = $this->request['PARAMETERS'];
        if ($this->Validate($query)) {
            $fields = [
                'NAME' => $this->Sanitize($query['name']),
                'PREVIEW_TEXT' => $this->Sanitize($query['comment']),
                'PROPERTY_VALUES' => [
                    'PHONE' => $this->Sanitize($query['phone']),
                    'STAR' => $this->Sanitize($query['star'])
                ]
            ];

            $result = \APIStarModel::create($fields);

            $this->Execute($result['ID']);
        }
    }

    protected function Sanitize($var)
    {
        return strip_tags(trim($var));
    }

    protected function Validate($query)
    {
        $fields = array_keys($query);
        $errors = [];
        foreach ($this->requireFields as $requireField) {
            if (!in_array($requireField, $fields)) {
                $errors[] = $requireField;
            }
        }

        foreach ($query as $key => $value) {
            if (in_array($key, $this->requireFields) && empty($value)) {
                $errors[] = $key;
            }
        }
        if (!empty($errors)) {
            $this->Error($errors);
            return false;
        } else {
            return true;
        }
    }
}