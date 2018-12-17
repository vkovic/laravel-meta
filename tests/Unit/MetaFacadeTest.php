<?php

namespace Vkovic\LaravelMeta\Test\Unit;

use Meta;
use Vkovic\LaravelMeta\Test\TestCase;

class MetaFacadeTest extends TestCase
{
    /**
     * Valid data provider for: key, value and type
     *
     * @return array
     */
    public function keyValueTypeProvider()
    {
        return [
            // key | value
            [str_random(), str_random()],
            [str_random(), null],
            [str_random(), 1],
            [str_random(), 1.1],
            [str_random(), true],
            [str_random(), false],
            [str_random(), []],
            [str_random(), range(1, 10)],
        ];
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_set_and_get_meta($key, $value)
    {
        Meta::set($key, $value);

        $this->assertSame(Meta::get($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_create_meta($key, $value)
    {
        Meta::set($key, $value);
        Meta::update($key, $value);

        $this->assertSame(Meta::get($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_update_meta($key, $value)
    {
        Meta::set($key, $value);
        Meta::update($key, $value);

        $this->assertSame(Meta::get($key), $value);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_updating_non_existing_meta()
    {
        $this->expectExceptionMessage("Can't update");


        Meta::update('unexistingKey', '');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_creating_same_meta()
    {
        $this->expectExceptionMessage("Can't create");


        Meta::set('foo', 'bar');

        Meta::create('foo', '');
    }

    /**
     * @test
     */
    public function it_will_return_default_value_when_key_not_exist()
    {
        $default = str_random();

        $this->assertEquals($default, Meta::get('nonExistingKey', $default));
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_check_meta_exists($key, $value)
    {
        Meta::set($key, $value);

        $this->assertTrue(Meta::exists($key));
        $this->assertFalse(Meta::exists(str_random()));
    }

    /**
     * @test
     */
    public function it_can_count_meta()
    {
        //
        // Check zero count
        //


        $this->assertTrue(Meta::count() === 0);

        //
        // Check count in default realm
        //

        $count = rand(0, 10);
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            Meta::set($key, $value);
        }

        $this->assertTrue(Meta::count() === $count);
    }

    /**
     * @test
     */
    public function it_can_get_all_meta()
    {
        $key1 = str_random();
        $value1 = str_random();
        Meta::set($key1, $value1);

        $key2 = str_random();
        $value2 = str_random();
        Meta::set($key2, $value2);

        $this->assertEquals([
            $key1 => $value1,
            $key2 => $value2,
        ], Meta::all());
    }


    /**
     * @test
     */
    public function it_can_get_all_keys()
    {
        $count = rand(0, 10);

        if ($count === 0) {
            $this->assertEmpty(Meta::keys());
        }

        $keysToSave = [];
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $keysToSave[] = $key;

            Meta::set($key, '');
        }

        $metaKeys = Meta::keys();

        foreach ($keysToSave as $keyToSave) {
            $this->assertContains($keyToSave, $metaKeys);
        }
    }

    /**
     * @test
     */
    public function it_can_remove_meta_by_key()
    {
        $key = str_random();
        $value = str_random();

        Meta::set($key, $value);
        Meta::remove($key);

        $this->assertEmpty(Meta::all());
    }

    /**
     * @test
     */
    public function it_can_purge_meta()
    {
        $count = rand(0, 10);
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            Meta::set($key, $value);
        }

        Meta::purge();

        $this->assertEmpty(Meta::all());
    }
}
