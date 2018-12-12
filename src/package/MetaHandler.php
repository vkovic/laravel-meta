<?php

namespace Vkovic\LaravelMeta;

use Vkovic\LaravelMeta\Models\Meta;

class MetaHandler
{
    /**
     * Package realm
     *
     * @var string
     */
    protected $realm = 'vkovic/laravel-meta';

    /**
     * Set meta at given key.
     * If meta exists, it'll be overwritten.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $type
     */
    public function set($key, $value, $type = 'string')
    {
        $meta = Meta::realm($this->realm)
            ->where('key', $key)
            ->first();

        if ($meta === null) {
            $meta = new Meta;
            $meta->key = $key;
        }

        $meta->value = $value;
        $meta->type = $type;
        $meta->realm = $this->realm;

        $meta->save();
    }

    /**
     * Create meta at given key.
     * If meta exists, exception will be thrown.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $type
     *
     * @throws \Exception
     */
    public function create($key, $value, $type = 'string')
    {
        $exists = Meta::realm($this->realm)
            ->where('key', $key)
            ->exists();

        if ($exists) {
            $message = "Can't create meta (key: $key). ";
            $message .= "Meta already exists";
            throw new \Exception($message);
        }

        $meta = new Meta;

        $meta->key = $key;
        $meta->type = $type;
        $meta->value = $value;
        $meta->realm = $this->realm;

        $meta->save();
    }

    /**
     * Create meta at given key.
     * If meta doesn't exists, exception will be thrown.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $type
     *
     * @throws \Exception
     */
    public function update($key, $value, $type = 'string')
    {
        try {
            $meta = Meta::realm($this->realm)
                ->where('key', $key)
                ->firstOrFail();
        } catch (\Exception $e) {
            $message = "Can't update meta (key: $key). ";
            $message .= "Meta doesn't exist";

            throw new \Exception($message);
        }

        $meta->type = $type;
        $meta->value = $value;
        $meta->realm = $this->realm;

        $meta->save();
    }

    /**
     * Get meta at given key
     *
     * @param string $key
     * @param mixed  $default
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @return array
     */
    public function get($key, $default = null)
    {
        $meta = Meta::realm($this->realm)
            ->where('key', $key)
            ->first();

        return $meta === null
            ? $default
            : $meta->value;
    }

    /**
     * Check if meta key record exists by given key
     * and optionally realm and metable
     *
     * @param string $key
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @return bool
     */
    public function exists($key)
    {
        return Meta::realm($this->realm)
            ->where('key', $key)
            ->exists();
    }

    /**
     * Count all meta for specified realm
     *
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @return int
     */
    public function count()
    {
        return Meta::realm($this->realm)
            ->count();
    }


    /**
     * Get all meta for package realm
     *
     * @return array
     */
    public function all()
    {
        $meta = Meta::realm($this->realm)
            ->get(['key', 'value', 'type']);

        $data = [];
        foreach ($meta as $m) {
            $data[$m->key] = $m->value;
        }

        return $data;
    }

    /**
     * Get all meta keys for package realm
     *
     * @return array
     */
    public function keys()
    {
        return Meta::realm($this->realm)
            ->pluck('key')
            ->toArray();
    }

    /**
     * Remove meta at given key or array of keys
     *
     * @param string|array $key
     */
    public function remove($key)
    {
        $keys = (array) $key;

        Meta::realm($this->realm)
            ->whereIn('key', $keys)
            ->delete();
    }

    /**
     * Purge meta in package realm
     *
     * @return int Number of records deleted
     *
     * @throws \Exception
     */
    public function purge()
    {
        return Meta::realm($this->realm)
            ->delete();
    }
}