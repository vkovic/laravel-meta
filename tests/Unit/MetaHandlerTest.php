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
        $metaHandler = new MetaHandler();

        if ($type === null) {
            $metaHandler->setMeta($key, $value);
        } else {
            $metaHandler->setMeta($key, $value, $type);
        }

        $this->assertSame($metaHandler->getMeta($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_create_meta($key, $value, $type = null)
    {
        $metaHandler = new MetaHandler();

        if ($type === null) {
            $metaHandler->setMeta($key, $value);
            $metaHandler->updateMeta($key, $value);
        } else {
            $metaHandler->setMeta($key, $value, $type);
            $metaHandler->updateMeta($key, $value, $type);
        }

        $this->assertSame($metaHandler->getMeta($key), $value);
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_update_meta($key, $value, $type = null)
    {
        $metaHandler = new MetaHandler();

        if ($type === null) {
            $metaHandler->setMeta($key, $value);
            $metaHandler->updateMeta($key, $value);
        } else {
            $metaHandler->setMeta($key, $value, $type);
            $metaHandler->updateMeta($key, $value, $type);
        }

        $this->assertSame($metaHandler->getMeta($key), $value);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_updating_non_existing_meta()
    {
        $this->expectExceptionMessage("Can't update");

        $metaHandler = new MetaHandler();

        $metaHandler->updateMeta('unexistingKey', '');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_creating_same_meta()
    {
        $this->expectExceptionMessage("Can't create");

        $metaHandler = new MetaHandler();

        $metaHandler->setMeta('foo', 'bar');

        $metaHandler->createMeta('foo', '');
    }

    /**
     * @test
     */
    public function it_will_return_default_value_when_key_not_exist()
    {
        $metaHandler = new MetaHandler();

        $default = str_random();

        $this->assertEquals($default, $metaHandler->getMeta('nonExistingKey', $default));
    }

    /**
     * @test
     * @dataProvider keyValueTypeProvider
     */
    public function it_can_check_meta_exists($key, $value)
    {
        $metaHandler = new MetaHandler();

        $metaHandler->setMeta($key, $value);

        $this->assertTrue($metaHandler->metaExists($key));
        $this->assertFalse($metaHandler->metaExists(str_random()));
    }

    /**
     * @test
     */
    public function it_can_count_meta()
    {
        //
        // Check zero count
        //

        $metaHandler = new MetaHandler();

        $this->assertTrue($metaHandler->countMeta() === 0);

        //
        // Check count in default realm
        //

        $count = rand(0, 10);
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            $metaHandler->setMeta($key, $value);
        }

        $this->assertTrue($metaHandler->countMeta() === $count);
    }

    /**
     * @test
     */
    public function it_can_get_all_meta()
    {
        $metaHandler = new MetaHandler();

        $key1 = str_random();
        $value1 = str_random();
        $metaHandler->setMeta($key1, $value1);

        $key2 = str_random();
        $value2 = str_random();
        $metaHandler->setMeta($key2, $value2);

        $this->assertEquals([
            $key1 => $value1,
            $key2 => $value2,
        ], $metaHandler->allMeta());
    }


    /**
     * @test
     */
    public function it_can_get_all_keys()
    {
        $metaHandler = new MetaHandler();

        $count = rand(0, 10);

        if ($count === 0) {
            $this->assertEmpty($metaHandler->metaKeys());
        }

        $keysToSave = [];
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $keysToSave[] = $key;

            $metaHandler->setMeta($key, '');
        }

        $metaKeys = $metaHandler->metaKeys();

        foreach ($keysToSave as $keyToSave) {
            $this->assertContains($keyToSave, $metaKeys);
        }
    }

    /**
     * @test
     */
    public function it_can_remove_meta_by_key()
    {
        $metaHandler = new MetaHandler();

        $key = str_random();
        $value = str_random();

        $metaHandler->setMeta($key, $value);
        $metaHandler->removeMeta($key);

        $this->assertEmpty($metaHandler->allMeta());
    }

    /**
     * @test
     */
    public function it_can_purge_meta()
    {
        $metaHandler = new MetaHandler();

        $count = rand(0, 10);
        for ($i = 0; $i < $count; $i++) {
            $key = str_random();
            $value = str_random();
            $metaHandler->setMeta($key, $value);
        }

        $metaHandler->purgeMeta();

        $this->assertEmpty($metaHandler->allMeta());
    }
}
