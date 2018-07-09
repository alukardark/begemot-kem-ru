<?php

namespace Artamonov\Api\Controllers;

use Artamonov\Api\Request;
use Artamonov\Api\Response;

class BaseAPIController
{
    protected $request;

    public function __construct()
    {
        $this->request = Request::get();
    }

    protected function Execute($result) {
        Response::ShowResult($result);
    }

    protected function Error($error) {
        Response::BadRequest($error);
    }
}