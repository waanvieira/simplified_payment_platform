<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\UseCases\DTO\Transaction\TransactionCreateInputDto;
use App\UseCases\Transaction\TransferCreateUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function storeTransfer(TransactionRequest $request, TransferCreateUseCase $transferCreateUseCase)
    {
        $response = $transferCreateUseCase->execute(
            new TransactionCreateInputDto(
                $request->payer_id,
                $request->payee_id,
                $request->value
            )
        );
        $model = new Transaction((array) $response);
        return response()->json(['data' => $model], Response::HTTP_CREATED);
    }
}
