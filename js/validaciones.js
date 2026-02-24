function validarBusqueda() {
  const q = document.getElementById("q");
  if (!q) return true;
  if (q.value.trim().length < 2) {
    alert("Escribe al menos 2 caracteres.");
    return false;
  }
  return true;
}
