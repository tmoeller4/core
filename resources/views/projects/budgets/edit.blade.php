@extends('app')

@section('title', 'Edit Budget - ' . $budget->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="clearfix">
                <h2 class="pull-left">
                    <i class="fa fa-edit"></i> Edit Budget
                </h2>
                <div class="pull-right">
                    <a href="{{route('budget-show', $budget->id)}}" class="btn btn-default" title="Back to budget">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <form id="budget-form" action="{{route('api.budgets.update', $budget->id)}}" method="PUT">
                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{$budget->name}}" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" maxlength="2000">{{$budget->description}}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" value="{{$budget->amount}}" required min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="spent">Spent</label>
                            <input type="number" class="form-control" id="spent" name="spent" value="{{$budget->spent}}" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select class="form-control" id="currency" name="currency">
                                <option value="USD" @if($budget->currency === 'USD') selected @endif>USD</option>
                                <option value="EUR" @if($budget->currency === 'EUR') selected @endif>EUR</option>
                                <option value="GBP" @if($budget->currency === 'GBP') selected @endif>GBP</option>
                                <option value="JPY" @if($budget->currency === 'JPY') selected @endif>JPY</option>
                                <option value="CAD" @if($budget->currency === 'CAD') selected @endif>CAD</option>
                                <option value="AUD" @if($budget->currency === 'AUD') selected @endif>AUD</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{$budget->start_date->format('Y-m-d')}}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{$budget->end_date->format('Y-m-d')}}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="active" name="active" @if($budget->active) checked @endif> Active
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Update Budget
                    </button>
                    <a href="{{route('budget-show', $budget->id)}}" class="btn btn-default">Cancel</a>
                    
                    @can('destroy', $budget)
                        <button type="button" class="btn btn-danger pull-right" onclick="deleteBudget()">
                            <i class="fa fa-trash"></i> Delete Budget
                        </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('budget-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Convert checkbox to boolean
        data.active = formData.has('active');
        
        fetch(form.action, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '{{route("budget-show", $budget->id)}}';
            } else {
                response.json().then(data => {
                    alert('Error: ' + (data.message || 'Failed to update budget'));
                });
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });
});

@can('destroy', $budget)
function deleteBudget() {
    if (confirm('Are you sure you want to delete this budget? This action cannot be undone.')) {
        fetch('{{route("api.budgets.destroy", $budget->id)}}', {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '{{route("project-budgets", $budget->project->id)}}';
            } else {
                response.json().then(data => {
                    alert('Error: ' + (data.message || 'Failed to delete budget'));
                });
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}
@endcan
</script>
@endsection