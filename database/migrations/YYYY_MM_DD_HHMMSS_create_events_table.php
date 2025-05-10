<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index(); // 事件日期，添加索引以便按日期查询
            $table->string('title'); // 事件标题
            $table->text('description')->nullable(); // 事件描述，可为空
            $table->timestamps(); // 创建和更新时间
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}