<?php

class ProjectsController extends APIController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Project::all();
	}

	public function upload() {
		// TODO: Image Upload for Images
		// Return JSON with image location for usage on project creation / update
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$project = [
			'title' => Input::get('title'),
			'description' => Input::get('description'),
			'tags' => Input::get('tags')
		];
		if ($this->hasUser()) {

		}
		else {
			return $this->requireUser();
		}
		// return $this->created(array('thing' => 'created'), '/projects/1');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// 
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// 
	}
}