<?php

class Project extends CachedEloquent {
	public static function boot() {
		parent::boot();

		static::creating(function($thing) {
			$thing->user = \Session::get("currentUser")->id;
		});
	}
}