@extends('projects.show.base')

@section('title', $project->name.' - Budgets')

@push('scripts')
<script type="text/javascript">
    biigle.$declare('projects.budgets', {!! $project->budgets->toJson() !!});
</script>
@endpush

@section('project-content')
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xs-6">
                <h3><i class="fa fa-dollar"></i> Budgets</h3>
            </div>
            <div class="col-xs-6 text-right">
                @can('update', $project)
                    <a href="{{route('project-budgets-create', $project->id)}}" class="btn btn-default" title="Create a new budget">
                        <i class="fa fa-plus"></i> Create Budget
                    </a>
                @endcan
            </div>
        </div>

        @if($project->budgets->isEmpty())
            <div class="well text-center">
                <p class="text-muted">
                    <i class="fa fa-dollar fa-3x"></i><br>
                    This project has no budgets yet.
                </p>
                @can('update', $project)
                    <a href="{{route('project-budgets-create', $project->id)}}" class="btn btn-default">
                        Create the first budget
                    </a>
                @endcan
            </div>
        @else
            <div class="budgets-list">
                @foreach($project->budgets as $budget)
                    <div class="panel panel-default budget-item">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>
                                        <a href="{{route('budget-show', $budget->id)}}">{{$budget->name}}</a>
                                        @if(!$budget->isActive())
                                            <small class="text-muted">(Inactive)</small>
                                        @endif
                                    </h4>
                                    @if($budget->description)
                                        <p class="text-muted">{{$budget->description}}</p>
                                    @endif
                                    <p>
                                        <strong>Period:</strong> {{$budget->start_date->format('M j, Y')}} - {{$budget->end_date->format('M j, Y')}}
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <h4>
                                        {{$budget->spent}} / {{$budget->amount}} {{$budget->currency}}
                                        @if($budget->isOverrun())
                                            <span class="label label-danger">Overrun</span>
                                        @endif
                                    </h4>
                                    <div class="progress">
                                        <div class="progress-bar @if($budget->isOverrun()) progress-bar-danger @elseif($budget->utilization_percentage > 80) progress-bar-warning @else progress-bar-success @endif"
                                             style="width: {{min($budget->utilization_percentage, 100)}}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{$budget->utilization_percentage}}% utilized</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@php $activeTab = 'budgets'; @endphp