Crear repositorio en GitHub (seguro)

Instrucciones seguras para crear y pushear el repo desde tu máquina local.

1) REVOCAR token compartido en chat (IMPORTANTE)
- Ve a https://github.com/settings/tokens
- Revoke el token que compartiste (buscar 'Personal access tokens').

2) Generar un token nuevo (si quieres usar token)
- Genera un nuevo token (classic) o un Fine-grained token.
- Scopes recomendados: `repo` (solo si necesitas crear/pushear). Mejor: usar `gh auth login` localmente.

3) Uso recomendado (con token en variable de entorno)
PowerShell example:
  $env:GITHUB_TOKEN = 'GITHUB_PAT_REDACTED'
  .\create_github_repo_via_token.ps1 youruser/BarandRest-backend

Esto hará la llamada a la API para crear el repo y luego imprimirá los comandos `git` que debes ejecutar localmente para hacer el push.

4) Alternativa (usar GitHub CLI local)
  winget install --id GitHub.cli
  gh auth login
  ./scripts/create_github_repo.sh youruser/BarandRest-backend

5) Notas de seguridad
- Nunca pegues tokens en chats públicos o privados que no sean medios seguros.
- Usa el Git Credential Manager o `gh auth login` para evitar exponer tokens en la línea de comandos.
