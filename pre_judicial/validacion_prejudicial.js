console.log("Script de validación cargado");

function validarFormularioPreJudicial() {
    console.log("Validando formulario...");

    // Obtener valores del formulario
    let fecha_acto = new Date(); // Obtener la fecha actual
    let acto = document.forms["preJudicialForm"]["acto"].value;
    let n_de_notif_voucher = document.forms["preJudicialForm"]["n_de_notif_voucher"].value;
    let descripcion = document.forms["preJudicialForm"]["descripcion"].value;
    let notif_compromiso_pago_Evidencia = document.forms["preJudicialForm"]["notif_compromiso_pago_Evidencia"].value;
    let fecha_clave = new Date(document.forms["preJudicialForm"]["fecha_clave"].value);
    let accion_fecha_clave = document.forms["preJudicialForm"]["accion_fecha_clave"].value;
    let actor = document.forms["preJudicialForm"]["actor"].value;
    let evidencia1_localizacion = document.forms["preJudicialForm"]["evidencia1_localizacion"].value;
    let evidencia2_foto_fecha = document.forms["preJudicialForm"]["evidencia2_foto_fecha"].value;
    let dias_desde_fecha_clave = document.forms["preJudicialForm"]["dias_desde_fecha_clave"].value;
    let objetivo_logrado = document.forms["preJudicialForm"]["objetivo_logrado"].value;
    let dias_de_mora = document.forms["preJudicialForm"]["dias_de_mora"].value;
    let dias_mora_PJ = document.forms["preJudicialForm"]["dias_mora_PJ"].value;
    let interes = document.forms["preJudicialForm"]["interes"].value;
    let saldo_int = document.forms["preJudicialForm"]["saldo_int"].value;
    let monto_amortizado = document.forms["preJudicialForm"]["monto_amortizado"].value;
    let saldo_fecha = document.forms["preJudicialForm"]["saldo_fecha"].value;

    // Validaciones
    if (descripcion.trim().split(/\s+/).length > 100) {
        alert("La descripción debe tener un máximo de 100 palabras.");
        return false;
    }

    if (!notif_compromiso_pago_Evidencia || ![".docx", ".pdf", ".jpg", ".png"].some(ext => notif_compromiso_pago_Evidencia.endsWith(ext))) {
        alert("Debe adjuntar un archivo de notificación/compromiso de pago en formato docx, pdf, jpg o png.");
        return false;
    }

    if (fecha_clave && fecha_clave < fecha_acto) {
        alert("La fecha clave no puede ser anterior a la fecha del acto.");
        return false;
    }

    if (dias_desde_fecha_clave && isNaN(dias_desde_fecha_clave)) {
        alert("Días desde fecha clave debe ser un número.");
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

    // Validación de evidencias
    if (!evidencia1_localizacion || ![".jpg", ".png"].some(ext => evidencia1_localizacion.endsWith(ext))) {
        alert("Debe adjuntar una imagen para la evidencia 1 en formato jpg o png.");
        return false;
    }

    if (!evidencia2_foto_fecha || ![".jpg", ".png"].some(ext => evidencia2_foto_fecha.endsWith(ext))) {
        alert("Debe adjuntar una imagen para la evidencia 2 en formato jpg o png.");
        return false;
    }

    // Validación de días desde fecha clave
    if (objetivo_logrado === "NO" && !dias_desde_fecha_clave) {
        alert("Si el objetivo no se logró, debe ingresar los días desde la fecha clave.");
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
