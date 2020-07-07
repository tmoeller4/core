<?php

namespace Biigle\Http\Controllers\Views\Admin;

use Biigle\LabelTree;
use Biigle\Http\Controllers\Views\Controller;

class LabelTreesController extends Controller
{
    /**
     * Show the label tree admin page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trees = LabelTree::global()->get();

        return view('label-trees::admin', [
            'trees' => $trees,
        ]);
    }
}
