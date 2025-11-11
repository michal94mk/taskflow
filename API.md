# TaskFlow API Documentation

This document describes the available API endpoints in TaskFlow.

## Authentication

All API endpoints require authentication using Laravel Sanctum.

### Headers

```
Accept: application/json
Content-Type: application/json
Authorization: Bearer {token}
```

## Endpoints

### Projects

#### List Projects

```http
GET /projects
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Project Name",
      "description": "Project description",
      "status": "active",
      "start_date": "2025-01-01",
      "end_date": "2025-12-31",
      "created_at": "2025-01-01T00:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Project

```http
POST /projects
```

**Body:**
```json
{
  "name": "New Project",
  "description": "Project description",
  "status": "active",
  "start_date": "2025-01-01",
  "end_date": "2025-12-31"
}
```

#### Get Project

```http
GET /projects/{id}
```

#### Update Project

```http
PUT /projects/{id}
```

**Body:**
```json
{
  "name": "Updated Project",
  "description": "Updated description",
  "status": "completed"
}
```

#### Delete Project

```http
DELETE /projects/{id}
```

---

### Tasks

#### List Tasks

```http
GET /tasks
```

**Query Parameters:**
- `project_id` - Filter by project
- `status_id` - Filter by status
- `priority_id` - Filter by priority
- `search` - Search in title and description

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Task Title",
      "description": "Task description",
      "due_date": "2025-01-15",
      "project": {
        "id": 1,
        "name": "Project Name"
      },
      "task_status": {
        "id": 1,
        "name": "To Do",
        "color": "blue"
      },
      "task_priority": {
        "id": 2,
        "name": "Medium",
        "color": "yellow"
      }
    }
  ]
}
```

#### Create Task

```http
POST /tasks
```

**Body:**
```json
{
  "title": "New Task",
  "description": "Task description",
  "project_id": 1,
  "task_status_id": 1,
  "task_priority_id": 2,
  "due_date": "2025-01-15"
}
```

#### Get Task

```http
GET /tasks/{id}
```

**Response:**
```json
{
  "id": 1,
  "title": "Task Title",
  "description": "Task description",
  "due_date": "2025-01-15",
  "project": {...},
  "task_status": {...},
  "task_priority": {...},
  "comments": [
    {
      "id": 1,
      "content": "<p>Comment text</p>",
      "user": {
        "id": 1,
        "name": "User Name"
      },
      "created_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

#### Update Task

```http
PUT /tasks/{id}
```

#### Delete Task

```http
DELETE /tasks/{id}
```

---

### Kanban

#### Get Kanban Board

```http
GET /kanban
```

**Query Parameters:**
- `project_id` - Filter by project

**Response:**
```json
{
  "tasksByStatus": [
    {
      "status": {
        "id": 1,
        "name": "To Do",
        "color": "blue"
      },
      "tasks": [...]
    }
  ]
}
```

#### Update Task Status

```http
PATCH /kanban/{task}/status
```

**Body:**
```json
{
  "task_status_id": 2
}
```

---

### Comments

#### Add Comment

```http
POST /tasks/{task}/comments
```

**Body:**
```json
{
  "content": "<p>Comment with <strong>rich text</strong></p>"
}
```

#### Update Comment

```http
PATCH /comments/{comment}
```

**Body:**
```json
{
  "content": "<p>Updated comment</p>"
}
```

#### Delete Comment

```http
DELETE /comments/{comment}
```

---

### Search

#### Global Search

```http
GET /search?q={query}
```

**Query Parameters:**
- `q` - Search query

**Response:**
```json
{
  "tasks": [
    {
      "id": 1,
      "title": "Task Title",
      "description": "Description",
      "type": "task",
      "url": "/tasks/1",
      "project": "Project Name",
      "status": "To Do",
      "priority": "Medium"
    }
  ],
  "projects": [
    {
      "id": 1,
      "title": "Project Name",
      "description": "Description",
      "type": "project",
      "url": "/projects/1",
      "status": "active"
    }
  ]
}
```

---

### Dashboard

#### Get Dashboard Data

```http
GET /dashboard
```

**Response:**
```json
{
  "kpi": {
    "totalTasks": 50,
    "completedTasks": 20,
    "inProgressTasks": 15,
    "overdueTasks": 5
  },
  "recentTasks": [...],
  "projects": [...],
  "charts": {
    "tasksByStatus": [...],
    "tasksByPriority": [...],
    "timeline": [...]
  }
}
```

---

## Error Responses

### Validation Error (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

### Unauthorized (401)

```json
{
  "message": "Unauthenticated."
}
```

### Forbidden (403)

```json
{
  "message": "This action is unauthorized."
}
```

### Not Found (404)

```json
{
  "message": "Resource not found."
}
```

---

## Rate Limiting

API requests are rate-limited to:
- 60 requests per minute for authenticated users
- 10 requests per minute for guests

---

## Pagination

List endpoints support pagination:

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

**Response:**
```json
{
  "data": [...],
  "links": {
    "first": "http://example.com/api/tasks?page=1",
    "last": "http://example.com/api/tasks?page=5",
    "prev": null,
    "next": "http://example.com/api/tasks?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 75
  }
}
```

