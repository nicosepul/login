<template>
  <div class="row justify-content-center mt-4">
    <div class="col-md-8">
      <div class="card shadow">
        <div class="card-header bg-dark text-white">
          Registrar Usuario
        </div>

        <div class="card-body">
          <div v-if="successMessage" class="alert alert-success">
            {{ successMessage }}
          </div>

          <div v-if="errors.general" class="alert alert-danger">
            {{ errors.general }}
          </div>

          <form @submit.prevent="register">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input v-model="form.name" type="text" class="form-control" />
              <small class="text-danger" v-if="errors.name">{{ errors.name[0] }}</small>
            </div>

            <div class="mb-3">
              <label class="form-label">Correo</label>
              <input v-model="form.email" type="email" class="form-control" />
              <small class="text-danger" v-if="errors.email">{{ errors.email[0] }}</small>
            </div>

            <div class="mb-3">
              <label class="form-label">Dirección</label>
              <input v-model="form.direccion" type="text" class="form-control" />
            </div>

            <div class="mb-3">
              <label class="form-label">Género</label>
              <select v-model="form.genero" class="form-select">
                <option value="">Seleccione</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de nacimiento</label>
                <input v-model="form.fecha_nacimiento" type="date" class="form-control" />

                <small class="text-muted d-block mt-1" v-if="edadCalculada !== null">
                    Edad: {{ edadCalculada }} años
                </small>
            </div>
            <div class="mb-3">
              <label class="form-label">Nacionalidad</label>
              <select v-model="form.nacionalidad" class="form-select">
                <option value="">Seleccione un país</option>
                <option
                  v-for="country in countries"
                  :key="country.alpha2Code"
                  :value="country.numericCode"
                >
                  {{ country.name }} 
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input v-model="form.password" type="password" class="form-control" />
              <small class="text-danger" v-if="errors.password">{{ errors.password[0] }}</small>
            </div>

            <div class="mb-3">
              <label class="form-label">Confirmar contraseña</label>
              <input v-model="form.password_confirmation" type="password" class="form-control" />
            </div>

            <button type="submit" class="btn btn-success w-100" :disabled="loading">
              {{ loading ? 'Registrando...' : 'Registrar' }}
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

export default {
  name: 'RegisterComponent',
  data() {
    return {
      loading: false,
      successMessage: '',
      countries: [],
      errors: {},
      form: {
        name: '',
        email: '',
        direccion: '',
        genero: '',
        fecha_nacimiento: '',
        nacionalidad: '',
        password: '',
        password_confirmation: '',
      }
    };
  },
  computed: {
    edadCalculada() {
      if (!this.form.fecha_nacimiento) return null;

      // Extraer año, mes, día directamente sin usar new Date para evitar desfase de zona horaria
      const [yearNac, monthNac, dayNac] = this.form.fecha_nacimiento.split('-').map(Number);
      
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
    this.loadCountries();
  },
  methods: {
    async loadCountries() {
      try {
        const response = await axios.get('/api/countries');
        this.countries = response.data;
      } catch (error) {
        console.error('Error cargando países', error);
      }
    },
    async register() {
      this.loading = true;
      this.errors = {};
      this.successMessage = '';

      try {
        const response = await axios.post('/register', this.form);

        this.successMessage = response.data.message;

        setTimeout(() => {
          window.location.href = response.data.redirect;
        }, 1000);
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.general = 'Ocurrió un error al registrar el usuario';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>