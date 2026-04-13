<template>
  <div class="row justify-content-center mt-4">
    <div class="col-md-8">
      <div class="card shadow">
        <div class="card-header bg-dark text-white">
          Registrar Usuario
        </div>

        <div class="card-body">
          <div v-if="mensajesAlerta.length" :class="['alert', tipoAlerta === 'success' ? 'alert-success' : 'alert-danger']">
            <ul class="mb-0 ps-3">
              <li v-for="(mensaje, index) in mensajesAlerta" :key="index">
                {{ mensaje }}
              </li>
            </ul>
          </div>

          <form @submit.prevent="registrarUsuario" novalidate>
            <div class="mb-3">
              <label for="registro_usuario_nombre" class="form-label">Nombre</label>
              <input id="registro_usuario_nombre" name="nombre" v-model="formulario.nombre" type="text" class="form-control" />
            </div>

            <div class="mb-3">
              <label for="registro_usuario_correo" class="form-label">Correo</label>
              <input id="registro_usuario_correo" name="correo" v-model="formulario.correo" type="email" class="form-control" />
            </div>

            <div class="mb-3">
              <label for="registro_usuario_direccion" class="form-label">Dirección</label>
              <input id="registro_usuario_direccion" name="direccion" v-model="formulario.direccion" type="text" class="form-control" />
            </div>

            <div class="mb-3">
              <label for="registro_usuario_genero" class="form-label">Género</label>
              <select id="registro_usuario_genero" name="genero" v-model="formulario.genero" class="form-select">
                <option value="">Seleccione</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
            <div class="mb-3">
                <label for="registro_usuario_fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
              <input id="registro_usuario_fecha_nacimiento" name="fecha_nacimiento" v-model="formulario.fechaNacimiento" type="date" class="form-control" :max="fechaHoy" />

                <small class="text-muted d-block mt-1" v-if="edadCalculada !== null">
                    Edad: {{ edadCalculada }} años
                </small>
            </div>
            <div class="mb-3">
              <label for="registro_usuario_nacionalidad" class="form-label">Nacionalidad</label>
              <select id="registro_usuario_nacionalidad" name="nacionalidad" v-model="formulario.nacionalidad" class="form-select">
                <option value="">Seleccione un país</option>
                <option
                  v-for="pais in paises"
                  :key="pais.alpha2Code"
                  :value="pais.numericCode"
                >
                  {{ pais.name }} 
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label for="registro_usuario_contrasena" class="form-label">Contraseña</label>
              <input id="registro_usuario_contrasena" name="contrasena" v-model="formulario.contrasena" type="password" class="form-control" />
            </div>

            <div class="mb-3">
              <label for="registro_usuario_confirmacion_contrasena" class="form-label">Confirmar contraseña</label>
              <input id="registro_usuario_confirmacion_contrasena" name="confirmacion_contrasena" v-model="formulario.confirmacionContrasena" type="password" class="form-control" />
            </div>

            <button type="submit" class="btn btn-success w-100" :disabled="cargando">
              {{ cargando ? 'Registrando...' : 'Registrar' }}
            </button>

            <a href="/login" class="btn btn-secondary w-100 mt-2">
              Volver al login
            </a>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import {
  REGEX_CORREO_CON_DOMINIO,
  extraerErroresServidor,
  obtenerFechaHoyISO,
  obtenerPaises,
} from '../utils/usuarioFormUtils';

export default {
  name: 'RegistroUsuarioComponente',
  data() {
    return {
      cargando: false,
      tipoAlerta: '',
      mensajesAlerta: [],
      paises: [],
      formulario: {
        nombre: '',
        correo: '',
        direccion: '',
        genero: '',
        fechaNacimiento: '',
        nacionalidad: '',
        contrasena: '',
        confirmacionContrasena: '',
      }
    };
  },
  computed: {
    fechaHoy() {
      return obtenerFechaHoyISO();
    },

    edadCalculada() {
      if (!this.formulario.fechaNacimiento) return null;

      // Extraer año, mes, día directamente sin usar new Date para evitar desfase de zona horaria
      const [yearNac, monthNac, dayNac] = this.formulario.fechaNacimiento.split('-').map(Number);
      
      if (!yearNac || !monthNac || !dayNac) return null;

      // Obtener fecha actual en formato numérico (sin zona horaria)
      const hoy = new Date();
      const yearHoy = hoy.getFullYear();
      const monthHoy = hoy.getMonth() + 1; // getMonth() retorna 0-11
      const dayHoy = hoy.getDate();

      // Calcular edad comparando solo fechas
      let edad = yearHoy - yearNac;

      // Si aún no ha pasado el cumpleaños este año, restar 1
      if (monthHoy < monthNac || (monthHoy === monthNac && dayHoy < dayNac)) {
        edad -= 1;
      }

      return edad >= 0 ? edad : null;
    }
  },
  mounted() {
    this.cargarPaises();
  },
  methods: {
    establecerAlerta(tipo, mensajes) {
      this.tipoAlerta = tipo;
      this.mensajesAlerta = Array.isArray(mensajes) ? mensajes : [mensajes];
    },

    limpiarAlerta() {
      this.tipoAlerta = '';
      this.mensajesAlerta = [];
    },

    validarFormulario() {
      const errores = [];

      if (!this.formulario.nombre.trim()) {
        errores.push('El nombre es obligatorio.');
      }

      if (!this.formulario.correo.trim()) {
        errores.push('El correo es obligatorio.');
      } else if (!REGEX_CORREO_CON_DOMINIO.test(this.formulario.correo.trim())) {
        errores.push('El correo debe tener un formato válido.');
      }

      if (this.formulario.fechaNacimiento && this.formulario.fechaNacimiento > this.fechaHoy) {
        errores.push('La fecha de nacimiento no puede ser mayor a hoy.');
      }

      if (!this.formulario.contrasena) {
        errores.push('La contraseña es obligatoria.');
      } else if (this.formulario.contrasena.length < 6) {
        errores.push('La contraseña debe tener al menos 6 caracteres.');
      }

      if (this.formulario.contrasena !== this.formulario.confirmacionContrasena) {
        errores.push('La confirmación de contraseña no coincide.');
      }

      return errores;
    },

    cargarPaises() {
      return obtenerPaises().then((paises) => {
        this.paises = paises;
      });
    },
    registrarUsuario() {
      this.cargando = true;
      this.limpiarAlerta();

      const erroresValidacion = this.validarFormulario();

      if (erroresValidacion.length) {
        this.establecerAlerta('danger', erroresValidacion);
        this.cargando = false;
        return false;
      }

      const payload = {
        nombre: this.formulario.nombre,
        correo: this.formulario.correo,
        direccion: this.formulario.direccion,
        genero: this.formulario.genero,
        fecha_nacimiento: this.formulario.fechaNacimiento,
        nacionalidad: this.formulario.nacionalidad,
        contrasena: this.formulario.contrasena,
        confirmacion_contrasena: this.formulario.confirmacionContrasena,
      };

      return axios.post('/register', payload)
        .then((response) => {
          this.establecerAlerta('success', response.data.message || 'Usuario registrado correctamente');

          setTimeout(() => {
            window.location.href = response.data.redirect;
          }, 1000);
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.establecerAlerta('danger', extraerErroresServidor(error.response.data.errors));
          } else {
            const mensajeServidor = error.response && error.response.data && error.response.data.message
              ? error.response.data.message
              : 'Ocurrió un error al registrar el usuario';

            this.establecerAlerta('danger', mensajeServidor);
          }
        })
        .finally(() => {
          this.cargando = false;
        });
    }
  }
};
</script>