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

            <div class="card" v-if="dueno && mascotas.length > 0">
                <div class="card-header bg-success text-white">Seleccionar Mascota</div>
                <div class="card-body">
                    <div class="mb-3">
                        <div><strong>Dueño:</strong> {{ dueno.nombre }} {{ dueno.apellido }}</div>
                        <div><strong>RUT:</strong> {{ dueno.rut }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-9 mb-3 mb-md-0">
                            <label for="mascota_select" class="form-label">Mascota</label>
                            <select id="mascota_select" class="form-select" v-model="mascotaSeleccionadaId">
                                <option disabled value="">Seleccione una mascota</option>
                                <option v-for="m in mascotas" :key="m.id" :value="m.id">
                                    {{ m.nombre }} - {{ m.especie }} ({{ m.raza ? m.raza.nombre : 'Sin raza' }})
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" @click="abrirPerfil" :disabled="!mascotaSeleccionadaId">
                                Ver perfil
                            </button>
                        </div>
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
            dueno: null,
            mascotaSeleccionadaId: '',
            buscado: false,
            cargando: false
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
            this.mascotas = [];
            this.dueno = null;
            this.mascotaSeleccionadaId = '';
            axios.get('/api/mascotas-por-rut', { params: { rut: this.rut } })
            .then(respuesta => {
                this.mascotas = respuesta.data;
                if (this.mascotas.length > 0 && this.mascotas[0].dueno) {
                    this.dueno = this.mascotas[0].dueno;
                }
                this.buscado = true;
                this.cargando = false;
            })
            .catch(() => {
                this.mascotas = [];
                this.dueno = null;
                this.buscado = true;
                this.cargando = false;
            });
        },
        abrirPerfil() {
            if (!this.mascotaSeleccionadaId) return;
            window.location.href = `/mascotas/${this.mascotaSeleccionadaId}/perfil`;
        }
    }
}
</script>
