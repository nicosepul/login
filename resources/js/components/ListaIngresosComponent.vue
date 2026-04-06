<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span>Lista de Ingresos Veterinarios</span>
                    <div v-if="cargando" class="spinner-border spinner-border-sm" role="status"></div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Mascota</th>
                                <th>Dueño</th>
                                <th>Motivo</th>
                                <th>Diagnóstico</th>
                                <th>Tratamiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="i in ingresosPaginados" :key="i.id">
                                <td>{{ i.fecha_ingreso }}</td>
                                <td>{{ i.mascota.nombre }} ({{ i.mascota.raza.nombre }})</td>
                                <td>{{ i.mascota.dueno.nombre }} {{ i.mascota.dueno.apellido }} ({{ i.mascota.dueno.rut }})</td>
                                <td>{{ i.motivo }}</td>
                                <td>{{ i.diagnostico }}</td>
                                <td>{{ i.tratamiento }}</td>
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
            ingresos: [],
            cargando: false,
            paginaActual: 1,
            porPagina: 2
        }
    },
    computed: {
        totalPaginas() {
            return Math.ceil(this.ingresos.length / this.porPagina) || 1;
        },
        ingresosPaginados() {
            const inicio = (this.paginaActual - 1) * this.porPagina;
            return this.ingresos.slice(inicio, inicio + this.porPagina);
        }
    },
    mounted() {
        this.obtenerIngresos();
    },
    methods: {
        obtenerIngresos() {
            this.cargando = true;
            axios.get('/api/ingresos').then(respuesta => {
                this.ingresos = respuesta.data;
                this.cargando = false;
            });
        }
    }
}
</script>
