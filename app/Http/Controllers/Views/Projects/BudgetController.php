<?php

namespace Biigle\Http\Controllers\Views\Projects;

use Biigle\Budget;
use Biigle\Http\Controllers\Views\Controller;
use Biigle\Project;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Shows the project budgets page.
     *
     * @param Request $request
     * @param int $id project ID
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('access', $project);

        return view('projects.budgets.index')
            ->with('project', $project);
    }

    /**
     * Shows the create budget page.
     *
     * @param Request $request
     * @param int $id project ID
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);

        return view('projects.budgets.create')
            ->with('project', $project);
    }

    /**
     * Shows the budget details page.
     *
     * @param Request $request
     * @param int $id budget ID
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $budget = Budget::with(['project', 'creator'])->findOrFail($id);
        $this->authorize('access', $budget->project);

        return view('projects.budgets.show')
            ->with('budget', $budget);
    }

    /**
     * Shows the edit budget page.
     *
     * @param Request $request
     * @param int $id budget ID
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $budget = Budget::with('project')->findOrFail($id);
        $this->authorize('update', $budget->project);

        return view('projects.budgets.edit')
            ->with('budget', $budget);
    }
}