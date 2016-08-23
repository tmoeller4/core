<?php

namespace Dias\Http\Controllers\Api;

use Dias\Label;
use Dias\LabelTree;
use Dias\LabelSource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LabelTreeLabelController extends Controller
{
    /**
     * Add labels to a label tree
     *
     * @api {post} label-trees/:id/labels Create new labels
     * @apiGroup Label Trees
     * @apiName StoreLabelTreesLabels
     * @apiPermission labelTreeEditor
     * @apiDescription If a label source is used to create a new label (or labels), this
     * endpoint may accept/require additional arguments. Also label sources may create
     * multiple new labels at once.
     *
     * @apiParam {Number} id The label tree ID
     *
     * @apiParam (Required arguments) {String} name Name of the new label
     * @apiParam (Required arguments) {String} color Color of the new label as hexadecimal string (like `bada55`). May have an optional `#` prefix.
     *
     * @apiParam (Optional arguments) {Number} parent_id ID of the parent label for ordering in a tree-like structure.
     * @apiParam (Optional arguments) {Number} label_source_id ID of the external label source (e.g. a database)
     * @apiParam (Optional arguments) {Number} source_id ID of the label in the external label source. Required is a label source is specified.
     *
     * @apiSuccessExample {json} Success response:
     * [
     *    {
     *       "id": 4,
     *       "name": "Sea Cucumber",
     *       "parent_id": null,
     *       "label_tree_id": 1,
     *       "color": "bada55"
     *    }
     * ]
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $tree = LabelTree::findOrFail($id);
        $this->authorize('create-label', $tree);

        $this->validate($request, Label::$createRules);

        // parent must be of the same tree
        if ($request->has('parent_id')) {
            $exists = Label::where('id', $request->input('parent_id'))
                ->where('label_tree_id', $id)
                ->exists();
            if (!$exists) {
                return $this->buildFailedValidationResponse($request, [
                    'parent_id' => ['The parent label must belong to the same label tree than the new label.']
                ]);
            }
        }

        if ($request->has('label_source_id')) {
            $source = LabelSource::findOrFail($request->input('label_source_id'));
            try {
                $labels = $source->getAdapter()->create((int) $id, $request);
            } catch (ValidationException $e) {
                return $this->buildFailedValidationResponse($request, $e->response);
            }
        } else {
            $label = new Label;
            $label->name = $request->input('name');
            $label->color = $request->input('color');
            $label->parent_id = $request->input('parent_id');
            $label->label_tree_id = (int) $id;
            $label->save();

            $labels = [$label];
        }

        if (static::isAutomatedRequest($request)) {
            return $labels;
        }

        if ($request->has('_redirect')) {
            return redirect($request->input('_redirect'))
                ->with('saved', true);
        }
        return redirect()->back()
            ->with('saved', true);
    }
}
