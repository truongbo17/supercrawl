<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->id();

            $table->string('site')->unique()->index(); //root url
            $table->string('driver_browser')->nullable();
            $table->text('url_start'); //url start crawl (json->array)
            $table->text('should_crawl'); //regex url should crawl
            $table->text('should_get_data'); //regex url should get data
            $table->text('should_get_info'); //regex url should get info from crawl (json->array)
            $table->boolean('config_root_url')->nullable(); //check relative path
            $table->text('skip_url')->nullable(); //skip url
            $table->boolean('ignore_page_child')->default(false);
            $table->integer('status')->default(0)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('urls');
    }
};
