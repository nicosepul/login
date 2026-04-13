<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <span>Registro de Citas Veterinarias</span>
                    <div class="d-flex gap-2 align-items-center">
                        <div v-if="cargando" class="spinner-border spinner-border-sm" role="status"></div>
                    </div>
                </div>
                <div class="card-body">
                    <form @submit.prevent="confirmarRegistro">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="dueno_id" class="form-label">Dueño</label>
                                <select id="dueno_id" name="dueno_id" v-model="formulario.dueno_id" class="form-select" @change="cargarMascotasDelDueno" :disabled="cargando">
                                    <option value="">Seleccione un dueño</option>
                                    <option v-for="dueno in duenos" :key="dueno.id" :value="dueno.id">
                                        {{ dueno.nombre }} {{ dueno.apellido }} - {{ dueno.rut }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="mascota_id" class="form-label">Mascota</label>
                                <select id="mascota_id" name="mascota_id" v-model="formulario.mascota_id" class="form-select" :disabled="!formulario.dueno_id || cargandoMascotas">
                                    <option value="">Seleccione una mascota</option>
                                    <option v-for="mascota in mascotas" :key="mascota.id" :value="mascota.id">
                                        {{ mascota.nombre }} - {{ mascota.raza ? mascota.raza.nombre : mascota.especie }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="sucursal_id" class="form-label">Sucursal</label>
                                <select id="sucursal_id" name="sucursal_id" v-model="formulario.sucursal_id" class="form-select" :disabled="cargando">
                                    <option value="">Seleccione una sucursal</option>
                                    <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
                                        {{ sucursal.nombre }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_cita" class="form-label">Fecha</label>
                                <input id="fecha_cita" name="fecha_cita" v-model="formulario.fecha_cita" type="date" class="form-control" :min="fechaHoy" :disabled="cargando">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hora</label>
                                <br />
                                <date-picker
                                    id="hora_cita"
                                    name="hora_cita"
                                    v-model="formulario.hora_cita"
                                    type="time"
                                    format="HH:mm"
                                    value-type="format"
                                    :minute-step="10"
                                    :clearable="false"
                                    :disabled-time="deshabilitarHorasPasadas"
                                    :disabled="cargando"
                                    input-class="form-control"
                                    placeholder="Seleccione la hora"
                                />
                                <small v-if="verificandoHorario" class="text-muted d-block mt-2">
                                    Verificando disponibilidad...
                                </small>
                                <small v-else-if="disponibilidadHorario === false" class="text-danger d-block mt-2">
                                    Esta hora ya está tomada para la sucursal seleccionada.
                                </small>
                                <small v-else-if="disponibilidadHorario === true" class="text-success d-block mt-2">
                                    Hora disponible.
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label for="prediagnostico" class="form-label">Prediagnóstico</label>
                                <textarea id="prediagnostico" name="prediagnostico" v-model="formulario.prediagnostico" class="form-control" rows="3" placeholder="Descripción breve del motivo de la cita"></textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-info text-white" :disabled="guardando || cargando">
                                {{ guardando ? 'Registrando...' : 'Registrar cita' }}
                            </button>
                            <button type="button" class="btn btn-secondary" @click="limpiarFormulario" :disabled="guardando || cargando">
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
    name: 'RegistroCitasComponent',
    components: {
        DatePicker,
    },
    data() {
        return {
            duenos: [],
            mascotas: [],
            sucursales: [],
            cargando: false,
            cargandoMascotas: false,
            guardando: false,
            verificandoHorario: false,
            disponibilidadHorario: null,
            ultimaConsultaHorario: 0,
            formulario: {
                dueno_id: '',
                mascota_id: '',
                sucursal_id: '',
                fecha_cita: '',
                hora_cita: '',
                prediagnostico: '',
            },
        };
    },
    computed: {
        fechaHoy() {
            const fecha = new Date();
            const year = fecha.getFullYear();
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const day = String(fecha.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        },
    },
    mounted() {
        this.cargarDuenos();
        this.cargarSucursales();
    },
    watch: {
        'formulario.sucursal_id'() {
            this.consultarDisponibilidadHorario();
        },
        'formulario.fecha_cita'() {
            this.consultarDisponibilidadHorario();
        },
        'formulario.hora_cita'() {
            this.consultarDisponibilidadHorario();
        },
    },
    methods: {
        mostrarSwal(configuracion) {
            return Swal.fire(configuracion);
        },
        cargarDuenos() {
            this.cargando = true;

            axios.get('/api/duenos')
                .then((respuesta) => {
                    this.duenos = Array.isArray(respuesta.data) ? respuesta.data : [];
                })
                .catch(() => {
                    this.mostrarSwal({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el listado de dueños.',
                    });
                })
                .finally(() => {
                    this.cargando = false;
                });
        },
        cargarSucursales() {
            this.cargando = true;

            axios.get('/api/sucursales')
                .then((respuesta) => {
                    this.sucursales = Array.isArray(respuesta.data) ? respuesta.data : [];
                })
                .catch(() => {
                    this.mostrarSwal({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el listado de sucursales.',
                    });
                })
                .finally(() => {
                    this.cargando = false;
                });
        },
        cargarMascotasDelDueno() {
            this.formulario.mascota_id = '';
            this.mascotas = [];

            if (!this.formulario.dueno_id) {
                return;
            }

            this.cargandoMascotas = true;

            axios.get(`/api/duenos/${this.formulario.dueno_id}/mascotas`)
                .then((respuesta) => {
                    this.mascotas = Array.isArray(respuesta.data) ? respuesta.data : [];
                })
                .catch(() => {
                    this.mostrarSwal({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar las mascotas del dueño seleccionado.',
                    });
                })
                .finally(() => {
                    this.cargandoMascotas = false;
                });
        },
        validarFormulario() {
            const errores = [];

            if (!this.formulario.dueno_id) {
                errores.push('Debes seleccionar un dueño.');
            }

            if (!this.formulario.mascota_id) {
                errores.push('Debes seleccionar una mascota.');
            }

            if (!this.formulario.sucursal_id) {
                errores.push('Debes seleccionar una sucursal.');
            }

            if (!this.formulario.fecha_cita) {
                errores.push('Debes seleccionar la fecha de la cita.');
            } else if (this.formulario.fecha_cita < this.fechaHoy) {
                errores.push('La fecha de la cita no puede ser anterior a hoy.');
            }

            if (!this.formulario.hora_cita) {
                errores.push('Debes seleccionar la hora de la cita.');
            } else if (!/^\d{2}:\d{2}$/.test(this.formulario.hora_cita)) {
                errores.push('La hora seleccionada no tiene un formato válido.');
            } else if (Number(this.formulario.hora_cita.split(':')[1]) % 10 !== 0) {
                errores.push('La hora debe seleccionarse en intervalos de 10 minutos.');
            } else if (this.esHorarioPasadoParaFecha(this.formulario.fecha_cita, this.formulario.hora_cita)) {
                errores.push('No puedes seleccionar una fecha y hora anteriores al momento actual.');
            }

            return errores;
        },
        esFechaHoy(fechaISO) {
            return fechaISO === this.fechaHoy;
        },
        esHorarioPasadoParaFecha(fechaISO, horaHHMM) {
            if (!fechaISO || !horaHHMM || !/^\d{2}:\d{2}$/.test(horaHHMM)) {
                return false;
            }

            const fechaSeleccionada = new Date(`${fechaISO}T${horaHHMM}:00`);

            if (Number.isNaN(fechaSeleccionada.getTime())) {
                return false;
            }

            return fechaSeleccionada < new Date();
        },
        deshabilitarHorasPasadas(fechaSeleccionada) {
            if (!this.esFechaHoy(this.formulario.fecha_cita)) {
                return false;
            }

            const ahora = new Date();
            const horaSeleccionada = fechaSeleccionada.getHours() * 60 + fechaSeleccionada.getMinutes();
            const horaActual = ahora.getHours() * 60 + ahora.getMinutes();

            return horaSeleccionada < horaActual;
        },
        async consultarDisponibilidadHorario(mostrarError = false) {
            this.disponibilidadHorario = null;

            if (!this.formulario.sucursal_id || !this.formulario.fecha_cita || !this.formulario.hora_cita) {
                return null;
            }

            if (!/^\d{2}:\d{2}$/.test(this.formulario.hora_cita)) {
                return null;
            }

            const tokenConsulta = this.ultimaConsultaHorario + 1;

            this.ultimaConsultaHorario = tokenConsulta;
            this.verificandoHorario = true;

            try {
                const respuesta = await axios.get('/api/citas/disponibilidad', {
                    params: {
                        sucursal_id: this.formulario.sucursal_id,
                        fecha_cita: this.formulario.fecha_cita,
                        hora_cita: this.formulario.hora_cita,
                    },
                });

                if (tokenConsulta !== this.ultimaConsultaHorario) {
                    return null;
                }

                const disponible = Boolean(respuesta.data && respuesta.data.disponible);

                this.disponibilidadHorario = disponible;

                return disponible;
            } catch (error) {
                if (tokenConsulta !== this.ultimaConsultaHorario) {
                    return null;
                }

                const mensaje = error.response && error.response.data && error.response.data.message
                    ? error.response.data.message
                    : 'No se pudo verificar la disponibilidad de la hora.';

                if (mostrarError) {
                    this.mostrarSwal({
                        icon: 'error',
                        title: 'Error',
                        text: mensaje,
                    });
                }

                this.disponibilidadHorario = null;

                return null;
            } finally {
                if (tokenConsulta === this.ultimaConsultaHorario) {
                    this.verificandoHorario = false;
                }
            }
        },
        async confirmarRegistro() {
            const errores = this.validarFormulario();

            if (errores.length) {
                this.mostrarSwal({
                    icon: 'error',
                    title: 'Revisa el formulario',
                    html: errores.map((error) => `<div>${error}</div>`).join(''),
                });
                return;
            }

            const horarioDisponible = await this.consultarDisponibilidadHorario(true);

            if (horarioDisponible === false) {
                this.mostrarSwal({
                    icon: 'warning',
                    title: 'Hora no disponible',
                    text: 'La hora seleccionada ya está tomada. Elige otra para continuar.',
                });
                return;
            }

            if (horarioDisponible === null) {
                return;
            }

            this.mostrarSwal({
                icon: 'question',
                title: 'Confirmar registro',
                text: '¿Deseas guardar esta cita veterinaria?',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
            }).then((resultado) => {
                if (!resultado.isConfirmed) {
                    return;
                }

                this.registrarCita();
            });
        },
        registrarCita() {
            this.guardando = true;

            const payload = {
                dueno_id: this.formulario.dueno_id,
                mascota_id: this.formulario.mascota_id,
                sucursal_id: this.formulario.sucursal_id,
                fecha_cita: this.formulario.fecha_cita,
                hora_cita: this.formulario.hora_cita,
                prediagnostico: this.formulario.prediagnostico,
            };

            axios.post('/api/citas', payload)
                .then((respuesta) => {
                    this.mostrarSwal({
                        icon: 'success',
                        title: 'Cita registrada',
                        text: respuesta.data.message || 'La cita se registró correctamente.',
                        timer: 1800,
                    });
                    this.limpiarFormulario();
                })
                .catch((error) => {
                    const mensaje = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'No se pudo registrar la cita.';

                    this.mostrarSwal({
                        icon: 'error',
                        title: 'Error',
                        text: mensaje,
                    });
                })
                .finally(() => {
                    this.guardando = false;
                });
        },
        limpiarFormulario() {
            this.formulario = {
                dueno_id: '',
                mascota_id: '',
                sucursal_id: '',
                fecha_cita: '',
                hora_cita: '',
                prediagnostico: '',
            };
            this.disponibilidadHorario = null;
            this.verificandoHorario = false;
            this.mascotas = [];
        },
    },
};
</script>

