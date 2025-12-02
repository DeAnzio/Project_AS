# Status check script untuk Windows PowerShell

Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "       Project Akhir - Docker Containers Status" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# Check if Docker is running
try {
    docker info | Out-Null
} catch {
    Write-Host "❌ Docker is not running!" -ForegroundColor Red
    exit 1
}

Write-Host "📊 Container Status:" -ForegroundColor Green
Write-Host "─────────────────────────────────────────────────────────" -ForegroundColor Gray
docker-compose ps

Write-Host ""
Write-Host "🔗 Access Points:" -ForegroundColor Green
Write-Host "─────────────────────────────────────────────────────────" -ForegroundColor Gray
Write-Host "✓ Main App:     http://localhost:8080" -ForegroundColor Yellow
Write-Host "✓ phpMyAdmin:   http://localhost:8081" -ForegroundColor Yellow
Write-Host "✓ Portainer:    http://localhost:9000" -ForegroundColor Yellow

Write-Host ""
Write-Host "📈 Resource Usage:" -ForegroundColor Green
Write-Host "─────────────────────────────────────────────────────────" -ForegroundColor Gray
docker stats --no-stream --format "table {{.Container}}`t{{.CPUPerc}}`t{{.MemUsage}}"

Write-Host ""
Write-Host "🔍 Recent Logs (last 5 lines):" -ForegroundColor Green
Write-Host "─────────────────────────────────────────────────────────" -ForegroundColor Gray
docker-compose logs --tail=5

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
