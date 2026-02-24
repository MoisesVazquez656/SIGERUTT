

// VALIDACIÓN LOGIN
if (document.getElementById('formLogin')) {
    document.getElementById('formLogin').addEventListener('submit', function (e) {
        let correo = document.getElementById('correo').value.trim();
        let contraseña = document.getElementById('contraseña').value.trim();

        if (correo === '' || contraseña === '') {
            alert('Todos los campos son obligatorios.');
            e.preventDefault();
        }
    });
}

// VALIDACIÓN REGISTRO DE USUARIOS
if (document.getElementById('formUsuario')) {
    document.getElementById('formUsuario').addEventListener('submit', function (e) {
        let nombre = document.getElementsByName('nombre')[0].value.trim();
        let correo = document.getElementsByName('correo')[0].value.trim();
        let contraseña = document.getElementsByName('contraseña')[0].value.trim();
        let rol = document.getElementsByName('rol')[0].value;

        if (nombre === '' || correo === '' || contraseña === '' || rol === '') {
            alert('Todos los campos son obligatorios.');
            e.preventDefault();
            return;
        }

        if (contraseña.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres.');
            e.preventDefault();
        }
    });
}

// VALIDACIÓN REGISTRO DE RUTAS
if (document.getElementById('formRuta')) {
    document.getElementById('formRuta').addEventListener('submit', function (e) {
        let nombre_ruta = document.getElementsByName('nombre_ruta')[0].value.trim();
        let origen = document.getElementsByName('origen')[0].value.trim();
        let destino = document.getElementsByName('destino')[0].value.trim();
        let distancia = document.getElementsByName('distancia')[0].value.trim();

        if (nombre_ruta === '' || origen === '' || destino === '' || distancia === '') {
            alert('Todos los campos son obligatorios.');
            e.preventDefault();
        }
    });
}

// VALIDACIÓN REGISTRO DE VEHÍCULOS
if (document.getElementById('formVehiculo')) {

    // Convertir a mayúsculas y limitar a 9 caracteres
    document.getElementById('placa').addEventListener('input', function () {
        let valor = this.value.toUpperCase();
        this.value = valor.substring(0, 9);
    });

    // Asignar capacidad automáticamente según el tipo de vehículo
    document.getElementById('tipo').addEventListener('change', function () {
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

    // Validación al enviar
    document.getElementById('formVehiculo').addEventListener('submit', function (e) {
        let placa = document.getElementsByName('placa')[0].value.trim().toUpperCase();
        let tipo = document.getElementsByName('tipo')[0].value.trim();
        let capacidad = document.getElementsByName('capacidad')[0].value.trim();

        if (placa === '' || tipo === '' || capacidad === '') {
            alert('Todos los campos son obligatorios.');
            e.preventDefault();
            return;
        }

        if (placa.length !== 9) {
            alert('La placa debe tener exactamente 9 caracteres.');
            e.preventDefault();
            return;
        }
    });
}


// VALIDACIÓN REGISTRO DE OPERADORES
if (document.getElementById('formOperador')) {
    document.getElementById('formOperador').addEventListener('submit', function (e) {
        let nombre = document.getElementsByName('nombre')[0].value.trim();
        let licencia = document.getElementsByName('licencia')[0].value.trim();
        let telefono = document.getElementsByName('telefono')[0].value.trim();

        if (nombre === '' || licencia === '' || telefono === '') {
            alert('Todos los campos son obligatorios.');
            e.preventDefault();
        }
    });
}

// VALIDACIÓN ASIGNACIÓN DE RUTAS
if (document.getElementById('formAsignacion')) {
    document.getElementById('formAsignacion').addEventListener('submit', function (e) {
        let id_ruta = document.getElementsByName('id_ruta')[0].value;
        let id_vehiculo = document.getElementsByName('id_vehiculo')[0].value;
        let id_operador = document.getElementsByName('id_operador')[0].value;
        let fecha_asignacion = document.getElementsByName('fecha_asignacion')[0].value;

        if (id_ruta === '' || id_vehiculo === '' || id_operador === '' || fecha_asignacion === '') {
            alert('Todos los campos son obligatorios.');
            e.preventDefault();
        }
    });
}

// Buscador del header (simple)
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

// Confirmación personalizada para eliminar

document.addEventListener('DOMContentLoaded', function () {
    console.log('scripts.js cargado correctamente');

    let botonesEliminar = document.querySelectorAll('.accion-eliminar');

    botonesEliminar.forEach(boton => {
        boton.addEventListener('click', function (e) {
            let confirmacion = confirm('¿Estás seguro que deseas eliminar este registro? ⚠️ Al hacerlo, también se eliminarán automáticamente todas las asignaciones relacionadas si las hay.');

            if (confirmacion) {
                // Redirige solo si el usuario confirma
                window.location.href = this.getAttribute('data-url');
            }
            // Si cancela, no hace nada (no es necesario el preventDefault porque no hay redirección por href)
        });
    });
});

