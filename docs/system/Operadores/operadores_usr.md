# Operadores

## 1. Descripción General
El módulo de Operadores permite registrar, consultar y gestionar la lista de personal encargado de conducir las unidades de la flota. Es esencial mantener esta lista actualizada para asegurar que solo personal calificado y disponible sea asignado a las rutas.

## 2. Acceso al Módulo
### Ruta de navegación
```
Menú Principal > Nuevo operador
```

### Permisos necesarios
- **Acceso estándar**: Todos los usuarios autenticados pueden registrar y consultar operadores.

## 3. Registro de Nuevo Operador
Para dar de alta a un conductor en el sistema:
1. Ingrese el **Nombre completo** del operador.
2. Proporcione el **Número de Licencia** oficial. El sistema no permite licencias duplicadas.
3. Ingrese un **Teléfono** de contacto directo.
4. Seleccione la **Disponibilidad** inicial ("Disponible" o "No Disponible").
5. Haga clic en el botón **Registrar Operador**.

---

## 4. Visualización y Control de Conductores
En el listado de "Operadores Registrados":
- **Nombre**: Identificación del conductor.
- **Licencia**: Referencia legal del operador.
- **Teléfono**: Medio de contacto principal.
- **Disponibilidad**: Indica si el operador está libre ("Disponible") para realizar viajes. 
- **Acciones**: Puede **Eliminar** o **Editar** la información personal del operador.

## 5. Recomendaciones de Uso
- **Estado de Disponibilidad**: Mantenga este estado actualizado. Un operador marcado como "No Disponible" no debería ser considerado para asignaciones críticas.
- **Eliminación**: No elimine a un operador que haya tenido asignaciones pasadas importantes para no perder el rastro logístico de quién realizó cada viaje.

---

## 6. Errores Frecuentes
| Mensaje | Causa | Solución |
|---------|-------|----------|
| "La licencia ya está registrada" | Se intentó dar de alta un número de licencia que ya pertenece a otro operador. | Verifique el número ingresado o busque al operador en el listado. |
| "Todos los campos son obligatorios" | Uno o más datos (nombre, licencia, teléfono o disponibilidad) se dejaron en blanco. | Complete toda la información requerida. |
| Problema al guardar los datos | Error de red o fallo interno del servidor. | Intente guardar de nuevo; si persiste, contacte a soporte técnico. |
