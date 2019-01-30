<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('files', function (Blueprint $table) {
            $table->string('id', 36);
            $table->integer('user_id')->unsigned()->index()->comment('用户id');
            $table->string('name');
            $table->string('type')->comment('类型：image/audio/application 等');
            $table->string('extension')->comment('扩展名');
            $table->string('path')->comment('本地存储相对路径');
            $table->string('mime')->comment('存储时mime信息');
            $table->string('original_name')->comment('原始文件名');
            $table->string('original_extension')->comment('原始文件扩展名');
            $table->string('original_mime')->comment('原始文件mime信息');
            $table->bigInteger('size')->comment('文件大小');
            $table->string('meta')->default('')->comment('json化文件元信息');
            $table->string('md5_hash')->comment('文件md5散列值');
            $table->string('note')->comment('备注');
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
