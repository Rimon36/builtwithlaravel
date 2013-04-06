<?php

class CachedEloquent extends Eloquent {

	/**
	 * If Eloquent Caching is enabled, hook model events such
	 * as created, updated, and deleting to automatically
	 * update the cache
	 * @return void
	 */
	public static function boot() {
		$name = static::name();

		if (static::isCaching()) {
			parent::created(function($thing) use ($name){
				\Cache::add($name . '-' . $thing->id, $thing, static::ttl());
				\Cache::forget($name . '-all');
			});

			parent::updated(function($thing) use ($name){
				\Cache::put($name . '-' . $thing->id, $thing, static::ttl());
				\Cache::forget($name . '-all');
			});

			parent::deleting(function($thing) use ($name){
				if (\Cache::has($name . '-' . $thing->id)) {
					\Cache::forget($name . '-' . $thing->id, $thing, static::ttl());
					\Cache::forget($name . '-all');
				}
			});
		}
	}

	/**
	 * Determines the name of the types of models we're
	 * in the process of caching
	 * @return string
	 */
	static function name () {
		return strtolower(\Str::plural(get_called_class()));
	}

	/**
	 * Determines if Eloquent Caching is enabled
	 * @return boolean
	 */
	static function isCaching() {
		return \Config::get('eloquent.cache');
	}

	/**
	 * Determines the current TTL for Eloquent Caching
	 * @return integer
	 */
	static function ttl() {
		return \Config::get('eloquent.ttl');
	}

	/**
	 * Collections should be made with our CachedCollection model
	 * so that when caching is enabled, we're getting the
	 * collection from cache rather than straight from the DB
	 * @param  array  $models
	 * @return CachedCollection
	 */
	function newCollection(array $models = array()) {
		return new CachedCollection($models);
	}

	/**
	 * Override the original find method.
	 * If Eloquent caching is enabled, get the information from
	 * the cache rather than the DB
	 * @param  integer $id
	 * @param  array  $columns
	 * @return object
	 */
	public static function find($id, $columns=array('*')) {
		if (static::isCaching() && \Cache::has(static::name() . '-' . $id)) {
			return \Cache::get(static::name() . '-' . $id);
		}
		elseif (static::isCaching()) {
			$data = parent::find($id, $columns); 
			\Cache::add(static::name() . '-' . $id, $data, static::ttl());
			return $data;
		}
		else {
			return parent::find($id, $columns);
		}
	}

	/**
	 * Override the original all method.
	 * If Eloquent caching is enabled, get the collection
	 * from the cache rather than the DB
	 * @param  array  $columns
	 * @return object
	 */
	public static function all($columns = array('*')) {
		if (static::isCaching() && \Cache::has(static::name() . '-all')) {
			return \Cache::get(static::name() . '-all');
		}
		elseif (static::isCaching()) {
			$data = parent::all($columns);
			\Cache::add(static::name() . '-all', $data, static::ttl());
			return $data;
		}
		else {
			return parent::all($columnns);
		}
	}

	public static function cached($name) {
		if (static::isCaching() && \Cache::has(static::name() . '-' . $name)) {
			return \Cache::get(static::name() . '-' . $name);
		}
		elseif (static::isCaching()) {
			throw new Exception;
		}
	}
}