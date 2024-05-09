<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\UseCases\DTO\Account\AccountCreateInputDto;
use App\UseCases\DTO\Account\AccountUpdateInputDto;
use App\UseCases\Account\AccountCreateUseCase;
use App\UseCases\Account\AccountDeleteUseCase;
use App\UseCases\Account\AccountFindByIdUseCase;
use App\UseCases\Account\AccountGetAllUseCase;
use App\UseCases\Account\AccountUpdateUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    public function index(Request $request, AccountGetAllUseCase $useCase)
    {
        $response = $useCase->execute($request->all());
        return response()->json($response, Response::HTTP_OK);
    }

    public function store(AccountRequest $request, AccountCreateUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new AccountCreateInputDto(
                name: $request->name,
                cpfCnpj: $request->cpf_cnpj,
                email: $request->email,
                password: $request->password
            )
        );

        $model = new Account((array) $response);
        return response()->json(['data' => $model], Response::HTTP_CREATED);
    }

    public function show(string $id, AccountFindByIdUseCase $useCase)
    {
        $response = $useCase->execute($id);
        $model = new Account((array) $response);
        return response()->json(['data' => $model], Response::HTTP_OK);
    }

    public function update(AccountRequest $request, string $id, AccountUpdateUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new AccountUpdateInputDto(
                id: $id,
                cpfCnpj: $request->cpf_cnpj,
                name: $request->name,
                email: $request->email,
            )
        );

        $model = new Account((array) $response);
        return response()->json(['data' => $model], Response::HTTP_OK);
    }

    public function destroy($id, AccountDeleteUseCase $useCase)
    {
        $useCase->execute($id);
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
