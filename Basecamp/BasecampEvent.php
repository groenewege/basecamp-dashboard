<?php

namespace Basecamp;

class BasecampEvent extends BasecampEntity
{
	public $summary	 = null;
	public $action = null;
	public $target = null;
	public $url = null;
	public $html_url = null;
	public $excerpt = null;
	public $raw_excerpt = null;
	public $attachments = null;
	public $creator = null;
	public $bucket = null;
}
