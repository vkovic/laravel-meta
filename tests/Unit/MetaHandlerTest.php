<?php

namespace Vkovic\LaravelMeta\Test\Unit;

use Vkovic\LaravelMeta\MetaHandler;
use Vkovic\LaravelMeta\Test\TestCase;

class MetaHandlerTest extends TestCase
{
    /**
     * Valid data provider for: key, value and type
     *
     * @return array
     */
    public function validKeyValueTypeProvider()
    {
        return [
            // key | value | type
            [str_random(), str_random(), 'string'],
            [str_random(), null, 'string'],
            [str_random(), 1, 'int'],
            [str_random(), 1.1, 'float'],
            [str_random(), true, 'boolean'],
            [str_random(), false, 'boolean'],
            [str_random(), [], 'array'],
            [str_random(), range(1, 1), 'array'],
        ];
    }

    /**
     * @test
     * @dataProvider validKeyValueTypeProvider
     */
    public function it_can_set_and_get_meta($key, $value, $type)
    {
        $meta = new MetaHandler;

        $meta->set($key, $value, $type);

        $this->assertSame($meta->get($key), $value);
    }

    /**
     * @test
     * @dataProvider validKeyValueTypeProvider
     */
    public function it_can_create_meta($key, $value, $type)
    {
        $meta = new MetaHandler;

        $meta->set($key, $value, $type);

        $meta->update($key, $value, $type);

        $this->assertSame($meta->get($key), $value);
    }

    /**
     * @test
     * @dataProvider validKeyValueTypeProvider
     */
    public function it_can_update_meta($key, $value, $type)
    {
        $meta = new MetaHandler;

        $meta->set($key, $value, $type);

        //dd(\DB::table($this->table)->get());

        $meta->update($key, $value, $type);

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
     * @dataProvider validKeyValueTypeProvider
     */
    public function it_can_set_and_get_meta_without_passing_type($key, $value, $unused)
    {
        $meta = new MetaHandler;

        $meta->set($key, $value);

        $this->assertEquals($meta->get($key), $value);
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
     * @dataProvider validKeyValueTypeProvider
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
        \DB::table($this->table)->truncate();

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
        \DB::table($this->table)->truncate();

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
        \DB::table($this->table)->truncate();

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
        \DB::table($this->table)->truncate();

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
        \DB::table($this->table)->truncate();

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
