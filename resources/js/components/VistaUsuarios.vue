<template>
	<div class="card shadow mt-4">
		<div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
			<span>Usuarios Registrados</span>
			<button class="btn btn-sm btn-light" @click="obtenerUsuarios" :disabled="cargando">
				{{ cargando ? 'Cargando...' : 'Recargar' }}
			</button>
		</div>

		<div class="card-body">
			<div v-if="mensajeAlerta" :class="['alert', tipoAlerta === 'success' ? 'alert-success' : 'alert-danger']">
				{{ mensajeAlerta }}
			</div>

			<div class="table-responsive">
				<table class="table table-striped table-hover align-middle mb-0">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Correo</th>
							<th>Direccion</th>
							<th>Genero</th>
							<th>Fecha nacimiento</th>
							<th>Nacionalidad</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<tr v-if="!usuarios.length && !cargando">
							<td colspan="7" class="text-center text-muted py-4">
								No hay usuarios registrados.
							</td>
						</tr>

						<tr v-for="usuario in usuarios" :key="usuario.id">
							<template v-if="idUsuarioEditando === usuario.id">
								<td>
									<input v-model="formularioEdicion.nombre" type="text" class="form-control form-control-sm" />
								</td>
								<td>
									<input v-model="formularioEdicion.correo" type="email" class="form-control form-control-sm" />
								</td>
								<td>
									<input v-model="formularioEdicion.direccion" type="text" class="form-control form-control-sm" />
								</td>
								<td>
									<select v-model="formularioEdicion.genero" class="form-select form-select-sm">
										<option value="">Seleccione</option>
										<option value="Masculino">Masculino</option>
										<option value="Femenino">Femenino</option>
										<option value="Otro">Otro</option>
									</select>
								</td>
								<td>
									<input v-model="formularioEdicion.fecha_nacimiento" type="date" class="form-control form-control-sm" :max="fechaHoy" />
								</td>
								<td>
									<select v-model="formularioEdicion.nacionalidad" class="form-select form-select-sm">
										<option value="">Seleccione un país</option>
										<option
											v-for="pais in paises"
											:key="pais.alpha2Code"
											:value="pais.numericCode"
										>
											{{ pais.name }}
										</option>
									</select>
								</td>
								<td class="d-flex gap-2">
									<button class="btn btn-sm btn-success" @click="guardarUsuario(usuario.id)" :disabled="guardando">
										{{ guardando ? 'Guardando...' : 'Guardar' }}
									</button>
									<button class="btn btn-sm btn-secondary" @click="cancelarEdicion" :disabled="guardando">
										Cancelar
									</button>
								</td>
							</template>

							<template v-else>
								<td>{{ usuario.nombre }}</td>
								<td>{{ usuario.correo }}</td>
								<td>{{ usuario.direccion || '-' }}</td>
								<td>{{ usuario.genero || '-' }}</td>
								<td>{{ formatearFecha(usuario.fecha_nacimiento) }}</td>
								<td>{{ obtenerNombrePais(usuario.nacionalidad) }}</td>
								<td class="d-flex gap-2">
									<button class="btn btn-sm btn-primary" @click="iniciarEdicion(usuario)">
										Editar
									</button>
									<button class="btn btn-sm btn-danger" @click="eliminarUsuario(usuario.id)">
										Eliminar
									</button>
								</td>
							</template>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>

<script>
import axios from 'axios';

