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
			<div v-if="mostrandoFormularioNuevo" class="border rounded p-3 mb-4 bg-light">
				<h6 class="mb-3">Crear nuevo usuario</h6>
				<div class="row g-3">
					<div class="col-md-6">
						<label for="nuevo_nombre" class="form-label">Nombre</label>
						<input id="nuevo_nombre" name="nombre" v-model="formularioNuevo.nombre" type="text" class="form-control" />
					</div>
					<div class="col-md-6">
						<label for="nuevo_correo" class="form-label">Correo</label>
						<input id="nuevo_correo" name="correo" v-model="formularioNuevo.correo" type="email" class="form-control" />
					</div>
					<div class="col-md-6">
						<label for="nuevo_direccion" class="form-label">Dirección</label>
						<input id="nuevo_direccion" name="direccion" v-model="formularioNuevo.direccion" type="text" class="form-control" />
					</div>
					<div class="col-md-6">
						<label for="nuevo_genero" class="form-label">Género</label>
						<select id="nuevo_genero" name="genero" v-model="formularioNuevo.genero" class="form-select">
							<option value="">Seleccione</option>
							<option value="Masculino">Masculino</option>
							<option value="Femenino">Femenino</option>
							<option value="Otro">Otro</option>
						</select>
					</div>
					<div class="col-md-6">
						<label for="nuevo_fecha_nacimiento" class="form-label">Fecha nacimiento</label>
						<input id="nuevo_fecha_nacimiento" name="fecha_nacimiento" v-model="formularioNuevo.fecha_nacimiento" type="date" class="form-control" :max="fechaHoy" />
					</div>
					<div class="col-md-6">
						<label for="nuevo_nacionalidad" class="form-label">Nacionalidad</label>
						<select id="nuevo_nacionalidad" name="nacionalidad" v-model="formularioNuevo.nacionalidad" class="form-select">
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
						<label for="nuevo_contrasena" class="form-label">Contraseña</label>
						<input id="nuevo_contrasena" name="contrasena" v-model="formularioNuevo.contrasena" type="password" class="form-control" />
					</div>
					<div class="col-md-6">
						<label for="nuevo_confirmacion_contrasena" class="form-label">Confirmar contraseña</label>
						<input id="nuevo_confirmacion_contrasena" name="confirmacion_contrasena" v-model="formularioNuevo.confirmacion_contrasena" type="password" class="form-control" />
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

			<div class="row g-3">
				<div :class="idUsuarioEditando ? 'col-lg-8' : 'col-12'">
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

								<tr v-for="usuario in usuarios" :key="usuario.id" :class="{ 'table-active': idUsuarioEditando === usuario.id }">
									<td>{{ usuario.nombre }}</td>
									<td>{{ usuario.correo }}</td>
									<td>{{ usuario.direccion || '-' }}</td>
									<td>{{ usuario.genero || '-' }}</td>
									<td>{{ formatearFecha(usuario.fecha_nacimiento) }}</td>
									<td>{{ obtenerNombrePais(usuario.nacionalidad) }}</td>
									<td class="d-flex gap-2">
										<button class="btn btn-sm btn-primary" @click="iniciarEdicion(usuario)" :disabled="guardando">
											Editar
										</button>
										<button class="btn btn-sm btn-danger" @click="eliminarUsuario(usuario.id)" :disabled="guardando">
											Eliminar
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div v-if="idUsuarioEditando" class="col-lg-4">
					<div class="border rounded p-3 bg-light h-100">
						<h6 class="mb-3">Editar usuario</h6>
						<div class="row g-2">
							<div class="col-12">
								<label for="edicion_nombre" class="form-label mb-1">Nombre</label>
								<input id="edicion_nombre" name="nombre" v-model="formularioEdicion.nombre" type="text" class="form-control form-control-sm" />
							</div>
							<div class="col-12">
								<label for="edicion_correo" class="form-label mb-1">Correo</label>
								<input id="edicion_correo" name="correo" v-model="formularioEdicion.correo" type="email" class="form-control form-control-sm" />
							</div>
							<div class="col-12">
								<label for="edicion_direccion" class="form-label mb-1">Dirección</label>
								<input id="edicion_direccion" name="direccion" v-model="formularioEdicion.direccion" type="text" class="form-control form-control-sm" />
							</div>
							<div class="col-12">
								<label for="edicion_genero" class="form-label mb-1">Género</label>
								<select id="edicion_genero" name="genero" v-model="formularioEdicion.genero" class="form-select form-select-sm">
									<option value="">Seleccione</option>
									<option value="Masculino">Masculino</option>
									<option value="Femenino">Femenino</option>
									<option value="Otro">Otro</option>
								</select>
							</div>
							<div class="col-12">
								<label for="edicion_fecha_nacimiento" class="form-label mb-1">Fecha nacimiento</label>
								<input id="edicion_fecha_nacimiento" name="fecha_nacimiento" v-model="formularioEdicion.fecha_nacimiento" type="date" class="form-control form-control-sm" :max="fechaHoy" />
							</div>
							<div class="col-12">
								<label for="edicion_nacionalidad" class="form-label mb-1">Nacionalidad</label>
								<select id="edicion_nacionalidad" name="nacionalidad" v-model="formularioEdicion.nacionalidad" class="form-select form-select-sm">
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
							<div class="col-12">
								<label for="edicion_contrasena" class="form-label mb-1">Nueva contraseña</label>
								<input id="edicion_contrasena" name="contrasena" v-model="formularioEdicion.contrasena" type="password" class="form-control form-control-sm" placeholder="Opcional" />
							</div>
							<div class="col-12">
								<label for="edicion_contrasena_confirmation" class="form-label mb-1">Confirmar contraseña</label>
								<input id="edicion_contrasena_confirmation" name="contrasena_confirmation" v-model="formularioEdicion.contrasena_confirmation" type="password" class="form-control form-control-sm" />
							</div>
						</div>
						<div class="d-flex gap-2 mt-3">
							<button class="btn btn-success btn-sm" @click="guardarUsuario(idUsuarioEditando)" :disabled="guardando">
								{{ guardando ? 'Guardando...' : 'Guardar cambios' }}
							</button>
							<button class="btn btn-secondary btn-sm" @click="cancelarEdicion" :disabled="guardando">
								Cancelar
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import {
	REGEX_CORREO_CON_DOMINIO,
	crearFormularioNuevoUsuarioVacio,
	extraerErroresServidor,
	obtenerFechaHoyISO,
	obtenerPaises,
} from '../utils/usuarioFormUtils';

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
			formularioNuevo: crearFormularioNuevoUsuarioVacio(),
			formularioEdicion: {
				nombre: '',
				correo: '',
				direccion: '',
				genero: '',
				fecha_nacimiento: '',
				nacionalidad: '',
				contrasena: '',
				contrasena_confirmation: '',
			},
		};
	},
	computed: {
		fechaHoy() {
			return obtenerFechaHoyISO();
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
		mostrarSwal({ icon, title, text, html, timer = null }) {
			return Swal.fire({
				icon,
				title,
				text,
				html,
				timer,
				showConfirmButton: timer === null,
			});
		},
		cargarPaises() {
			return obtenerPaises().then((paises) => {
				this.paises = paises;
			});
		},
		limpiarFormularioNuevo() {
			this.formularioNuevo = crearFormularioNuevoUsuarioVacio();
		},
		iniciarCreacion() {
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

			if (!this.formularioNuevo.nombre.trim()) {
				errores.push('El nombre es obligatorio.');
			}

			if (!this.formularioNuevo.correo.trim()) {
				errores.push('El correo es obligatorio.');
			} else if (!REGEX_CORREO_CON_DOMINIO.test(this.formularioNuevo.correo.trim())) {
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

			const erroresValidacion = this.validarFormularioNuevo();

			if (erroresValidacion.length) {
				this.mostrarSwal({
					icon: 'error',
					title: 'Revisa el formulario',
					html: erroresValidacion.map((error) => `<div>${error}</div>`).join(''),
				});
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
					this.mostrarSwal({
						icon: 'success',
						title: 'Usuario creado',
						text: response.data.message || 'Usuario creado correctamente.',
						timer: 1800,
					});
					this.cancelarCreacion();
					return this.obtenerUsuarios();
				})
				.catch((error) => {
					if (error.response && error.response.status === 422 && error.response.data.errors) {
						const mensajes = extraerErroresServidor(error.response.data.errors);
						this.mostrarSwal({
							icon: 'error',
							title: 'No se pudo crear el usuario',
							html: mensajes.map((mensaje) => `<div>${mensaje}</div>`).join(''),
						});
					} else {
						const mensajeServidor = error.response && error.response.data && error.response.data.message
							? error.response.data.message
							: 'No se pudo crear el usuario.';
						this.mostrarSwal({
							icon: 'error',
							title: 'Error',
							text: mensajeServidor,
						});
					}
				})
				.finally(() => {
					this.creando = false;
				});
		},
		obtenerUsuarios() {
			this.cargando = true;

			return axios.get('/usuarios/listado')
				.then((response) => {
					this.usuarios = Array.isArray(response.data) ? response.data : [];
				})
				.catch(() => {
					this.mostrarSwal({
						icon: 'error',
						title: 'Error',
						text: 'No se pudo cargar el listado de usuarios.',
					});
				})
				.finally(() => {
					this.cargando = false;
				});
		},
		iniciarEdicion(usuario) {
			this.idUsuarioEditando = usuario.id;
			this.formularioEdicion = {
				nombre: usuario.nombre || '',
				correo: usuario.correo || '',
				direccion: usuario.direccion || '',
				genero: usuario.genero || '',
				fecha_nacimiento: this.normalizarFechaParaInput(usuario.fecha_nacimiento),
				nacionalidad: usuario.nacionalidad || '',
				contrasena: '',
				contrasena_confirmation: '',
			};
		},
		cancelarEdicion() {
			this.idUsuarioEditando = null;
			this.guardando = false;
		},
		guardarUsuario(idUsuario) {
			this.guardando = true;

			const payload = {
				nombre: this.formularioEdicion.nombre,
				correo: this.formularioEdicion.correo,
				direccion: this.formularioEdicion.direccion,
				genero: this.formularioEdicion.genero,
				fecha_nacimiento: this.formularioEdicion.fecha_nacimiento,
				nacionalidad: this.formularioEdicion.nacionalidad,
			};

			if (this.formularioEdicion.contrasena) {
				payload.contrasena = this.formularioEdicion.contrasena;
				payload.contrasena_confirmation = this.formularioEdicion.contrasena_confirmation;
			}

			axios.put(`/usuarios/${idUsuario}`, payload)
				.then((response) => {
					this.mostrarSwal({
						icon: 'success',
						title: 'Usuario actualizado',
						text: response.data.message || 'Usuario actualizado correctamente.',
						timer: 1800,
					});
					this.idUsuarioEditando = null;
					return this.obtenerUsuarios();
				})
				.catch((error) => {
					if (error.response && error.response.status === 422 && error.response.data.errors) {
						const mensajes = extraerErroresServidor(error.response.data.errors);
						this.mostrarSwal({
							icon: 'error',
							title: 'No se pudo actualizar el usuario',
							html: mensajes.map((mensaje) => `<div>${mensaje}</div>`).join(''),
						});
					} else {
						const mensajeServidor = error.response && error.response.data && error.response.data.message
							? error.response.data.message
							: 'No se pudo actualizar el usuario.';
						this.mostrarSwal({
							icon: 'error',
							title: 'Error',
							text: mensajeServidor,
						});
					}
				})
				.finally(() => {
					this.guardando = false;
				});
		},
		eliminarUsuario(idUsuario) {
				Swal.fire({
				icon: 'warning',
				title: 'Eliminar usuario',
				text: '¿Seguro que deseas eliminar este usuario?',
				showCancelButton: true,
				confirmButtonText: 'Sí, eliminar',
					cancelButtonText: 'Cancelar',
				confirmButtonColor: '#d33',
				}).then((resultado) => {
					if (!resultado.isConfirmed) {
						return;
					}

					axios.delete(`/usuarios/${idUsuario}`)
						.then((response) => {
							this.mostrarSwal({
								icon: 'success',
								title: 'Usuario eliminado',
								text: response.data.message || 'Usuario eliminado correctamente.',
								timer: 1800,
							});
							return this.obtenerUsuarios();
						})
						.catch((error) => {
							const mensajeServidor = error.response && error.response.data && error.response.data.message
								? error.response.data.message
								: 'No se pudo eliminar el usuario.';
							this.mostrarSwal({
								icon: 'error',
								title: 'Error',
								text: mensajeServidor,
							});
						});
				});
		},
	},
};

</script>
