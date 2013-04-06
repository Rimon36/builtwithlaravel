<?php

class Category extends CachedEloquent {

	public static function boot() {
		parent::boot();

		static::creating(function($thing) {
			$thing->slug = static::slugify($thing->title);
		});
	}

	static private function slugify($input) {
		return urlencode(strtolower(str_replace(' ', '-', $input)));
	}

	static function byTitleSlug($title) {
		return static::where('slug', static::slugify($title));
	}

	static function bySlug($slug) {
		return static::where('slug', static::slugify($slug));
	}
}