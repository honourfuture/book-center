<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_chapters', function (Blueprint $table) {
            $table->charset = 'gbk';
            $table->collation = 'gbk_chinese_ci';
            $table->increments('id');
            $table->integer('chapterid')->nullable()->index();
            $table->integer('articleid')->nullable()->index();
            $table->integer('chapterorder')->nullable();
            $table->integer('size')->nullable();
            $table->integer('strlen')->nullable();
            $table->text('error_message')->nullable();
            $table->text('chaptername')->nullable();
            $table->text('content');
            $table->integer('lastupdate');
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
        Schema::dropIfExists('error_chapters');
    }
}