export default {
	name: 'VistaUsuarios',
	data() {
		return {
			usuarios: [],
			paises: [],
			cargando: false,
			guardando: false,
			idUsuarioEditando: null,
			tipoAlerta: '',
			mensajeAlerta: '',
			formularioEdicion: {
				nombre: '',
				correo: '',
				direccion: '',
				genero: '',
				fecha_nacimiento: '',
				nacionalidad: '',
			},
		};
	},
	computed: {
		fechaHoy() {
			const today = new Date();
			const year = today.getFullYear();
			const month = String(today.getMonth() + 1).padStart(2, '0');
			const day = String(today.getDate()).padStart(2, '0');

			return `${year}-${month}-${day}`;
		},
	},
	mounted() {
		this.cargarPaises();
		this.obtenerUsuarios();
	},
	methods: {
		normalizarFechaParaInput(fecha) {
			if (!fecha) {
				return '';
			}

			return String(fecha).split('T')[0];
		},
		formatearFecha(fecha) {
			const fechaNormalizada = this.normalizarFechaParaInput(fecha);

			if (!fechaNormalizada) {
				return '-';
			}

			const [anio, mes, dia] = fechaNormalizada.split('-');

			if (!anio || !mes || !dia) {
				return fechaNormalizada;
			}

			return `${dia}/${mes}/${anio}`;
		},
		obtenerNombrePais(codigoPais) {
			if (!codigoPais) {
				return '-';
			}

			const paisEncontrado = this.paises.find((pais) => String(pais.numericCode) === String(codigoPais));

			return paisEncontrado ? paisEncontrado.name : codigoPais;
		},
		cargarPaises() {
            axios.get('/api/countries')
                .then((respuesta) => {
                    this.paises = Array.isArray(respuesta.data) ? respuesta.data : [];
                })
                .catch((error) => {
                    this.paises = [];
                });
        },
		establecerAlerta(tipo, mensaje) {
			this.tipoAlerta = tipo;
			this.mensajeAlerta = mensaje;
		},
		limpiarAlerta() {
			this.tipoAlerta = '';
			this.mensajeAlerta = '';
		},
		obtenerUsuarios() {
            this.cargando = true;
            this.limpiarAlerta();

			return axios.get('/usuarios/listado')
                .then((response) => {
					this.usuarios = Array.isArray(response.data) ? response.data : [];
                })
                .catch(() => {
                    this.establecerAlerta('danger', 'No se pudo cargar el listado de usuarios.');
                })
                .finally(() => {
                    this.cargando = false;
                });
        },
		iniciarEdicion(usuario) {
			this.limpiarAlerta();
			this.idUsuarioEditando = usuario.id;
			this.formularioEdicion = {
				nombre: usuario.nombre || '',
				correo: usuario.correo || '',
				direccion: usuario.direccion || '',
				genero: usuario.genero || '',
				fecha_nacimiento: this.normalizarFechaParaInput(usuario.fecha_nacimiento),
				nacionalidad: usuario.nacionalidad || '',
			};
		},
		cancelarEdicion() {
			this.idUsuarioEditando = null;
			this.guardando = false;
		},
		extraerErroresServidor(errores) {
			return Object.values(errores || {}).reduce((todosLosErrores, erroresActuales) => todosLosErrores.concat(erroresActuales), []);
		},
		guardarUsuario(idUsuario) {
			this.guardando = true;
			this.limpiarAlerta();

			const payload = {
				nombre: this.formularioEdicion.nombre,
				correo: this.formularioEdicion.correo,
				direccion: this.formularioEdicion.direccion,
				genero: this.formularioEdicion.genero,
				fecha_nacimiento: this.formularioEdicion.fecha_nacimiento,
				nacionalidad: this.formularioEdicion.nacionalidad,
			};

			axios.put(`/usuarios/${idUsuario}`, payload)
				.then((response) => {
					this.establecerAlerta('success', response.data.message || 'Usuario actualizado correctamente.');
					this.idUsuarioEditando = null;
					return this.obtenerUsuarios();
				})
				.catch((error) => {
					if (error.response && error.response.status === 422 && error.response.data.errors) {
						const mensajes = this.extraerErroresServidor(error.response.data.errors);
						this.establecerAlerta('danger', mensajes.join(' '));
					} else {
						const mensajeServidor = error.response && error.response.data && error.response.data.message
							? error.response.data.message
							: 'No se pudo actualizar el usuario.';
						this.establecerAlerta('danger', mensajeServidor);
					}
				})
				.finally(() => {
					this.guardando = false;
				});
		},
		eliminarUsuario(idUsuario) {
			this.limpiarAlerta();

			if (!window.confirm('¿Seguro que deseas eliminar este usuario?')) {
				return;
			}

			axios.delete(`/usuarios/${idUsuario}`)
				.then((response) => {
					this.establecerAlerta('success', response.data.message || 'Usuario eliminado correctamente.');
					return this.obtenerUsuarios();
				})
				.catch((error) => {
					const mensajeServidor = error.response && error.response.data && error.response.data.message
						? error.response.data.message
						: 'No se pudo eliminar el usuario.';
					this.establecerAlerta('danger', mensajeServidor);
				});
		},
	},
};
</script>
