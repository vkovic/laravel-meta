<?php

namespace Vkovic\LaravelMeta\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $guarded = ['id'];

    protected $table = 'meta';

    protected static $realm = 'vkovic/laravel-meta';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Set realm to always be in attributes by default
        $this->attributes['realm'] = static::$realm;
    }

    public static function boot()
    {
        parent::boot();

        // Add global scope to be used in every query
        static::addGlobalScope('realm', function (Builder $query) {
            $query->where(['realm' => static::$realm]);
        });

        // Saving callback
        static::saving(function (Meta $model) {
            $value = $model->attributes['value'];

            if (is_array($value)) {
                $type = 'array';
                $value = json_encode($value);
            } elseif (is_bool($value)) {
                $type = 'bool';
                $value = $value === true ? '1' : '0';
            } elseif ($value === null) {
                $type = 'null';
            } elseif (is_int($value)) {
                $type = 'int';
                $value = (string) $value;
            } elseif (is_float($value)) {
                $type = 'float';
                $value = (string) $value;
            } else {
                $type = 'string';
                $value = (string) $value;
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
        // Key must be string
        if (!is_string($value)) {
            throw new \Exception('Invalid key type. Key must be string');
        }

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
        throw new \Exception("Meta type can't be set explicitly");
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
        } elseif ($type == 'int') {
            return (int) $value;
        } elseif ($type == 'float') {
            return (float) $value;
        } elseif ($type == 'bool') {
            return (bool) $value;
        }
    }
}