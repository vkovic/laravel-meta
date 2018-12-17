<?php

namespace Vkovic\LaravelMeta;

use Vkovic\LaravelMeta\Models\Meta;

class MetaHandler
{
    /**
     * Set meta at given key
     * for package realm.
     * If meta exists, it'll be overwritten.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $meta = Meta::where('key', $key)->first();

        if ($meta === null) {
            $meta = new Meta;
            $meta->key = $key;
        }

        $meta->value = $value;

        $meta->save();
    }

    /**
     * Create meta at given key
     * for package realm.
     * If meta exists, exception will be thrown.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws \Exception
     */
    public function create($key, $value)
    {
        $exists = Meta::where('key', $key)
            ->exists();

        if ($exists) {
            $message = "Can't create meta (key: $key). ";
            $message .= "Meta already exists";
            throw new \Exception($message);
        }

        $meta = new Meta;

        $meta->key = $key;
        $meta->value = $value;

        $meta->save();
    }

    /**
     * Update meta at given key
     * for package realm.
     * If meta doesn't exists, exception will be thrown.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws \Exception
     */
    public function update($key, $value)
    {
        try {
            $meta = Meta::where('key', $key)
                ->firstOrFail();
        } catch (\Exception $e) {
            $message = "Can't update meta (key: $key). ";
            $message .= "Meta doesn't exist";

            throw new \Exception($message);
        }

        $meta->value = $value;

        $meta->save();
    }

    /**
     * Get meta at given key
     * for package realm
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    public function get($key, $default = null)
    {
        $meta = Meta::where('key', $key)
            ->first();

        return $meta === null
            ? $default
            : $meta->value;
    }

    /**
     * Check if meta key record exists by given key
     * for package realm
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return Meta::where('key', $key)
            ->exists();
    }

    /**
     * Count all meta
     * for package realm
     *
     * @param string $realm
     *
     * @return int
     */
    public function count()
    {
        return Meta::count();
    }

    /**
     * Get all meta
     * for package realm
     *
     * @return array
     */
    public function all()
    {
        $meta = Meta::get(['key', 'value', 'type']);

        $data = [];
        foreach ($meta as $m) {
            $data[$m->key] = $m->value;
        }

        return $data;
    }

    /**
     * Get all meta keys
     * for package realm
     *
     * @return array
     */
    public function keys()
    {
        return Meta::pluck('key')->toArray();
    }

    /**
     * Remove meta at given key or array of keys
     * for package realm
     *
     * @param string|array $key
     */
    public function remove($key)
    {
        $keys = (array) $key;

        Meta::whereIn('key', $keys)->delete();
    }

    /**
     * Purge meta
     * for package realm
     *
     * @return int Number of records deleted
     */
    public function purge()
    {
        return Meta::realm()->delete();
    }
}