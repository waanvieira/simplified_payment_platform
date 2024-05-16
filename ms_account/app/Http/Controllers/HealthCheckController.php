<?php

namespace App\Http\Controllers;

use Decimal\Decimal;
use Illuminate\Http\Response;

class HealthCheckController extends Controller
{
    public function health()
    {
        $debitos = [];
        $creditos = [];
        $a = new Decimal('0.1564');
        $b = new Decimal('0.2789');
        $res = $a + $b;           // prints 0.3
        echo $res->round(2);
        echo "\n";
        array_push($debitos, new Decimal('0.1'));
        array_push($debitos, new Decimal('0.2'));

        array_push($creditos, new Decimal('0.3'));

        function saldo(array $creditos, array $debitos)
        {
            return array_sum($creditos) - array_sum($debitos);
        }
        echo "saldo \n";
        echo saldo($creditos, $debitos);
        echo "saldo \n";

        return response()->json([
            'app' => env('APP_NAME'),
            'external_link' => true,
            'version' => date('d/m/Y H:m:i'),
        ], Response::HTTP_OK);
    }
}
