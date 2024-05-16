<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\UseCases\DTO\User\UserCreateInputDto;
use App\UseCases\DTO\User\UserUpdateInputDto;
use App\UseCases\User\UserCreateUseCase;
use App\UseCases\User\UserDeleteUseCase;
use App\UseCases\User\UserFindByIdUseCase;
use App\UseCases\User\UserGetAllUseCase;
use App\UseCases\User\UserUpdateUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(Request $request, UserGetAllUseCase $useCase)
    {
        $response = $useCase->execute($request->all());

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(UserRequest $request, UserCreateUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new UserCreateInputDto(
                name: $request->name,
                cpfCnpj: $request->cpf_cnpj,
                email: $request->email,
                password: $request->password
            )
        );

        $model = new User((array) $response);

        return response()->json(['data' => $model], Response::HTTP_CREATED);
    }

    public function show(string $id, UserFindByIdUseCase $useCase)
    {
        $response = $useCase->execute($id);
        $model = new User((array) $response);

        return response()->json(['data' => $model], Response::HTTP_OK);
    }

    public function update(UserRequest $request, string $id, UserUpdateUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new UserUpdateInputDto(
                id: $id,
                cpfCnpj: $request->cpf_cnpj,
                name: $request->name,
                email: $request->email,
            )
        );

        $model = new User((array) $response);

        return response()->json(['data' => $model], Response::HTTP_OK);
    }

    public function destroy($id, UserDeleteUseCase $useCase)
    {
        $useCase->execute($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
