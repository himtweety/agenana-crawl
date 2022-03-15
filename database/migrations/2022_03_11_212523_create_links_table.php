<?php

use App\Models\Page;
use App\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id(); 
            $table->string("href")->nullable();
            $table->string('scheme');
            $table->string('path');
            $table->string('host');
            $table->string('base_url'); //
            $table->unsignedInteger('level');
            $table->foreignIdFor(Site::class);
            $table->foreignIdFor(Page::class);
            $table->boolean('external')->nullable();
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
        Schema::dropIfExists('links');
    }
}
