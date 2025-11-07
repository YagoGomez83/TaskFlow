Write-Host "🧪 Testing TaskFlow API - Día 3" -ForegroundColor Cyan
Write-Host "================================`n" -ForegroundColor Cyan

$baseUrl = "http://localhost:8000/api"
$testsPassed = 0
$testsFailed = 0

# 1. LOGIN
Write-Host "1️⃣ Testing Login..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/auth/login" `
        -Method Post `
        -Body '{"email":"admin@taskflow.com","password":"password123"}' `
        -ContentType "application/json" -ErrorAction Stop
    
    $token = $response.access_token
    Write-Host "   ✅ Login exitoso" -ForegroundColor Green
    $testsPassed++
} catch {
    Write-Host "   ❌ Login falló" -ForegroundColor Red
    $testsFailed++
    exit 1
}

# 2. GET ME
Write-Host "`n2️⃣ Testing Get Current User..." -ForegroundColor Yellow
try {
    $me = Invoke-RestMethod -Uri "$baseUrl/auth/me" `
        -Method Get `
        -Headers @{Authorization="Bearer $token"} -ErrorAction Stop
    
    Write-Host "   ✅ Usuario: $($me.email)" -ForegroundColor Green
    $testsPassed++
} catch {
    Write-Host "   ❌ Get Me falló" -ForegroundColor Red
    $testsFailed++
}

# 3. LIST PROJECTS
Write-Host "`n3️⃣ Testing List Projects..." -ForegroundColor Yellow
try {
    $projects = Invoke-RestMethod -Uri "$baseUrl/projects" `
        -Method Get `
        -Headers @{Authorization="Bearer $token"} -ErrorAction Stop
    
    Write-Host "   ✅ Proyectos: $($projects.data.Count)" -ForegroundColor Green
    $testsPassed++
} catch {
    Write-Host "   ❌ List Projects falló" -ForegroundColor Red
    $testsFailed++
}

# 4. CREATE PROJECT
Write-Host "`n4️⃣ Testing Create Project..." -ForegroundColor Yellow
try {
    $projectBody = @{
        name = "Test Project API"
        description = "Testing endpoint"
        color = "#FF5733"
        status = "active"
    } | ConvertTo-Json
    
    $newProject = Invoke-RestMethod -Uri "$baseUrl/projects" `
        -Method Post `
        -Headers @{Authorization="Bearer $token"} `
        -Body $projectBody `
        -ContentType "application/json" -ErrorAction Stop
    
    $projectId = $newProject.data.id
    Write-Host "   ✅ Proyecto creado (ID: $projectId)" -ForegroundColor Green
    $testsPassed++
} catch {
    Write-Host "   ❌ Create Project falló" -ForegroundColor Red
    $testsFailed++
    $projectId = $null
}

# 5. GET PROJECT
if ($projectId) {
    Write-Host "`n5️⃣ Testing Get Project..." -ForegroundColor Yellow
    try {
        $project = Invoke-RestMethod -Uri "$baseUrl/projects/$projectId" `
            -Method Get `
            -Headers @{Authorization="Bearer $token"} -ErrorAction Stop
        
        Write-Host "   ✅ Proyecto obtenido: $($project.data.name)" -ForegroundColor Green
        $testsPassed++
    } catch {
        Write-Host "   ❌ Get Project falló" -ForegroundColor Red
        $testsFailed++
    }
}

# 6. CREATE TASK
if ($projectId) {
    Write-Host "`n6️⃣ Testing Create Task..." -ForegroundColor Yellow
    try {
        $taskBody = @{
            title = "Test Task"
            description = "Testing task endpoint"
            status = "todo"
            priority = "high"
            project_id = $projectId
        } | ConvertTo-Json
        
        $newTask = Invoke-RestMethod -Uri "$baseUrl/tasks" `
            -Method Post `
            -Headers @{Authorization="Bearer $token"} `
            -Body $taskBody `
            -ContentType "application/json" -ErrorAction Stop
        
        $taskId = $newTask.data.id
        Write-Host "   ✅ Tarea creada (ID: $taskId)" -ForegroundColor Green
        $testsPassed++
    } catch {
        Write-Host "   ❌ Create Task falló: $($_.Exception.Message)" -ForegroundColor Red
        $testsFailed++
        $taskId = $null
    }
}

