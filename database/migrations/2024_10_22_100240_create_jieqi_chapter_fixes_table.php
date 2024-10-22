<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJieqiChapterFixesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jieqi_chapter_fixes', function (Blueprint $table) {
            $table->id();
            $table->string('md5_content', 32)->comment('MD5');
            $table->integer('chapter_id')->comment('章节id');
            $table->string('site', 10)->comment('站点');
            $table->index(['chapter_id', 'site']);
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
        Schema::dropIfExists('jieqi_chapter_fixes');
    }
}
