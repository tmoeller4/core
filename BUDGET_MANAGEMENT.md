# Budget Management

This document describes the budget management functionality added to BIIGLE.

## Overview

The budget management system allows project administrators to create and manage budgets for their projects. Users can track spending against budgets and monitor budget utilization through a web interface.

## Features

### Budget Creation
- Create budgets for projects with:
  - Name and description
  - Budget amount and currency
  - Start and end dates
  - Active/inactive status

### Budget Tracking
- Track spent amounts against budget
- Visual progress indicators
- Overrun warnings
- Utilization percentages

### Authorization
- Project members can view budgets
- Project editors can create and update budgets  
- Project admins can delete budgets
- Global admins have full access

## API Endpoints

### List Project Budgets
```
GET /api/v1/projects/{id}/budgets
```

### Create Budget
```
POST /api/v1/projects/{id}/budgets
```

### Get Budget
```
GET /api/v1/budgets/{id}
```

### Update Budget
```
PUT /api/v1/budgets/{id}
```

### Delete Budget
```
DELETE /api/v1/budgets/{id}
```

## Web Routes

### Project Budget Management
- `/projects/{id}/budgets` - List project budgets
- `/projects/{id}/budgets/create` - Create new budget

### Individual Budget Management  
- `/budgets/{id}` - View budget details
- `/budgets/{id}/edit` - Edit budget

## Database Schema

### Budgets Table
- `id` - Primary key
- `name` - Budget name (required)
- `description` - Optional description
- `amount` - Budget amount (decimal, required)
- `spent` - Amount spent (decimal, default 0)
- `currency` - 3-letter currency code (default USD)
- `start_date` - Budget start date (required)
- `end_date` - Budget end date (required)
- `active` - Boolean active status (default true)
- `project_id` - Foreign key to projects table
- `creator_id` - Foreign key to users table (nullable)
- `created_at` - Timestamp
- `updated_at` - Timestamp

## Model Relationships

### Project Model
- `budgets()` - Has many budgets
- `activeBudgets()` - Has many active budgets (active and current)

### Budget Model
- `project()` - Belongs to project
- `creator()` - Belongs to user (creator)

## Model Methods

### Budget Model
- `getRemainingAttribute()` - Calculate remaining budget
- `getUtilizationPercentageAttribute()` - Calculate utilization percentage
- `isActive()` - Check if budget is currently active
- `isOverrun()` - Check if budget is overrun
- `scopeActive()` - Query scope for active budgets
- `scopeCurrent()` - Query scope for current budgets (within date range)

## Views

### Budget Index (`resources/views/projects/budgets/index.blade.php`)
- Lists all budgets for a project
- Shows budget status, amounts, and progress bars
- Links to create new budget and view details

### Budget Create (`resources/views/projects/budgets/create.blade.php`)
- Form to create new budget
- Client-side validation and API submission

### Budget Show (`resources/views/projects/budgets/show.blade.php`)
- Detailed budget view with progress visualization
- Quick actions for updating spent amount and toggling status
- Links to edit budget

## Testing

### Unit Tests
- `BudgetTest.php` - Tests Budget model functionality
- Project relationship tests added to `ProjectTest.php`

### API Tests
- `BudgetControllerTest.php` - Tests all API endpoints
- Authorization and validation testing

### Database Factory
- `BudgetFactory.php` - Creates test budget instances
- Includes states for active, inactive, and overrun budgets

## Usage Example

1. Navigate to a project
2. Click the "Budgets" tab  
3. Click "Create Budget" to add a new budget
4. Fill in budget details and save
5. View budget progress on the budgets list
6. Click individual budgets to view details and update spending
7. Use quick actions to update spent amounts or toggle active status

## Security

- All budget operations require appropriate project permissions
- API endpoints are protected by authentication middleware
- Authorization is handled through Laravel policies
- CSRF protection on form submissions