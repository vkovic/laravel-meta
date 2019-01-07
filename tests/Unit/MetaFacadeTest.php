<?php

namespace Vkovic\LaravelMeta\Test\Unit;

use Meta;
use Vkovic\LaravelMeta\Models\Meta as MetaModel;
use Vkovic\LaravelMeta\Test\TestCase;

class MetaFacadeTest extends TestCase
{
    /**
     * Valid data provider for: key and value
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
     */
    public function it_saves_to_correct_realm()
    {
        Meta::set('foo', '');

        $this->assertDatabaseHas('meta', [
            'realm' => MetaModel::getRealm()
        ]);
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
        Meta::create($key, $value);

        $this->assertSame(Meta::get($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_query_meta($key, $value)
    {
        Meta::set($key, $value);

        $keyStart = substr($key, 0, 10);
        $keyMiddle = substr($key, 4, 8);
        $keyEnd = substr($key, 8, 15);

        $this->assertEquals(Meta::query("$keyStart*")[$key], $value);
        $this->assertEquals(Meta::query("*$keyMiddle*")[$key], $value);
        $this->assertEquals(Meta::query("*$keyEnd")[$key], $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_update_meta($key, $value)
    {
        $newValue = str_random();

        Meta::set($key, $value);
        Meta::update($key, $newValue);

        $this->assertSame(Meta::get($key), $newValue);
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
        // Check zero count
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
        $value2 = range(0, 10);
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
        $this->assertEmpty(Meta::keys());

        $keysToSave = [];
        for ($i = 0; $i < rand(1, 10); $i++) {
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

        $this->assertEquals(0, Meta::count());
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

        $this->assertEquals(0, Meta::count());
    }
}
