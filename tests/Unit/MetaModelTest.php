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
     * Valid data provider for: key, value and type
     *
     * @return array
     */
    public function keyValueTypeProvider()
    {
        return [
            // key | value | type
            [str_random(), str_random()],
            [str_random(), str_random(), 'string'],
            [str_random(), null],
            [str_random(), null, 'null'],
            [str_random(), 1, 'int'],
            [str_random(), 1.1, 'float'],
            [str_random(), true, 'boolean'],
            [str_random(), false, 'boolean'],
            [str_random(), []],
            [str_random(), [], 'array'],
            [str_random(), range(1, 10)],
            [str_random(), range(1, 10), 'array'],
        ];
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     * @group        xxx
     */
    public function it_can_set_and_get_meta($key, $value, $type = null)
    {
        if ($type === null) {
            Meta::setMeta($key, $value);
        } else {
            Meta::setMeta($key, $value, $type);
        }

        $this->assertSame(Meta::getMeta($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     * @group        xxx
     */
    public function it_can_create_meta($key, $value, $type = null)
    {
        if ($type === null) {
            Meta::setMeta($key, $value);
            Meta::updateMeta($key, $value);
        } else {
            Meta::setMeta($key, $value, $type);
            Meta::updateMeta($key, $value, $type);
        }

        $this->assertSame(Meta::getMeta($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     * @group        xxx
     */
    public function it_can_update_meta($key, $value, $type = null)
    {
        if ($type === null) {
            Meta::setMeta($key, $value);
            Meta::updateMeta($key, $value);
        } else {
            Meta::setMeta($key, $value, $type);
            Meta::updateMeta($key, $value, $type);
        }

        $this->assertSame(Meta::getMeta($key), $value);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_updating_non_existing_meta()
    {
        $this->expectExceptionMessage("Can't update");

        Meta::updateMeta('unexistingKey', '');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_creating_same_meta()
    {
        $this->expectExceptionMessage("Can't create");

        Meta::setMeta('foo', 'bar');

        Meta::createMeta('foo', '');
    }

    /**
     * @test
     */
    public function it_will_return_default_value_when_key_not_exist()
    {
        $default = str_random();

        $this->assertEquals($default, Meta::getMeta('nonExistingKey', $default));
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_check_meta_exists($key, $value)
    {
        Meta::setMeta($key, $value);

        $this->assertTrue(Meta::metaExists($key));
        $this->assertFalse(Meta::metaExists(str_random()));
    }

    /**
     * @test
     * @group qwe
     */
    public function it_can_count_meta()
    {
        \DB::table((new Meta)->getTable())->truncate();

        //
        // Check zero count
        //

        $this->assertTrue(Meta::countMeta() === 0);

        //
        // Check count in default realm
        //

        $count = rand(0, 10);
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            Meta::setMeta($key, $value);
        }

        $this->assertTrue(Meta::countMeta() === $count);
    }

    /**
     * @test
     */
    public function it_can_get_all_meta()
    {
        \DB::table((new Meta)->getTable())->truncate();

        $key1 = str_random();
        $value1 = str_random();
        Meta::setMeta($key1, $value1);

        $key2 = str_random();
        $value2 = str_random();
        Meta::setMeta($key2, $value2);

        $this->assertEquals([
            $key1 => $value1,
            $key2 => $value2,
        ], Meta::allMeta());
    }


    /**
     * @test
     */
    public function it_can_get_all_keys()
    {
        \DB::table((new Meta)->getTable())->truncate();

        $count = rand(0, 10);

        if ($count === 0) {
            $this->assertEmpty(Meta::metaKeys());
        }

        $keysToSave = [];
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $keysToSave[] = $key;

            Meta::setMeta($key, '');
        }

        $metaKeys = Meta::metaKeys();

        foreach ($keysToSave as $keyToSave) {
            $this->assertContains($keyToSave, $metaKeys);
        }
    }

    /**
     * @test
     */
    public function it_can_remove_meta_by_key()
    {
        \DB::table((new Meta)->getTable())->truncate();

        $key = str_random();
        $value = str_random();

        Meta::setMeta($key, $value);
        Meta::removeMeta($key);

        $this->assertEmpty(Meta::allMeta());
    }

    /**
     * @test
     */
    public function it_can_purge_meta()
    {
        \DB::table((new Meta)->getTable())->truncate();


        $count = rand(0, 10);
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            Meta::setMeta($key, $value);
        }

        Meta::purgeMeta();

        $this->assertEmpty(Meta::allMeta());
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
