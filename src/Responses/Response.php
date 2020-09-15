<?php


namespace ElegantMedia\OxygenFoundation\Responses;

class Response extends \Illuminate\Http\Response
{

	/*
	 |-----------------------------------------------------------
	 | Custom Response Codes
	 |-----------------------------------------------------------
	 */
	public const HTTP_AUTH_EXPIRED = 440;		// Non-RFC code, from Microsoft (Session has expired)
	public const HTTP_UNPROCESSABLE_ENTITY = 422;
}
