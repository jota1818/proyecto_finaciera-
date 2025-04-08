function validarFormularioPreJudicial() {
    // Obtener valores del formulario
    let fecha_acto = new Date(); // Obtener la fecha actual
    let fecha_clave = new Date(document.forms["preJudicialForm"]["fecha_clave"].value);
    let descripcion = document.forms["preJudicialForm"]["descripcion"].value;
    let monto_amortizado = document.forms["preJudicialForm"]["monto_amortizado"].value;
    let n_de_notif_voucher = document.forms["preJudicialForm"]["n_de_notif_voucher"].value;

    let regexNumeral = /^\d+$/;

    // Validaciones
    if (descripcion.trim().split(/\s+/).length > 100) {
        alert("La descripción debe tener un máximo de 100 palabras.");
        return false;
    }

    if (fecha_clave && fecha_clave < fecha_acto) {
        alert("La fecha clave no puede ser anterior a la fecha del actual.");
        return false;
    }

    if (monto_amortizado && isNaN(monto_amortizado)) {
        alert("El monto amortizado debe ser un número.");
        return false;
    }

    // Validar que el número de notificación/voucher solo contenga números del 0 al 9
    if (!regexNumeral.test(n_de_notif_voucher)) {
        alert("El Número de Notificación/Voucher solo debe contener números del 0 al 9.");
        return false;
    }
    

    console.log("Formulario validado correctamente");
    return true;

}

function validarFormularioJudicial() {
    // Obtener valores del formulario
    let fecha = new Date();
    let fecha_clave = new Date(document.forms["judicialForm"]["fecha_clave"].value);
    let descripcion = document.forms["judicialForm"]["descripcion"].value;

    // Validaciones
    if (descripcion.trim().split(/\s+/).length > 100) {
        alert("La descripción debe tener un máximo de 100 palabras.");
        return false;
    }

    if (fecha_clave && fecha_clave < fecha) {
        alert("La fecha clave no puede ser anterior a la fecha actual.");
        return false;
    }

    console.log("Formulario validado correctamente");
    return true;
}
