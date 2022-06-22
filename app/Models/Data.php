<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Data
 *
 * @property int $id
 * @property string $url
 * @property string $title
 * @property string $title_hash
 * @property string $language
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Data newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Data newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Data query()
 * @method static \Illuminate\Database\Eloquent\Builder|Data whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Data whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Data whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Data whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Data whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Data whereTitleHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Data whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Data whereUrl($value)
 * @mixin \Eloquent
 */
class Data extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'data';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
