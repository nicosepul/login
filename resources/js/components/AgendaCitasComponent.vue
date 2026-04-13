<template>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Agenda de Citas</span>
            <div class="d-flex gap-2 align-items-center">
                <select v-model="ordenAgenda" class="form-select form-select-sm w-auto">
                    <option value="estado">Por estado</option>
                </select>
                <button class="btn btn-sm btn-light" @click="cargarCitas" :disabled="cargando || actualizandoEstado">
                    {{ cargando ? 'Cargando...' : 'Recargar' }}
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Dueno</th>
                            <th>Mascota</th>
                            <th>Sucursal</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!citas.length && !cargando">
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay citas agendadas.
                            </td>
                        </tr>
                        <tr v-for="cita in citasOrdenadas" :key="cita.id">
                            <td>{{ cita.dueno || '-' }}</td>
                            <td>{{ cita.mascota || '-' }}</td>
                            <td>{{ cita.sucursal || '-' }}</td>
                            <td>{{ formatearFecha(cita.fecha_cita) }}</td>
                            <td>{{ formatearHora(cita.hora_cita) }}</td>
                            <td>
                                <span class="badge" :class="claseEstado(cita.estado)">
                                    {{ cita.estado }}
                                </span>
                            </td>
                            <td>
                                <button
                                    class="btn btn-sm btn-success"
                                    @click="confirmarAtendido(cita)"
                                    :disabled="actualizandoEstado"
                                    v-if="cita.estado === 'Pendiente'"
                                >
                                    Marcar Atendido
                                </button>
                                <span v-else class="text-muted">-</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    name: 'AgendaCitasComponent',
    data() {
        return {
            citas: [],
            cargando: false,
            actualizandoEstado: false,
            temporizadorEstado: null,
            ordenAgenda: 'estado',
        };
    },
    computed: {
        citasOrdenadas() {
            return [...this.citas].sort((primera, segunda) => {
                if (this.ordenAgenda === 'estado') {
                    return this.ordenEstado(primera.estado) - this.ordenEstado(segunda.estado)
                        || this.marcaTiempoCita(segunda) - this.marcaTiempoCita(primera);
                }
                return this.marcaTiempoCita(segunda) - this.marcaTiempoCita(primera);
            });
        },
    },
    mounted() {
        this.cargarCitas();

        this.temporizadorEstado = setInterval(() => {
            this.cargarCitas();
        }, 60000);
    },
    beforeDestroy() {
        if (this.temporizadorEstado) {
            clearInterval(this.temporizadorEstado);
            this.temporizadorEstado = null;
        }
    },
    methods: {
        marcaTiempoCita(cita) {
            return new Date(`${cita.fecha_cita}T${cita.hora_cita || '00:00'}:00`).getTime();
        },
        ordenEstado(estado) {
            if (estado === 'Pendiente') {
                return 1;
            }
            if (estado === 'Atendido') {
                return 2;
            }
            return 3;
        },
        mostrarSwal(configuracion) {
            return Swal.fire(configuracion);
        },
        cargarCitas() {
            this.cargando = true;

            axios.get('/api/citas')
                .then((respuesta) => {
                    this.citas = Array.isArray(respuesta.data) ? respuesta.data : [];
                })
                .catch(() => {
                    this.mostrarSwal({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar la agenda de citas.',
                    });
                })
                .finally(() => {
                    this.cargando = false;
                });
        },
        confirmarAtendido(cita) {
            this.mostrarSwal({
                icon: 'question',
                title: 'Confirmar accion',
                text: 'Quieres marcar esta cita como atendida?',
                showCancelButton: true,
                confirmButtonText: 'Si, marcar',
                cancelButtonText: 'Cancelar',
            }).then((resultado) => {
                if (!resultado.isConfirmed) {
                    return;
                }

                this.marcarAtendido(cita.id);
            });
        },
        marcarAtendido(citaId) {
            this.actualizandoEstado = true;

            axios.put(`/api/citas/${citaId}/atender`)
                .then((respuesta) => {
                    this.mostrarSwal({
                        icon: 'success',
                        title: 'Cita actualizada',
                        text: respuesta.data.message || 'La cita fue marcada como atendida.',
                        timer: 1600,
                        showConfirmButton: false,
                    });
                    this.cargarCitas();
                })
                .catch((error) => {
                    const mensaje = error.response && error.response.data && error.response.data.message
                        ? error.response.data.message
                        : 'No se pudo actualizar el estado de la cita.';

                    this.mostrarSwal({
                        icon: 'error',
                        title: 'Error',
                        text: mensaje,
                    });
                })
                .finally(() => {
                    this.actualizandoEstado = false;
                });
        },
        formatearFecha(fecha) {
            if (!fecha) {
                return '-';
            }

            const partes = String(fecha).split('-');

            if (partes.length !== 3) {
                return fecha;
            }

            return `${partes[2]}/${partes[1]}/${partes[0]}`;
        },
        formatearHora(hora) {
            if (!hora) {
                return '-';
            }

            return String(hora).slice(0, 5);
        },
        claseEstado(estado) {
            if (estado === 'Atendido') {
                return 'bg-success';
            }

            if (estado === 'No asistió') {
                return 'bg-danger';
            }

            return 'bg-warning text-dark';
        },
    },
};
</script>
