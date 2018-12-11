<?php

namespace Vkovic\LaravelMeta;

use Vkovic\LaravelMeta\Models\Meta;

class MetaHandler
{
    /**
     * Set meta at given key.
     * If value exists, it'll be overwritten
     *
     * @param string $key
     * @param mixed  $value
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @throws \Exception
     */
    public function set($key, $value, $type = 'string', $realm = null, $metableType = '', $metableId = '')
    {
        $meta = Meta::filter($realm, $metableType, $metableId, $key)
            ->first();

        if ($meta === null) {
            $meta = new Meta;
            $meta->key = $key;
        }

        $meta->value = $value;
        $meta->type = $type;
        $meta->realm = $realm;
        $meta->metable_type = $metableType;
        $meta->metable_id = $metableId;

        $meta->save();
    }

    public function create($key, $value, $type = 'string', $realm = null, $metableType = '', $metableId = '')
    {
        $exists = Meta::filter($realm, $metableType, $metableId, $key)
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
        $meta->realm = $realm;
        $meta->metable_type = $metableType;
        $meta->metable_id = $metableId;

        $meta->save();
    }

    public function update($key, $value, $type = 'string', $realm = null, $metableType = '', $metableId = '')
    {
        try {
            $meta = Meta::filter($realm, $metableType, $metableId, $key)
                ->firstOrFail();
        } catch (\Exception $e) {
            $message = "Can't update meta (key: $key). ";
            $message .= "Meta doesn't exist";

            throw new \Exception($message);
        }

        $meta->type = $type;
        $meta->value = $value;
        $meta->realm = $realm;
        $meta->metable_type = $metableType;
        $meta->metable_id = $metableId;

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
     * @throws \Exception
     *
     * @return array
     */
    public function get($key, $default = null, $realm = null, $metableType = '', $metableId = '')
    {
        $meta = Meta::filter($realm, $metableType, $metableId)
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
    public function exists($key, $realm = null, $metableType = '', $metableId = '')
    {
        return Meta::filter($realm, $metableType, $metableId)
            ->where('key', $key)
            ->exists();
    }

    /**
     * Count all meta for specified realm, type and id
     *
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @return bool
     */
    public function count($realm = null, $metableType = '', $metableId = '')
    {
        return Meta::filter($realm, $metableType, $metableId)
            ->count();
    }


    /**
     * Get all meta
     *
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @throws \Exception
     *
     * @return array
     */
    public function all($realm = null, $metableType = '', $metableId = '')
    {
        //$meta = Meta::filter($realm, $metableType, $metableId)->get();
        $meta = Meta::filter($realm, $metableType, $metableId)
            ->get(['key', 'value', 'type']);

        $data = [];
        foreach ($meta as $m) {
            $data[$m->key] = $m->value;
        }

        return $data;

    }

    /**
     * Get all meta keys
     *
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @throws \Exception
     *
     * @return array
     */
    public function keys($realm = null, $metableType = '', $metableId = '')
    {
        return Meta::filter($realm, $metableType, $metableId)
            ->pluck('key')
            ->toArray();
    }

    /**
     * Remove meta at given key or array of keys
     *
     * @param string $key
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @throws \Exception
     */
    public function remove($key, $realm = null, $metableType = '', $metableId = '')
    {
        $keys = (array) $key;

        Meta::filter($realm, $metableType, $metableId)
            ->whereIn('key', $keys)
            ->delete();
    }

    /**
     * Purge meta
     *
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @return int Number of records deleted
     *
     * @throws \Exception
     */
    public function purge($realm = null, $metableType = '', $metableId = '')
    {
        return Meta::filter($realm, $metableType, $metableId)->delete();
    }
}