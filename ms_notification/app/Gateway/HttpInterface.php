<?php

namespace App\Gateway;

interface HttpInterface
{
    public function post(string $endpoint, array $data);
    public function get(string $endpoint);
    public function put(string $endpoint, array $data);
    public function delete(string $endpoint);
    public function makeQueryParams(array $params): string;
}
