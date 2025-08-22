@extends('manual.base')
@section('manual-title', 'Budget Management')

@section('manual-content')
    <div class="row">
        <p class="lead">
            Learn how to create and manage budgets for your projects.
        </p>

        <p>
            Budget management in BIIGLE allows project administrators to track spending and monitor resource allocation for their research projects. Each project can have multiple budgets with different time periods and purposes.
        </p>

        <h3><a name="creating-budgets"></a>Creating Budgets</h3>

        <p>
            To create a budget for your project:
        </p>

        <ol>
            <li>Navigate to your project page</li>
            <li>Click on the <button class="btn btn-default btn-xs"><i class="fa fa-dollar"></i> Budgets</button> tab</li>
            <li>Click the <button class="btn btn-default btn-xs"><i class="fa fa-plus"></i> Create Budget</button> button</li>
            <li>Fill in the budget details:
                <ul>
                    <li><strong>Name:</strong> A descriptive name for your budget</li>
                    <li><strong>Description:</strong> Optional details about the budget purpose</li>
                    <li><strong>Amount:</strong> The total budget amount</li>
                    <li><strong>Currency:</strong> The currency for the budget (USD, EUR, GBP, etc.)</li>
                    <li><strong>Start Date:</strong> When the budget period begins</li>
                    <li><strong>End Date:</strong> When the budget period ends</li>
                    <li><strong>Active:</strong> Whether the budget is currently active</li>
                </ul>
            </li>
            <li>Click <button class="btn btn-success btn-xs"><i class="fa fa-save"></i> Create Budget</button> to save</li>
        </ol>

        <p>
            <strong>Note:</strong> You need at least <em>editor</em> permissions in the project to create budgets.
        </p>

        <h3><a name="viewing-budgets"></a>Viewing Budgets</h3>

        <p>
            The budget overview shows all budgets for a project with:
        </p>

        <ul>
            <li>Budget name and description</li>
            <li>Budget period (start and end dates)</li>
            <li>Amount spent vs. total budget</li>
            <li>Visual progress bar showing utilization</li>
            <li>Status indicators (active/inactive, overrun warnings)</li>
        </ul>

        <p>
            Click on a budget name to view detailed information including:
        </p>

        <ul>
            <li>Complete budget details</li>
            <li>Creator and creation date</li>
            <li>Remaining budget amount</li>
            <li>Utilization percentage</li>
        </ul>

        <h3><a name="updating-budgets"></a>Updating Budgets</h3>

        <p>
            Project editors and administrators can update budget information:
        </p>

        <h4>Updating Spent Amount</h4>
        <p>
            From the budget detail page, use the "Update Spent" form to record expenses:
        </p>
        <ol>
            <li>Enter the current total spent amount</li>
            <li>Click <button class="btn btn-primary btn-xs">Update Spent</button></li>
        </ol>

        <h4>Toggling Budget Status</h4>
        <p>
            You can activate or deactivate budgets using the toggle button on the budget detail page. Inactive budgets will not appear in current budget calculations.
        </p>

        <h4>Editing Budget Details</h4>
        <p>
            To modify budget details like name, amount, or dates:
        </p>
        <ol>
            <li>Go to the budget detail page</li>
            <li>Click <button class="btn btn-default btn-xs"><i class="fa fa-edit"></i> Edit</button></li>
            <li>Update the desired fields</li>
            <li>Save your changes</li>
        </ol>

        <h3><a name="budget-monitoring"></a>Budget Monitoring</h3>

        <p>
            BIIGLE provides several visual indicators to help monitor budget status:
        </p>

        <h4>Progress Bars</h4>
        <ul>
            <li><span class="label label-success">Green</span> - Budget utilization under 80%</li>
            <li><span class="label label-warning">Yellow</span> - Budget utilization 80-100%</li>
            <li><span class="label label-danger">Red</span> - Budget overrun (over 100%)</li>
        </ul>

        <h4>Status Indicators</h4>
        <ul>
            <li><span class="label label-success">Active</span> - Budget is currently active and within date range</li>
            <li><span class="label label-default">Inactive</span> - Budget is deactivated or outside date range</li>
            <li><span class="label label-danger">Overrun</span> - Spent amount exceeds budget amount</li>
        </ul>

        <h3><a name="permissions"></a>Budget Permissions</h3>

        <p>
            Budget management permissions are tied to your project role:
        </p>

        <ul>
            <li><strong>Guest:</strong> Can view budgets</li>
            <li><strong>Editor:</strong> Can view, create, and update budgets</li>
            <li><strong>Expert:</strong> Can view, create, and update budgets</li>
            <li><strong>Admin:</strong> Can view, create, update, and delete budgets</li>
        </ul>

        <p>
            Global administrators have full access to all budgets across all projects.
        </p>

        <h3><a name="best-practices"></a>Best Practices</h3>

        <ul>
            <li>Use descriptive budget names that indicate the purpose or funding source</li>
            <li>Set realistic start and end dates that match your project timeline</li>
            <li>Update spent amounts regularly to maintain accurate tracking</li>
            <li>Use the description field to note any special conditions or restrictions</li>
            <li>Consider creating separate budgets for different expense categories</li>
            <li>Monitor utilization percentages to avoid budget overruns</li>
            <li>Deactivate completed or cancelled budgets to keep the list current</li>
        </ul>
    </div>
@endsection