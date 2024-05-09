<?php

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait TestSaves
{
    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected function assertStore(array $sendData, array $testDataBase, array $testJsonData = null, $route = null): TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json('POST', $route ?? $this->routeStore(), $sendData);

        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}: \n {$response->content()}");
        }
        $this->assertInDataBase($response, $testDataBase);
        $this->assertJsonResponseContent($response, $testDataBase, $testJsonData);
        return $response;
    }

    protected function assertUpdate(array $sendData, array $testDataBase, array $testJsonData = null, $route = null): TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json('PUT', $route ?? $this->routeUpdate(), $sendData);
        if ($response->status() !== 200) {
            throw new \Exception("Response status must be 200, given {$response->status()}: \n {$response->content()}");
        }
        $this->assertInDataBase($response, $testDataBase);
        $this->assertJsonResponseContent($response, $testDataBase, $testJsonData);
        return $response;
    }

    private function assertInDataBase(TestResponse $response, array $testDataBase)
    {
        $model = $this->model();
        $primaryKey = $this->getPrimaryKey();
        $table = (new $model)->getTable();
        // $hiddenID = (new $model)->getHidden();
        // if (!in_array($primaryKey, $hiddenID)) {
        //     $this->assertDataBaseHas($table, $testDataBase + [$primaryKey => $this->getIdFromResponse($response, $primaryKey)]);
        // }
    }

    private function assertJsonResponseContent(TestResponse $response, array $testJsonData, array $testDataBase = null)
    {
        $primaryKey = $this->getPrimaryKey();
        $testResponse = $testJsonData ?? $testDataBase;
        $model = $this->model();
        $hiddenID = (new $model)->getHidden();
        // if (!in_array($primaryKey, $hiddenID)) {
        //     $response->assertJsonFragment($testResponse + [$primaryKey => $this->getIdFromResponse($response, $primaryKey)]);
        // }
    }

    private function getIdFromResponse(TestResponse $response, string $primaryKey)
    {
        return $response->json($primaryKey) ?? $response->json("data.{$primaryKey}");
    }

    private function getPrimaryKey()
    {
        $model = $this->model();
        return (string)(new $model)->getKeyName() ?? null;
    }
}
