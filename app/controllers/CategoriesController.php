<?php

class CategoriesController extends APIController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Category::all();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if ($this->hasAdmin()) {
			$title = Input::get('title');

			if (!empty($title) && strlen($title) < 50) {
				if (Category::byTitleSlug($title)->count() == 0) {
					$cat = new Category;
					$cat->title = $title;
					$cat->user = $this->user->id;

					try {
						$cat->save();
						return $this->created($cat, '/categories/' . $cat->slug);
					} catch (Exception $e) {
						return $this->error([], 409);
					}
				}
				else {
					$category = Category::byTitleSlug($title)->get()->first();
					return $this->alreadyExists($category, '/categories/' . $category->slug);
				}
			}
			else {
				return $this->error(['Title field must not be empty, and must contain less than 50 characters.'], 400);
			}
		}
		else {
			return $this->requireAdmin();
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  string  $slug
	 * @return Response
	 */
	public function show($slug)
	{
		return Category::bySlug($slug)->get();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $slug
	 * @return Response
	 */
	public function destroy($slug)
	{
		if ($this->hasAdmin()) {
			$category = Category::byTitleSlug($slug)->get();

			try {
				$category->destroy();
				return $this->deleted();
			} catch (Exception $e) {
				return $this->error([], 409);
			}
		}
		return $this->requireAdmin();
	}
}