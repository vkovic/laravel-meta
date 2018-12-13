<?php

namespace Vkovic\LaravelMeta\Test\Unit;

use Vkovic\LaravelMeta\Models\Meta;
use Vkovic\LaravelMeta\Test\TestCase;

class MetaModelTest extends TestCase
{
    /**
     * Valid data provider for key
     *
     * @return array
     */
    public function validKeyProvider()
    {
        return [
            [-1],
            [0],
            [random_int(1, 100)],
            ['foo'],
            [str_random(rand(0, 10))],
        ];
    }

    /**
     * Invalid data provider for key
     *
     * @return array
     */
    public function invalidKeyProvider()
    {
        return [
            [[]],
            [new \stdClass()],
            [1.1],
        ];
    }

    /**
     * @test
     * @dataProvider validKeyProvider
     */
    public function it_can_set_key($key)
    {
        $meta = new Meta;

        $meta->key = $key;
        $meta->value = '';
        $meta->type = 'string';

        $meta->save();

        $this->assertDatabaseHas((new Meta)->getTable(), [
            'key' => $key
        ]);
    }

    /**
     * @test
     * @dataProvider invalidKeyProvider
     */
    public function it_throws_exception_on_wrong_key_type($key)
    {
        $this->expectExceptionMessage('Invalid key type');

        $meta = new Meta;

        $meta->key = $key;
    }

    /**
     * @test
     */
    public function it_throws_exception_on_wrong_key_length()
    {
        $this->expectExceptionMessage('Invalid key length');

        $meta = new Meta;

        $meta->key = str_random(rand(129, 200));
    }
}
