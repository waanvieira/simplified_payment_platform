<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    use TestValidations;
    use TestSaves;
    use TestResources;
    // use WithOutMiddleware;

    private $user;
    private $controller;
    private $moduloPagamentoMock;
    private $paymentMock;
    private $cartaoMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = $this->controller();
        $fakeUser = User::factory()->create([
            'id' => Uuid::uuid4()->toString(),
            'name' => fake()->name(),
            'email' => 'teste@dev.com.br',
            'email_verified_at' => now(),
            'password' => '1234',
        ]);

        $this->user = $this->model()::where('email', $fakeUser->email)->first();
    }

    private $serializedFields = [
        'id',
        'name',
        'cpf_cnpj',
        'email',
        'created_at'
    ];

    public function testGetAll()
    {
        $response = $this->get(route('users.index'));
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
        $response = $this->get(route('users.show', ['user' => $this->user->id]));
        $response
            ->assertStatus(200);
        // ->assertJsonStructure([
        //     'data' => $this->serializedFields
        // ]);
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

        $response = $this->assertStore($data, $data, [], route('users.store'));
        $response
            ->assertStatus(201);
        // ->assertJsonStructure([
        //     'data' => $this->serializedFields
        // ]);
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'teste updated',
            'cpf_cnpj' => '589.944.690-22',
            'email' => 'upddev@dev.com.br'
        ];
        $response = $this->assertUpdate($data, $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('users.destroy', ['user' => $this->user->id]));
        $response->assertStatus(204);
        $userExcluded = $this->model()::where('id', $this->user->id)->first();
        $this->assertNull($userExcluded);
    }


    public function routeStore()
    {
        return route('users.store');
    }

    public function routeUpdate()
    {
        return route('users.update', ['user' => $this->user->id]);
    }

    public function model()
    {
        return User::class;
    }

    public function controller()
    {
        return new UserController();
    }
}
