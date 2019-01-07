<?php

namespace Vkovic\LaravelMeta;

use Illuminate\Database\Eloquent\Model;
use Vkovic\LaravelMeta\Models\Meta;

class MetaHandler
{
    /**
     * @var Model
     */
    protected $metaModel;

    /**
     * MetaHandler constructor.
     *
     * @param Model|null $model Model that'll handle writing to meta table
     */
    public function __construct(Model $model = null)
    {
        $this->metaModel = $model ?? new Meta;
    }

    public function getModel()
    {
        return $this->metaModel;
    }

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
        $meta = $this->metaModel::where('key', $key)->first();

        if ($meta === null) {
            $meta = new $this->metaModel;
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
        $exists = $this->metaModel::where('key', $key)
            ->exists();

        if ($exists) {
            $message = "Can't create meta (key: $key). ";
            $message .= "Meta already exists";
            throw new \Exception($message);
        }

        $meta = new $this->metaModel;

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
            $meta = $this->metaModel::where('key', $key)
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
        $meta = $this->metaModel::where('key', $key)
            ->first();

        return $meta === null
            ? $default
            : $meta->value;
    }

    /**
     * Get multiple meta key value pairs using wildcard "*"
     *
     * @param      $query
     * @param null $default
     *
     * @return array
     */
    public function query($query, $default = null)
    {
        $query = str_replace('*', '%', $query);
        $meta = $this->metaModel::where('key', 'LIKE', $query)->get(['key', 'value', 'type']);

        if ($meta->isEmpty()) {
            return $default;
        }

        $data = [];
        foreach ($meta as $m) {
            $data[$m->key] = $m->value;
        }

        return $data;
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
        return $this->metaModel::where('key', $key)
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
        return $this->metaModel::count();
    }

    /**
     * Get all meta
     * for package realm
     *
     * @return array
     */
    public function all()
    {
        $meta = $this->metaModel::get(['key', 'value', 'type']);

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
        return $this->metaModel::pluck('key')->toArray();
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

        $this->metaModel::whereIn('key', $keys)->delete();
    }

    /**
     * Purge meta
     * for package realm
     *
     * @return int Number of records deleted
     */
    public function purge()
    {
        return $this->metaModel::realm()->delete();
    }
}