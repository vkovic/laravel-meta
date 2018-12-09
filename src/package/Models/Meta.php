<?php

namespace Vkovic\LaravelMeta\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Meta extends Model
{
    protected $guarded = ['id'];

    /**
     * Set table name dynamically
     *
     * @return string
     */
    public function getTable()
    {
        return config('laravel-meta.table_name');
    }

    /**
     * Metable relation
     *
     * @return MorphTo
     */
    public function metable()
    {
        return $this->morphTo();
    }

    /**
     * Filter meta by standard values (realm, type and id),
     * and order by meta key
     *
     * @param        $query
     * @param null   $realm
     * @param string $metableType
     * @param string $metableId
     *
     * @return mixed
     */
    public function scopeFilter($query, $realm, $metableType, $metableId)
    {
        return $query->where([
            'realm' => $realm,
            'metable_type' => $metableType,
            'metable_id' => $metableId,
        ])->orderBy('key');
    }

    /**
     * Setter for "value" field
     *
     * @param $value
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value)
            ? json_encode($value)
            : $value;
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
        // Check if $value is json and if it is, return array,
        // otherwise return $value

        $array = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE
            ? $array
            : $value;
    }
}