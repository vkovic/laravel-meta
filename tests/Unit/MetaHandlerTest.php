<?php

namespace Vkovic\LaravelMeta\Test\Integration;

use Vkovic\LaravelMeta\MetaHandler;
use Vkovic\LaravelMeta\Test\TestCase;

class MetaHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_set_meta()
    {
        $meta = new MetaHandler;

        //
        // Standard key value
        //

        $key = str_random();
        $value = str_random();

        $meta->set($key, $value);

        $this->assertDatabaseHas($this->table, [
            'key' => $key,
            'value' => $value
        ]);

        //
        // Realm type and metable id
        //

        $key = str_random();
        $value = str_random();
        $realm = str_random();
        $metableType = str_random();
        $metableId = str_random();

        $meta->set($key, $value, $realm, $metableType, $metableId);

        $this->assertDatabaseHas($this->table, [
            'key' => $key,
            'value' => $value,
            'realm' => $realm,
            'metable_type' => $metableType,
            'metable_id' => $metableId,
        ]);
    }

    /**
     * @test
     */
    public function it_can_get_meta()
    {
        $meta = new MetaHandler;

        $key = str_random();
        $value = str_random();

        $meta->set($key, $value);

        $this->assertEquals($value, $meta->get($key));
    }

    /**
     * @test
     */
    public function it_can_check_meta_exists()
    {
        $meta = new MetaHandler;

        $key = str_random();
        $value = str_random();

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

        //
        // Check count in custom realm
        //

        $count = rand(2, 10);
        $realm = str_random();
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            $meta->set($key, $value, $realm);
        }

        $this->assertTrue($meta->count($realm) === $count);

        //
        // Check count in custom realm with metable type and id
        //

        $realm = str_random();
        $metableType = str_random();
        $metableId = str_random();
        $meta->set(str_random(), str_random(), $realm, $metableType, $metableId);

        $this->assertTrue($meta->count($realm, $metableType, $metableId) === 1);
    }

    /**
     * @test
     */
    public function it_can_get_all_meta()
    {
        \DB::table($this->table)->truncate();

        $meta = new MetaHandler;

        $realm = str_random();
        $metableType = str_random();
        $metableId = str_random();

        $key1 = str_random();
        $value1 = str_random();
        $meta->set($key1, $value1, $realm, $metableType, $metableId);

        $key2 = str_random();
        $value2 = str_random();
        $meta->set($key2, $value2, $realm, $metableType, $metableId);

        $this->assertEquals([
            $key1 => $value1,
            $key2 => $value2,
        ], $meta->all($realm, $metableType, $metableId));
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

        // TODO: check keys when metable present
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

        // TODO: check keys when metable present
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

        // TODO: check keys when metable present
    }
}
