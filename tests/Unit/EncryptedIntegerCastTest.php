<?php


namespace RichardStyles\EloquentEncryption\Tests\Unit;


use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Storage;
use RichardStyles\EloquentEncryption\Casts\EncryptedInteger;
use RichardStyles\EloquentEncryption\EloquentEncryptionFacade;
use RichardStyles\EloquentEncryption\Tests\TestCase;
use RichardStyles\EloquentEncryption\Tests\Traits\WithRSAHelpers;

class EncryptedIntegerCastTest extends TestCase
{
    use WithRSAHelpers;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake();
    }

    /** @test */
    function encrypted_integer_cast_decrypts_values()
    {

            EloquentEncryptionFacade::shouldReceive('exists')
                ->andReturn(true)
                ->shouldReceive('decrypt')
                ->with('001100110011')
                ->andReturn('001100110011');

        $cast = new EncryptedInteger();

        $user = new User();

        $response = $cast->get($user, 'encrypted', '001100110011', []);

        $this->assertIsInt($response);
        $this->assertEquals(1100110011, $response);
    }

    /** @test */
    function encrypted_cast_encrypts_values()
    {
        EloquentEncryptionFacade::partialMock()
            ->shouldReceive('exists')
            ->andReturn(true)
            ->shouldReceive('encrypt')
            ->with(110011001100)
            ->andReturn('001100110011');

        $cast = new EncryptedInteger();

        $user = new User();

        $this->assertEquals('001100110011', $cast->set($user, 'encrypted', 110011001100, []));
    }
}