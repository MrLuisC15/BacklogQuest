// --- FUNCIÓN GLOBAL DE ALERTAS MODERNAS (Estilo Bootstrap 5) ---
function mostrarAlerta(tipo, mensaje, onClosed = null) {
  const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

  Swal.fire({
    icon: tipo, 
    title: tipo === 'success' ? '¡Éxito!' : (tipo === 'warning' ? 'Aviso' : 'Atención'),
    text: mensaje,
    // Usamos las clases de Bootstrap para que hereden exactamente el mismo fondo que las tarjetas
    customClass: {
      popup: `rounded-4 shadow border border-secondary-subtle p-4 ${isDark ? 'bg-body-tertiary text-body' : 'bg-white text-dark'}`,
      confirmButton: 'btn btn-primary rounded-pill px-4 py-2 fw-semibold',
      icon: 'border-0',
      title: isDark ? 'text-body' : '',
      htmlContainer: isDark ? 'text-body-secondary' : ''
    },
    buttonsStyling: false,
    timer: tipo === 'success' ? 2000 : undefined,
    timerProgressBar: tipo === 'success'
  }).then((result) => {
    if (onClosed) onClosed();
  });
}

// --- GESTIÓN DE BÚSQUEDA RAWG ---
const inputBuscarRAWG = document.getElementById('inputBuscarRAWG');
const btnBuscarRAWG = document.getElementById('btnBuscarRAWG');
const resultadosRAWG = document.getElementById('resultadosRAWG');

if (btnBuscarRAWG) {
  btnBuscarRAWG.addEventListener('click', async () => {
    const query = inputBuscarRAWG.value.trim();
    if (!query) return;

    btnBuscarRAWG.textContent = 'Cargando...';
    btnBuscarRAWG.disabled = true;

    try {
      const response = await fetch(`includes/buscar-igdb.php?search=${encodeURIComponent(query)}`);
      const data = await response.json();
      mostrarResultadosRAWG(data.results);
    } catch (error) {
      console.error('Error al consultar RAWG:', error);
      mostrarAlerta('error', 'Hubo un error al buscar el juego. Inténtalo de nuevo.');
    } finally {
      btnBuscarRAWG.textContent = '🔍 Buscar';
      btnBuscarRAWG.disabled = false;
    }
  });
}

// Renderizar la lista de resultados de RAWG
function mostrarResultadosRAWG(juegos) {
  if (!resultadosRAWG) return;
  
  resultadosRAWG.innerHTML = '';
  resultadosRAWG.classList.remove('d-none');

  if (!juegos || juegos.length === 0) {
    resultadosRAWG.innerHTML = '<div class="p-3 text-muted small text-center">No se encontraron juegos.</div>';
    return;
  }

  juegos.forEach(juego => {
    const item = document.createElement('button');
    item.type = 'button';
    item.className = 'list-group-item list-group-item-action d-flex align-items-center gap-3';
    
    const imagen = juego.background_image || 'assets/portada-placeholder.svg';
    const ano = juego.released ? juego.released.split('-')[0] : 'N/A';

    item.innerHTML = `
      <img src="${imagen}" alt="${juego.name}" class="rounded object-fit-cover" width="70" height="40">
      <div class="flex-grow-1 text-start">
        <h6 class="mb-0 fw-bold">${juego.name}</h6>
        <small class="text-muted">${ano}</small>
      </div>
      <span class="btn btn-sm btn-outline-primary rounded-pill">Seleccionar</span>
    `;

    item.addEventListener('click', () => seleccionarJuego(juego));
    resultadosRAWG.appendChild(item);
  });
}

// Seleccionar un juego de los resultados
function seleccionarJuego(juego) {
  const imagen = juego.background_image || 'assets/portada-placeholder.svg';

  if (resultadosRAWG) {
    resultadosRAWG.classList.add('d-none');
  }

  const imgEl = document.getElementById('imagenJuegoSeleccionado');
  const tituloEl = document.getElementById('tituloJuegoSeleccionado');
  const infoEl = document.getElementById('infoJuegoSeleccionado');

  if (imgEl) imgEl.src = imagen;
  if (tituloEl) tituloEl.textContent = juego.name;
  if (infoEl) infoEl.textContent = `Año: ${juego.released ? juego.released.split('-')[0] : 'N/A'} · Valoración: ⭐ ${juego.rating || 'N/A'}`;

  const rawgGameId = document.getElementById('rawgGameId');
  const rawgImageUrl = document.getElementById('rawgImageUrl');
  const tituloJuegoOculto = document.getElementById('tituloJuegoOculto');
  const rawgPlaytime = document.getElementById('rawgPlaytime');

  if (rawgGameId) rawgGameId.value = juego.id;
  if (rawgImageUrl) rawgImageUrl.value = imagen;
  if (tituloJuegoOculto) tituloJuegoOculto.value = juego.name;
  if (rawgPlaytime) rawgPlaytime.value = juego.playtime || 0;

  actualizarSelectorPlataformas(juego.platforms);

  const btnCambiarJuego = document.getElementById('btnCambiarJuego');
  if (btnCambiarJuego) {
    btnCambiarJuego.classList.remove('d-none');
  }
}

