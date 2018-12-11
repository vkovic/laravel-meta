<?php

namespace Vkovic\LaravelMeta\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    protected $allowedTypes = [
        'array',
        'string',
        'int', 'integer',
        'float', 'double', 'real',
        'bool', 'boolean'
    ];

    /**
     * Set table name dynamically
     *
     * @return string
     */
    public function getTable()
    {
        return config('laravel-meta.table_name');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function (Meta $model) {
            // Realm
            if (is_null($model->realm)) {
                $model->realm = config('laravel-meta.default_realm');
            }

            // Default type is string
            $type = 'string';
            $value = $model->attributes['value'];

            // Force type and convert attribute
            // in case of array and bool
            if (is_array($model->attributes['value'])) {
                $type = 'array';
                $value = json_encode($model->attributes['value']);
            } elseif (is_bool($model->attributes['value'])) {
                $type = 'bool';
                $value = $model->attributes['value'] === true ? '1' : '0';
            } else {
                $type = $model->attributes['type'];
                $value = $model->attributes['value'];
            }

            $model->attributes['type'] = $type;
            $model->attributes['value'] = $value;
        });
    }

    /**
     * Filter meta by standard values (realm, type, id and optionally key),
     * and order by meta key
     *
     * @param        $queryBuilder
     * @param null   $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @return mixed
     */
    public function scopeFilter($queryBuilder, $realm, $metableType, $metableId, $key = null)
    {
        $query = [
            'realm' => $realm ?? config('laravel-meta.default_realm'),
            'metable_type' => $metableType,
            'metable_id' => $metableId,
        ];

        if ($key !== null) {
            $query['key'] = $key;
        }

        return $queryBuilder->where($query)->orderBy('key');
    }

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

    public function setTypeAttribute($value)
    {
        if (!in_array($value, $this->allowedTypes)) {
            // Convert value to string
            $value = is_array($value)
                ? json_encode($value)
                : (string) $value;

            $message = "Invalid type $value . Allowed types: ";
            $message .= implode(', ', $this->allowedTypes);
            throw new \InvalidArgumentException($message);
        }

        $this->attributes['type'] = $value;
    }

    /**
     * Getter for "value" field
     *
     * @param $value
     *
     * @return array|string
     */
    public function getValueAttribute($value)
    {
        $type = $this->attributes['type'];

        if ($type == 'string') {
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
     * Validate cast type
     *
     * @param $type
     */
    protected function checkType($type)
    {

    }
}