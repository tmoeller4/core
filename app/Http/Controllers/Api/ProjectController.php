<?php namespace Dias\Http\Controllers\Api;

use Dias\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Dias\Project;

class ProjectController extends ApiController {

	/**
	 * Shows all projects the requesting user belongs to.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return $this->auth->user()->projects;
	}

	/**
	 * Displays the specified project.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$project = Project::find($id);
		if (!$project || !$project->hasUser($this->auth->user()))
		{
			abort(401);
		}
		
		return $project;
	}

	/**
	 * Updates the attributes of the specified project.
	 *
	 * @param  int  $id
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function update($id, Request $request)
	{
		$project = Project::find($id);
		if (!$project || !$project->hasAdmin($this->auth->user()))
		{
			abort(401);
		}
		
		$project->name = $request->input('name', $project->name);
		$project->description = $request->input('description', $project->description);
		$project->save();

		return response('Ok.', 200);
	}

	/**
	 * Creates a new project.
	 *
	 * @param  Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if (!$request->has('name', 'description'))
		{
			abort(400, 'Bad arguments.');
		}
		
		$project = new Project;
		$project->name = $request->input('name');
		$project->description = $request->input('description');
		$project->setCreator($this->auth->user());
		$project->save();
		// makes sure the project was successfully stored
		// and doesn't contain additional information like the creator object
		return Project::find($project->id);
	}

	/**
	 * Removes the specified project.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$project = Project::find($id);
		if (!$project)
		{
			abort(404);
		}

		if (!$project->hasAdmin($this->auth->user()))
		{
			abort(401);
		}

		$project->delete();
		return response('Deleted.', 200);
	}

}
