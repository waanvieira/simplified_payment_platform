<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class HealthCheckController extends Controller
{
    protected $sap;

    // public function __construct()
    // {
    //     $this->sap = new SapClientInterface();
    // }

    /**
     * @OA\Get(
     *     tags={"Health"},
     *     path="/health",
     *      summary="Verifica o status da aplicação",
     *      @OA\Response(response=202, description="Sistema no ar"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="End point não encontrado"),
     *      @OA\Response(response=500, description="Erro interno")
     * )
     */
    public function health()
    {
        return response()->json([
            'external_link' => true,
            'version'   => date("d/m/Y H:m:i")
        ], Response::HTTP_OK);
    }
}
