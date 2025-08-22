<?php

namespace Biigle\Http\Controllers\Api;

use Biigle\Budget;
use Biigle\Http\Requests\StoreBudget;
use Biigle\Http\Requests\UpdateBudget;
use Biigle\Project;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display a listing of budgets for a project.
     *
     * @api {get} projects/:id/budgets Get project budgets
     * @apiGroup Budgets
     * @apiName IndexBudgets
     * @apiPermission projectMember
     * @apiDescription Returns all budgets belonging to the specified project.
     *
     * @apiParam {Number} id Project ID.
     *
     * @apiSuccessExample {json} Success response:
     * [
     *    {
     *       "id": 1,
     *       "name": "Research Budget 2024",
     *       "description": "Annual research budget",
     *       "amount": "50000.00",
     *       "spent": "12500.50",
     *       "currency": "USD",
     *       "start_date": "2024-01-01",
     *       "end_date": "2024-12-31",
     *       "active": true,
     *       "project_id": 1,
     *       "creator_id": 1,
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-08-22T13:00:00.000000Z",
     *       "remaining": "37499.50",
     *       "utilization_percentage": 25.00
     *    }
     * ]
     *
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        $this->authorize('access', $project);

        return $project->budgets()->with('creator:id,firstname,lastname')->get();
    }

    /**
     * Store a newly created budget in storage.
     *
     * @api {post} projects/:id/budgets Create a new budget
     * @apiGroup Budgets
     * @apiName StoreBudget
     * @apiPermission projectAdmin
     * @apiDescription Creates a new budget for the specified project.
     *
     * @apiParam {Number} id Project ID.
     * @apiParam {String} name Budget name.
     * @apiParam {String} [description] Budget description.
     * @apiParam {Number} amount Budget amount.
     * @apiParam {String} [currency=USD] Currency code (3 characters).
     * @apiParam {String} start_date Budget start date (YYYY-MM-DD).
     * @apiParam {String} end_date Budget end date (YYYY-MM-DD).
     * @apiParam {Boolean} [active=true] Whether the budget is active.
     *
     * @apiSuccessExample {json} Success response:
     * {
     *    "id": 1,
     *    "name": "Research Budget 2024",
     *    "description": "Annual research budget",
     *    "amount": "50000.00",
     *    "spent": "0.00",
     *    "currency": "USD",
     *    "start_date": "2024-01-01",
     *    "end_date": "2024-12-31",
     *    "active": true,
     *    "project_id": 1,
     *    "creator_id": 1,
     *    "created_at": "2024-08-22T13:00:00.000000Z",
     *    "updated_at": "2024-08-22T13:00:00.000000Z"
     * }
     *
     * @param StoreBudget $request
     * @param Project $project
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBudget $request, Project $project)
    {
        $this->authorize('update', $project);

        $budget = new Budget($request->validated());
        $budget->project_id = $project->id;
        $budget->creator_id = $request->user()->id;
        $budget->save();

        return $budget;
    }

    /**
     * Display the specified budget.
     *
     * @api {get} budgets/:id Get a budget
     * @apiGroup Budgets
     * @apiName ShowBudget
     * @apiPermission projectMember
     * @apiDescription Shows details of a specific budget.
     *
     * @apiParam {Number} id Budget ID.
     *
     * @apiSuccessExample {json} Success response:
     * {
     *    "id": 1,
     *    "name": "Research Budget 2024",
     *    "description": "Annual research budget",
     *    "amount": "50000.00",
     *    "spent": "12500.50",
     *    "currency": "USD", 
     *    "start_date": "2024-01-01",
     *    "end_date": "2024-12-31",
     *    "active": true,
     *    "project_id": 1,
     *    "creator_id": 1,
     *    "created_at": "2024-01-01T00:00:00.000000Z",
     *    "updated_at": "2024-08-22T13:00:00.000000Z",
     *    "remaining": "37499.50",
     *    "utilization_percentage": 25.00,
     *    "project": {
     *       "id": 1,
     *       "name": "Test Project"
     *    },
     *    "creator": {
     *       "id": 1,
     *       "firstname": "Joe",
     *       "lastname": "User"
     *    }
     * }
     *
     * @param Budget $budget
     * @return \Illuminate\Http\Response
     */
    public function show(Budget $budget)
    {
        $this->authorize('access', $budget->project);

        return $budget->load(['project:id,name', 'creator:id,firstname,lastname']);
    }

    /**
     * Update the specified budget in storage.
     *
     * @api {put} budgets/:id Update a budget
     * @apiGroup Budgets
     * @apiName UpdateBudget
     * @apiPermission projectAdmin
     * @apiDescription Updates the specified budget.
     *
     * @apiParam {Number} id Budget ID.
     * @apiParam {String} [name] Budget name.
     * @apiParam {String} [description] Budget description.
     * @apiParam {Number} [amount] Budget amount.
     * @apiParam {Number} [spent] Amount spent.
     * @apiParam {String} [currency] Currency code (3 characters).
     * @apiParam {String} [start_date] Budget start date (YYYY-MM-DD).
     * @apiParam {String} [end_date] Budget end date (YYYY-MM-DD).
     * @apiParam {Boolean} [active] Whether the budget is active.
     *
     * @param UpdateBudget $request
     * @param Budget $budget
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBudget $request, Budget $budget)
    {
        $this->authorize('update', $budget->project);

        $budget->update($request->validated());

        return $budget;
    }

    /**
     * Remove the specified budget from storage.
     *
     * @api {delete} budgets/:id Delete a budget
     * @apiGroup Budgets
     * @apiName DestroyBudget
     * @apiPermission projectAdmin
     * @apiDescription Deletes the specified budget.
     *
     * @apiParam {Number} id Budget ID.
     *
     * @param Budget $budget
     * @return \Illuminate\Http\Response
     */
    public function destroy(Budget $budget)
    {
        $this->authorize('update', $budget->project);

        $budget->delete();

        return response('', 200);
    }
}