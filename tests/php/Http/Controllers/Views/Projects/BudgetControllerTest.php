<?php

namespace Biigle\Tests\Http\Controllers\Views\Projects;

use Biigle\Role;
use Biigle\Tests\BudgetTest;
use Biigle\Tests\ProjectTest;
use Biigle\Tests\UserTest;
use TestCase;

class BudgetControllerTest extends TestCase
{
    public function testIndexBudgets()
    {
        $project = ProjectTest::create();
        $user = UserTest::create();
        
        // Not logged in
        $this->get("projects/{$project->id}/budgets")
            ->assertStatus(302);
        
        // Not a project member
        $this->be($user);
        $this->get("projects/{$project->id}/budgets")
            ->assertStatus(403);
        
        // Project member can view
        $project->addUserId($user->id, Role::guestId());
        $this->get("projects/{$project->id}/budgets")
            ->assertStatus(200)
            ->assertViewIs('projects.budgets.index')
            ->assertViewHas('project', $project);
    }

    public function testCreate()
    {
        $project = ProjectTest::create();
        $user = UserTest::create();
        
        // Not logged in
        $this->get("projects/{$project->id}/budgets/create")
            ->assertStatus(302);
        
        // Guest cannot create
        $project->addUserId($user->id, Role::guestId());
        $this->be($user);
        $this->get("projects/{$project->id}/budgets/create")
            ->assertStatus(403);
        
        // Editor can create
        $project->changeRole($user->id, Role::editorId());
        $this->get("projects/{$project->id}/budgets/create")
            ->assertStatus(200)
            ->assertViewIs('projects.budgets.create')
            ->assertViewHas('project', $project);
    }

    public function testShow()
    {
        $project = ProjectTest::create();
        $budget = BudgetTest::create(['project_id' => $project->id]);
        $user = UserTest::create();
        
        // Not logged in
        $this->get("budgets/{$budget->id}")
            ->assertStatus(302);
        
        // Not a project member
        $this->be($user);
        $this->get("budgets/{$budget->id}")
            ->assertStatus(403);
        
        // Project member can view
        $project->addUserId($user->id, Role::guestId());
        $this->get("budgets/{$budget->id}")
            ->assertStatus(200)
            ->assertViewIs('projects.budgets.show')
            ->assertViewHas('budget');
    }

    public function testEdit()
    {
        $project = ProjectTest::create();
        $budget = BudgetTest::create(['project_id' => $project->id]);
        $user = UserTest::create();
        
        // Not logged in
        $this->get("budgets/{$budget->id}/edit")
            ->assertStatus(302);
        
        // Guest cannot edit
        $project->addUserId($user->id, Role::guestId());
        $this->be($user);
        $this->get("budgets/{$budget->id}/edit")
            ->assertStatus(403);
        
        // Editor can edit
        $project->changeRole($user->id, Role::editorId());
        $this->get("budgets/{$budget->id}/edit")
            ->assertStatus(200)
            ->assertViewIs('projects.budgets.edit')
            ->assertViewHas('budget');
    }
}