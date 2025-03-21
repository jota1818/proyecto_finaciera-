function verificarDNI(dni) {
    fetch('verificar_dni.php?dni=' + encodeURIComponent(dni))
        .then(response => response.text())
        .then(data => {
            if (data === "exists") {
                if (confirm("Ya existe un cliente con este DNI. ¿Desea intentar con otro DNI?")) {
                    document.forms["registroForm"]["dni"].value = "";
                    document.forms["registroForm"]["dni"].focus();
                }
            }
        });
}


function validarFormulario() {
    let nombre = document.forms["registroForm"]["nombre"].value;
    let apellidos = document.forms["registroForm"]["apellidos"].value;
    let dni = document.forms["registroForm"]["dni"].value;
    let telefono = document.forms["registroForm"]["telefono"].value;
    let fecha_nacimiento = document.forms["registroForm"]["fecha_nacimiento"].value;
    let domicilio1 = document.forms["registroForm"]["domicilio1"].value;
    let referencia1 = document.forms["registroForm"]["referencia1"].value;
    let domicilio2 = document.forms["registroForm"]["domicilio2"].value;
    let referencia2 = document.forms["registroForm"]["referencia2"].value;
    let ocupacion = document.forms["registroForm"]["ocupacion"].value;
    let clasificacion_riesgo = document.forms["registroForm"]["clasificacion_riesgo"].value;
    let fecha_desembolso = document.forms["registroForm"]["fecha_desembolso"].value;
    let fecha_vencimiento = document.forms["registroForm"]["fecha_vencimiento"].value;
    let monto = document.forms["registroForm"]["monto"].value;
    let saldo = document.forms["registroForm"]["saldo"].value;
    let nombre_garante = document.forms["registroForm"]["nombre_garante"].value;
    let apellidos_garante = document.forms["registroForm"]["apellidos_garante"].value;
    let dni_garante = document.forms["registroForm"]["dni_garante"].value;
    let telefono_garante = document.forms["registroForm"]["telefono_garante"].value;
    let fecha_nacimiento_garante = document.forms["registroForm"]["fecha_nacimiento_garante"].value;
    let domicilio1_garante = document.forms["registroForm"]["domicilio1_garante"].value;
    let referencia1_garante = document.forms["registroForm"]["referencia1_garante"].value;
    let domicilio2_garante = document.forms["registroForm"]["domicilio2_garante"].value;
    let referencia2_garante = document.forms["registroForm"]["referencia2_garante"].value;
    let ocupacion_garante = document.forms["registroForm"]["ocupacion_garante"].value;
    let clasificacion_riesgo_garante = document.forms["registroForm"]["clasificacion_riesgo_garante"].value;
    let fecha_clave = document.forms["registroForm"]["fecha_clave"].value;
    let accion_fecha_clave = document.forms["registroForm"]["accion_fecha_clave"].value;
    let analista = document.forms["registroForm"]["analista"].value;
    let gestor = document.forms["registroForm"]["gestor"].value;
    let supervisor = document.forms["registroForm"]["supervisor"].value;
    let administrador = document.forms["registroForm"]["administrador"].value;

    // Expresiones regulares
    let regexTexto = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/;
    let regexDNI = /^\d{8}$/;
    let regexTelefono = /^9\d{8}$/;
    let regexDireccion = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ][a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 .,"'() ]*$/;
    let regexFechaNacimiento = /^\d{4}-\d{2}-\d{2}$/;
    let regexNumeral = /^\d{1}$/;

    // Validaciones del cliente
    if (!regexTexto.test(nombre)) {
        alert("Nombre del cliente inválido. Debe contener solo letras y espacios."); return false;
    }
    if (!regexTexto.test(apellidos)) {
        alert("Apellidos del cliente inválido. Debe contener solo letras y espacios."); return false;
    }
    if (!regexDNI.test(dni)) {
        alert("DNI del cliente inválido"); return false;
    }
    if (!regexTelefono.test(telefono)) {
        alert("Teléfono del cliente inválido"); return false;
    }
    let fechaNa = new Date(fecha_nacimiento);
    let hoyy = new Date();
    let edadCliente = hoyy.getFullYear() - fechaNa.getFullYear();
    let n = hoyy.getMonth() - fechaNa.getMonth();
    if (n < 0 || (n === 0 && hoyy.getDate() < fechaNa.getDate())) {
        edadCliente--;
    }
    if (edadCliente < 18) {
        alert("El cliente es menor de edad"); return false;
    }
    if (!regexFechaNacimiento.test(fecha_nacimiento)) {
        alert("Fecha de Nacimiento inválida del cliente"); return false;
    }
    if (!regexDireccion.test(domicilio1)) {
        alert("Domicilio 1 del cliente inválido. No comenzar con numero o algún caracter"); return false;
    }
    if (!regexDireccion.test(referencia1)) {
        alert("Referencia 1 del cliente inválido. NNo comenzar con numero o algún caracter"); return false;
    }
    if (domicilio2 && !regexDireccion.test(domicilio2)) {
        alert("Domicilio 2 del cliente inválido. No comenzar con numero o algún caracter"); return false;
    }
    if (referencia2 && !regexDireccion.test(referencia2)) {
        alert("Referencia 2 del cliente inválido. No comenzar con numero o algún caracter"); return false;
    }
    if (!regexTexto.test(ocupacion)) {
        alert("Ocupación del cliente inválida. Debe contener solo letras y espacios."); return false;
    }
    if (isNaN(monto) || parseFloat(monto) <= 0) {
        alert("Monto inválido"); return false;
    }
    if (isNaN(saldo) || parseFloat(saldo) <= 0) {
        alert("Saldo inválido"); return false;
    }

    // Validaciones del garante
    if (!regexTexto.test(nombre_garante)) {
        alert("Nombre del garante inválido. Debe contener solo letras y espacios."); return false;
    }
    if (!regexTexto.test(apellidos_garante)) {
        alert("Apellidos del garante inválido. Debe contener solo letras y espacios."); return false;
    }
    if (!regexDNI.test(dni_garante)) {
        alert("DNI del garante inválido"); return false;
    }
    if (!regexTelefono.test(telefono_garante)) {
        alert("Teléfono del garante inválido"); return false;
    }
    let fechaNacimiento = new Date(fecha_nacimiento_garante);
    let hoy = new Date();
    let edadGarante = hoy.getFullYear() - fechaNacimiento.getFullYear();
    let m = hoy.getMonth() - fechaNacimiento.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
        edadGarante--;
    }
    if (edadGarante < 18) {
        alert("El garante es menor de edad"); return false;
    }
    if (!regexFechaNacimiento.test(fecha_nacimiento_garante)) {
        alert("Fecha de Nacimiento inválida del garante"); return false;
    }
    if (!regexDireccion.test(domicilio1_garante)) {
        alert("Domicilio 1 del garante inválido. No comenzar con numero o algún caracter"); return false;
    }
    if (!regexDireccion.test(referencia1_garante)) {
        alert("Referencia 1 del garante inválido. No comenzar con numero o algún caracter"); return false;
    }
    if (domicilio2_garante && !regexDireccion.test(domicilio2_garante)) {
        alert("Domicilio 2 del garante inválido. No comenzar con numero o algún caracter"); return false;
    }
    if (referencia2_garante && !regexDireccion.test(referencia2_garante)) {
        alert("Referencia 2 del garante inválido. No comenzar con numero o algún caracter"); return false;
    }
    if (!regexTexto.test(ocupacion_garante)) {
        alert("Ocupación del garante inválida. Debe contener solo letras y espacios."); return false;
    }
    if (!regexTexto.test(accion_fecha_clave)) {
        alert("Accion de fecha clave inválida. Debe contener solo letras y espacios."); return false;
    }

    // Valores para personal Asignado
    if (!regexNumeral.test(analista)) {
        alert("Máximo hasta 9"); return false;
    }
    if (!regexNumeral.test(gestor)) {
        alert("Máximo hasta 9"); return false;
    }
    if (!regexNumeral.test(supervisor)) {
        alert("Máximo hasta 9"); return false;
    }
    if (!regexNumeral.test(administrador)) {
        alert("Máximo hasta 9"); return false;
    }

    return true;
}

function cerrarRegistro() {
    document.getElementById('registroCliente').style.display = 'none';
    document.getElementById('registroContent').innerHTML = '';
}