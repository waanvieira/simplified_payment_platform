<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\NewsLetterController;
use App\Models\NewsLetter;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
class NewsLetterControllerTest extends TestCase
{
    use DatabaseTransactions;
    use TestValidations;
    use TestSaves;
    use TestResources;
    // use WithOutMiddleware;

    private $newsletter;
    private $controller;
    private $moduloPagamentoMock;
    private $paymentMock;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        // $this->withoutMiddleware([
        //     JWTAuthenticateAccess::class
        // ]);

        $this->controller = $this->controller();
        $fakeUser = NewsLetter::factory()->create();
        $user = User::factory()->create();
        $this->user = User::where('email', $user->email)->first();
        $this->newsletter = $this->model()::orderBy('id', 'DESC')->first();
    }

    private $serializedFields = [
        'id',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];


    public function testGetAll()
    {
        $response = $this->get(route('newsletter.index'));
        $response
            ->assertStatus(200)
            // ->assertJson([
            //     // 'message' => 'Registros encontrados com sucesso',
            //     'data' => [
            //         '*' => $this->serializedFields
            //     ],
            //     'per_page' => 20
            // ]);
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => $this->serializedFields
                    ],
                    'links' => [],
                    // 'meta' => [],
                ]
            );
    }


    public function testShow()
    {
        $response = $this->get(route('newsletter.show', ['newsletter' => $this->newsletter->id]));
        $response
            ->assertStatus(200);
        // ->assertJsonStructure([
        //     'data' => $this->serializedFields
        // ]);
    }


    public function testAssertInvalidationStore()
    {
        $data = [
            'name' => null,
            'email' => null
        ];

        $this->assertInvalidationInStoreAction($data, 'required', [], $this->routeStore());
    }

    public function testAssertInvalidationUpdate()
    {
        $data = [
            'name' => null,
            'email' => null
        ];

        $this->assertInvalidationInUpdateAction($data, 'required', [], $this->routeUpdate());
    }

    public function testHandleRelation()
    {
        $this->assertTrue(true);
    }

    public function testStore()
    {
        $Adminuser = User::factory()->create(['is_admin' => true]);
        // $this->user = User::where('email', $user->email)->first();
        $data = [
            'name' => 'teste',
            "email" => $Adminuser->email,
            'description' => 'description',
        ];

        $response = $this->assertStore($data, $data, [], route('newsletter.store'));
        $response
            ->assertStatus(201);
        // ->assertJsonStructure([
        //     'data' => $this->serializedFields
        // ]);
    }

    public function testStoreUserNotAdmin()
    {
        $data = [
            'name' => 'teste',
            "email" => $this->user->email,
            'description' => 'description',
        ];

        $response = $this->post(route('newsletter.store'), $data);
        $messageExpected = ["message" => "Usuário não tem permissão para criar lista"];
        $response->assertStatus(Response::HTTP_BAD_REQUEST)->assertContent(
            json_encode($messageExpected)
        );
    }

    public function testUpdate()
    {
        $Adminuser = User::factory()->create(['is_admin' => true]);
        $data = [
            'name' => 'updated',
            "email" => $Adminuser->email,
            'description' => 'updated descript',
        ];

        $response = $this->assertUpdate($data, $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);
    }

    public function testRegisterUserOnTheList()
    {
        $data = [
            "email" => $this->user->email,
        ];
        $response = $this->put(route('newsletter.link_user', ['id' => $this->newsletter->id]), $data);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertHasUser($this->user->id, $this->newsletter->id);
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('newsletter.destroy', ['newsletter' => $this->newsletter->id]));
        $response->assertStatus(204);
        $userExcluded = $this->model()::where('id', $this->newsletter->id)->first();
        $this->assertNull($userExcluded);
    }

    public function routeStore()
    {
        return route('newsletter.store');
    }

    public function routeUpdate()
    {
        return route('newsletter.update', ['newsletter' => $this->newsletter->id]);
    }

    protected function assertHasUser($userId, $nesLetterId)
    {
        $this->assertDatabaseHas('newsletter_user', [
            'user_id' => $userId,
            'newsletter_id' => $nesLetterId
        ]);
    }


    public function model()
    {
        return NewsLetter::class;
    }

    public function controller()
    {
        return new NewsLetterController();
    }
}
