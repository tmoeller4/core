<?php

namespace Biigle\Http\Controllers\Api\Volumes\Filters;

use Biigle\Http\Controllers\Api\Controller;
use Biigle\Volume;

class ImageFilenameController extends Controller
{
    /**
     * List the IDs of images with a filename matching the given pattern.
     *
     * @api {get} volumes/:id/images/filter/filename/:pattern Get images with matching filename
     * @apiGroup Volumes
     * @apiName VolumeImagesFilterFilename
     * @apiPermission projectMember
     *
     * @apiParam {Number} id The volume ID
     * @apiParam {Number} pattern The filename pattern. May be a full filename like `abcde.jpg` or a pattern like `a*.jpg` where `*` matches any string of zero or more characters. Example: `a*.jpg` will match `abcde.jpg` as well as `a.jpg`. Example 2: `*3.jpg` will match `123.jpg` as well as `3.jpg`.
     *
     * @apiSuccessExample {json} Success response:
     * [1, 5, 6]
     *
     * @param  int  $id
     * @param  string  $pattern
     * @return \Illuminate\Http\Response
     */
    public function index($id, $pattern)
    {
        $volume = Volume::findOrFail($id);
        $this->authorize('access', $volume);

        // Escape trailing backslashes, else there would be an error with ilike.
        $pattern = preg_replace('/\\\\$/', '\\\\\\\\', $pattern);

        return $volume->images()
            ->where('filename', 'ilike', str_replace('*', '%', $pattern))
            ->pluck('id');
    }
}