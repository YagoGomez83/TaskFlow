# API Testing Guide

## Base URL
```
http://localhost:8000/api
```

## Authentication Endpoints

### Register
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@taskflow.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@taskflow.com",
    "role": "admin"
  },
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Get Current User
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Test Credentials

| Email | Password | Role |
|-------|----------|------|
| admin@taskflow.com | password123 | admin |
| john@taskflow.com | password123 | manager |
| jane@taskflow.com | password123 | member |
| bob@taskflow.com | password123 | member |
