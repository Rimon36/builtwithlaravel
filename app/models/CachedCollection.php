<?php

class CachedCollection extends \Illuminate\Database\Eloquent\Collection {
	
	var $cachedName;

	function __construct(array $models = array()) {
		parent::__construct($models);
	}
	/**
	 * We've got a collection here we'd like to cache
	 * this could be something like the popular projects
	 * or the tags list or whatever.
	 *
	 * Give it a title and it'll be cached
	 * 
	 * @param  [type] $title
	 * @return [type]
	 */
	function cache($title) {
		if (!static::isEmpty()){
			$this->cachedName = static::name(static::all()[0]) . '-' . $title;
			// echo $this->cachedName;
			if (!!static::isCaching() && !!\Cache::has($this->cachedName)) {
				\Cache::add($this->cachedName, $this, static::ttl());
			}
			elseif (!!static::isCaching()) {
				\Cache::put($this->cachedName, $this, static::ttl());
			}
		} 
		return $this;
	}

	/**
	 * If the collection has a model with the given
	 * ID, it is now invalid/stale so the cache
	 * should be purged to regenerate on the 
	 * next request.
	 * @param  integer $id
	 * @return void
	 */
	function invalidate($id, $title='') {
		if (empty($title) && !empty($this->cachedName)) {
			$name = $this->cachedName;
		}
		else {
			$name = static::name(static::all()[0] . '-' . $title);
		}
		if (!!static::isCaching()) {
			if ($this->contains($id)) {
				\Cache::forget($name);
			}
		}
	}

	/**
	 * Determine the types of things we're storing
	 * for the cache name
	 * @param  string $table
	 * @return string
	 */
	static function name ($table) {
		return strtolower(\Str::plural(get_class($table)));
	}

	/**
	 * Determine if Eloquent Caching is enabled
	 * @return boolean
	 */
	static function isCaching() {
		return \Config::get('eloquent.cache');
	}

	/**
	 * Determine what the current TTL for the Eloquent cache is
	 * @return integer
	 */
	static function ttl() {
		return \Config::get('eloquent.ttl');
	}
}