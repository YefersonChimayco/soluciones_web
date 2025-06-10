// Mostrar el popup de carga
function mostrarPopupCarga() {
    const popup = document.getElementById('popup-carga');
    if (popup) {
        popup.style.display = 'flex';
    }
}
// Ocultar el popup de carga
function ocultarPopupCarga() {
    const popup = document.getElementById('popup-carga');
    if (popup) {
        popup.style.display = 'none';
    }
}
//funcion en caso de session acudacada
async function alerta_sesion() {
    Swal.fire({
        type: 'error',
        title: 'Error de Sesión',
        text: "Sesión Caducada, Por favor inicie sesión",
        confirmButtonClass: 'btn btn-confirm mt-2',
        footer: '',
        timer: 1000
    });
    location.replace(base_url + "login");
}
// cargar elementos de menu
async function cargar_institucion_menu(id_ies = 0) {
    const formData = new FormData();
    formData.append('sesion', session_session);
    formData.append('token', token_token);
    try {
        let respuesta = await fetch(base_url_server + 'src/control/Institucion.php?tipo=listar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        let json = await respuesta.json();
        if (json.status) {
            let datos = json.contenido;
            let contenido = '';
            let sede = '';
            datos.forEach(item => {
                if (id_ies == item.id) {
                    sede = item.nombre;
                }
                contenido += `<button href="javascript:void(0);" class="dropdown-item notify-item" onclick="actualizar_ies_menu(${item.id});">${item.nombre}</button>`;
            });
            document.getElementById('contenido_menu_ies').innerHTML = contenido;
            document.getElementById('menu_ies').innerHTML = sede;
        }
        //console.log(respuesta);
    } catch (e) {
        console.log("Error al cargar categorias" + e);
    }

}
async function cargar_datos_menu(sede) {
    cargar_institucion_menu(sede);
}
// actualizar elementos del menu
async function actualizar_ies_menu(id) {
    const formData = new FormData();
    formData.append('id_ies', id);
    try {
        let respuesta = await fetch(base_url + 'src/control/sesion_cliente.php?tipo=actualizar_ies_sesion', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        let json = await respuesta.json();
        if (json.status) {
            location.reload();
        }
        //console.log(respuesta);
    } catch (e) {
        console.log("Error al cargar instituciones" + e);
    }
}
function generar_paginacion(total, cantidad_mostrar) {
    let actual = document.getElementById('pagina').value;
    let paginas = Math.ceil(total / cantidad_mostrar);
    let paginacion = '<li class="page-item';
    if (actual == 1) {
        paginacion += ' disabled';
    }
    paginacion += ' "><button class="page-link waves-effect" onclick="numero_pagina(1);">Inicio</button></li>';
    paginacion += '<li class="page-item ';
    if (actual == 1) {
        paginacion += ' disabled';
    }
    paginacion += '"><button class="page-link waves-effect" onclick="numero_pagina(' + (actual - 1) + ');">Anterior</button></li>';
    if (actual > 4) {
        var iin = (actual - 2);
    } else {
        var iin = 1;
    }
    for (let index = iin; index <= paginas; index++) {
        if ((paginas - 7) > index) {
            var n_n = iin + 5;
        }
        if (index == n_n) {
            var nn = actual + 1;
            paginacion += '<li class="page-item"><button class="page-link" onclick="numero_pagina(' + nn + ')">...</button></li>';
            index = paginas - 2;
        }
        paginacion += '<li class="page-item ';
        if (actual == index) {
            paginacion += "active";
        }
        paginacion += '" ><button class="page-link" onclick="numero_pagina(' + index + ');">' + index + '</button></li>';
    }
    paginacion += '<li class="page-item ';
    if (actual >= paginas) {
        paginacion += "disabled";
    }
    paginacion += '"><button class="page-link" onclick="numero_pagina(' + (parseInt(actual) + 1) + ');">Siguiente</button></li>';

    paginacion += '<li class="page-item ';
    if (actual >= paginas) {
        paginacion += "disabled";
    }
    paginacion += '"><button class="page-link" onclick="numero_pagina(' + paginas + ');">Final</button></li>';
    return paginacion;
}
function generar_texto_paginacion(total, cantidad_mostrar) {
    let actual = document.getElementById('pagina').value;
    let paginas = Math.ceil(total / cantidad_mostrar);
    let iniciar = (actual - 1) * cantidad_mostrar;
    if (actual < paginas) {

        var texto = '<label>Mostrando del ' + (parseInt(iniciar) + 1) + ' al ' + ((parseInt(iniciar) + 1) + 9) + ' de un total de ' + total + ' registros</label>';
    } else {
        var texto = '<label>Mostrando del ' + (parseInt(iniciar) + 1) + ' al ' + total + ' de un total de ' + total + ' registros</label>';
    }
    return texto;
}
// ---------------------------------------------  DATOS DE CARGA PARA FILTRO DE BUSQUEDA -----------------------------------------------
//cargar programas de estudio
function cargar_ambientes_filtro(datos, form = 'busqueda_tabla_ambiente', filtro = 'filtro_ambiente') {
    let ambiente_actual = document.getElementById(filtro).value;
    lista_ambiente = `<option value="0">TODOS</option>`;
    datos.forEach(ambiente => {
        pe_selected = "";
        if (ambiente.id == ambiente_actual) {
            pe_selected = "selected";
        }
        lista_ambiente += `<option value="${ambiente.id}" ${pe_selected}>${ambiente.detalle}</option>`;
    });
    document.getElementById(form).innerHTML = lista_ambiente;
}
//cargar programas de estudio
function cargar_sede_filtro(sedes) {
    let sede_actual = document.getElementById('sede_actual_filtro').value;
    lista_sede = `<option value="0">TODOS</option>`;
    sedes.forEach(sede => {
        sede_selected = "";
        if (sede.id == sede_actual) {
            sede_selected = "selected";
        }
        lista_sede += `<option value="${sede.id}" ${sede_selected}>${sede.nombre}</option>`;
    });
    document.getElementById('busqueda_tabla_sede').innerHTML = lista_sede;
}
async function validar_datos_reset_password() {
    let id = document.getElementById('data').value;
    let token = document.getElementById('data2').value;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('token', token);
    formData.append('sesion', '');

    try {
        let respuesta = await fetch(base_url + 'src/control/Usuario.php?tipo=validar_datos_reset_password', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        let json = await respuesta.json();
        if (json.status==false) {
            Swal.fire({
                type: 'error',
                title: 'Error de Link',
                text: "Link  Caducada, Por favor verifique su correo",
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 3000
            });
            let formulario = document.getElementById('logincontainer');
            formulario.innerHTML=`<style>
                h1 {
      font-size: 3rem;
      font-weight: 800;
      margin: 0;
      color: var(--color-accent);
      line-height: 1.1;
      user-select: text;
    }   p {
      font-size: 1.125rem;
      line-height: 1.5;
      margin: 0;
      color: var(--color-text-secondary);
      max-width: 380px;
      user-select: text;
    }
    .characters {
      display: flex;
      gap: 2rem;
      justify-content: center;
      width: 100%;
      max-width: 400px;
      user-select: none;
    }
    .character-image {
      width: 140px;
      max-width: 100%;
      border-radius: var(--border-radius);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s var(--transition-timing);
      cursor: default;
      background: #fff;
      object-fit: contain;
    }
    .character-image:hover,
    .character-image:focus {
      transform: scale(1.05) rotate(3deg);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
      outline: none;
    }
      button {
      background-color: var(--color-button-bg);
      color: var(--color-button-text);
      border: none;
      border-radius: var(--border-radius);
      padding: 0.85rem 2.25rem;
      font-weight: 600;
      font-size: 1.125rem;
      cursor: pointer;
      transition: background-color 0.25s var(--transition-timing), transform 0.2s var(--transition-timing);
      box-shadow: 0 6px 15px rgba(239, 68, 68, 0.45);
      user-select: none;
    }
    button:hover,
    button:focus {
      background-color: #dc2626;
      outline: none;
      transform: scale(1.05);
      box-shadow: 0 10px 25px rgba(220, 38, 38, 0.7);
    }
    button:active {
      transform: scale(0.95);
      box-shadow: 0 4px 14px rgba(220, 38, 38, 0.9);
    }
    @media (max-width: 520px) {
      .card {
        padding: 2rem 1.5rem 2.5rem;
      }
      h1 {
        font-size: 2.25rem;
      }
      p {
        max-width: 100%;
        font-size: 1rem;
      }
      .characters {
        gap: 1rem;
      }
      .character-image {
        width: 110px;
      }
      button {
        font-size: 1rem;
        padding: 0.7rem 1.75rem;
      }
    }
      </style>

             <main class="card" role="main" aria-labelledby="error-title" aria-describedby="error-desc">
    <h1 id="error-title">¡Oops! No pudimos recuperar tu contraseña</h1>
    <p id="error-desc">Pero no te preocupes, <strong>Daffy Duck</strong> y <strong>Bugs Bunny</strong> están aquí para animarte.</p>
    <section class="characters" aria-label="Imágenes de Daffy Duck y Bugs Bunny">
      <!-- Daffy Duck image from Wikimedia Commons -->
      <img
        src="https://upload.wikimedia.org/wikipedia/en/9/99/Daffy_Duck.svg"
        alt="Dibujo de Daffy Duck"
        class="character-image"
        width="140" height="140"
        loading="lazy"
        decoding="async"
        tabindex="0"
      />
      <!-- Bugs Bunny image from Wikimedia Commons -->
      <img
        src="https://upload.wikimedia.org/wikipedia/en/0/0b/Bugs_Bunny.svg"
        alt="Dibujo de Bugs Bunny"
        class="character-image"
        width="140" height="140"
        loading="lazy"
        decoding="async"
        tabindex="0"
      />
    </section>
    <button type="button" onclick="location.reload()" aria-label="Reintentar recuperación de contraseña">
      Intentar de nuevo
    </button>
  </main>`;
        }
        //console.log(respuesta);

    } catch (e) {
        console.log("Error al cargar categorias" + e);

    }
}

function validar_imputs_password(){
    let pass1 = document.getElementById('password').value;
    let pass2 = document.getElementById('password1').value;

    if (pass1 !==pass2) {
        Swal.fire({
                type: 'error',
                title: 'contraseñas no coinciden',
                text: "error",
                footer: '',
                timer: 1000
            });
            return;
    }
    if (pass1.length<=8 && pass2.length<8) {
        Swal.fire({
                type: 'error',
                title: 'error',
                text: "contraseña debe tener minimo 8 caracteres",
                footer: '',
                timer: 3000
            });
            return;
    } else {
        actualizar_password();
    }
}
async function actualizar_password(params) {
    //enviar informacin de password y id al controlador   usuario
    //recibir informacion y encriptar la nueva contraseña
    //guardar en la base  de datos y actualizar campo reset password =0 y token paswword=''
    //notificar a usuario sobre el estado del proceso
    
}

// ------------------------------------------- FIN DE DATOS DE CARGA PARA FILTRO DE BUSQUEDA -----------------------------------------------