# 7. UPDATE TASK STATUS
if ($taskId) {
    Write-Host "`n7️⃣ Testing Update Task Status..." -ForegroundColor Yellow
    try {
        $statusBody = @{status = "in_progress"} | ConvertTo-Json
        
        $updatedTask = Invoke-RestMethod -Uri "$baseUrl/tasks/$taskId/status" `
            -Method Patch `
            -Headers @{Authorization="Bearer $token"} `
            -Body $statusBody `
            -ContentType "application/json" -ErrorAction Stop
        
        Write-Host "   ✅ Estado actualizado a: $($updatedTask.data.status)" -ForegroundColor Green
        $testsPassed++
    } catch {
        Write-Host "   ❌ Update Status falló" -ForegroundColor Red
        $testsFailed++
    }
}

# 8. LIST TASKS
if ($projectId) {
    Write-Host "`n8️⃣ Testing List Tasks..." -ForegroundColor Yellow
    try {
        $tasks = Invoke-RestMethod -Uri "$baseUrl/projects/$projectId/tasks" `
            -Method Get `
            -Headers @{Authorization="Bearer $token"} -ErrorAction Stop
        
        Write-Host "   ✅ Tareas encontradas: $($tasks.data.Count)" -ForegroundColor Green
        $testsPassed++
    } catch {
        Write-Host "   ❌ List Tasks falló" -ForegroundColor Red
        $testsFailed++
    }
}

# 9. UPDATE PROJECT
if ($projectId) {
    Write-Host "`n9️⃣ Testing Update Project..." -ForegroundColor Yellow
    try {
        $updateBody = @{
            name = "Updated Test Project"
            status = "completed"
        } | ConvertTo-Json
        
        $updated = Invoke-RestMethod -Uri "$baseUrl/projects/$projectId" `
            -Method Put `
            -Headers @{Authorization="Bearer $token"} `
            -Body $updateBody `
            -ContentType "application/json" -ErrorAction Stop
        
        Write-Host "   ✅ Proyecto actualizado" -ForegroundColor Green
        $testsPassed++
    } catch {
        Write-Host "   ❌ Update Project falló" -ForegroundColor Red
        $testsFailed++
    }
}

# 10. DELETE TASK
if ($taskId) {
    Write-Host "`n🔟 Testing Delete Task..." -ForegroundColor Yellow
    try {
        Invoke-RestMethod -Uri "$baseUrl/tasks/$taskId" `
            -Method Delete `
            -Headers @{Authorization="Bearer $token"} -ErrorAction Stop
        
        Write-Host "   ✅ Tarea eliminada" -ForegroundColor Green
        $testsPassed++
    } catch {
        Write-Host "   ❌ Delete Task falló" -ForegroundColor Red
        $testsFailed++
    }
}

# RESUMEN
Write-Host "`n================================" -ForegroundColor Cyan
Write-Host "📊 RESUMEN DE TESTS" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host "✅ Tests Pasados: $testsPassed" -ForegroundColor Green
Write-Host "❌ Tests Fallidos: $testsFailed" -ForegroundColor Red
Write-Host "📈 Total: $($testsPassed + $testsFailed)" -ForegroundColor White

if ($testsFailed -eq 0) {
    Write-Host "`n🎉 ¡Todos los tests pasaron!" -ForegroundColor Green
} else {
    Write-Host "`n⚠️ Algunos tests fallaron" -ForegroundColor Yellow
}

Write-Host "================================`n" -ForegroundColor Cyan