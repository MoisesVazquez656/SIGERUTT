# Vehículos

## 1. Descripción General
El módulo de Vehículos permite registrar, consultar y gestionar la flota de transporte terrestre disponible en el sistema SIGERUTT. Es fundamental mantener este catálogo actualizado para poder realizar asignaciones de rutas de manera correcta.

## 2. Acceso al Módulo
### Ruta de navegación
```
Menú Principal > Nuevo vehículo
```

### Permisos necesarios
- **Acceso estándar**: Todos los usuarios autenticados pueden registrar y consultar vehículos.

## 3. Registro de Nuevo Vehículo
Para dar de alta una unidad en la flota:
1. Ingrese la **Placa** del vehículo siguiendo el formato oficial (Ejemplo: "AB-123-CD"). Debe tener exactamente 9 caracteres.
2. Seleccione el **Tipo de Vehículo** de la lista desplegable.
3. Observe que el campo **Capacidad** se llena automáticamente según el tipo seleccionado (no es necesario escribirlo).
4. Seleccione el **Estado** del vehículo ("Activo" o "Inactivo").
5. Haga clic en el botón **Registrar Vehículo**.

---

## 4. Visualización y Control de Flota
En el listado de "Vehículos Registrados":
- **Placa**: Identificación oficial de la unidad.
- **Tipo**: Categoría del vehículo (Camión, Tráiler, etc.).
- **Capacidad**: Cantidad de carga que puede transportar.
- **Estado**: Indica si el vehículo está disponible ("Activo") para asignaciones.
- **Acciones**: Puede **Eliminar** o **Editar** la información del vehículo.

## 5. Recomendaciones de Uso
- **Unicidad**: El sistema no permite registrar dos vehículos con la misma placa. Asegúrese de tener la documentación oficial a la mano.
- **Baja de Unidades**: Si un vehículo entra en mantenimiento o ya no forma parte de la empresa, cámbielo al estado "Inactivo" en lugar de eliminarlo para mantener el historial de asignaciones.

---

## 6. Errores Frecuentes
| Mensaje | Causa | Solución |
|---------|-------|----------|
| "La placa ya está registrada" | El vehículo ya existe en el sistema. | Verifique la placa ingresada o localícela en el listado. |
| "Todos los campos son obligatorios" | Olvidó llenar algún dato o la placa no tiene la longitud correcta (9 caracteres). | Revise que todos los campos y el formato de la placa sean correctos. |
| El botón de registro no se activa | Falta información requerida. | Complete todos los campos marcados con el menú desplegable. |
