SECURITY NOTES
===============

Se ha eliminado `calc_fingerprint.php` porque contenía una clave codificada incluida directamente en el repositorio.

Acciones realizadas:
- Eliminado el archivo con datos sensibles en `backend/calc_fingerprint.php`.
- Añadida esta nota para indicar la razón y las acciones a seguir.

Recomendaciones:
- No almacenar claves privadas ni pares de claves en el repositorio.
- Mover cualquier clave pública/privada a un gestor de secretos (GitHub Secrets, Azure Key Vault, HashiCorp Vault, etc.).
- Para operaciones de diagnóstico similares, crear scripts que lean la clave desde variables de entorno o archivos fuera del repositorio.

Acción aplicada:
- Se añadió `backend/scripts/calc_fingerprint_from_env.php` — script seguro que calcula la huella SHA256 de una clave pública leyendo desde la variable de entorno `PUBLIC_SSH_KEY` o desde un archivo externo pasado como argumento.
- Se actualizaron `.gitignore` (raíz y `backend/.gitignore`) para ignorar patrones comunes de claves y certificados.

Uso del script seguro:
1) Exportar la clave pública en la variable `PUBLIC_SSH_KEY` y ejecutar:

   php backend/scripts/calc_fingerprint_from_env.php

2) O pasar la ruta al archivo de clave pública:

   php backend/scripts/calc_fingerprint_from_env.php /path/to/id_ed25519.pub

Si quieres, puedo abrir un PR con estos cambios en la rama `chore/sanitize-scripts`.
