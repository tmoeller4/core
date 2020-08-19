<?php

namespace Biigle\Http\Controllers\Api;

use Cache;
use Biigle\Jobs\GenerateFederatedSearchIndex;

class FederatedSearchIndexController extends Controller
{
    /**
     * Show the federated search index.
     *
     * @api {get} federated-search-index Get the federated search index
     * @apiGroup Federated Search
     * @apiName Index
     * @apiPermission federatedSearchInstance
     * @apiDescription **Important:** This endpoint does not use the regular
     * authentication method of API endpoints and expects an authentication token of a
     * remote instance that was configured for federated search.
     *
     * @apiSuccessExample {json} Success response:
     * {
     *    "label_trees": [
     *        {
     *            "id": 1,
     *            "name": "test tree",
     *            "description": "this is my test tree",
     *            "created_at": "2020-08-19 13:19:06",
     *            "updated_at": "2020-08-19 13:19:06",
     *            "url": "/label-trees/1",
     *            "members" => [2]
     *        }
     *    ],
     *    "projects": [
     *        {
     *            "id": 2,
     *            "name": "test project",
     *            "description": "this is my test project",
     *            "created_at": "2020-08-19 13:21:49",
     *            "updated_at": "2020-08-19 13:21:49",
     *            "thumbnail_url": null,
     *            "url": "/projects/2",
     *            "members": [2],
     *            "label_trees": [1]
     *        }
     *    ],
     *    "volumes": [
     *        {
     *            "id": 1,
     *            "name": "test volume",
     *            "created_at": "2020-08-19 13:23:19",
     *            "updated_at": "2020-08-19 13:23:19",
     *            "url": "/volumes/1",
     *            "thumbnail_url": null,
     *            "thumbnail_urls": null,
     *            "projects": [2]
     *        }
     *    ],
     *    "users": [
     *        {
     *             "id": 2,
     *             "uuid": "a24ada02-5f3f-3fe6-b64e-98473ce70b9a"
     *        }
     *    ]
     * }
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $key = config('biigle.federated_search.cache_key');

        if (!Cache::has($key)) {
            GenerateFederatedSearchIndex::dispatchNow();
        }

        return Cache::get($key);
    }
}
