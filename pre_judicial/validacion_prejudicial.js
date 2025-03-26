function validarFormularioPreJudicial() {
    // Obtener valores del formulario
    let fecha_acto = new Date(); // Obtener la fecha actual
    let fecha_clave = new Date(document.forms["preJudicialForm"]["fecha_clave"].value);
    let descripcion = document.forms["preJudicialForm"]["descripcion"].value;
    let dias_de_mora = document.forms["preJudicialForm"]["dias_de_mora"].value;
    let dias_mora_PJ = document.forms["preJudicialForm"]["dias_mora_PJ"].value;
    let interes = document.forms["preJudicialForm"]["interes"].value;
    let saldo_int = document.forms["preJudicialForm"]["saldo_int"].value;
    let monto_amortizado = document.forms["preJudicialForm"]["monto_amortizado"].value;
    let saldo_fecha = document.forms["preJudicialForm"]["saldo_fecha"].value;
    let actor = document.forms["preJudicialForm"]["actor"].value;

    // Validaciones
    if (descripcion.trim().split(/\s+/).length > 100) {
        alert("La descripción debe tener un máximo de 100 palabras.");
        return false;
    }

    if (fecha_clave && fecha_clave < fecha_acto) {
        alert("La fecha clave no puede ser anterior a la fecha del actual.");
        return false;
    }

    if (dias_de_mora && isNaN(dias_de_mora)) {
        alert("Días de mora debe ser un número.");
        return false;
    }

    if (dias_mora_PJ && isNaN(dias_mora_PJ)) {
        alert("Días de mora PJ debe ser un número.");
        return false;
    }

    if (interes && isNaN(interes)) {
        alert("El interés debe ser un número.");
        return false;
    }

    if (saldo_int && isNaN(saldo_int)) {
        alert("El saldo más interés debe ser un número.");
        return false;
    }

    if (monto_amortizado && isNaN(monto_amortizado)) {
        alert("El monto amortizado debe ser un número.");
        return false;
    }

    if (saldo_fecha && isNaN(saldo_fecha)) {
        alert("El saldo a la fecha debe ser un número.");
        return false;
    }

    // Validación de actor
    if (actor && !["Gestor", "Cliente", "Supervisor", "Administrador"].includes(actor)) {
        alert("El actor debe ser uno de los siguientes: Gestor, Cliente, Supervisor, Administrador.");
        return false;
    }

    console.log("Formulario validado correctamente");
    return true;
}
