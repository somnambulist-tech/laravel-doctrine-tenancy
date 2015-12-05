<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Somnambulist\Tenancy\View;

use Illuminate\View\FileViewFinder as BaseFinder;

/**
 * Class FileViewFinder
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\FileViewFinder
 * @author     Dave Redfern
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
    public function setPaths(array $paths = [])
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