<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNginxAccessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nginx_access_logs', function (Blueprint $table) {
            $table->id();
            $table->char('remote_addr', 30)->default('-')->nullable();
            $table->char('remote_user', 30)->default('-')->nullable();
            $table->char('time_local', 30);
            $table->timestamp('time');
            $table->date('date');
            $table->char('request', 30);
            $table->text('url');
            $table->integer('article_id')->index();
            $table->char('http', 30)->default('-')->nullable();
            $table->integer('status');
            $table->integer('bytes_sent');
            $table->text('http_referer');
            $table->text('http_user_agent');
            $table->enum('source', ['Baidu', 'Shenma']);
            $table->text('note');

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
        Schema::dropIfExists('nginx_access_logs');
    }
}
