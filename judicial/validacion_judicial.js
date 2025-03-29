function validarFormularioJudicial() {
    // Obtener valores del formulario
    let fecha = new Date(document.forms["judicialForm"]["fecha"].value);
    let fecha_clave = new Date(document.forms["judicialForm"]["fecha_clave"].value);
    let descripcion = document.forms["judicialForm"]["descripcion"].value;
    let fecha_actual = new Date();

    // Validaciones
    if (descripcion.trim().split(/\s+/).length > 100) {
        alert("La descripción debe tener un máximo de 100 palabras.");
        return false;
    }

    if (fecha_clave && fecha_clave < fecha_actual) {
        alert("La fecha clave no puede ser anterior a la fecha actual.");
        return false;
    }

    console.log("Formulario validado correctamente");
    return true;
}
