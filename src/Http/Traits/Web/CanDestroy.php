<?php


namespace ElegantMedia\OxygenFoundation\Http\Traits\Web;

use Illuminate\Http\RedirectResponse;

trait CanDestroy
{

	/**
	 *
	 * Handle destroy/DELETE method for the controller
	 *
	 * @param $id
	 *
	 * @return RedirectResponse
	 */
	public function destroy($id): ?RedirectResponse
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
