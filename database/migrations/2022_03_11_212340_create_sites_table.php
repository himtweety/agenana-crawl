<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('url');
            $table->string('scheme')->nullable()->default("http");
            $table->string('host')->nullable();
            $table->unsignedInteger('max_depth')->default(0);
            $table->unsignedInteger('no_of_pages')->default(0);
            $table->unsignedInteger('internal_links')->default(0);
            $table->unsignedInteger('external_links')->default(0);
            $table->unsignedInteger('unique_images')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('sites');
    }
}
