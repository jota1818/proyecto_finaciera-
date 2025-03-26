function validarFormularioPreJudicial() {
    // Obtener valores del formulario
    let fecha_acto = new Date(); // Obtener la fecha actual
    let fecha_clave = new Date(document.forms["preJudicialForm"]["fecha_clave"].value);
    let descripcion = document.forms["preJudicialForm"]["descripcion"].value;
    let monto_amortizado = document.forms["preJudicialForm"]["monto_amortizado"].value;

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

    console.log("Formulario validado correctamente");
    return true;
}
