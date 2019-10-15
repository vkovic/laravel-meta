<?php

namespace Vkovic\LaravelMeta\Test\Unit;

use Illuminate\Support\Str;
use Vkovic\LaravelMeta\Models\Meta;
use Vkovic\LaravelMeta\Test\TestCase;

class MetaModelTest extends TestCase
{
    /**
     * Invalid data provider for key
     *
     * @return array
     */
    public function invalidKeyProvider()
    {
        return [
            [[]], // array
            [new \stdClass()], // object
            [rand(-100, 100) / 10], // float
            [rand(-100, 100)] // int
        ];
    }

    /**
     * @test
     */
    public function it_can_set_key()
    {
        $key = 'foo';
        $meta = new Meta;

        $meta->key = $key;
        $meta->value = '';

        $meta->save();

        $this->assertDatabaseHas((new Meta)->getTable(), [
            'key' => $key
        ]);
    }

    /**
     * @test
     * @dataProvider invalidKeyProvider
     */
    public function it_throws_exception_on_invalid_key_type($key)
    {
        $this->expectExceptionMessage('Invalid key type');

        $meta = new Meta;

        $meta->key = $key;
    }

    /**
     * @test
     */
    public function it_throws_exception_on_invalid_key_length()
    {
        $this->expectExceptionMessage('Invalid key length');

        $meta = new Meta;

        $meta->key = Str::random(rand(129, 200));
    }

    /**
     * @test
     */
    public function it_throws_exception_when_trying_to_set_type_explicitly()
    {
        $this->expectExceptionMessage("Meta type can't be set explicitly");

        $meta = new Meta;

        $meta->type = '';
    }
}
