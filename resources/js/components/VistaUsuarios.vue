<template>
	<div class="card shadow mt-4">
		<div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
			<span>Usuarios Registrados</span>
			<div class="d-flex gap-2">
				<button class="btn btn-sm btn-success" @click="iniciarCreacion" :disabled="cargando || creando || guardando">
					{{ mostrandoFormularioNuevo ? 'Ocultar formulario' : 'Nuevo usuario' }}
				</button>
				<button class="btn btn-sm btn-light" @click="obtenerUsuarios" :disabled="cargando || creando || guardando">
					{{ cargando ? 'Cargando...' : 'Recargar' }}
				</button>
			</div>
		</div>

		<div class="card-body">
			<div v-if="mensajeAlerta" :class="['alert', tipoAlerta === 'success' ? 'alert-success' : 'alert-danger']">
				{{ mensajeAlerta }}
			</div>

			<div v-if="mostrandoFormularioNuevo" class="border rounded p-3 mb-4 bg-light">
				<h6 class="mb-3">Crear nuevo usuario</h6>
				<div class="row g-3">
					<div class="col-md-6">
						<label class="form-label">Nombre</label>
						<input v-model="formularioNuevo.nombre" type="text" class="form-control" />
					</div>
					<div class="col-md-6">
						<label class="form-label">Correo</label>
						<input v-model="formularioNuevo.correo" type="email" class="form-control" />
					</div>
					<div class="col-md-6">
						<label class="form-label">Dirección</label>
						<input v-model="formularioNuevo.direccion" type="text" class="form-control" />
					</div>
					<div class="col-md-6">
						<label class="form-label">Género</label>
						<select v-model="formularioNuevo.genero" class="form-select">
							<option value="">Seleccione</option>
							<option value="Masculino">Masculino</option>
							<option value="Femenino">Femenino</option>
							<option value="Otro">Otro</option>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label">Fecha nacimiento</label>
						<input v-model="formularioNuevo.fecha_nacimiento" type="date" class="form-control" :max="fechaHoy" />
					</div>
					<div class="col-md-6">
						<label class="form-label">Nacionalidad</label>
						<select v-model="formularioNuevo.nacionalidad" class="form-select">
							<option value="">Seleccione un país</option>
							<option
								v-for="pais in paises"
								:key="pais.alpha2Code"
								:value="pais.numericCode"
							>
								{{ pais.name }}
							</option>
						</select>
					</div>
					<div class="col-md-6">
						<label class="form-label">Contraseña</label>
						<input v-model="formularioNuevo.contrasena" type="password" class="form-control" />
					</div>
					<div class="col-md-6">
						<label class="form-label">Confirmar contraseña</label>
						<input v-model="formularioNuevo.confirmacion_contrasena" type="password" class="form-control" />
					</div>
				</div>
				<div class="d-flex gap-2 mt-3">
					<button class="btn btn-success" @click="crearUsuario" :disabled="creando || guardando || cargando">
						{{ creando ? 'Creando...' : 'Crear usuario' }}
					</button>
					<button class="btn btn-secondary" @click="cancelarCreacion" :disabled="creando">
						Cancelar
					</button>
				</div>
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
			creando: false,
			guardando: false,
			mostrandoFormularioNuevo: false,
			idUsuarioEditando: null,
			tipoAlerta: '',
			mensajeAlerta: '',
			formularioNuevo: {
				nombre: '',
				correo: '',
				direccion: '',
				genero: '',
				fecha_nacimiento: '',
				nacionalidad: '',
				contrasena: '',
				confirmacion_contrasena: '',
			},
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
		limpiarFormularioNuevo() {
			this.formularioNuevo = {
				nombre: '',
				correo: '',
				direccion: '',
				genero: '',
				fecha_nacimiento: '',
				nacionalidad: '',
				contrasena: '',
				confirmacion_contrasena: '',
			};
		},
		iniciarCreacion() {
			this.limpiarAlerta();

			if (this.mostrandoFormularioNuevo) {
				this.cancelarCreacion();
				return;
			}

			this.idUsuarioEditando = null;
			this.mostrandoFormularioNuevo = true;
		},
		cancelarCreacion() {
			this.mostrandoFormularioNuevo = false;
			this.creando = false;
			this.limpiarFormularioNuevo();
		},
		validarFormularioNuevo() {
			const errores = [];
			const regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

			if (!this.formularioNuevo.nombre.trim()) {
				errores.push('El nombre es obligatorio.');
			}

			if (!this.formularioNuevo.correo.trim()) {
				errores.push('El correo es obligatorio.');
			} else if (!regexCorreo.test(this.formularioNuevo.correo.trim())) {
				errores.push('El correo debe terminar con un dominio, por ejemplo: usuario@correo.com.');
			}

			if (this.formularioNuevo.fecha_nacimiento && this.formularioNuevo.fecha_nacimiento > this.fechaHoy) {
				errores.push('La fecha de nacimiento no puede ser mayor a hoy.');
			}

			if (!this.formularioNuevo.contrasena) {
				errores.push('La contraseña es obligatoria.');
			} else if (this.formularioNuevo.contrasena.length < 6) {
				errores.push('La contraseña debe tener al menos 6 caracteres.');
			}

			if (this.formularioNuevo.contrasena !== this.formularioNuevo.confirmacion_contrasena) {
				errores.push('La confirmación de contraseña no coincide.');
			}

			return errores;
		},
		crearUsuario() {
			this.creando = true;
			this.limpiarAlerta();

			const erroresValidacion = this.validarFormularioNuevo();

			if (erroresValidacion.length) {
				this.establecerAlerta('danger', erroresValidacion.join(' '));
				this.creando = false;
				return;
			}

			const payload = {
				nombre: this.formularioNuevo.nombre,
				correo: this.formularioNuevo.correo,
				direccion: this.formularioNuevo.direccion,
				genero: this.formularioNuevo.genero,
				fecha_nacimiento: this.formularioNuevo.fecha_nacimiento,
				nacionalidad: this.formularioNuevo.nacionalidad,
				contrasena: this.formularioNuevo.contrasena,
				confirmacion_contrasena: this.formularioNuevo.confirmacion_contrasena,
			};

			axios.post('/usuarios', payload)
				.then((response) => {
					this.establecerAlerta('success', response.data.message || 'Usuario creado correctamente.');
					this.cancelarCreacion();
					return this.obtenerUsuarios();
				})
				.catch((error) => {
					if (error.response && error.response.status === 422 && error.response.data.errors) {
						const mensajes = this.extraerErroresServidor(error.response.data.errors);
						this.establecerAlerta('danger', mensajes.join(' '));
					} else {
						const mensajeServidor = error.response && error.response.data && error.response.data.message
							? error.response.data.message
							: 'No se pudo crear el usuario.';
						this.establecerAlerta('danger', mensajeServidor);
					}
				})
				.finally(() => {
					this.creando = false;
				});
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
