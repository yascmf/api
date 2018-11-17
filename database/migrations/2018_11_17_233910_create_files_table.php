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
            $table->string('local_path')->comment('本地存储相对路径');
            $table->string('driver_bucket')->comment('远端存储驱动及仓库');
            $table->string('remote_path')->comment('远端存储相对路径');
            $table->string('mime')->comment('存储时mime信息');
            $table->string('original_name')->comment('原始文件名');
            $table->string('original_extension')->comment('原始文件扩展名');
            $table->string('original_mime')->comment('原始文件mime信息');
            $table->bigInteger('size')->comment('文件大小');
            $table->integer('width')->unsigned()->default(0)->comment('如果是图片，则表示图片的宽');
            $table->integer('height')->unsigned()->default(0)->comment('如果是图片，则表示图片的高');
            $table->bigInteger('duration')->unsigned()->default(0)->comment('如果是音视频，则表示音视频的时长');
            $table->integer('bitrate')->unsigned()->default(0)->comment('如果是音视频，则表示音视频的比特率');
            $table->string('meta')->default('')->comment('其它meta信息可json化存储');
            $table->string('md5_hash')->comment('文件md5散列值');
            $table->text('ocr')->nullable()->comment('ocr识别结果');
            $table->string('note')->comment('备注');
            $table->tinyInteger('state')->default(0)->comment('状态 0 - 仅本地存储 1 - 正在同步到远端存储 2 - 远端存储同步完成 3 - 仅远端存储');
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
