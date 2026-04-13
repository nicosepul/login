<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span>Registro de Ingreso de Mascota</span>
                    <div v-if="cargando" class="spinner-border spinner-border-sm" role="status"></div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <label for="ingreso_rut" class="form-label">RUT del Dueño</label>
                            <input id="ingreso_rut" name="rut" type="text" class="form-control" v-model="rut" @input="formatearRut" placeholder="Ingrese RUT del Dueño" maxlength="12">
                        </div>
                        <div class="col-md-2">
                            <button @click="buscar" class="btn btn-warning w-100" :disabled="cargando">Buscar</button>
                        </div>
                    </div>

                    <div v-if="mascotas.length > 0">
                        <h5>Mascotas del Dueño</h5>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Especie</th>
                                    <th>Raza</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="m in mascotasPaginadas" :key="m.id">
                                    <td>{{ m.nombre }}</td>
                                    <td>{{ m.especie }}</td>
                                    <td>{{ m.raza.nombre }}</td>
                                    <td>
                                        <button @click="seleccionar(m)" class="btn btn-sm btn-primary">Seleccionar</button>
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

                    <div v-if="mascota_seleccionada" class="card p-3 mt-3 bg-light">
                        <h5>Datos del Ingreso para: {{ mascota_seleccionada.nombre }} ({{ mascota_seleccionada.raza.nombre }})</h5>
                        <form @submit.prevent="registrar">
                            <div class="mb-3">
                                <label for="motivo_ingreso" class="form-label">Motivo de Ingreso</label>
                                <input id="motivo_ingreso" name="motivo" type="text" class="form-control" v-model="formulario.motivo" required>
                            </div>
                            <div class="mb-3">
                                <label for="diagnostico_ingreso" class="form-label">Diagnóstico</label>
                                <textarea id="diagnostico_ingreso" name="diagnostico" class="form-control" v-model="formulario.diagnostico"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tratamiento_ingreso" class="form-label">Tratamiento</label>
                                <textarea id="tratamiento_ingreso" name="tratamiento" class="form-control" v-model="formulario.tratamiento"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="observaciones_ingreso" class="form-label">Observaciones</label>
                                <textarea id="observaciones_ingreso" name="observaciones" class="form-control" v-model="formulario.observaciones"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success" :disabled="cargando">Registrar Ingreso</button>
                        </form>
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
            rut: '',
            mascotas: [],
            mascota_seleccionada: null,
            cargando: false,
            paginaActual: 1,
            porPagina: 5,
            formulario: {
                mascota_id: '',
                motivo: '',
                diagnostico: '',
                tratamiento: '',
                observaciones: ''
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
    methods: {
        formatearRut() {
            let valor = this.rut.replace(/[^0-9kK]/g, '');
            if (valor.length <= 1) {
                this.rut = valor;
                return;
            }
            let cuerpo = valor.slice(0, -1);
            let dv = valor.slice(-1).toUpperCase();
            cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            this.rut = cuerpo + '-' + dv;
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
        buscar() {
            if (!this.validarRut(this.rut)) {
                alert('RUT inválido');
                return;
            }
            if (!this.rut) return;
            this.cargando = true;
            axios.get('/api/mascotas-por-rut', { params: { rut: this.rut } })
            .then(respuesta => {
                this.mascotas = respuesta.data;
                this.mascota_seleccionada = null;
                this.cargando = false;
                this.paginaActual = 1;
            })
            .catch(error => {
                alert(error.respuesta.data.mensaje);
                this.mascotas = [];
                this.mascota_seleccionada = null;
                this.cargando = false;
            });
        },
        seleccionar(mascota) {
            this.mascota_seleccionada = mascota;
            this.formulario.mascota_id = mascota.id;
        },
        registrar() {
            this.cargando = true;
            axios.post('/api/registrar-ingreso', this.formulario).then(respuesta => {
                alert(respuesta.data.mensaje);
                this.limpiarFormulario();
                this.cargando = false;
            });
        },
        limpiarFormulario() {
            this.formulario = {
                mascota_id: '',
                motivo: '',
                diagnostico: '',
                tratamiento: '',
                observaciones: ''
            };
            this.mascota_seleccionada = null;
            this.mascotas = [];
            this.rut = '';
        }
    }
}
</script>
