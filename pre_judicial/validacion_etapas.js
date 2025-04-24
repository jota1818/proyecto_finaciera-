function validarFormularioPreJudicial() {
    // Obtener valores del formulario
    /* let fecha_acto = new Date(); */ // Obtener la fecha actual
    let fecha_acto = new Date(document.forms["preJudicialForm"]["fecha_acto"].value);
    let fecha_clave = new Date(document.forms["preJudicialForm"]["fecha_clave"].value);
    let descripcion = document.forms["preJudicialForm"]["descripcion"].value;
    let monto_amortizado = document.forms["preJudicialForm"]["monto_amortizado"].value;
    let n_de_notif_voucher = document.forms["preJudicialForm"]["n_de_notif_voucher"].value;
    let acto = document.forms["preJudicialForm"]["acto"].value;

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
    if (n_de_notif_voucher && !regexNumeral.test(n_de_notif_voucher)) {
        alert("El Número de Notificación/Voucher solo debe contener números del 0 al 9.");
        return false;
    }
    
     // Validaciones específicas según el acto seleccionado
     if (acto === "Amortizacion") {
        if (!monto_amortizado || monto_amortizado === "0") {
            alert("El campo Monto Amortizado es obligatorio para la opción Amortización.");
            return false;
        }
    } else if (acto === "Notificacion" || acto === "Fin de caso") {
        // El campo Monto Amortizado es opcional
    } else if (["Inicio caso prejudicial", "Cambio Gestor", "Postergacion", "Pasa a Judicial"].includes(acto)) {
        if (monto_amortizado && monto_amortizado !== "0") {
            alert("El campo Monto Amortizado no debe ser llenado para la opción seleccionada en 'Acto'.");
            return false;
        }
    }

    console.log("Formulario validado correctamente");
    return true;

}

function validarFormularioJudicial() {
    // Obtener valores del formulario
    // let fecha_judicial = new Date();
    let fecha_judicial = new Date(document.forms["judicialForm"]["fecha_judicial"].value);
    let fecha_clave_judicial = new Date(document.forms["judicialForm"]["fecha_clave_judicial"].value);
    let descripcion_judicial = document.forms["judicialForm"]["descripcion_judicial"].value;

    // Validaciones
    if (descripcion_judicial.trim().split(/\s+/).length > 100) {
        alert("La descripción debe tener un máximo de 100 palabras.");
        return false;
    }

    if (fecha_clave_judicial && fecha_clave_judicial < fecha_judicial) {
        alert("La fecha clave no puede ser anterior a la fecha actual.");
        return false;
    }

    console.log("Formulario validado correctamente");
    return true;
}
