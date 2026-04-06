<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">{{ modoEdicion ? 'Editar Mascota y Dueño' : 'Registro de Mascota y Dueño' }}</div>
                <div class="card-body">
                    <form @submit.prevent="modoEdicion ? actualizar() : registrar()">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h5>Datos del Dueño</h5>
                                <input type="text" class="form-control mb-2" v-model="formulario.rut" @input="formatearRut" placeholder="RUT (ej: 12.345.678-9)" required maxlength="12" :disabled="modoEdicion">
                                <input type="text" class="form-control mb-2" v-model="formulario.nombre_dueno" placeholder="Nombre" required>
                                <input type="text" class="form-control mb-2" v-model="formulario.apellido_dueno" placeholder="Apellido" required>
                                <input type="text" class="form-control mb-2" v-model="formulario.telefono" placeholder="Teléfono">
                                <input type="text" class="form-control mb-2" v-model="formulario.direccion" placeholder="Dirección">
                                <input type="email" class="form-control mb-2" v-model="formulario.email" placeholder="Email">
                            </div>
                            <div class="col-md-6 mb-3">
                                <h5>Datos de la Mascota</h5>
                                <input type="text" class="form-control mb-2" v-model="formulario.nombre_mascota" placeholder="Nombre Mascota" required>
                                <input type="text" class="form-control mb-2" v-model="formulario.especie" placeholder="Especie (ej: Perro)" required>
                                <select class="form-control mb-2" v-model="formulario.raza_id" required>
                                    <option value="">Seleccione Raza</option>
                                    <option v-for="raza in razas" :key="raza.id" :value="raza.id">{{ raza.nombre }}</option>
                                </select>
                                <select class="form-control mb-2" v-model="formulario.sexo" required>
                                    <option value="">Seleccione Sexo</option>
                                    <option value="Macho">Macho</option>
                                    <option value="Hembra">Hembra</option>
                                </select>
                                <input type="text" class="form-control mb-2" v-model="formulario.color" placeholder="Color">
                                <input type="date" class="form-control mb-2" v-model="formulario.fecha_nacimiento">
                                <input type="number" step="0.1" class="form-control mb-2" v-model="formulario.peso" placeholder="Peso (kg)">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ modoEdicion ? 'Actualizar' : 'Registrar' }}</button>
                        <button type="button" v-if="modoEdicion" class="btn btn-secondary" @click="cancelarEdicion">Cancelar</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span>Mascotas Registradas</span>
                    <div v-if="cargando" class="spinner-border spinner-border-sm" role="status"></div>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Especie</th>
                                <th>Raza</th>
                                <th>Dueño</th>
                                <th>RUT Dueño</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="m in mascotasPaginadas" :key="m.id">
                                <td>{{ m.nombre }}</td>
                                <td>{{ m.especie }}</td>
                                <td>{{ m.raza.nombre }}</td>
                                <td>{{ m.dueno.nombre }} {{ m.dueno.apellido }}</td>
                                <td>{{ m.dueno.rut }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" @click="editar(m)">Editar</button>
                                    <button class="btn btn-sm btn-danger" @click="eliminar(m.id)">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-sm btn-secondary" :disabled="paginaActual == 1" @click="paginaActual--">Anterior</button>
                        <span>Página {{ paginaActual }} de {{ totalPaginas }}</span>
                        <button class="btn btn-sm btn-secondary" :disabled="paginaActual >= totalPaginas" @click="paginaActual++">Siguiente</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            razas: [],
            mascotas: [],
            cargando: false,
            paginaActual: 1,
            porPagina: 3,
            modoEdicion: false,
            editandoId: null,
            formulario: {
                rut: '',
                nombre_dueno: '',
                apellido_dueno: '',
                telefono: '',
                direccion: '',
                email: '',
                nombre_mascota: '',
                especie: '',
                raza_id: '',
                sexo: '',
                color: '',
                fecha_nacimiento: '',
                peso: ''
            }
        }
    },
    computed: {
        totalPaginas() {
            return Math.ceil(this.mascotas.length / this.porPagina) || 1;
        },
        mascotasPaginadas() {
            const inicio = (this.paginaActual - 1) * this.porPagina;
            return this.mascotas.slice(inicio, inicio + this.porPagina);
        }
    },
    mounted() {
        this.obtenerRazas();
        this.obtenerMascotas();
    },
    methods: {
        obtenerRazas() {
            axios.get('/api/razas').then(respuesta => {
                this.razas = respuesta.data;
            });
        },
        obtenerMascotas() {
            this.cargando = true;
            axios.get('/api/mascotas').then(respuesta => {
                this.mascotas = respuesta.data;
                this.cargando = false;
            });
        },
        formatearRut() {
            let valor = this.formulario.rut.replace(/[^0-9kK]/g, '');
            if (valor.length <= 1) {
                this.formulario.rut = valor;
                return;
            }
            let cuerpo = valor.slice(0, -1);
            let dv = valor.slice(-1).toUpperCase();
            cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            this.formulario.rut = cuerpo + '-' + dv;
        },
        validarRut(rut) {
            if (!/^[0-9.]+-[0-9kK]{1}$/.test(rut)) return false;
            let tmp = rut.split('-');
            let dv = tmp[1].toUpperCase();
            let res = 0;
            let multiplicador = 2;
            let cuerpo = tmp[0].replace(/\./g, '');
            for (let i = cuerpo.length - 1; i >= 0; i--) {
                res += multiplicador * cuerpo.charAt(i);
                multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
            }
            let dvr = 11 - (res % 11);
            dvr = dvr === 11 ? '0' : dvr === 10 ? 'K' : dvr.toString();
            return dv === dvr;
        },
        registrar() {
            if (!this.validarRut(this.formulario.rut)) {
                alert('RUT inválido');
                return;
            }
            this.cargando = true;
            axios.post('/api/registro-mascota-dueno', this.formulario).then(respuesta => {
                alert(respuesta.data.mensaje);
                this.obtenerMascotas();
                this.limpiarFormulario();
                this.cargando = false;
            });
        },
        editar(mascota) {
            this.modoEdicion = true;
            this.editandoId = mascota.id;
            this.formulario = {
                rut: mascota.dueno.rut,
                nombre_dueno: mascota.dueno.nombre,
                apellido_dueno: mascota.dueno.apellido,
                telefono: mascota.dueno.telefono,
                direccion: mascota.dueno.direccion,
                email: mascota.dueno.email,
                nombre_mascota: mascota.nombre,
                especie: mascota.especie,
                raza_id: mascota.raza_id,
                sexo: mascota.sexo,
                color: mascota.color,
                fecha_nacimiento: mascota.fecha_nacimiento,
                peso: mascota.peso
            };
            window.scrollTo(0, 0);
        },
        actualizar() {
            this.cargando = true;
            axios.put(`/api/mascota/${this.editandoId}`, this.formulario).then(respuesta => {
                alert(respuesta.data.mensaje);
                this.obtenerMascotas();
                this.cancelarEdicion();
                this.cargando = false;
            });
        },
        eliminar(id) {
            if (!confirm('¿Está seguro de eliminar esta mascota?')) return;
            this.cargando = true;
            axios.delete(`/api/mascota/${id}`).then(respuesta => {
                alert(respuesta.data.mensaje);
                this.obtenerMascotas();
                this.cargando = false;
            });
        },
        cancelarEdicion() {
            this.modoEdicion = false;
            this.editandoId = null;
            this.limpiarFormulario();
        },
        limpiarFormulario() {
            this.formulario = {
                rut: '',
                nombre_dueno: '',
                apellido_dueno: '',
                telefono: '',
                direccion: '',
                email: '',
                nombre_mascota: '',
                especie: '',
                raza_id: '',
                sexo: '',
                color: '',
                fecha_nacimiento: '',
                peso: ''
            };
        }
    }
}
</script>
