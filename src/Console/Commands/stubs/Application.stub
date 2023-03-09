<?php
namespace App;

class Application extends \Illuminate\Foundation\Application
{

	public function publicPath($path = '')
	{
    	return $this->joinPaths($this->publicPath ?: $this->basePath('public_html'), $path);
	}

}
