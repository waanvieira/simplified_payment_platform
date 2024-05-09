<?php

namespace App\Domain\ValueObjects;

use App\Exceptions\InvalidArgumentException;

class CpfCnpj
{
    public function __construct(
        protected string $value
    ) {
        $value = preg_replace('/[^0-9]/is', '', $value);

        if (strlen($value) < 11 || strlen($value) > 14) {
            throw new InvalidArgumentException('Invalid CPF/CNPJ');
        }

        if (strlen($value) === 11) {
            $this->validCpf($value);
        }

        if (strlen($value) === 14) {
            $this->validCnpj($value);
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validCpf(string $cpf): bool
    {
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            throw new InvalidArgumentException('Invalid CPF');
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                throw new InvalidArgumentException('Invalid CPF');
            }
        }

        return true;
    }

    private function validCnpj(string $cnpj)
    {
        if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
            throw new InvalidArgumentException("Invalid CNPJ");
        }

        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            throw new InvalidArgumentException("Invalid CNPJ");
        }

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return true;
    }
}
