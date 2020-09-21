<?php

return [

	// static API KEY for the application
	'api_key' => env('API_KEY', false),

	// page display settings
	'dashboard' => [
		'per_page' => 50,
	],

	// Standard Date/Timer formats
	'date_format' => 'd/M/Y',

	'date_time_format' => 'd/M/Y h:i A',

	'time_format' => 'h:i A',
];
