<?php

class Image extends Eloquent {

	/**
	 * Don't maintain timestamps for this model.
	 *
	 * @var boolean
	 */
	public $timestamps = false;

	public function transect()
	{
		return $this->belongsTo('Transect');
	}

	public function annotations()
	{
		return $this->hasMany('Annotation');
	}
}