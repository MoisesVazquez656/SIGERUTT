#!/bin/bash
# Crea la VM de Compute Engine para SIGERUTT (capa gratuita: e2-micro).
# Requiere: gcloud CLI autenticado y con un proyecto activo (gcloud config set project <ID>).
#
# Uso: ./create-vm.sh
set -e

PROJECT_ID="$(gcloud config get-value project)"
ZONE="${ZONE:-us-central1-a}"
VM_NAME="${VM_NAME:-sigerutt-vm}"

echo "Proyecto: $PROJECT_ID | Zona: $ZONE | VM: $VM_NAME"

# Regla de firewall: permite HTTP (80) y SSH (22) entrantes.
gcloud compute firewall-rules create sigerutt-allow-http \
  --project="$PROJECT_ID" \
  --allow=tcp:80,tcp:22 \
  --direction=INGRESS \
  --target-tags=sigerutt \
  --quiet || echo "La regla de firewall ya existe, se omite."

# VM e2-micro (elegible para el nivel gratuito "Always Free" en us-central1/us-west1/us-east1).
gcloud compute instances create "$VM_NAME" \
  --project="$PROJECT_ID" \
  --zone="$ZONE" \
  --machine-type=e2-micro \
  --image-family=debian-12 \
  --image-project=debian-cloud \
  --tags=sigerutt \
  --metadata-from-file=startup-script="$(dirname "$0")/startup-script.sh"

echo "VM creada. Espera 1-2 minutos a que termine de instalar Docker antes de conectarte."
echo "Conectate con: gcloud compute ssh $VM_NAME --zone=$ZONE"
