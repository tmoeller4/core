@extends('projects.show.base')

@section('title', $project->name.' - Create Budget')

@section('project-content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <h3><i class="fa fa-dollar"></i> Create Budget</h3>

        <form id="budget-form" action="{{route('api.projects.budgets.store', $project->id)}}" method="POST">
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required maxlength="255">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" maxlength="2000"></textarea>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="amount">Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="amount" name="amount" required min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select class="form-control" id="currency" name="currency">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                            <option value="JPY">JPY</option>
                            <option value="CAD">CAD</option>
                            <option value="AUD">AUD</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_date">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_date">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="active" name="active" checked> Active
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Create Budget
                </button>
                <a href="{{route('project-budgets', $project->id)}}" class="btn btn-default">Cancel</a>
            </div>
        </form>
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
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '{{route("project-budgets", $project->id)}}';
            } else {
                response.json().then(data => {
                    alert('Error: ' + (data.message || 'Failed to create budget'));
                });
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });
});
</script>
@endsection

@php $activeTab = 'budgets'; @endphp