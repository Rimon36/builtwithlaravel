<?php

Route::get('/', function()
{
	return View::make('home');
});

Route::get('grid', function()
{
	return View::make('gridstrap');
});

Route::get('github', 'UserController@goGithub');
Route::get('authorize', 'UserController@getAuthorization');
Route::get('logout', 'UserController@logout');

Route::get('user', ['before' => 'ghauth', function(){
	return \Session::get('currentUser');
}]);

Route::group(array(), function(){
	Route::resource('categories', 'CategoriesController', ['except' => ['create', 'edit', 'update']]);
	Route::resource('projects', 'ProjectsController', ['except' => ['create', 'edit']]);
	Route::post('images/upload', 'ProjectsController@upload');
});