// Poblar dinámicamente el <select> de plataformas
function actualizarSelectorPlataformas(plataformasRawg) {
  const selectPlataforma = document.getElementById('plataformaJuego');
  if (!selectPlataforma) return;
  
  selectPlataforma.innerHTML = '<option value="" selected disabled>Selecciona una plataforma disponible...</option>';

  if (!plataformasRawg || plataformasRawg.length === 0) {
    const option = document.createElement('option');
    option.value = 'PC';
    option.textContent = 'PC / Genérica';
    selectPlataforma.appendChild(option);
  } else {
    plataformasRawg.forEach(p => {
      const nombrePlataforma = p.name;
      if (!nombrePlataforma) return;
      
      const yaExiste = Array.from(selectPlataforma.options).some(opt => opt.value === nombrePlataforma);
      
      if (!yaExiste) {
        const option = document.createElement('option');
        option.value = nombrePlataforma;
        option.textContent = nombrePlataforma;
        selectPlataforma.appendChild(option);
      }
    });
  }

  selectPlataforma.disabled = false;
}

// Botón para cambiar de juego
const btnCambiarJuego = document.getElementById('btnCambiarJuego');
if (btnCambiarJuego) {
  btnCambiarJuego.addEventListener('click', () => {
    if (resultadosRAWG) {
      resultadosRAWG.classList.remove('d-none');
    }
    
    const selectPlataforma = document.getElementById('plataformaJuego');
    if (selectPlataforma) {
      selectPlataforma.innerHTML = '<option value="" selected disabled>Primero busca y selecciona un juego...</option>';
      selectPlataforma.disabled = true;
    }
  });
}


// --- GESTIÓN DE USUARIOS ---

// Registro
const formRegistro = document.getElementById('formRegistro');

if (formRegistro) {
  formRegistro.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nombre = document.getElementById('registroNombre').value.trim();
    const email = document.getElementById('registroEmail').value.trim();
    const password = document.getElementById('registroPassword').value;
    const confirmPassword = document.getElementById('registroConfirmPassword').value;

    if (password !== confirmPassword) {
      mostrarAlerta('warning', 'Las contraseñas no coinciden.');
      return;
    }

    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('email', email);
    formData.append('password', password);

    try {
      const response = await fetch('includes/registro.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        // 1. Mostrar mensaje de éxito indicando que revise su correo
        mostrarAlerta('success', data.message || '¡Cuenta creada con éxito! Revisa tu correo para verificarla.');
        
        // 2. Limpiar el formulario de registro
        formRegistro.reset();

        // 3. Cambiar automáticamente a la pestaña de Iniciar Sesión (usando Bootstrap Tabs)
        const loginTabEl = document.getElementById('login-tab');
        if (loginTabEl) {
          const tab = new bootstrap.Tab(loginTabEl);
          tab.show();
        }
      } else {
        mostrarAlerta('error', data.message);
      }
    } catch (error) {
      console.error('Error en el registro:', error);
      mostrarAlerta('error', 'Hubo un error de conexión con el servidor.');
    }
  });
}

// Inicio de sesión (Sin alerta molesta: redirige al instante)
const formLogin = document.getElementById('formLogin');

if (formLogin) {
  formLogin.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    const recordar = document.getElementById('recordarSesion') ? document.getElementById('recordarSesion').checked : false;

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    formData.append('recordar', recordar ? '1' : '0');

    try {
      const response = await fetch('includes/login.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        // Redirección limpia e inmediata sin popups invasivos
        window.location.href = 'biblioteca.php';
      } else {
        mostrarAlerta('error', data.message);
      }
    } catch (error) {
      console.error('Error en el inicio de sesión:', error);
      mostrarAlerta('error', 'Hubo un error de conexión con el servidor.');
    }
  });
}

