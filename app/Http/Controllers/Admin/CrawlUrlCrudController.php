<?php

namespace App\Http\Controllers\Admin;

use App\Enum\CrawlStatus;
use App\Enum\DataStatus;
use App\Http\Requests\CrawlUrlRequest;
use App\Models\Url;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Arr;

/**
 * Class CrawlUrlCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CrawlUrlCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\CrawlUrl::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/crawl-url');
        CRUD::setEntityNameStrings('crawl url', 'crawl urls');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name' => 'id',
                'type' => 'text'
            ],
            [
                'label' => 'Site',
                'name' => 'site',
                'type' => 'text'
            ],
            [
                'name' => 'url',
                'type' => 'url_reducer',
            ],
            [
                'name' => 'status',
                'type' => 'select_from_array',
                'options' => array_flip(CrawlStatus::asArray()),
            ],
            [
                'name' => 'data_status',
                'type' => 'select_from_array',
                'options' => array_flip(DataStatus::asArray()),
            ],
        ]);

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'status',
            'label' => 'Filter Status'
        ], array_flip(CrawlStatus::asArray()), function ($value) {
            $this->crud->addClause('where', 'status', $value);
        });

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'data_status',
            'label' => 'Filter Data Status'
        ], array_flip(DataStatus::asArray()), function ($value) {
            $this->crud->addClause('where', 'data_status', $value);
        });

        $urls = Url::select(['id', 'site'])->get()->toArray();
        $url_filter = [];

        foreach ($urls as $url) {
            $url_filter[$url['id']] = $url['site'];
        }
//        dd($url_filter);

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'url_id',
            'label' => 'Filter Site'
        ], $url_filter, function ($value) {
            $this->crud->addClause('where', 'url_id', $value);
        });
    }
}
