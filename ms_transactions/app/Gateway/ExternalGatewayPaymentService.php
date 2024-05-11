<?php

namespace App\Gateway;

class ExternalGatewayPaymentService extends BaseRequestClientService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = getenv('EXTERNAL_GATEWAY_PAYMENT');
        // $this->timeout = [
        //     'timeout'           => 120,
        //     'connect_timeout'   => 120
        // ];
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

    public function processPayment(array $data)
    {
        $dataFake = ['5794d450-d2e2-4412-8131-73d0293ac1cc'];
        $dataParsed = $this->makeQueryParams($dataFake);
        return $this->get($dataParsed);
    }
}