// Cierre de sesión (Sin alerta molesta: redirige al instante)
const btnCerrarSesion = document.getElementById('btnCerrarSesion');

if (btnCerrarSesion) {
  btnCerrarSesion.addEventListener('click', async (e) => {
    e.preventDefault();

    try {
      const response = await fetch('includes/logout.php');
      const data = await response.json();

      if (data.success) {
        window.location.href = 'index.php';
      } else {
        mostrarAlerta('error', 'Error al cerrar sesión.');
      }
    } catch (error) {
      console.error('Error de red:', error);
      mostrarAlerta('error', 'Error de conexión con el servidor.');
    }
  });
}


// --- GESTIÓN PARA GUARDAR UN JUEGO (Recarga directa sin popup) ---
const formAnadirJuego = document.getElementById('formAnadirJuego');

if (formAnadirJuego) {
  formAnadirJuego.addEventListener('submit', async (e) => {
    e.preventDefault();

    const rawgId = document.getElementById('rawgGameId').value;
    const titulo = document.getElementById('tituloJuegoOculto').value;
    const imagenUrl = document.getElementById('rawgImageUrl').value;
    const plataforma = document.getElementById('plataformaJuego').value;
    const estado = document.getElementById('estadoJuego') ? document.getElementById('estadoJuego').value : 'Pendiente';
    const horas = document.getElementById('horasJuego') ? document.getElementById('horasJuego').value : 0;
    const duracionEstimada = document.getElementById('rawgPlaytime') ? document.getElementById('rawgPlaytime').value : 0;
    const notasCheckpoint = document.getElementById('notaJuego') ? document.getElementById('notaJuego').value : '';

    if (!rawgId || !plataforma) {
      mostrarAlerta('warning', 'Por favor, busca y selecciona un juego de la lista y elige una plataforma.');
      return;
    }

    const formData = new FormData();
    formData.append('rawg_id', rawgId);
    formData.append('titulo', titulo);
    formData.append('imagen_url', imagenUrl);
    formData.append('plataforma', plataforma);
    formData.append('estado', estado);
    formData.append('horas', horas);
    formData.append('duracion_estimada_horas', duracionEstimada);
    formData.append('notas_checkpoint', notasCheckpoint);

    try {
      const response = await fetch('includes/anadir-juego.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        const modalEl = document.getElementById('modalAnadirJuego');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    
        // Recarga instantánea sin popup de éxito
        window.location.reload();
      } else {
        mostrarAlerta('error', data.message);
      }
    } catch (error) {
      console.error('Error al guardar el juego:', error);
      mostrarAlerta('error', 'Hubo un error de conexión con el servidor.');
    }
  });
}


// --- GESTIÓN PARA ACTUALIZAR PROGRESO DEL JUEGO (Recarga directa sin popup) ---
const modalActualizarJuego = document.getElementById('modalActualizarJuego');

if (modalActualizarJuego) {
  modalActualizarJuego.addEventListener('show.bs.modal', (event) => {
    const button = event.relatedTarget;
    
    document.getElementById('updateBibliotecaId').value = button.getAttribute('data-id');
    document.getElementById('tituloJuegoActualizar').textContent = button.getAttribute('data-titulo');
    document.getElementById('updateEstado').value = button.getAttribute('data-estado');
    document.getElementById('updateHoras').value = button.getAttribute('data-horas');
    document.getElementById('updateNota').value = button.getAttribute('data-nota');
  });

  const formActualizarJuego = document.getElementById('formActualizarJuego');
  formActualizarJuego.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(formActualizarJuego);

    try {
      const response = await fetch('includes/actualizar-juego.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        window.location.reload();
      } else {
        mostrarAlerta('error', data.message);
      }
    } catch (error) {
      console.error('Error al actualizar:', error);
      mostrarAlerta('error', 'Error de conexión con el servidor.');
    }
  });
}


// --- GESTIÓN PARA ELIMINAR JUEGO DE LA BIBLIOTECA (Recarga directa sin popup) ---
const modalEliminarJuego = document.getElementById('modalEliminarJuego');

