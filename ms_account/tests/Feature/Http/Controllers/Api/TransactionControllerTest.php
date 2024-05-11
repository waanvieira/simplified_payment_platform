<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Domain\Enum\TransactionStatus;
use App\Domain\Enum\TransactionType;
use App\Http\Controllers\Api\TransactionController;
use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class TransactionControllerTest extends TestCase
{
    use DatabaseTransactions;
    use TestValidations;
    use TestSaves;
    use TestResources;

    private $transaction;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = $this->controller();
        // $fakeTransaction = Transaction::factory()->create();
    }

    private $serializedFields = [
        "id",
        "transaction_type",
        "payer_id",
        "payee_id",
        "value",
        "transaction_status",
        "created_at",
    ];

    // public function testGetAll()
    // {
    //     $response = $this->get(route('transactions.index'));
    //     $response
    //         ->assertStatus(200)
    //         ->assertJsonStructure(
    //             [
    //                 'data' => [
    //                     '*' => $this->serializedFields
    //                 ],
    //                 'links' => [],
    //             ]
    //         );
    // }

    // public function testShow()
    // {
    //     $response = $this->get(route('transactions.show', ['transaction' => $this->transaction->id]));
    //     $response
    //         ->assertStatus(200)
    //         ->assertJsonStructure([
    //             'data' => $this->serializedFields
    //         ]);
    // }

    public function testAssertInvalidationStore()
    {
        $data = [
            'payer_id' => null,
            'payee_id' => null,
            'value' => null
        ];

        $this->assertInvalidationInStoreAction($data, 'required', [], route('transfer'));
    }

    public function testHandleRelation()
    {
        $this->assertTrue(true);
    }

    public function testStoreSuccessTransfer()
    {
        $payee = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
        ]);
        $payer = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
            'cpf_cnpj' =>  "668.733.240-60",
            "balance" => 10.12
        ]);

        $data = [
            "payer_id" => $payer->id,
            "payee_id" => $payee->id,
            "value" => 1.05
        ];

        $response = $this->assertStore($data, $data, [], route('transfer'));
        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

        $return = $response->json()['data'];
        $this->assertEquals(TransactionStatus::PROCESSING->value, $return['transaction_status']);
        $this->assertEquals(TransactionType::TRANSFER->value, $return['transaction_type']);
        $accountUpdated = Account::find($payer->id);
        $this->assertEquals(9.07, $accountUpdated->balance);
    }

    public function testStoreTransferWithoutBalance()
    {
        $payer = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
            'cpf_cnpj' => "668.733.240-60",
            'balance' => 0
        ]);

        $payee = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
        ]);

        $data = [
            "payer_id" => $payer->id,
            "payee_id" => $payee->id,
            "value" => rand(1, 100)
        ];

        $response = $this->post(route('transfer'), $data);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $return = $response->json();
        $expectedReturn = [
            "message" => "balance unavailable to carry out transaction"
        ];
        $this->assertEquals($expectedReturn, $return);
    }

    public function testStoreTransferWithoutNoSufficientBalance()
    {
        $payer = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
            'cpf_cnpj' => "668.733.240-60",
            'balance' => 5
        ]);

        $payee = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
        ]);

        $data = [
            "payer_id" => $payer->id,
            "payee_id" => $payee->id,
            "value" => 10
        ];

        $response = $this->post(route('transfer'), $data);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $return = $response->json();
        $expectedReturn = [
            "message" => "balance unavailable to carry out transaction"
        ];
        $this->assertEquals($expectedReturn, $return);
    }

    public function testStoreTransferAccountPayeeNotFound()
    {
        $payer = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
        ]);

        $idNotFound = Uuid::uuid4()->toString();
        $data = [
            "payer_id" => $payer->id,
            "payee_id" => $idNotFound,
            "value" => rand(1, 100)
        ];

        $response = $this->post(route('transfer'), $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $return = $response->json();
        $expectedReturn = [
            "message" => "Register {$idNotFound} Not Found"
        ];
        $this->assertEquals($expectedReturn, $return);
    }

    public function testStoreTransferAccountPayerNotFound()
    {
        $idNotFound = Uuid::uuid4()->toString();
        $payee = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
        ]);


        $data = [
            "payer_id" => $idNotFound,
            "payee_id" => $payee->id,
            "value" => rand(1, 100)
        ];

        $response = $this->post(route('transfer'), $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $return = $response->json();
        $expectedReturn = [
            "message" => "Register {$idNotFound} Not Found"
        ];
        $this->assertEquals($expectedReturn, $return);
    }

    public function routeStore()
    {
        return route('transactions.store');
    }

    public function routeUpdate()
    {
        return route('transactions.update', ['transaction' => $this->transaction->id]);
    }

    public function model()
    {
        return Transaction::class;
    }

    public function controller()
    {
        return new TransactionController();
    }
}
