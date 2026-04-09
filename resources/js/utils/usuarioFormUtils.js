import axios from 'axios';

export const REGEX_CORREO_CON_DOMINIO = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function obtenerFechaHoyISO() {
    const hoy = new Date();
    const year = hoy.getFullYear();
    const month = String(hoy.getMonth() + 1).padStart(2, '0');
    const day = String(hoy.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

export function extraerErroresServidor(errores) {
    return Object.values(errores || {}).reduce((todosLosErrores, erroresActuales) => {
        return todosLosErrores.concat(erroresActuales);
    }, []);
}

export function obtenerPaises() {
    return axios.get('/api/countries')
        .then((response) => {
            return Array.isArray(response.data) ? response.data : [];
        })
        .catch(() => {
            return [];
        });
}

export function crearFormularioNuevoUsuarioVacio() {
    return {
        nombre: '',
        correo: '',
        direccion: '',
        genero: '',
        fecha_nacimiento: '',
        nacionalidad: '',
        contrasena: '',
        confirmacion_contrasena: '',
    };
}
