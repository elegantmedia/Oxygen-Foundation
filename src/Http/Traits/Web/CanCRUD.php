<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Http\Traits\Web;

trait CanCRUD
{
    use CanBrowse;
    use CanCreate;
    use CanDestroy;
    use CanEdit;
    use FollowsConventions;
}
