<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\AccountController;
use App\Models\Account;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class AccountControllerTest extends TestCase
{
    use DatabaseTransactions;
    use TestValidations;
    use TestSaves;
    use TestResources;
    // use WithOutMiddleware;

    private $account;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = $this->controller();
        $fakeAccount = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
            'name' => fake()->name(),
            'email' => 'teste@dev.com.br',
            'balance' => 10,
            'password' => '1234',
        ]);

        $this->account = $this->model()::where('email', $fakeAccount->email)->first();
    }

    private $serializedFields = [
        'id',
        'name',
        'cpf_cnpj',
        'email',
        'balance',
        'created_at'
    ];

    public function testGetAll()
    {
        $response = $this->get(route('accounts.index'));
        $response
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => $this->serializedFields
                    ],
                    'links' => [],
                ]
            );
    }

    public function testShow()
    {
        $response = $this->get(route('accounts.show', ['account' => $this->account->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
    }

    public function testAssertInvalidationStore()
    {
        $data = [
            'name'     => null,
            'cpf_cnpj'    => null,
            'email'    => null,
            'password' => null,
        ];

        $this->assertInvalidationInStoreAction($data, 'required', [], $this->routeStore());
    }

    public function testAssertInvalidationUpdate()
    {
        $data = [
            'name'     => null,
            'cpf_cnpj'     => null,
            'email'    => null,
        ];

        $this->assertInvalidationInUpdateAction($data, 'required', [], $this->routeUpdate());
    }

    public function testHandleRelation()
    {
        $this->assertTrue(true);
    }

    public function testStore()
    {
        $data = [
            'name' => 'teste',
            'cpf_cnpj' => '589.944.690-01',
            'email' => 'testdev@dev.com.br',
            'password' => '123',
        ];

        $response = $this->assertStore($data, $data, [], route('accounts.store'));
        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
    }

    public function testStoreCnpj()
    {
        $data = [
            'name' => 'teste',
            'cpf_cnpj' => '21.504.213/0001-20',
            'email' => 'testdev@dev.com.br',
            'password' => '123',
        ];

        $response = $this->assertStore($data, $data, [], route('accounts.store'));
        $data = $response->json()['data'];
        $this->assertEquals(true, $data['shopkeeper']);
        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'teste updated',
            'cpf_cnpj' => '589.944.690-01',
            'email' => 'upddev@dev.com.br'
        ];
        $response = $this->assertUpdate($data, $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);
    }

    public function testUpdateNotShopKepperByShopKepper()
    {
        $cpfAccount = Account::factory()->create([
            'id' => Uuid::uuid4()->toString(),
            'cpf_cnpj' => '32850236004',
            'name' => fake()->name(),
            'email' => 'testecpf@dev.com.br',
            'shopkeeper' => false,
            'balance' => 10,
            'password' => '1234',
        ]);
        $this->assertFalse($cpfAccount->shopkeeper);
        $data = [
            'name' => 'teste updated',
            'cpf_cnpj' => '37.578.189/0001-04',
            'email' => 'upddev@dev.com.br'
        ];

        $response = $this->assertUpdate($data, $data);
        $response->assertStatus(Response::HTTP_OK);
        $responseData = $response->json()['data'];
        $this->assertTrue($responseData['shopkeeper']);
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('accounts.destroy', ['account' => $this->account->id]));
        $response->assertStatus(204);
        $accountExcluded = $this->model()::where('id', $this->account->id)->first();
        $this->assertNull($accountExcluded);
    }


    public function routeStore()
    {
        return route('accounts.store');
    }

    public function routeUpdate()
    {
        return route('accounts.update', ['account' => $this->account->id]);
    }

    public function model()
    {
        return Account::class;
    }

    public function controller()
    {
        return new AccountController();
    }
}
