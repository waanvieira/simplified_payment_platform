<?php

namespace App\Gateway;

class SendMailGatewayService extends BaseRequestClientService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = getenv('SEND_MAIL_GATEWAY_URL');
    }

    public function baseUrl()
    {
        return $this->baseUrl;
    }

    public function authorization(): array
    {
        return array();
    }

    protected function getHeader()
    {
        return [
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ];
    }

    public function sendMail(array $data)
    {
        $dataFake = ['54dc2cf1-3add-45b5-b5a9-6bf7e7f1f4a6'];
        $dataParsed = $this->makeQueryParams($dataFake);
        return $this->get($dataParsed);
    }
}
