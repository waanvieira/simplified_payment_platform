<?php

namespace App\Gateway;

use App\Exceptions\ExternalErrorException;
use App\Exceptions\NotFoundException;
use GuzzleHttp\Client;
use Illuminate\Http\Response;

abstract class BaseRequestClientService implements HttpInterface
{
    protected abstract function baseUrl();

    protected abstract function authorization(): array;

    protected abstract function getHeader();

    public function get($path)
    {
        try {
            return $this->makeRequest()->get($this->getUrl($path), $this->makeAuthorization($this->authorization()));
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function post($path, $data)
    {
        try {
            return $this->makeRequest()->post($this->getUrl($path), $this->makeAuthorization($this->authorization(), $data));
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->getErrorResponse($e, $data, $this->getUrl($path));
        }
    }

    public function put($path, $data = null)
    {
        try {
            return $this->makeRequest()->put($this->getUrl($path), $this->makeAuthorization($this->authorization(), $data));
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function delete($path)
    {
        try {
            return json_encode(json_decode($this->makeRequest()->delete($this->getUrl($path))->getBody()->getContents()));
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function postJsonDecode(string $path, array $data)
    {
        return json_encode(json_decode($this->makeRequest()->put($this->getUrl($path), $this->makeAuthorization($this->authorization(), $data))->getBody()->getContents(), true));
    }

    private function makeRequest()
    {
        return new Client(['base_uri' => $this->baseUrl()]);
    }

    private function getUrl($path)
    {
        return sprintf('%s%s', $this->baseUrl(), $path);
    }

    private function getErrorResponse($error, $data = [], string $endPoint = '')
    {
        $statusCode = $error->getCode() ?? null;
        $message = $error->getMessage() ?? 'Requisição externa sem sucesso';
        throw new ExternalErrorException($message, null, $statusCode);
    }

    public function makeQueryParams(array $params): string
    {
        return !$params ? '' : '?' . http_build_query($params);
    }

    protected function makeAuthorization(array $authorization, array $data = [])
    {
        $type = $authorization['type'] ?? '';
        switch ($type) {
            case 'auth':
                return [$this->getHeader(), 'auth' => $authorization['keys'], 'body' => json_encode($data), 'verify' => false];
            default:
                return ['body' => json_encode($data), 'headers' => $this->getHeader()];
        }
    }
}