if (modalEliminarJuego) {
  modalEliminarJuego.addEventListener('show.bs.modal', (event) => {
    const button = event.relatedTarget;
    document.getElementById('eliminarBibliotecaId').value = button.getAttribute('data-id');
    document.getElementById('tituloJuegoEliminar').textContent = button.getAttribute('data-titulo');
  });

  const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
  if (btnConfirmarEliminar) {
    btnConfirmarEliminar.onclick = async () => {
      const bibliotecaId = document.getElementById('eliminarBibliotecaId').value;

      const formData = new FormData();
      formData.append('biblioteca_id', bibliotecaId);

      try {
        const response = await fetch('includes/eliminar-juego.php', {
          method: 'POST',
          body: formData
        });
        const data = await response.json();

        if (data.success) {
          window.location.reload();
        } else {
          mostrarAlerta('error', data.message);
        }
      } catch (error) {
        console.error('Error al eliminar:', error);
        mostrarAlerta('error', 'Error de conexión con el servidor.');
      }
    };
  }
}

// Mostrar desplegable/popup con las notas del juego (Este sí se mantiene porque es un contenido de lectura que el usuario solicita explícitamente)
document.addEventListener('click', function (e) {
  const btnNota = e.target.closest('.btn-ver-nota');
  if (!btnNota) return;

  const tituloJuego = btnNota.dataset.titulo;
  const notaJuego = btnNota.dataset.nota;

  Swal.fire({
    title: `<span class="fs-5 fw-bold text-body">📝 Última nota: ${tituloJuego}</span>`,
    html: `<div class="text-start bg-light p-3 rounded-3 border text-secondary" style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;">${notaJuego}</div>`,
    showCloseButton: true,
    showConfirmButton: false,
    customClass: {
      popup: 'rounded-4 shadow',
      closeButton: 'border-0 shadow-none'
    }
  });
});


// ==========================================
// GESTIÓN DEL MODO OSCURO (DARK MODE)
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
  const btnToggle = document.getElementById('btnToggleTheme');
  const themeIcon = document.getElementById('themeIcon');

  // Sincronizar el icono según el tema actual que ya viene aplicado del <head>
  const currentTheme = document.documentElement.getAttribute('data-bs-theme');
  if (themeIcon) {
    themeIcon.textContent = currentTheme === 'dark' ? '☀️' : '🌙';
  }

  // Evento de clic en el botón de cambio de tema
  if (btnToggle) {
    btnToggle.addEventListener('click', () => {
      const activeTheme = document.documentElement.getAttribute('data-bs-theme');
      const newTheme = activeTheme === 'dark' ? 'light' : 'dark';

      // Cambiar atributo y guardar preferencia
      document.documentElement.setAttribute('data-bs-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      
      if (themeIcon) {
        themeIcon.textContent = newTheme === 'dark' ? '☀️' : '🌙';
      }
    });
  }
});


// --- GESTIÓN DE CAMBIO RÁPIDO DE ESTADO EN TARJETAS (Recarga directa sin popup) ---
document.addEventListener('click', async (e) => {
  const btnEstadoRapido = e.target.closest('.btn-estado-rapido');
  if (!btnEstadoRapido) return;

  const bibliotecaId = btnEstadoRapido.dataset.id;
  const nuevoEstado = btnEstadoRapido.dataset.siguienteEstado;

  try {
    const formData = new FormData();
    formData.append('biblioteca_id', bibliotecaId);
    formData.append('nuevo_estado', nuevoEstado);

    const response = await fetch('includes/cambiar-estado-rapido.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();

    if (data.success) {
      window.location.reload();
    } else {
      mostrarAlerta('error', data.message);
    }
  } catch (error) {
    console.error('Error al cambiar el estado:', error);
    mostrarAlerta('error', 'Error de conexión con el servidor.');
  }
});


// --- GESTIÓN DE RECUPERACIÓN DE CONTRASEÑA (Mantiene alerta porque no redirige y el usuario debe leer el aviso) ---
const formRecuperar = document.getElementById('formRecuperar');
if (formRecuperar) {
  formRecuperar.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('emailRecuperar').value;

    try {
      const formData = new FormData();
      formData.append('email', email);

      const response = await fetch('includes/procesar-recuperacion.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      const modalEl = document.getElementById('modalRecuperar');
      const modal = bootstrap.Modal.getInstance(modalEl);
      if (modal) modal.hide();

      if (data.success) {
        mostrarAlerta('success', data.message);
        formRecuperar.reset();
      } else {
        mostrarAlerta('error', data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      mostrarAlerta('error', 'Error de conexión con el servidor.');
    }
  });
}