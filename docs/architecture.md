# 🏗️ TaskFlow - Architecture Documentation

## System Architecture
```
┌─────────────────┐
│  React Frontend │
│   (Port 3000)   │
└────────┬────────┘
         │
         │ HTTP/REST
         │
┌────────▼────────┐
│ Laravel Backend │
│   (Port 8000)   │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
┌───▼──┐  ┌──▼───┐
│ MySQL│  │ Redis│
└──────┘  └──────┘
```

## Tech Stack Details

### Backend (Laravel 11)
- **Framework**: Laravel 11.x
- **Authentication**: JWT (tymon/jwt-auth)
- **Database ORM**: Eloquent
- **Cache**: Redis
- **API**: RESTful

### Frontend (React 18)
- **Framework**: React 18 + Vite
- **Styling**: Tailwind CSS
- **HTTP Client**: Axios
- **State Management**: React Query
- **Routing**: React Router DOM

### Database Schema (Preview)

**users**
- id, name, email, password, role, timestamps

**projects**
- id, name, description, owner_id, timestamps

**tasks**
- id, title, description, status, priority, project_id, assigned_to, timestamps

**project_user** (pivot)
- project_id, user_id, role

## API Endpoints (Preview)

### Authentication
- POST /api/auth/register
- POST /api/auth/login
- POST /api/auth/logout
- GET /api/auth/me

### Projects
- GET /api/projects
- POST /api/projects
- GET /api/projects/{id}
- PUT /api/projects/{id}
- DELETE /api/projects/{id}

### Tasks
- GET /api/projects/{id}/tasks
- POST /api/tasks
- PUT /api/tasks/{id}
- DELETE /api/tasks/{id}
- PATCH /api/tasks/{id}/status

