<?php

namespace Basecamp;

class BasecampEntity
{
	public $id = null;
	public $created_at = null;
	public $updated_at = null;

	function __construct($data)
	{
		foreach ($data as $key => $val)
		{
			$this->$key = $val;
		}
	}
}
