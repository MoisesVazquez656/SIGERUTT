

// =============================================
// SIGERUTT - Scripts con AJAX (fetch API)
// =============================================

/**
 * Función utilitaria para mostrar mensajes dinámicos en la página.
 * Busca o crea un contenedor #mensaje-ajax para mostrar el resultado.
 * @param {string} tipo - 'exito', 'error' o 'alerta'
 * @param {string} texto - Texto del mensaje
 * @param {HTMLElement|null} referencia - Elemento de referencia para insertar el mensaje antes de él
 */
function mostrarMensaje(tipo, texto, referencia) {
    let previos = document.querySelectorAll('.mensaje-ajax-dinamico');
    previos.forEach(m => m.remove());

    let div = document.createElement('div');
    div.className = 'mensaje ' + tipo + ' mensaje-ajax-dinamico';
    div.textContent = texto;

    if (referencia) {
        referencia.parentNode.insertBefore(div, referencia);
    } else {
        let h2 = document.querySelector('h2');
        if (h2) {
            h2.parentNode.insertBefore(div, h2.nextSibling);
        } else {
            document.body.prepend(div);
        }
    }

    setTimeout(() => {
        div.style.transition = 'opacity 0.5s';
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 500);
    }, 5000);
}

/**
 * Envía un formulario vía AJAX usando fetch().
 * @param {HTMLFormElement} form - El formulario a enviar
 * @param {Function|null} onExito - Callback opcional en caso de éxito
 */
function enviarFormularioAjax(form, onExito) {
    let formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            let tipo = (data.status === 'ok') ? 'exito' : 'error';
            mostrarMensaje(tipo, data.mensaje, form);

            if (data.status === 'ok') {
                if (onExito) {
                    onExito(data);
                } else {
                    form.reset();
                }
            }
        })
        .catch(error => {
            console.error('Error en la petición AJAX:', error);
            mostrarMensaje('error', 'Error de conexión. Intenta nuevamente.', form);
        });
}

/**
 * Elimina un registro vía AJAX usando fetch().
 * @param {string} url - URL del endpoint de eliminación
 * @param {HTMLElement} fila - Elemento <tr> a eliminar del DOM
 */
function eliminarRegistroAjax(url, fila) {
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                fila.style.transition = 'opacity 0.4s, transform 0.4s';
                fila.style.opacity = '0';
                fila.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    fila.remove();
                    let tabla = document.querySelector('table');
                    if (tabla && tabla.querySelectorAll('tr').length <= 1) {
                        tabla.remove();
                        mostrarMensaje('alerta', 'No hay registros.', null);
                    }
                }, 400);
                mostrarMensaje('exito', data.mensaje, null);
            } else {
                mostrarMensaje('error', data.mensaje, null);
            }
        })
        .catch(error => {
            console.error('Error en la petición AJAX:', error);
            mostrarMensaje('error', 'Error de conexión al eliminar.', null);
        });
}

// =============================================
// INTERCEPTAR FORMULARIOS AL CARGAR LA PÁGINA
// =============================================

