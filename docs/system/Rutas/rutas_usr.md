# Rutas

## 1. Descripción General
El módulo de Rutas permite a los usuarios definir trayectos interactivos utilizando un mapa digital. Una ruta se compone por un punto de origen, un punto de destino y paradas intermedias opcionales que servirán para la logística de transporte.

## 2. Acceso al Módulo
### Ruta de navegación
```
Menú Principal > Nueva ruta
```

### Permisos necesarios
- **Acceso estándar**: Todos los usuarios autenticados pueden registrar y consultar rutas.

## 3. Registro de Nueva Ruta con Mapa
Para crear una ruta, siga estos pasos:
1. Ingrese el **Nombre de la Ruta** (Ejemplo: "Ruta Villahermosa-Cárdenas").
2. **Definir Origen**: Haga clic una sola vez en el mapa sobre el lugar donde inicia el trayecto. Aparecerá un marcador azul con el texto "Origen".
3. **Definir Destino**: Haga clic una segunda vez en el mapa sobre el lugar de llegada. Aparecerá un marcador azul con el texto "Destino".
4. **Agregar Paradas**: Si la ruta tiene paradas intermedias, continúe haciendo clic en el mapa. Cada clic creará una "Parada" numerada en orden.
5. Verfique que los campos "Origen", "Destino" y "Paradas" se han llenado automáticamente con las coordenadas.
6. Haga clic en el botón **Registrar Ruta**.

---

## 4. Visualización y Gestión de Rutas
En el listado de "Rutas Registradas":
- **Ver Mapa**: Muestra la ruta trazada en el mapa para su revisión visual.
- **Editar**: Permite modificar el nombre de la ruta o sus puntos geográficos.
- **Eliminar**: Elimina la ruta y todas sus paradas del sistema.

## 5. Recomendaciones de Uso
- **Precisión**: Utilice el zoom del mapa (botones + y - o rueda del mouse) para colocar los marcadores con mayor exactitud.
- **Orden de Clics**: Recuerde siempre el orden: 1° Origen, 2° Destino, 3°+ Paradas.
- **Nombres Claros**: Asigne nombres descriptivos a las rutas (Ej: "Abastecimiento CEDIS Regional") para facilitar su identificación en el módulo de asignaciones.

---

## 6. Errores Frecuentes
| Mensaje | Causa | Solución |
|---------|-------|----------|
| No aparece el mapa | Problemas de conexión a internet o bloqueo de scripts. | Verifique su conexión y refresque la página. |
| Coordenadas vacías | No se ha hecho clic en el mapa para definir origen o destino. | Haga los clics necesarios en el mapa antes de guardar. |
| La ruta no se guarda | Falta el nombre de la ruta o fallo de red. | Ingrese el nombre y presione Registrar de nuevo. |
