<?php

namespace Vkovic\LaravelMeta\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $guarded = ['id'];

    protected $table = 'meta';

    protected static $realm = 'vkovic/laravel-meta';

    protected $allowedTypes = [
        'null',
        'string',
        'int', 'integer',
        'float', 'double', 'real',
        'bool', 'boolean',
        'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function (Meta $model) {
            $value = $model->attributes['value'];
            $type = $model->attributes['type'];

            // Force type and convert attribute
            // in case of array and bool
            if (is_array($value)) {
                $type = 'array';
                $value = json_encode($value);
            } elseif (is_bool($value)) {
                $type = 'bool';
                $value = $value === true ? '1' : '0';
            } elseif ($value === null) {
                $type = 'null';
            }

            $model->attributes['type'] = $type;
            $model->attributes['value'] = $value;
        });
    }

    /**
     * Filter meta by realm and order by meta key
     *
     * @param Builder $query
     *
     * @return mixed
     */
    public function scopeRealm(Builder $query)
    {
        return $query->where(['realm' => static::$realm])->orderBy('key');
    }

    /**
     * Key setter
     *
     * @param $value
     *
     * @throws \Exception
     */
    public function setKeyAttribute($value)
    {
        // Key must be either integer or string
        if (!is_string($value) && !is_int($value)) {
            throw new \Exception('Invalid key type. Allowed: string, integer');
        }

        $value = (string) $value;

        // Key must be below 129 chars
        if (strlen($value) > 128) {
            throw new \Exception('Invalid key length. Key must be below 128 chars');
        }

        $this->attributes['key'] = $value;
    }

    /**
     * Type setter
     *
     * @param $value
     *
     * @throws \Exception
     */
    public function setTypeAttribute($value)
    {
        if (!in_array($value, $this->allowedTypes)) {
            // Convert value to string
            $value = is_array($value)
                ? json_encode($value)
                : (string) $value;

            $message = "Invalid type $value . Allowed types: ";
            $message .= implode(', ', $this->allowedTypes);
            throw new \Exception($message);
        }

        $this->attributes['type'] = $value;
    }

    /**
     * Value getter
     *
     * @param $value
     *
     * @return float|array|string
     */
    public function getValueAttribute($value)
    {
        $type = $this->attributes['type'];

        if ($type == 'string' || $type == 'null') {
            return $value;
        } elseif ($type == 'array') {
            return json_decode($value, true);
        } elseif ($type == 'int' || $type == 'integer') {
            return (int) $value;
        } elseif ($type == 'float' || $type == 'double' || $type == 'real') {
            return (float) $value;
        } elseif ($type == 'bool' || $type == 'boolean') {
            return (bool) $value;
        }
    }

    /**
     * Set meta at given key
     * for package realm.
     * If meta exists, it'll be overwritten.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $type
     */
    public static function setMeta($key, $value, $type = 'string')
    {
        $meta = static::realm()
            ->where('key', $key)
            ->first();

        if ($meta === null) {
            $meta = new static;
            $meta->key = $key;
        }

        $meta->value = $value;
        $meta->type = $type;
        $meta->realm = static::$realm;

        $meta->save();
    }

    /**
     * Create meta at given key
     * for package realm.
     * If meta exists, exception will be thrown.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $type
     *
     * @throws \Exception
     */
    public static function createMeta($key, $value, $type = 'string')
    {
        $exists = static::realm()
            ->where('key', $key)
            ->exists();

        if ($exists) {
            $message = "Can't create meta (key: $key). ";
            $message .= "Meta already exists";
            throw new \Exception($message);
        }

        $meta = new static;

        $meta->key = $key;
        $meta->type = $type;
        $meta->value = $value;
        $meta->realm = static::$realm;

        $meta->save();
    }

    /**
     * Create meta at given key
     * for package realm.
     * If meta doesn't exists, exception will be thrown.
     *
     * @param string $key
     * @param mixed  $value
     * @param string $type
     *
     * @throws \Exception
     */
    public static function updateMeta($key, $value, $type = 'string')
    {
        try {
            $meta = static::realm()
                ->where('key', $key)
                ->firstOrFail();
        } catch (\Exception $e) {
            $message = "Can't update meta (key: $key). ";
            $message .= "Meta doesn't exist";

            throw new \Exception($message);
        }

        $meta->type = $type;
        $meta->value = $value;
        $meta->realm = static::$realm;

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
    public static function getMeta($key, $default = null)
    {
        $meta = static::realm()
            ->where('key', $key)
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
    public static function metaExists($key)
    {
        return static::realm()
            ->where('key', $key)
            ->exists();
    }

    /**
     * Count all meta
     * for package realm
     *
     * @param string $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @return int
     */
    public static function countMeta()
    {
        return static::realm()
            ->count();
    }


    /**
     * Get all meta
     * for package realm
     *
     * @return array
     */
    public static function allMeta()
    {
        $meta = static::realm()
            ->get(['key', 'value', 'type']);

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
    public static function metaKeys()
    {
        return static::realm()
            ->pluck('key')
            ->toArray();
    }

    /**
     * Remove meta at given key or array of keys
     * for package realm
     *
     * @param string|array $key
     */
    public static function removeMeta($key)
    {
        $keys = (array) $key;

        static::realm()
            ->whereIn('key', $keys)
            ->delete();
    }

    /**
     * Purge meta
     * for package realm
     *
     * @return int Number of records deleted
     */
    public static function purgeMeta()
    {
        return static::realm()
            ->delete();
    }
}