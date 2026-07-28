# Despliegue en Google Cloud (Compute Engine + Docker Compose)

Se eligió **Google Cloud** porque es la opción mas simple para este stack:
una sola VM (capa gratuita `e2-micro`) corriendo `docker compose` con los 3
contenedores (MySQL + API + SIGERUTT), igual que en local. No hace falta
Cloud SQL, balanceadores ni IAM complejo.

## 1. Crear la cuenta y el proyecto (lo haces tú)

1. Entra a https://console.cloud.google.com con tu cuenta de Google y activa
   el nivel gratuito (Google suele dar $300 USD de crédito por 90 días; la
   VM `e2-micro` en `us-central1`, `us-west1` o `us-east1` normalmente cae
   dentro del nivel "Always Free" incluso después).
2. Crea un proyecto nuevo, por ejemplo `sigerutt-parcial`.
3. Activa la facturación del proyecto (Google la pide aunque uses solo
   capa gratuita).
4. En el buscador de la consola, activa la **Compute Engine API**.

## 2. Crear credenciales para que yo pueda desplegar (lo haces tú)

1. Ve a **IAM y administración → Cuentas de servicio → Crear cuenta de
   servicio**.
2. Nombre: `sigerutt-deploy`.
3. Asigna los roles:
   - `Compute Admin` (crear/administrar la VM)
   - `Service Account User`
4. Entra a la cuenta de servicio creada → pestaña **Claves** → **Agregar
   clave → Crear clave nueva → JSON**. Se descarga un archivo `.json`.
5. Copia y pégame el contenido de ese archivo JSON en el chat (o súbelo).
   Con eso puedo autenticar `gcloud` en esta sesión y crear la VM por ti.

> Nota de seguridad: esa clave da acceso a tu proyecto de GCP. Es tu
> proyecto de práctica escolar así que el riesgo es bajo, pero cuando ya
> hayas entregado el parcial puedes revocarla/eliminarla desde la misma
> pantalla de "Claves".

## 3. Lo que hago yo una vez tenga la clave

1. `gcloud auth activate-service-account --key-file=clave.json`
2. `gcloud config set project <tu-project-id>`
3. Ejecuto `deploy/gcp/create-vm.sh`, que:
   - Abre el firewall para HTTP (80) y SSH (22).
   - Crea una VM `e2-micro` con Docker instalado automáticamente
     (`startup-script.sh`).
4. Copio el código de `API/` y `SIGERUTT/` a la VM (`gcloud compute scp`).
5. Creo `api/.env` en la VM con un `SECRET_KEY` real.
6. Levanto todo con `docker compose up -d --build`.
7. Corro el script de arranque del primer administrador:
   ```
   docker compose exec api sh -c "ADMIN_NOMBRE='Tu Nombre' ADMIN_EMAIL='admin@sigerutt.com' ADMIN_PASSWORD='clave-segura' python seed_admin.py"
   ```
8. Te paso la IP pública de la VM para que entres a
   `http://<ip>/SIGERUTT/login.php`.

## 4. Costos

- `e2-micro` + 30GB de disco estándar están dentro del nivel "Always Free"
  en las regiones mencionadas. Fuera de esas regiones, o si excedes el
  límite, se cobra a la tarjeta asociada al proyecto — revisa el
  [panel de facturación](https://console.cloud.google.com/billing) después
  de entregar el parcial.
