# Rotación de credenciales (plantilla)

Instrucciones para rotar secrets y credenciales expuestas. No ejecutes estos pasos sin coordinar con el equipo.

1) Revocar claves expuestas en los paneles de los proveedores (GitHub PAT, AWS, GCP, proveedores de SMS/email, etc.).
2) Generar nuevas claves/secretos.
3) Actualizar los secrets del repositorio usando GitHub UI o la CLI `gh`:

```bash
# Autenticar con `gh`:
gh auth login
# Actualizar secreto (ejemplo):
gh secret set MY_SERVICE_KEY --body "$(cat ./secrets/new_key.txt)" --repo ejaimedguez-sudo/BarandRest-backend
```

4) Actualizar cualquier infraestructura (CI/CD, servidores) que use los secretos.
5) Pedir a los colaboradores que reclonen el repositorio: `git clone --depth 1 https://github.com/ejaimedguez-sudo/BarandRest-backend.git`.

Notas:
- Esta plantilla no ejecuta la rotación por ti: requiere que tengas privilegios en los proveedores y/o el token `gh` configurado.
