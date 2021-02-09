<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\View;

use Illuminate\View\FileViewFinder as BaseFinder;

/**
 * Class FileViewFinder
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\FileViewFinder
 */
class FileViewFinder extends BaseFinder
{

    public function prependLocation($location)
    {
        if ($location) {
            $location = $this->resolvePath($location);

            if ($location && !in_array($location, $this->paths)) {
                parent::prependLocation($location);
            }
        }

        return $this;
    }

    /**
     * Inverts the order of the paths, sets and returns the paths array
     *
     * @return array
     */
    public function reversePaths()
    {
        return $this->paths = array_reverse($this->paths);
    }
}
