<?php

Route::get('/', function()
{
	return View::make('hello');
});

Route::get('grid', function(){
	return View::make('grid');
});

Route::get('github', 'UserController@goGithub');
Route::get('authorize', 'UserController@getAuthorization');
Route::get('logout', 'UserController@logout');

Route::get('user', ['before' => 'ghauth', function(){
	return \Session::get('currentUser');
}]);

Route::group(array(), function(){
	Route::resource('categories', 'Categories', ['except' => ['create', 'edit']]);
	Route::resource('projects', 'ProjectsController', ['except' => ['create', 'edit']]);
});