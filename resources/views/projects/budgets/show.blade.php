@extends('app')

@section('title', $budget->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="clearfix">
                <h2 class="pull-left">
                    <i class="fa fa-dollar"></i> {{$budget->name}}
                    @if(!$budget->isActive())
                        <small class="text-muted">(Inactive)</small>
                    @endif
                </h2>
                <div class="pull-right">
                    @can('update', $budget->project)
                        <a href="{{route('budget-edit', $budget->id)}}" class="btn btn-default" title="Edit budget">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    @endcan
                    <a href="{{route('project-budgets', $budget->project->id)}}" class="btn btn-default" title="Back to project budgets">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl>
                                <dt>Project</dt>
                                <dd><a href="{{route('project', $budget->project->id)}}">{{$budget->project->name}}</a></dd>
                                
                                @if($budget->description)
                                    <dt>Description</dt>
                                    <dd>{{$budget->description}}</dd>
                                @endif
                                
                                <dt>Period</dt>
                                <dd>{{$budget->start_date->format('M j, Y')}} - {{$budget->end_date->format('M j, Y')}}</dd>
                                
                                <dt>Created by</dt>
                                <dd>{{$budget->creator ? $budget->creator->firstname.' '.$budget->creator->lastname : 'Unknown'}}</dd>
                                
                                <dt>Created</dt>
                                <dd>{{$budget->created_at->format('M j, Y g:i A')}}</dd>
                                
                                @if($budget->created_at != $budget->updated_at)
                                    <dt>Last updated</dt>
                                    <dd>{{$budget->updated_at->format('M j, Y g:i A')}}</dd>
                                @endif
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3>Budget Overview</h3>
                                
                                <div class="budget-chart">
                                    <h4>{{$budget->spent}} / {{$budget->amount}} {{$budget->currency}}</h4>
                                    
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar @if($budget->isOverrun()) progress-bar-danger @elseif($budget->utilization_percentage > 80) progress-bar-warning @else progress-bar-success @endif"
                                             style="width: {{min($budget->utilization_percentage, 100)}}%">
                                            {{$budget->utilization_percentage}}%
                                        </div>
                                    </div>
                                    
                                    @if($budget->isOverrun())
                                        <div class="alert alert-danger">
                                            <i class="fa fa-exclamation-triangle"></i>
                                            Budget is overrun by {{$budget->currency}} {{$budget->spent - $budget->amount}}
                                        </div>
                                    @else
                                        <p class="text-muted">
                                            Remaining: {{$budget->currency}} {{$budget->remaining}}
                                        </p>
                                    @endif
                                    
                                    @if($budget->isActive())
                                        <span class="label label-success">Active</span>
                                    @else
                                        <span class="label label-default">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @can('update', $budget->project)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="panel-body">
                        <form id="update-spent-form" style="display: inline-block; margin-right: 10px;">
                            <div class="input-group">
                                <input type="number" class="form-control" id="spent-amount" 
                                       value="{{$budget->spent}}" min="0" step="0.01" 
                                       placeholder="Amount spent">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary">Update Spent</button>
                                </span>
                            </div>
                        </form>
                        
                        <button type="button" class="btn btn-warning" onclick="toggleActive()">
                            @if($budget->active)
                                <i class="fa fa-pause"></i> Deactivate
                            @else
                                <i class="fa fa-play"></i> Activate
                            @endif
                        </button>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>

@can('update', $budget->project)
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    const updateSpentForm = document.getElementById('update-spent-form');
    
    updateSpentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const spentAmount = document.getElementById('spent-amount').value;
        
        fetch('{{route("api.budgets.update", $budget->id)}}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                spent: spentAmount
            })
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                response.json().then(data => {
                    alert('Error: ' + (data.message || 'Failed to update spent amount'));
                });
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });
});

function toggleActive() {
    const currentActive = {{$budget->active ? 'true' : 'false'}};
    
    fetch('{{route("api.budgets.update", $budget->id)}}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            active: !currentActive
        })
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            response.json().then(data => {
                alert('Error: ' + (data.message || 'Failed to toggle budget status'));
            });
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
</script>
@endcan
@endsection