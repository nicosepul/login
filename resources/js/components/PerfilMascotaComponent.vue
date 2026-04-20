<template>
    <div>
        <div v-if="cargando" class="alert alert-info">Cargando perfil de mascota...</div>
        <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-else-if="mascota" class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">Multimedia de Mascota</div>
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            <div class="mb-2 text-start"><strong>Foto de mascota</strong></div>
                            <img
                                :src="mascota.imagen_url || imagenPlaceholder"
                                alt="Foto de mascota"
                                class="img-fluid rounded mb-3"
                                style="max-height: 280px; width: 100%; object-fit: cover;"
                            >
                            <div class="mb-3">
                                <input
                                    ref="inputImagen"
                                    type="file"
                                    class="form-control"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                    @change="onImagenSeleccionada"
                                >
                            </div>
                            <button class="btn btn-primary w-100" :disabled="subiendoImagen || !imagenSeleccionada" @click="subirImagen">
                                {{ subiendoImagen ? 'Subiendo imagen...' : 'Actualizar imagen' }}
                            </button>
                        </div>

                        <hr class="my-3">

                        <div class="text-center">
                            <div class="mb-2 text-start"><strong>Video de mascota</strong></div>
                            <div class="mb-3" v-if="mascota.video_url">
                                <video controls class="w-100 rounded" style="max-height: 280px; object-fit: cover;">
                                    <source :src="mascota.video_url" type="video/mp4">
                                    Tu navegador no soporta la reproducción de video.
                                </video>
                            </div>
                            <div class="mb-3" v-else>
                                <div class="alert alert-secondary mb-0">Sin video cargado.</div>
                            </div>
                            <div class="mb-3">
                                <input
                                    ref="inputVideo"
                                    type="file"
                                    class="form-control"
                                    accept="video/mp4,video/quicktime,video/webm,video/ogg"
                                    @change="onVideoSeleccionado"
                                >
                            </div>
                            <button class="btn btn-outline-primary w-100" :disabled="subiendoVideo || !videoSeleccionado" @click="subirVideo">
                                {{ subiendoVideo ? 'Subiendo video...' : 'Actualizar video' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">Perfil de Mascota</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2"><strong>Nombre:</strong> {{ mascota.nombre }}</div>
                            <div class="col-md-6 mb-2"><strong>Especie:</strong> {{ mascota.especie || '-' }}</div>
                            <div class="col-md-6 mb-2"><strong>Raza:</strong> {{ mascota.raza ? mascota.raza.nombre : '-' }}</div>
                            <div class="col-md-6 mb-2"><strong>Sexo:</strong> {{ mascota.sexo || '-' }}</div>
                            <div class="col-md-6 mb-2"><strong>Color:</strong> {{ mascota.color || '-' }}</div>
                            <div class="col-md-6 mb-2"><strong>Fecha nacimiento:</strong> {{ mascota.fecha_nacimiento || '-' }}</div>
                            <div class="col-md-6 mb-2"><strong>Peso:</strong> {{ mascota.peso ? mascota.peso + ' kg' : '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-info text-white">Dueño</div>
                    <div class="card-body" v-if="mascota.dueno">
                        <div><strong>Nombre:</strong> {{ mascota.dueno.nombre }} {{ mascota.dueno.apellido }}</div>
                        <div><strong>RUT:</strong> {{ mascota.dueno.rut }}</div>
                        <div><strong>Teléfono:</strong> {{ mascota.dueno.telefono || '-' }}</div>
                        <div><strong>Email:</strong> {{ mascota.dueno.email || '-' }}</div>
                        <div><strong>Dirección:</strong> {{ mascota.dueno.direccion || '-' }}</div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">Historial de Ingresos</div>
                    <div class="card-body p-0">
                        <div v-if="!mascota.ingresos || mascota.ingresos.length === 0" class="p-3">Sin ingresos registrados.</div>
                        <div v-else class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Motivo</th>
                                        <th>Diagnóstico</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="ingreso in mascota.ingresos" :key="ingreso.id">
                                        <td>{{ ingreso.fecha_ingreso }}</td>
                                        <td>{{ ingreso.motivo || '-' }}</td>
                                        <td>{{ ingreso.diagnostico || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">Citas</div>
                    <div class="card-body p-0">
                        <div v-if="!mascota.citas || mascota.citas.length === 0" class="p-3">Sin citas registradas.</div>
                        <div v-else class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Sucursal</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="cita in mascota.citas" :key="cita.id">
                                        <td>{{ cita.fecha_cita }}</td>
                                        <td>{{ cita.hora_cita }}</td>
                                        <td>{{ cita.sucursal ? cita.sucursal.nombre : '-' }}</td>
                                        <td>{{ cita.estado }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        mascotaId: {
            type: [String, Number],
            required: true
        }
    },
    data() {
        return {
            mascota: null,
            cargando: true,
            error: '',
            imagenSeleccionada: null,
            subiendoImagen: false,
            videoSeleccionado: null,
            subiendoVideo: false,
            imagenPlaceholder: 'https://via.placeholder.com/500x350?text=Sin+imagen'
        };
    },
    mounted() {
        this.cargarPerfil();
    },
    methods: {
        cargarPerfil() {
            this.cargando = true;
            this.error = '';

            axios.get(`/api/mascotas/${this.mascotaId}/perfil`)
                .then((respuesta) => {
                    this.mascota = respuesta.data;
                })
                .catch(() => {
                    this.error = 'No fue posible cargar el perfil de la mascota.';
                })
                .finally(() => {
                    this.cargando = false;
                });
        },
        onImagenSeleccionada(evento) {
            const archivo = evento.target.files && evento.target.files[0] ? evento.target.files[0] : null;
            this.imagenSeleccionada = archivo;
        },
        onVideoSeleccionado(evento) {
            const archivo = evento.target.files && evento.target.files[0] ? evento.target.files[0] : null;
            this.videoSeleccionado = archivo;
        },
        subirImagen() {
            if (!this.imagenSeleccionada) {
                return;
            }

            const formData = new FormData();
            formData.append('imagen', this.imagenSeleccionada);
            this.subiendoImagen = true;

            axios.post(`/api/mascotas/${this.mascotaId}/imagen`, formData)
                .then((respuesta) => {
                    if (this.mascota) {
                        this.mascota.imagen_url = respuesta.data.imagen_url;
                    }
                    this.imagenSeleccionada = null;
                    if (this.$refs.inputImagen) {
                        this.$refs.inputImagen.value = '';
                    }
                    alert('Imagen actualizada correctamente.');
                })
                .catch((error) => {
                    const errores = error?.response?.data?.errors;
                    const mensaje = error?.response?.data?.message;

                    if (errores) {
                        const primerCampo = Object.keys(errores)[0];
                        const primerError = primerCampo && errores[primerCampo] && errores[primerCampo][0]
                            ? errores[primerCampo][0]
                            : 'No se pudo subir la imagen.';
                        alert(primerError);
                        return;
                    }

                    alert(mensaje || 'No se pudo subir la imagen. Intente nuevamente.');
                })
                .finally(() => {
                    this.subiendoImagen = false;
                });
        },
        subirVideo() {
            if (!this.videoSeleccionado) {
                return;
            }

            const formData = new FormData();
            formData.append('video', this.videoSeleccionado);
            this.subiendoVideo = true;

            axios.post(`/api/mascotas/${this.mascotaId}/video`, formData)
                .then((respuesta) => {
                    if (this.mascota) {
                        this.mascota.video_url = respuesta.data.video_url;
                    }
                    this.videoSeleccionado = null;
                    if (this.$refs.inputVideo) {
                        this.$refs.inputVideo.value = '';
                    }
                    alert('Video actualizado correctamente.');
                })
                .catch((error) => {
                    const errores = error?.response?.data?.errors;
                    const mensaje = error?.response?.data?.message;

                    if (errores) {
                        const primerCampo = Object.keys(errores)[0];
                        const primerError = primerCampo && errores[primerCampo] && errores[primerCampo][0]
                            ? errores[primerCampo][0]
                            : 'No se pudo subir el video.';
                        alert(primerError);
                        return;
                    }

                    alert(mensaje || 'No se pudo subir el video. Intente nuevamente.');
                })
                .finally(() => {
                    this.subiendoVideo = false;
                });
        }
    }
};
</script>
