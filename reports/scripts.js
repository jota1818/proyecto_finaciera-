function abrirModal(filtro) {
    document.getElementById('filtro').value = filtro;
    document.getElementById('mesModal').value = document.getElementById('mes').value;
    document.getElementById('anioModal').value = document.getElementById('anio').value;

    const opcionesEncabezados = document.getElementById('opcionesEncabezados');
    opcionesEncabezados.innerHTML = '';

    const encabezadosPrejudicial = JSON.parse(localStorage.getItem('encabezadosPrejudicial')) || [];
    const encabezadosJudicial = JSON.parse(localStorage.getItem('encabezadosJudicial')) || [];
    const encabezadosSinHistorial = JSON.parse(localStorage.getItem('encabezadosSinHistorial')) || [];

    if (filtro === 'con_historial') {
        agregarOpcionEncabezado(opcionesEncabezados, 'Opción Pre-judicial', ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción', 'Objetivo Logrado'], 'encabezados_prejudicial', encabezadosPrejudicial);
        agregarOpcionEncabezado(opcionesEncabezados, 'Opción Judicial', ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción'], 'encabezados_judicial', encabezadosJudicial);
    } else if (filtro === 'sin_historial') {
        agregarOpcionEncabezado(opcionesEncabezados, 'Opción sin historial', ['Nombres', 'DNI'], 'encabezados_sin_historial', encabezadosSinHistorial);
    } else {
        agregarOpcionEncabezado(opcionesEncabezados, 'Opción Pre-judicial', ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción', 'Objetivo Logrado'], 'encabezados_prejudicial', encabezadosPrejudicial);
        agregarOpcionEncabezado(opcionesEncabezados, 'Opción Judicial', ['Fecha', 'Fecha Clave', 'Acto', 'Acción en Fecha Clave', 'Descripción'], 'encabezados_judicial', encabezadosJudicial);
        agregarOpcionEncabezado(opcionesEncabezados, 'Opción sin historial', ['Nombres', 'DNI'], 'encabezados_sin_historial', encabezadosSinHistorial);
    }

    const modal = new bootstrap.Modal(document.getElementById('encabezadosModal'));
    modal.show();
}

function agregarOpcionEncabezado(contenedor, titulo, encabezados, nombreCampo, opcionesPorDefecto) {
    const opcionDiv = document.createElement('div');
    opcionDiv.classList.add('mb-3');

    const tituloLabel = document.createElement('label');
    tituloLabel.classList.add('form-label');
    tituloLabel.textContent = titulo;
    opcionDiv.appendChild(tituloLabel);

    encabezados.forEach(encabezado => {
        const checkboxDiv = document.createElement('div');
        checkboxDiv.classList.add('form-check');

        const checkboxInput = document.createElement('input');
        checkboxInput.type = 'checkbox';
        checkboxInput.name = nombreCampo + '[]';
        checkboxInput.value = encabezado;
        checkboxInput.classList.add('form-check-input');
        checkboxInput.checked = opcionesPorDefecto.includes(encabezado);
        checkboxDiv.appendChild(checkboxInput);

        const checkboxLabel = document.createElement('label');
        checkboxLabel.classList.add('form-check-label');
        checkboxLabel.textContent = encabezado;
        checkboxDiv.appendChild(checkboxLabel);

        opcionDiv.appendChild(checkboxDiv);
    });

    contenedor.appendChild(opcionDiv);
}

function abrirConfiguracion() {
    const modal = new bootstrap.Modal(document.getElementById('configuracionModal'));
    modal.show();
}

function guardarConfiguracion() {
    const encabezadosPrejudicial = Array.from(document.querySelectorAll('#encabezadosPrejudicial input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);
    const encabezadosJudicial = Array.from(document.querySelectorAll('#encabezadosJudicial input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);
    const encabezadosSinHistorial = Array.from(document.querySelectorAll('#encabezadosSinHistorial input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);

    localStorage.setItem('encabezadosPrejudicial', JSON.stringify(encabezadosPrejudicial));
    localStorage.setItem('encabezadosJudicial', JSON.stringify(encabezadosJudicial));
    localStorage.setItem('encabezadosSinHistorial', JSON.stringify(encabezadosSinHistorial));

    const modal = bootstrap.Modal.getInstance(document.getElementById('configuracionModal'));
    modal.hide();
}

window.addEventListener('load', function() {
    const encabezadosPrejudicial = JSON.parse(localStorage.getItem('encabezadosPrejudicial')) || [];
    const encabezadosJudicial = JSON.parse(localStorage.getItem('encabezadosJudicial')) || [];
    const encabezadosSinHistorial = JSON.parse(localStorage.getItem('encabezadosSinHistorial')) || [];

    encabezadosPrejudicial.forEach(encabezado => {
        document.querySelector(`#encabezadosPrejudicial input[value="${encabezado}"]`).checked = true;
    });
    encabezadosJudicial.forEach(encabezado => {
        document.querySelector(`#encabezadosJudicial input[value="${encabezado}"]`).checked = true;
    });
    encabezadosSinHistorial.forEach(encabezado => {
        document.querySelector(`#encabezadosSinHistorial input[value="${encabezado}"]`).checked = true;
    });
});
