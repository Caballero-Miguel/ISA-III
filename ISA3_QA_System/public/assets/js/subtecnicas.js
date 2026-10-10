const SUBTECNICAS_CAJA_NEGRA = [
    'Partición de equivalencia',
    'Análisis de valores límite',
    'Tabla de decisiones',
    'Pruebas de transición de estados',
    'Grafos causa-efecto',
    'Pruebas combinatorias por pares',
    'Pruebas basadas en casos de uso',
    'Pruebas basadas en escenarios',
    'Pruebas basadas en modelos',
    'Pruebas aleatorias'
];

const SUBTECNICAS_CAJA_BLANCA = [
    'Cobertura de sentencias',
    'Cobertura de decisiones',
    'Cobertura de condiciones',
    'Cobertura de decisión/condición',
    'Cobertura de condiciones múltiples',
    'Cobertura de caminos',
    'Cobertura de caminos básicos',
    'Cobertura de bucles',
    'Cobertura de funciones/métodos',
    'Cobertura de clases'
];

const AYUDAS_SUBTECNICAS = {
    'Partición de equivalencia': 'Divide las entradas en clases válidas e inválidas y prueba un representante de cada clase.',
    'Análisis de valores límite': 'Prueba mínimo-1, mínimo, mínimo+1, máximo-1, máximo, máximo+1.',
    'Tabla de decisiones': 'Modela combinaciones de condiciones y sus acciones resultantes.',
    'Pruebas de transición de estados': 'Verifica las transiciones entre estados válidos e inválidos del sistema.',
    'Grafos causa-efecto': 'Relaciona causas (entradas) con efectos (salidas) mediante un grafo.',
    'Pruebas combinatorias por pares': 'Prueba todas las combinaciones de pares de parámetros de entrada.',
    'Pruebas basadas en casos de uso': 'Deriva pruebas a partir de los flujos principal y alternativos.',
    'Pruebas basadas en escenarios': 'Prueba escenarios reales de uso del sistema.',
    'Pruebas basadas en modelos': 'Prueba a partir de un modelo formal del comportamiento esperado.',
    'Pruebas aleatorias': 'Genera entradas aleatorias para descubrir fallos inesperados.',
    'Cobertura de sentencias': 'Ejecuta cada línea de código al menos una vez.',
    'Cobertura de decisiones': 'Evalúa cada rama verdadera y falsa de las decisiones.',
    'Cobertura de condiciones': 'Evalúa cada condición booleana individualmente.',
    'Cobertura de decisión/condición': 'Combina cobertura de decisiones y condiciones.',
    'Cobertura de condiciones múltiples': 'Evalúa todas las combinaciones posibles de condiciones.',
    'Cobertura de caminos': 'Ejecuta todos los caminos independientes del flujo.',
    'Cobertura de caminos básicos': 'Ejecuta los caminos básicos del grafo de flujo.',
    'Cobertura de bucles': 'Prueba bucles con 0, 1 y múltiples iteraciones.',
    'Cobertura de funciones/métodos': 'Verifica que cada función o método sea ejecutado.',
    'Cobertura de clases': 'Verifica que cada clase sea ejercitada por las pruebas.'
};

const tecnica = document.querySelector('#tecnica');
const subtecnica = document.querySelector('#subtecnica');
const ayuda = document.querySelector('#ayuda-contextual');

function actualizarAyuda() {
    ayuda.textContent = AYUDAS_SUBTECNICAS[subtecnica.value] || '';
}

function cargarSubtecnicas() {
    const opciones = tecnica.value === 'caja negra'
        ? SUBTECNICAS_CAJA_NEGRA
        : tecnica.value === 'caja blanca'
            ? SUBTECNICAS_CAJA_BLANCA
            : [];
    const seleccionada = subtecnica.dataset.subtecnicaSeleccionada || subtecnica.value;

    subtecnica.replaceChildren();
    if (opciones.length === 0) {
        subtecnica.append(new Option('Selecciona primero una técnica', ''));
    } else {
        subtecnica.append(new Option('Selecciona una subtécnica', ''));
        opciones.forEach((opcion) => subtecnica.append(new Option(opcion, opcion)));
        if (opciones.includes(seleccionada)) {
            subtecnica.value = seleccionada;
        }
    }
    subtecnica.dataset.subtecnicaSeleccionada = '';
    actualizarAyuda();
}

if (tecnica && subtecnica && ayuda) {
    tecnica.addEventListener('change', cargarSubtecnicas);
    subtecnica.addEventListener('change', actualizarAyuda);
    cargarSubtecnicas();
}
