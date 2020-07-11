<?php

namespace Tests\Unit;

use App\DocumentModels\Cnpj;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR\Company as FakeCompanyProvider;
use Tests\TestCase;

class CnpjTest extends TestCase
{
    private function getFaker(): Faker
    {
        $faker = new Faker();
        $faker->addProvider(new FakeCompanyProvider($faker));
        return $faker;
    }

    public function testValid(): void
    {
        $validCnpj = $this->getFaker()->cnpj;
        $cnpj = new Cnpj($validCnpj);
        $this->assertTrue($cnpj->isValid(), "CNPJ inválido.");
        $this->assertIsString($cnpj->getValue(), "O CNPJ não é uma String");
        $this->assertTrue(
            preg_replace("/[^0-9]/", "", $cnpj->getValue()) === $cnpj->getValue(),
            "O CNPJ não está sendo desmascarado"
        );
        $this->assertTrue(
            boolval(preg_match("/^(\d{2}).(\d{3}).(\d{3})\/(\d{4})-(\d{2})$/", $cnpj->getMaskedValue())),
            "A classe não conseguiu mascarar o valor: $validCnpj != {$cnpj->getMaskedValue()}"
        );
        $this->assertTrue(
            $cnpj->getMaskedValue() === $validCnpj,
            "O CNPJ mascarado ficou diferente do CNPJ original"
        );
    }

    public function testInvalid(): void
    {
        $invalidCpf = "11.111.111/1111-11";
        $cnpj = new Cnpj($invalidCpf);
        $this->assertFalse($cnpj->isValid(), "CNPJ válido quando não deveria ser.");
        $this->assertNull($cnpj->getValue(), "O CNPJ não está nulo quando deveria estar.");
    }
}
