// Clave API de RAWG.io (puedes obtener una gratis registrándote en https://rawg.io/apidocs)
const RAWG_API_KEY = '85db24c5ecba4f809d8929bdbafc6d95';

// Referencias a elementos del DOM
const inputBuscarRAWG = document.getElementById('inputBuscarRAWG');
const btnBuscarRAWG = document.getElementById('btnBuscarRAWG');
const resultadosRAWG = document.getElementById('resultadosRAWG');

// Escuchar el clic en el botón de búsqueda
btnBuscarRAWG.addEventListener('click', async () => {
  const query = inputBuscarRAWG.value.trim();
  if (!query) return;

  btnBuscarRAWG.textContent = 'Cargando...';
  btnBuscarRAWG.disabled = true;

  try {
    // Consulta a la API de RAWG
    const response = await fetch(`https://api.rawg.io/api/games?key=${RAWG_API_KEY}&search=${encodeURIComponent(query)}&page_size=5`);
    const data = await response.json();

    mostrarResultadosRAWG(data.results);
  } catch (error) {
    console.error('Error al consultar RAWG:', error);
    alert('Hubo un error al buscar el juego. Inténtalo de nuevo.');
  } finally {
    btnBuscarRAWG.textContent = '🔍 Buscar';
    btnBuscarRAWG.disabled = false;
  }
});

// Renderizar la lista de resultados de RAWG
function mostrarResultadosRAWG(juegos) {
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

    // Evento al hacer clic en un juego de la lista
    item.addEventListener('click', () => seleccionarJuego(juego));
    resultadosRAWG.appendChild(item);
  });
}

// Actualizamos la función seleccionarJuego para procesar también las plataformas
function seleccionarJuego(juego) {
  const imagen = juego.background_image || 'assets/portada-placeholder.svg';

  // 1. Ocultar la lista de resultados de búsqueda
  resultadosRAWG.classList.add('d-none');

  // 2. Actualizar la tarjeta de vista previa
  document.getElementById('imagenJuegoSeleccionado').src = imagen;
  document.getElementById('tituloJuegoSeleccionado').textContent = juego.name;
  document.getElementById('infoJuegoSeleccionado').textContent = `Año: ${juego.released ? juego.released.split('-')[0] : 'N/A'} · Valoración: ⭐ ${juego.rating || 'N/A'}`;

  // 3. Guardar datos en los campos ocultos
  document.getElementById('rawgGameId').value = juego.id;
  document.getElementById('rawgImageUrl').value = imagen;
  document.getElementById('tituloJuegoOculto').value = juego.name;

  // 4. Filtrar y cargar únicamente las plataformas disponibles para este juego
  actualizarSelectorPlataformas(juego.platforms);

  // 5. Mostrar el botón de cambiar juego
  document.getElementById('btnCambiarJuego').classList.remove('d-none');
}

// Nueva función para poblar dinámicamente el <select> de plataformas
function actualizarSelectorPlataformas(plataformasRawg) {
  const selectPlataforma = document.getElementById('plataformaJuego');
  
  selectPlataforma.innerHTML = '<option value="" selected disabled>Selecciona una plataforma disponible...</option>';

  if (!plataformasRawg || plataformasRawg.length === 0) {
    const option = document.createElement('option');
    option.value = 'PC';
    option.textContent = 'PC / Genérica';
    selectPlataforma.appendChild(option);
  } else {
    plataformasRawg.forEach(p => {
      const nombrePlataforma = p.platform.name;
      const yaExiste = Array.from(selectPlataforma.options).some(opt => opt.value === nombrePlataforma);
      
      if (!yaExiste) {
        const option = document.createElement('option');
        option.value = nombrePlataforma;
        option.textContent = nombrePlataforma;
        selectPlataforma.appendChild(option);
      }
    });
  }

  // Habilitamos el campo una vez poblado
  selectPlataforma.disabled = false;
}

// Botón para cambiar de juego (resetea también el select de plataformas)
document.getElementById('btnCambiarJuego').addEventListener('click', () => {
  resultadosRAWG.classList.remove('d-none');
  
  // Reseteamos y deshabilitamos el selector de plataforma
  const selectPlataforma = document.getElementById('plataformaJuego');
  selectPlataforma.innerHTML = '<option value="" selected disabled>Primero busca y selecciona un juego...</option>';
  selectPlataforma.disabled = true;
});

// --- GESTION DEL USUARIO

// --- REGISTRO ---
const formRegistro = document.getElementById('formRegistro');

if (formRegistro) {
  formRegistro.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nombre = document.getElementById('registroNombre').value.trim();
    const email = document.getElementById('registroEmail').value.trim();
    const password = document.getElementById('registroPassword').value;
    const confirmPassword = document.getElementById('registroConfirmPassword').value;

    // Validación rápida de contraseñas coincidentes
    if (password !== confirmPassword) {
      alert('Las contraseñas no coinciden.');
      return;
    }

    // Preparar datos para enviar por POST
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
        alert('¡Cuenta creada con éxito! Bienvenido, ' + nombre);
        // Ocultar modal y recargar la página para actualizar el estado de la sesión
        const modalRegistroEl = document.getElementById('modalRegistro');
        const modalRegistro = bootstrap.Modal.getInstance(modalRegistroEl);
        modalRegistro.hide();
        window.location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    } catch (error) {
      console.error('Error en el registro:', error);
      alert('Hubo un error de conexión con el servidor.');
    }
  });
}

// --- INICIO DE SESIÓN ---
const formLogin = document.getElementById('formLogin');

if (formLogin) {
  formLogin.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    try {
      const response = await fetch('includes/login.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        alert('¡Bienvenido de nuevo, ' + data.usuario.nombre + '!');
        // Ocultar modal y recargar la página
        const modalLoginEl = document.getElementById('modalLogin');
        const modalLogin = bootstrap.Modal.getInstance(modalLoginEl);
        modalLogin.hide();
        window.location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    } catch (error) {
      console.error('Error en el inicio de sesión:', error);
      alert('Hubo un error de conexión con el servidor.');
    }
  });
}

// --- GESTIÓN DEL CIERRE DE SESIÓN ---
const btnCerrarSesion = document.getElementById('btnCerrarSesion');

if (btnCerrarSesion) {
  btnCerrarSesion.addEventListener('click', async (e) => {
    e.preventDefault();

    try {
      const response = await fetch('includes/logout.php');
      const data = await response.json();

      if (data.success) {
        window.location.reload(); // Recarga la página para mostrar el navbar como invitado
      } else {
        alert('Error al cerrar sesión.');
      }
    } catch (error) {
      console.error('Error de red:', error);
    }
  });
}