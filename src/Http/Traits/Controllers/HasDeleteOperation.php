<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Controllers;

use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

trait HasDeleteOperation
{
	/**
	 * Handle destroy/DELETE method for the controller.
	 */
	public function destroy(int|string $id): RedirectResponse|Response
	{
		// for safety, you must enable access with `$this->isDestroyAllowed = true;` at the constructor
		// you should also check for the user's valid permissions before doing this

		if ($this->isDestroyAllowed()) {
			$this->repo->delete($id);

			return redirect()->route($this->getIndexRouteName())->with('success', 'Record deleted.');
		}

		abort(401, 'You are not authorized to access this URL');
	}
}
