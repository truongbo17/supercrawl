<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CrawlUrl
 *
 * @property int $id
 * @property int|null $url_id
 * @property string|null $parent
 * @property string $url
 * @property string $url_hash
 * @property int $data_status
 * @property string|null $data_file
 * @property int $status
 * @property int $visited
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl query()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereDataFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereDataStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereUrlHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereUrlId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereVisited($value)
 * @mixin \Eloquent
 * @property string $site
 * @method static \Illuminate\Database\Eloquent\Builder|CrawlUrl whereSite($value)
 */
class CrawlUrl extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'crawl_urls';
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
