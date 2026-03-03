# Asignaciones

## 1. Descripción General
El módulo de Asignaciones es la herramienta central donde se coordina la logística del transporte terrestres terrestres. Permite vincular una **Ruta** ya definida con un **Vehículo** disponible y un **Operador** libre para una fecha específica de salida.

## 2. Acceso al Módulo
### Ruta de navegación
```
Menú Principal > Asignar ruta
```

### Permisos necesarios
- **Acceso estándar**: Todos los usuarios autenticados pueden realizar nuevas asignaciones y consultar el historial.

## 3. Realizar una Nueva Asignación
Para programar un trayecto:
1. Seleccione la **Ruta** de la lista desplegable. Si la ruta no aparece, verifique que ha sido registrada previamente en el módulo de Rutas.
2. Seleccione el **Vehículo** que realizará el viaje. El sistema solo mostrará unidades en estado "Activo".
3. Seleccione el **Operador** asignado. El sistema solo mostrará conductores marcados como "Disponibles".
4. Indique la **Fecha de Asignación** utilizando el calendario.
5. Haga clic en el botón **Asignar Ruta**.

---

## 4. Visualización y Seguimiento
En el listado de "Asignaciones Registradas":
- **Ruta**: Nombre del trayecto asignado.
- **Vehículo**: Placa de la unidad asignada.
- **Operador**: Nombre del conductor responsable.
- **Fecha**: Día programado para la operación.
- **Ver Mapa**: Permite visualizar rápidamente el trayecto que el operador deberá seguir.
- **Acciones**: Puede **Eliminar** o **Editar** la asignación si hay cambios de última hora.

## 5. Recomendaciones de Uso
- **Disponibilidad de Recursos**: Si no ve un vehículo o operador en la lista de selección, asegúrese de que su estado en los catálogos maestros sea "Activo" y "Disponible", respectivamente.
- **Verificación Visual**: Se recomienda usar la opción "Ver Mapa" desde el listado para confirmar que la ruta seleccionada es la correcta antes de que el despacho se concrete.

---

## 6. Errores Frecuentes
| Mensaje | Causa | Solución |
|---------|-------|----------|
| No aparece el vehículo/operador | El recurso está en estado Inactivo o No Disponible. | Cambie el estado del recurso en su catálogo respectivo. |
| "Error al asignar la ruta" | Problema técnico al guardar la información. | Intente de nuevo; si persiste, consulte al administrador. |
| "Todos los campos son obligatorios" | Falta seleccionar una ruta, vehículo, operador o fecha. | Complete la selección de todos los campos del formulario. |
