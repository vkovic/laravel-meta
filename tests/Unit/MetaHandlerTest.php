<?php

namespace Vkovic\LaravelMeta\Test\Unit;

use Vkovic\LaravelMeta\MetaHandler;
use Vkovic\LaravelMeta\Models\Meta;
use Vkovic\LaravelMeta\Test\TestCase;

class MetaHandlerTest extends TestCase
{
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
     */
    public function it_can_set_and_get_meta($key, $value, $type = null)
    {
        $meta = new MetaHandler;

        if ($type === null) {
            $meta->set($key, $value);
        } else {
            $meta->set($key, $value, $type);
        }

        $this->assertSame($meta->get($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_create_meta($key, $value, $type = null)
    {
        $meta = new MetaHandler;

        if ($type === null) {
            $meta->set($key, $value);
            $meta->update($key, $value);
        } else {
            $meta->set($key, $value, $type);
            $meta->update($key, $value, $type);
        }

        $this->assertSame($meta->get($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_update_meta($key, $value, $type = null)
    {
        $meta = new MetaHandler;

        if ($type === null) {
            $meta->set($key, $value);
            $meta->update($key, $value);
        } else {
            $meta->set($key, $value, $type);
            $meta->update($key, $value, $type);
        }

        $this->assertSame($meta->get($key), $value);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_updating_non_existing_meta()
    {
        $this->expectExceptionMessage("Can't update");

        $meta = new MetaHandler;

        $meta->update('unexistingKey', '');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_creating_same_meta()
    {
        $this->expectExceptionMessage("Can't create");

        $meta = new MetaHandler;

        $meta->set('foo', 'bar');

        $meta->create('foo', '');
    }

    /**
     * @test
     */
    public function it_will_return_default_value_when_key_not_exist()
    {
        $meta = new MetaHandler;

        $default = str_random();

        $this->assertEquals($default, $meta->get('nonExistingKey', $default));
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_check_meta_exists($key, $value)
    {
        $meta = new MetaHandler;

        $meta->set($key, $value);

        $this->assertTrue($meta->exists($key));
        $this->assertFalse($meta->exists(str_random()));
    }

    /**
     * @test
     */
    public function it_can_count_meta()
    {
        \DB::table((new Meta)->getTable())->truncate();

        $meta = new MetaHandler;

        //
        // Check zero count
        //

        $this->assertTrue($meta->count() === 0);

        //
        // Check count in default realm
        //

        $count = rand(0, 10);
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            $meta->set($key, $value);
        }

        $this->assertTrue($meta->count() === $count);
    }

    /**
     * @test
     */
    public function it_can_get_all_meta()
    {
        \DB::table((new Meta)->getTable())->truncate();

        $meta = new MetaHandler;

        $key1 = str_random();
        $value1 = str_random();
        $meta->set($key1, $value1);

        $key2 = str_random();
        $value2 = str_random();
        $meta->set($key2, $value2);

        $this->assertEquals([
            $key1 => $value1,
            $key2 => $value2,
        ], $meta->all());
    }


    /**
     * @test
     */
    public function it_can_get_all_keys()
    {
        \DB::table((new Meta)->getTable())->truncate();

        $meta = new MetaHandler;

        $count = rand(0, 10);

        if ($count === 0) {
            $this->assertEmpty($meta->keys());
        }

        $keysToSave = [];
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $keysToSave[] = $key;

            $meta->set($key, '');
        }

        $metaKeys = $meta->keys();

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

        $meta = new MetaHandler;

        $key = str_random();
        $value = str_random();

        $meta->set($key, $value);
        $meta->remove($key);

        $this->assertEmpty($meta->all());
    }

    /**
     * @test
     */
    public function it_can_purge_meta()
    {
        \DB::table((new Meta)->getTable())->truncate();

        $meta = new MetaHandler;

        $count = rand(0, 10);
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            $meta->set($key, $value);
        }

        $meta->purge();

        $this->assertEmpty($meta->all());
    }
}
