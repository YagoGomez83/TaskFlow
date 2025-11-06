# API Testing Guide

## Base URL
```
http://localhost:8000/api
```

## Authentication Endpoints

### Register

**Linux/Unix/Git Bash:**

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

**Windows PowerShell:**

```powershell
curl -X POST "http://localhost:8000/api/auth/register" `
  -H "Content-Type: application/json" `
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
```

**Windows PowerShell (Alternative - Invoke-RestMethod):**

```powershell
$body = @{
    name = "Test User"
    email = "test@example.com"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/auth/register" `
  -Method POST `
  -ContentType "application/json" `
  -Body $body
```

### Login

**Linux/Unix/Git Bash:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@taskflow.com",
    "password": "password123"
  }'
```

**Windows PowerShell:**
```powershell
curl -X POST "http://localhost:8000/api/auth/login" `
  -H "Content-Type: application/json" `
  -d '{"email":"admin@taskflow.com","password":"password123"}'
```

**Windows PowerShell (Alternative - Invoke-RestMethod):**
```powershell
$body = @{
    email = "admin@taskflow.com"
    password = "password123"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" `
  -Method POST `
  -ContentType "application/json" `
  -Body $body
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

**Linux/Unix/Git Bash:**

```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Windows PowerShell:**

```powershell
curl -X GET "http://localhost:8000/api/auth/me" `
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Windows PowerShell (Alternative - Invoke-RestMethod):**

```powershell
$headers = @{
    "Authorization" = "Bearer YOUR_TOKEN_HERE"
}

Invoke-RestMethod -Uri "http://localhost:8000/api/auth/me" `
  -Method GET `
  -Headers $headers
```

### Logout

**Linux/Unix/Git Bash:**

```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Windows PowerShell:**

```powershell
curl -X POST "http://localhost:8000/api/auth/logout" `
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Windows PowerShell (Alternative - Invoke-RestMethod):**

```powershell
$headers = @{
    "Authorization" = "Bearer YOUR_TOKEN_HERE"
}

Invoke-RestMethod -Uri "http://localhost:8000/api/auth/logout" `
  -Method POST `
  -Headers $headers
```

## Troubleshooting

### PowerShell Issues

**Problem**: `curl: (3) URL rejected: Bad hostname`

- **Solution**: Use quotes around the URL or switch to Invoke-RestMethod

**Problem**: `-H` or `-d` options not recognized

- **Solution**: PowerShell doesn't recognize Unix curl syntax, use the PowerShell examples above

**Problem**: JSON parsing errors

- **Solution**: Ensure proper escaping of quotes in JSON strings

**Problem**: Garbled output or encoding issues

- **Solutions**:
  1. Try using a different terminal (Command Prompt instead of PowerShell)
  2. Test the API endpoint directly in your browser: `http://localhost:8000/api/auth/login`
  3. Use a REST client like Postman, Insomnia, or Thunder Client (VS Code extension)
  4. Set PowerShell encoding: `[Console]::OutputEncoding = [System.Text.Encoding]::UTF8`

### Alternative Testing Methods

**Using Browser (GET requests only)**:

```http
http://localhost:8000/api/projects
```

**Using VS Code Thunder Client Extension**:

1. Install Thunder Client extension
2. Create new request
3. Set method to POST
4. URL: `http://localhost:8000/api/auth/login`
5. Add header: `Content-Type: application/json`
6. Body: `{"email":"admin@taskflow.com","password":"password123"}`

**Using Command Prompt (cmd)**:

```cmd
curl -X POST http://localhost:8000/api/auth/login ^
     -H "Content-Type: application/json" ^
     -d "{\"email\":\"admin@taskflow.com\",\"password\":\"password123\"}"
```

## Test Credentials

| Email | Password | Role |
|-------|----------|------|
| `admin@taskflow.com` | `password123` | admin |
| `john@taskflow.com` | `password123` | manager |
| `jane@taskflow.com` | `password123` | member |
| `bob@taskflow.com` | `password123` | member |
