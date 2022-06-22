<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Url
 *
 * @property int $id
 * @property string $site
 * @property string|null $driver_browser
 * @property string $url_start
 * @property string $should_crawl
 * @property string $should_get_data
 * @property string $should_get_info
 * @property int|null $config_root_url
 * @property string|null $skip_url
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Url newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Url newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Url query()
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereConfigRootUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereDriverBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereShouldCrawl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereShouldGetData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereShouldGetInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereSkipUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereUrlStart($value)
 * @mixin \Eloquent
 * @property int $ignore_page_child
 * @method static \Illuminate\Database\Eloquent\Builder|Url whereIgnorePageChild($value)
 */
class Url extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'urls';
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
