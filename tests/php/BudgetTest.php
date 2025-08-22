<?php

namespace Biigle\Tests;

use Biigle\Budget;
use Biigle\Project;
use Biigle\Tests\BudgetTest;
use Biigle\Tests\ProjectTest;
use Biigle\Tests\UserTest;
use Illuminate\Database\QueryException;
use ModelTestCase;

class BudgetTest extends ModelTestCase
{
    /**
     * The model class this class will test.
     */
    protected static $modelClass = Budget::class;

    public function testAttributes()
    {
        $this->assertNotNull($this->model->name);
        $this->assertNotNull($this->model->amount);
        $this->assertNotNull($this->model->spent);
        $this->assertNotNull($this->model->currency);
        $this->assertNotNull($this->model->start_date);
        $this->assertNotNull($this->model->end_date);
        $this->assertNotNull($this->model->active);
        $this->assertNotNull($this->model->project_id);
        $this->assertNotNull($this->model->created_at);
        $this->assertNotNull($this->model->updated_at);
    }

    public function testNameRequired()
    {
        $this->model->name = null;
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testAmountRequired()
    {
        $this->model->amount = null;
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testProjectRequired()
    {
        $this->model->project_id = null;
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testStartDateRequired()
    {
        $this->model->start_date = null;
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testEndDateRequired()
    {
        $this->model->end_date = null;
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testProject()
    {
        $this->assertInstanceOf(Project::class, $this->model->project);
        $this->assertEquals($this->model->project_id, $this->model->project->id);
    }

    public function testCreator()
    {
        $creator = UserTest::create();
        $this->model->creator_id = $creator->id;
        $this->model->save();
        
        $this->assertEquals($creator->id, $this->model->creator->id);
    }

    public function testRemainingAttribute()
    {
        $this->model->amount = 1000;
        $this->model->spent = 250;
        $this->assertEquals(750, $this->model->remaining);
    }

    public function testUtilizationPercentageAttribute()
    {
        $this->model->amount = 1000;
        $this->model->spent = 250;
        $this->assertEquals(25.0, $this->model->utilization_percentage);
    }

    public function testUtilizationPercentageZeroAmount()
    {
        $this->model->amount = 0;
        $this->model->spent = 100;
        $this->assertEquals(0, $this->model->utilization_percentage);
    }

    public function testIsActive()
    {
        $this->model->active = true;
        $this->model->start_date = now()->subDay();
        $this->model->end_date = now()->addDay();
        $this->assertTrue($this->model->isActive());
        
        $this->model->active = false;
        $this->assertFalse($this->model->isActive());
        
        $this->model->active = true;
        $this->model->start_date = now()->addDay();
        $this->assertFalse($this->model->isActive());
    }

    public function testIsOverrun()
    {
        $this->model->amount = 1000;
        $this->model->spent = 1200;
        $this->assertTrue($this->model->isOverrun());
        
        $this->model->spent = 800;
        $this->assertFalse($this->model->isOverrun());
    }

    public function testScopeActive()
    {
        $activeBudget = BudgetTest::create(['active' => true]);
        $inactiveBudget = BudgetTest::create(['active' => false]);
        
        $active = Budget::active()->get();
        $this->assertTrue($active->contains($activeBudget));
        $this->assertFalse($active->contains($inactiveBudget));
    }

    public function testScopeCurrent()
    {
        $currentBudget = BudgetTest::create([
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);
        
        $pastBudget = BudgetTest::create([
            'start_date' => now()->subWeek(),
            'end_date' => now()->subDay(),
        ]);
        
        $current = Budget::current()->get();
        $this->assertTrue($current->contains($currentBudget));
        $this->assertFalse($current->contains($pastBudget));
    }

    public function testCasts()
    {
        $this->model->amount = '1000.50';
        $this->model->spent = '250.25';
        $this->model->active = '1';
        $this->model->start_date = '2024-01-01';
        $this->model->end_date = '2024-12-31';
        $this->model->save();
        
        $this->model->refresh();
        
        $this->assertIsFloat($this->model->amount);
        $this->assertIsFloat($this->model->spent);
        $this->assertIsBool($this->model->active);
        $this->assertInstanceOf(\Carbon\Carbon::class, $this->model->start_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $this->model->end_date);
    }
}