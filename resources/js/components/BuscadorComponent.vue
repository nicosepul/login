<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <span>Buscador de Mascotas por Dueño</span>
                    <div v-if="cargando" class="spinner-border spinner-border-sm" role="status"></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <label for="buscador_rut" class="form-label">RUT del Dueño</label>
                            <input id="buscador_rut" name="rut" type="text" class="form-control" v-model="rut" @input="formatearRut" placeholder="Ingrese RUT del Dueño" maxlength="12">
                        </div>
                        <div class="col-md-2">
                            <button @click="buscar" class="btn btn-info w-100" :disabled="cargando">Buscar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" v-if="mascotas.length > 0">
                <div class="card-header bg-success text-white">Resultados de la Búsqueda</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Especie</th>
                                <th>Raza</th>
                                <th>Sexo</th>
                                <th>Color</th>
                                <th>Fecha Nac.</th>
                                <th>Peso (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="m in mascotasPaginadas" :key="m.id">
                                <td>{{ m.nombre }}</td>
                                <td>{{ m.especie }}</td>
                                <td>{{ m.raza.nombre }}</td>
                                <td>{{ m.sexo }}</td>
                                <td>{{ m.color }}</td>
                                <td>{{ m.fecha_nacimiento }}</td>
                                <td>{{ m.peso }}</td>
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
            <div v-else-if="buscado && !cargando" class="alert alert-warning">
                No se encontraron mascotas asociadas a ese RUT.
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
            buscado: false,
            cargando: false,
            paginaActual: 1,
            porPagina: 2
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
            this.buscado = false;
            axios.get('/api/mascotas-por-rut', { params: { rut: this.rut } })
            .then(respuesta => {
                this.mascotas = respuesta.data;
                this.buscado = true;
                this.cargando = false;
                this.paginaActual = 1;
            })
            .catch(error => {
                this.mascotas = [];
                this.buscado = true;
                this.cargando = false;
            });
        }
    }
}
</script>
