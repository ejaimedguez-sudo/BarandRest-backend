Param()
Write-Host "== Start local server and queue worker (Windows) =="
Start-Process -FilePath php -ArgumentList "artisan","serve","--host=127.0.0.1","--port=8000" -WindowStyle Hidden
Start-Sleep -Seconds 1
Start-Process -FilePath php -ArgumentList "artisan","queue:work","--sleep=3","--tries=3" -WindowStyle Hidden
Write-Host "Server and worker started in background. Visit http://127.0.0.1:8000"
