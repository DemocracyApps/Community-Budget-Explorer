<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatasourceAction extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('datasource_actions', function (Blueprint $table) {
      $table->increments('id');
      $table->string('type');
      $table->integer('government');
      $table->foreign('government')->references('id')->on('government_organizations');
      $table->integer('datasource_id');
      $table->foreign('datasource_id')->references('id')->on('datasources');
      $table->string('status')->nullable();
      $table->dateTime('status_date')->nullable();
      $table->text('properties')->nullable();
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
    Schema::drop('datasource_actions');
  }
}
