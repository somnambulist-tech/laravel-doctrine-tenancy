<?php

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

    /**
     * Append a path to the top of the array of paths
     *
     * @param string $location
     *
     * @return $this
     */
    public function prependLocation($location)
    {
        if ($location && !in_array($location, $this->paths)) {
            array_unshift($this->paths, $location);
        }

        return $this;
    }

    /**
     * Reset the entire stack of paths with a new array
     *
     * @param array $paths
     *
     * @return $this
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;

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
