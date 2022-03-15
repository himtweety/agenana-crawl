<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Site;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('path');
            $table->string('scheme');
            $table->string('host');
            $table->string('base_url');
            $table->foreignIdFor(Site::class);
            $table->integer('parent_page')->nullable(); //
            $table->unsignedInteger('level');
            $table->integer('word_length')->default(0);
            $table->integer('total_links')->default(0);
            $table->integer('unique_links')->default(0);
            $table->integer('total_images')->default(0);
            $table->integer('unique_images')->default(0);
            $table->boolean('external')->default(false);
            $table->unsignedInteger('status')->default(0);
            $table->decimal('request_time')->nullable();
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
        Schema::dropIfExists('pages');
    }
}
