<?php

namespace Vkovic\LaravelMeta\Test\Unit;

use Illuminate\Database\Eloquent\Model;
use Vkovic\LaravelMeta\MetaHandler;
use Vkovic\LaravelMeta\Models\Meta;
use Vkovic\LaravelMeta\Test\TestCase;

class MetaHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_constructs_correctly()
    {
        $handler = new MetaHandler(new Meta);

        $this->assertInstanceOf(Model::class, $handler->getModel());
    }

    /**
     * @test
     */
    public function it_constructs_correctly_without_parameters()
    {
        $handler = new MetaHandler;

        $this->assertInstanceOf(Model::class, $handler->getModel());
    }

    /**
     * Quick test to confirm meta handler works without facade
     *
     * @test
     */
    public function it_works()
    {
        $handler = new MetaHandler;
        $handler->set('foo', 'bar');

        $handler = new MetaHandler(new Meta);
        $handler->set('bar', 'baz');

        $this->assertDatabaseHas('meta', ['key' => 'foo', 'value' => 'bar']);
        $this->assertDatabaseHas('meta', ['key' => 'bar', 'value' => 'baz']);
    }
}