document.addEventListener('DOMContentLoaded', function () {
    console.log('scripts.js cargado correctamente (con AJAX)');

    // -----------------------------------------------
    // LOGIN - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formLogin')) {
        document.getElementById('formLogin').addEventListener('submit', function (e) {
            e.preventDefault();

            let correo = document.getElementById('correo').value.trim();
            let contraseña = document.getElementById('contraseña').value.trim();

            if (correo === '' || contraseña === '') {
                mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                return;
            }

            enviarFormularioAjax(this, function (data) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            });
        });
    }

    // -----------------------------------------------
    // REGISTRAR USUARIO - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formUsuario')) {
        document.getElementById('formUsuario').addEventListener('submit', function (e) {
            e.preventDefault();

            let nombre = document.getElementsByName('nombre')[0].value.trim();
            let correo = document.getElementsByName('correo')[0]?.value.trim() || '';
            let contraseña = document.getElementsByName('contraseña')[0]?.value.trim() || '';
            let rol = document.getElementsByName('rol')[0]?.value || '';

            if (contraseña !== undefined && document.getElementsByName('contraseña')[0]) {
                if (nombre === '' || correo === '' || contraseña === '' || rol === '') {
                    mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                    return;
                }
                if (contraseña.length < 6) {
                    mostrarMensaje('alerta', 'La contraseña debe tener al menos 6 caracteres.', this);
                    return;
                }
            } else {
                if (nombre === '' || correo === '' || rol === '') {
                    mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                    return;
                }
            }

            enviarFormularioAjax(this, null);
        });
    }

    // -----------------------------------------------
    // REGISTRAR VEHÍCULO - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formVehiculo')) {

        let placaInput = document.getElementById('placa');
        if (placaInput) {
            placaInput.addEventListener('input', function () {
                let valor = this.value.toUpperCase();
                this.value = valor.substring(0, 9);
            });
        }

        let tipoSelect = document.getElementById('tipo');
        if (tipoSelect) {
            tipoSelect.addEventListener('change', function () {
                let tipo = this.value;
                let capacidadInput = document.getElementById('capacidad');

                let capacidades = {
                    "Camión de carga ligera": "1,500 kg",
                    "Camión de carga pesada": "10 toneladas",
                    "Tráiler": "20 toneladas",
                    "Torton": "15 toneladas",
                    "Rabón": "8 toneladas",
                    "Caja seca": "10 toneladas",
                    "Refrigerado": "12 toneladas",
                    "Plataforma": "25 toneladas",
                    "Camioneta tipo van": "800 kg",
                    "Motocicleta de reparto": "150 kg"
                };

                capacidadInput.value = capacidades[tipo] || '';
            });
        }

        document.getElementById('formVehiculo').addEventListener('submit', function (e) {
            e.preventDefault();

            let placa = document.getElementsByName('placa')[0].value.trim().toUpperCase();
            let tipo = document.getElementsByName('tipo')[0].value.trim();
            let capacidad = document.getElementsByName('capacidad')[0].value.trim();

            if (placa === '' || tipo === '' || capacidad === '') {
                mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                return;
            }

            if (placa.length !== 9) {
                mostrarMensaje('alerta', 'La placa debe tener exactamente 9 caracteres.', this);
                return;
            }

            enviarFormularioAjax(this, null);
        });
    }

    // -----------------------------------------------
    // REGISTRAR OPERADOR - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formOperador')) {
        document.getElementById('formOperador').addEventListener('submit', function (e) {
            e.preventDefault();

            let nombre = document.getElementsByName('nombre')[0].value.trim();
            let licencia = document.getElementsByName('licencia')[0].value.trim();
            let telefono = document.getElementsByName('telefono')[0].value.trim();

            if (nombre === '' || licencia === '' || telefono === '') {
                mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                return;
            }

            enviarFormularioAjax(this, null);
        });
    }

    // -----------------------------------------------
    // ASIGNAR RUTA - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formAsignacion')) {
        document.getElementById('formAsignacion').addEventListener('submit', function (e) {
            e.preventDefault();

            let id_ruta = document.getElementsByName('id_ruta')[0].value;
            let id_vehiculo = document.getElementsByName('id_vehiculo')[0].value;
            let id_operador = document.getElementsByName('id_operador')[0].value;
            let fecha_asignacion = document.getElementsByName('fecha_asignacion')[0]?.value || '';

            if (id_ruta === '' || id_vehiculo === '' || id_operador === '' || fecha_asignacion === '') {
                mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                return;
            }

            enviarFormularioAjax(this, null);
        });
    }

    // -----------------------------------------------
    // REGISTRAR RUTA (formulario del mapa) - Envío AJAX
    // -----------------------------------------------
    let formRutaMapa = document.querySelector('form[action="php/registrar_ruta.php"]');
    if (formRutaMapa) {
        formRutaMapa.addEventListener('submit', function (e) {
            e.preventDefault();

            let nombre_ruta = document.getElementsByName('nombre_ruta')[0].value.trim();
            let origen = document.getElementById('origen').value.trim();
            let destino = document.getElementById('destino').value.trim();

            if (nombre_ruta === '' || origen === '' || destino === '') {
                mostrarMensaje('alerta', 'Debes asignar un nombre y trazar al menos una ruta con origen y destino en el mapa.', this);
                return;
            }

            enviarFormularioAjax(this, function (data) {
                mostrarMensaje('exito', data.mensaje, formRutaMapa);
                formRutaMapa.reset();
                document.getElementById('origen').value = '';
                document.getElementById('destino').value = '';
                document.getElementById('paradas').value = '';
                document.getElementById('distancia_total').value = '';
                document.getElementById('distancia_mostrada').value = '';
            });
        });
    }

    // -----------------------------------------------
    // REGISTRAR RUTA DINÁMICA - Envío AJAX
    // -----------------------------------------------
    let formRutaDinamica = document.querySelector('form[action="php/registrar_ruta_dinamica.php"]');
    if (formRutaDinamica) {
        formRutaDinamica.addEventListener('submit', function (e) {
            e.preventDefault();

            let nombre_ruta = document.getElementsByName('nombre_ruta')[0].value.trim();
            let origen = document.getElementById('origen').value.trim();
            let destino = document.getElementById('destino').value.trim();

            if (nombre_ruta === '' || origen === '' || destino === '') {
                mostrarMensaje('alerta', 'Debes asignar un nombre y marcar al menos origen y destino en el mapa.', this);
                return;
            }

            enviarFormularioAjax(this, function (data) {
                mostrarMensaje('exito', data.mensaje, formRutaDinamica);
            });
        });
    }

    // -----------------------------------------------
    // EDITAR RUTA DINÁMICA - Envío AJAX
    // -----------------------------------------------
    let formEditarRuta = document.querySelector('form[action="php/actualizar_ruta_dinamica.php"]');
    if (formEditarRuta) {
        formEditarRuta.addEventListener('submit', function (e) {
            e.preventDefault();

            let origen = document.getElementById('origen').value.trim();
            let destino = document.getElementById('destino').value.trim();

            if (origen === '' || destino === '') {
                mostrarMensaje('alerta', 'Debes trazar la ruta con al menos origen y destino.', this);
                return;
            }

            enviarFormularioAjax(this, function (data) {
                mostrarMensaje('exito', data.mensaje, formEditarRuta);
            });
        });
    }

    // -----------------------------------------------
    // EDITAR PERFIL - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formEditarPerfil')) {
        document.getElementById('formEditarPerfil').addEventListener('submit', function (e) {
            e.preventDefault();

            let nombre = document.getElementsByName('nombre')[0].value.trim();
            let correo = document.getElementsByName('correo')[0].value.trim();

            if (nombre === '' || correo === '') {
                mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                return;
            }

            enviarFormularioAjax(this, function (data) {
                mostrarMensaje('exito', data.mensaje, document.getElementById('formEditarPerfil'));
            });
        });
    }

    // -----------------------------------------------
    // CAMBIAR CONTRASEÑA - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formCambiarContrasena')) {
        document.getElementById('formCambiarContrasena').addEventListener('submit', function (e) {
            e.preventDefault();

            let actual = document.getElementsByName('contrasena_actual')[0].value;
            let nueva = document.getElementsByName('contrasena_nueva')[0].value;
            let confirmar = document.getElementsByName('contrasena_confirmar')[0].value;

            if (actual === '' || nueva === '' || confirmar === '') {
                mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                return;
            }

            if (nueva.length < 6) {
                mostrarMensaje('alerta', 'La nueva contraseña debe tener al menos 6 caracteres.', this);
                return;
            }

            if (nueva !== confirmar) {
                mostrarMensaje('alerta', 'Las contraseñas nuevas no coinciden.', this);
                return;
            }

            enviarFormularioAjax(this, function (data) {
                mostrarMensaje('exito', data.mensaje, document.getElementById('formCambiarContrasena'));
                document.getElementById('formCambiarContrasena').reset();
            });
        });
    }

    // -----------------------------------------------
    // RECUPERAR CONTRASEÑA - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formRecuperar')) {
        document.getElementById('formRecuperar').addEventListener('submit', function (e) {
            e.preventDefault();

            let correo = document.getElementById('correo').value.trim();
            if (correo === '') {
                mostrarMensaje('alerta', 'Debes ingresar tu correo electrónico.', this);
                return;
            }

            enviarFormularioAjax(this, function (data) {
                mostrarMensaje('exito', data.mensaje, document.getElementById('formRecuperar'));
            });
        });
    }

    // -----------------------------------------------
    // RESTABLECER CONTRASEÑA - Envío AJAX
    // -----------------------------------------------
    if (document.getElementById('formRestablecer')) {
        document.getElementById('formRestablecer').addEventListener('submit', function (e) {
            e.preventDefault();

            let nueva = document.getElementsByName('contrasena_nueva')[0].value;
            let confirmar = document.getElementsByName('contrasena_confirmar')[0].value;

            if (nueva === '' || confirmar === '') {
                mostrarMensaje('alerta', 'Todos los campos son obligatorios.', this);
                return;
            }

            if (nueva.length < 6) {
                mostrarMensaje('alerta', 'La contraseña debe tener al menos 6 caracteres.', this);
                return;
            }

            if (nueva !== confirmar) {
                mostrarMensaje('alerta', 'Las contraseñas no coinciden.', this);
                return;
            }

            enviarFormularioAjax(this, function (data) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            });
        });
    }

    // -----------------------------------------------
    // FORMULARIOS DE EDICIÓN GENÉRICOS
    // -----------------------------------------------
    let formsEditar = document.querySelectorAll('form[action*="actualizar_usuario"], form[action*="actualizar_vehiculo"], form[action*="actualizar_operador"], form[action*="actualizar_asignacion"]');
    formsEditar.forEach(function (formEditar) {
        if (formEditar.id === 'formVehiculo' || formEditar.id === 'formUsuario' || formEditar.id === 'formOperador' || formEditar.id === 'formAsignacion' || formEditar.id === 'formEditarPerfil') {
            return;
        }
        formEditar.addEventListener('submit', function (e) {
            e.preventDefault();
            enviarFormularioAjax(this, null);
        });
    });

    // -----------------------------------------------
    // ELIMINAR - Usar fetch() en lugar de window.location
    // -----------------------------------------------
    let botonesEliminar = document.querySelectorAll('.accion-eliminar');

    botonesEliminar.forEach(boton => {
        boton.addEventListener('click', function (e) {
            e.preventDefault();

            let confirmacion = confirm('¿Estás seguro que deseas eliminar este registro? Al hacerlo, también se eliminarán automáticamente todas las asignaciones relacionadas si las hay.');

            if (confirmacion) {
                let url = this.getAttribute('data-url');
                let fila = this.closest('tr');
                eliminarRegistroAjax(url, fila);
            }
        });
    });

    // -----------------------------------------------
    // BUSCADOR DEL HEADER
    // -----------------------------------------------

});

function validarBusquedaHeader() {
    var input = document.getElementById('q');
    if (!input) return true;
    var q = (input.value || '').trim();
    if (q.length < 2) {
        alert('Escribe al menos 2 caracteres para buscar.');
        return false;
    }
    return true;
}
