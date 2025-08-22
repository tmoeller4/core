<?php

namespace Biigle\Tests\Http\Controllers\Api;

use ApiTestCase;
use Biigle\Budget;
use Biigle\Project;
use Biigle\Tests\BudgetTest;

class BudgetControllerTest extends ApiTestCase
{
    public function testIndexProjectBudgets()
    {
        $this->doTestApiRoute('GET', "/api/v1/projects/{$this->project()->id}/budgets");

        $budget = BudgetTest::create(['project_id' => $this->project()->id]);

        $this->beUser();
        $this->get("/api/v1/projects/{$this->project()->id}/budgets")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $budget->id]);
    }

    public function testIndexProjectBudgetsAccessDenied()
    {
        $project = Project::factory()->create();
        $this->beUser();
        $this->get("/api/v1/projects/{$project->id}/budgets")
            ->assertStatus(403);
    }

    public function testStore()
    {
        $this->doTestApiRoute('POST', "/api/v1/projects/{$this->project()->id}/budgets");

        $this->beUser();
        $this->postJson("/api/v1/projects/{$this->project()->id}/budgets", [
            'name' => 'Test Budget',
            'amount' => 1000.50,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ])
        ->assertStatus(200)
        ->assertJsonFragment(['name' => 'Test Budget']);

        $this->assertDatabaseHas('budgets', [
            'name' => 'Test Budget',
            'project_id' => $this->project()->id,
            'creator_id' => $this->user()->id,
        ]);
    }

    public function testStoreValidation()
    {
        $this->beUser();
        
        // Test required name
        $this->postJson("/api/v1/projects/{$this->project()->id}/budgets", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Test required amount
        $this->postJson("/api/v1/projects/{$this->project()->id}/budgets", [
            'name' => 'Test Budget',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['amount']);

        // Test end date after start date
        $this->postJson("/api/v1/projects/{$this->project()->id}/budgets", [
            'name' => 'Test Budget',
            'amount' => 1000,
            'start_date' => '2024-12-31',
            'end_date' => '2024-01-01',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['end_date']);
    }

    public function testShow()
    {
        $budget = BudgetTest::create(['project_id' => $this->project()->id]);
        $this->doTestApiRoute('GET', "/api/v1/budgets/{$budget->id}");

        $this->beUser();
        $this->get("/api/v1/budgets/{$budget->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $budget->id])
            ->assertJsonStructure([
                'id',
                'name',
                'amount',
                'spent',
                'currency',
                'start_date',
                'end_date',
                'active',
                'project' => ['id', 'name'],
                'creator' => ['id', 'firstname', 'lastname'],
            ]);
    }

    public function testShowAccessDenied()
    {
        $budget = BudgetTest::create();
        $this->beUser();
        $this->get("/api/v1/budgets/{$budget->id}")
            ->assertStatus(403);
    }

    public function testUpdate()
    {
        $budget = BudgetTest::create(['project_id' => $this->project()->id]);
        $this->doTestApiRoute('PUT', "/api/v1/budgets/{$budget->id}");

        $this->beEditor();
        $this->putJson("/api/v1/budgets/{$budget->id}", [
            'name' => 'Updated Budget',
            'spent' => 500.25,
        ])
        ->assertStatus(200);

        $budget->refresh();
        $this->assertEquals('Updated Budget', $budget->name);
        $this->assertEquals(500.25, $budget->spent);
    }

    public function testUpdateAccessDenied()
    {
        $budget = BudgetTest::create();
        $this->beUser();
        $this->putJson("/api/v1/budgets/{$budget->id}", [
            'name' => 'Updated Budget',
        ])
        ->assertStatus(403);
    }

    public function testDestroy()
    {
        $budget = BudgetTest::create(['project_id' => $this->project()->id]);
        $this->doTestApiRoute('DELETE', "/api/v1/budgets/{$budget->id}");

        $this->beAdmin();
        $this->delete("/api/v1/budgets/{$budget->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }

    public function testDestroyAccessDenied()
    {
        $budget = BudgetTest::create(['project_id' => $this->project()->id]);
        
        $this->beEditor();
        $this->delete("/api/v1/budgets/{$budget->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('budgets', ['id' => $budget->id]);
    }
}