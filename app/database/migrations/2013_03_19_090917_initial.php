<?php

use Illuminate\Database\Migrations\Migration;

class Initial extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table){
			$table->integer('id')->unsigned();
			$table->primary('id');
			$table->string('username');
			$table->string('name');
			$table->string('email');
			$table->timestamp('last_visit');
			$table->boolean('is_admin')->default(false);
			$table->timestamps();
		});

		Schema::create('categories', function($table){
			$table->increments('id');
			$table->string('title');
			$table->string('slug');
			$table->integer('user')->unsigned();
			$table->foreign('user')->references('id')->on('users');
			$table->timestamps();
		});

		Schema::create('projects', function($table){
			$table->increments('id');
			$table->integer('category')->unsigned();
			$table->foreign('category')->references('id')->on('categories');
			$table->string('title');
			$table->text('description');
			$table->string('thumbnail_path')->nullable();
			$table->string('image_path');
			$table->integer('user')->unsigned();
			$table->foreign('user')->references('id')->on('users');
			$table->timestamps();
		});

		Schema::create('likes', function($table){
			$table->increments('id');
			$table->integer('user')->unsigned();
			$table->foreign('user')->references('id')->on('users');
			$table->integer('project')->unsigned();
			$table->foreign('project')->references('id')->on('projects');
			$table->timestamps();
		});

		Schema::create('tags', function($table){
			$table->increments('id');
			$table->string('tag');
			$table->timestamps();
		});

		Schema::create('tagmaps', function($table){
			$table->increments('id');
			$table->integer('project')->unsigned();
			$table->foreign('project')->references('id')->on('projects');			
			$table->integer('tag')->unsigned();
			$table->foreign('tag')->references('id')->on('tags');
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
		Schema::drop('users');
		Schema::drop('categories');
		Schema::drop('projects');
		Schema::drop('likes');
		Schema::drop('tags');
		Schema::drop('tagmaps');
	}

}