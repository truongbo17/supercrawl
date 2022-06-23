<?php

namespace App\Http\Controllers\Admin;

use App\Enum\DataStatus;
use App\Enum\UploadStatus;
use App\Http\Requests\DataRequest;
use App\Models\Data;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DataCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DataCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Data::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/data');
        CRUD::setEntityNameStrings('data', 'data');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id');
        CRUD::column('title');
        $this->crud->addColumn(
            [
                'name' => 'url',
                'type' => 'url_reducer'
            ],
        );
        $this->crud->addColumn(
            [
                'name' => 'status',
                'type' => 'select_from_array',
                'options' => array_flip(DataStatus::asArray()),
            ],
        );

        $this->crud->addColumn(
            [
                'name' => 'upload_status',
                'type' => 'select_from_array',
                'options' => array_flip(UploadStatus::asArray()),
            ],
        );

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'status',
            'label' => 'Filter Status'
        ], array_flip(DataStatus::asArray()), function ($value) {
            $this->crud->addClause('where', 'status', $value);
        });

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'upload_status',
            'label' => 'Filter Data Status'
        ], array_flip(UploadStatus::asArray()), function ($value) {
            $this->crud->addClause('where', 'upload_status', $value);
        });

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(DataRequest::class);

        CRUD::field('id');
        CRUD::field('title');
        CRUD::field('title_hash');
        CRUD::field('language');
        CRUD::field('status');
        CRUD::field('created_at');
        CRUD::field('updated_at');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
