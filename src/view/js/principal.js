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
            :root {
                --color-primary: #ff6b35;
                --color-secondary: #ffa726;
                --color-accent: #ff8f00;
                --color-text-secondary: #4a4a4a;
                --color-button-bg: #ef4444;
                --color-button-text: #ffffff;
                --border-radius: 16px;
                --transition-timing: cubic-bezier(0.4, 0, 0.2, 1);
                --gradient-primary: linear-gradient(135deg, #ff6b35 0%, #ffa726 50%, #ff8f00 100%);
            }

            .card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 24px;
                padding: 3rem 2.5rem;
                box-shadow: 0 25px 50px rgba(255, 107, 53, 0.3);
                text-align: center;
                max-width: 600px;
                width: 100%;
                position: relative;
                overflow: hidden;
                animation: slideUp 0.8s var(--transition-timing);
                margin: 0 auto;
            }

            .card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 5px;
                background: var(--gradient-primary);
                border-radius: 24px 24px 0 0;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(50px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            h1 {
                font-size: 3rem;
                font-weight: 800;
                margin: 0 0 1.5rem 0;
                background: var(--gradient-primary);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                line-height: 1.1;
                user-select: text;
                animation: textGlow 2s ease-in-out infinite alternate;
            }

            @keyframes textGlow {
                from { filter: brightness(1); }
                to { filter: brightness(1.1); }
            }

            p {
                font-size: 1.25rem;
                line-height: 1.6;
                margin: 0 0 2.5rem 0;
                color: var(--color-text-secondary);
                max-width: 450px;
                margin-left: auto;
                margin-right: auto;
                user-select: text;
            }

            p strong {
                color: var(--color-accent);
                font-weight: 700;
            }

            .icon-container {
                display: flex;
                justify-content: center;
                margin-bottom: 2rem;
                position: relative;
            }

            .animated-icon {
                width: 120px;
                height: 120px;
                background: linear-gradient(135deg, #ffb74d 0%, #ffa726 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 20px 40px rgba(255, 107, 53, 0.2);
                animation: pulse 2s ease-in-out infinite;
                position: relative;
                font-size: 3.5rem;
                color: white;
                text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            }

            .animated-icon::after {
                content: '';
                position: absolute;
                width: 140px;
                height: 140px;
                border: 3px solid rgba(255, 107, 53, 0.3);
                border-radius: 50%;
                animation: ripple 2s ease-out infinite;
            }

            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }

            @keyframes ripple {
                0% {
                    transform: scale(1);
                    opacity: 1;
                }
                100% {
                    transform: scale(1.3);
                    opacity: 0;
                }
            }

            .motivational-elements {
                display: flex;
                gap: 2rem;
                justify-content: center;
                width: 100%;
                max-width: 500px;
                margin: 0 auto 2.5rem auto;
                user-select: none;
            }

            .motivational-card {
                flex: 1;
                background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
                border-radius: var(--border-radius);
                padding: 1.5rem;
                box-shadow: 0 8px 24px rgba(255, 152, 0, 0.15);
                transition: all 0.4s var(--transition-timing);
                cursor: default;
                border: 2px solid transparent;
                position: relative;
                overflow: hidden;
            }

            .motivational-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
                transition: left 0.8s;
            }

            .motivational-card:hover::before {
                left: 100%;
            }

            .motivational-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 20px 40px rgba(255, 152, 0, 0.25);
                border-color: var(--color-accent);
            }

            .card-icon {
                font-size: 2.5rem;
                margin-bottom: 0.5rem;
                display: block;
                animation: bounce 2s ease-in-out infinite;
            }

            .card-text {
                font-size: 0.95rem;
                font-weight: 600;
                color: #e65100;
                line-height: 1.4;
            }

            @keyframes bounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }

            button {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                color: var(--color-button-text);
                border: none;
                border-radius: var(--border-radius);
                padding: 1rem 2.5rem;
                font-weight: 700;
                font-size: 1.125rem;
                cursor: pointer;
                transition: all 0.3s var(--transition-timing);
                box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
                user-select: none;
                position: relative;
                overflow: hidden;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
                transition: left 0.5s;
            }

            button:hover::before {
                left: 100%;
            }

            button:hover,
            button:focus {
                background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
                outline: none;
                transform: translateY(-3px) scale(1.02);
                box-shadow: 0 15px 35px rgba(220, 38, 38, 0.5);
            }

            button:active {
                transform: translateY(-1px) scale(0.98);
                box-shadow: 0 8px 20px rgba(220, 38, 38, 0.7);
            }

            .additional-info {
                margin-top: 2rem;
                padding: 1.5rem;
                background: rgba(255, 183, 77, 0.1);
                border: 1px solid rgba(255, 183, 77, 0.3);
                border-radius: var(--border-radius);
                color: var(--color-text-secondary);
                font-size: 0.95rem;
                line-height: 1.5;
            }

            .additional-info strong {
                color: var(--color-accent);
            }

            /* Responsive */
            @media (max-width: 520px) {
                .card {
                    padding: 2rem 1.5rem 2.5rem;
                    margin: 0.5rem;
                }
                
                h1 {
                    font-size: 2.25rem;
                }
                
                p {
                    max-width: 100%;
                    font-size: 1.1rem;
                    margin-bottom: 2rem;
                }
                
                .animated-icon {
                    width: 100px;
                    height: 100px;
                    font-size: 2.5rem;
                }
                
                .motivational-elements {
                    gap: 1rem;
                    margin-bottom: 2rem;
                }
                
                .motivational-card {
                    padding: 1rem;
                }
                
                .card-icon {
                    font-size: 2rem;
                }
                
                .card-text {
                    font-size: 0.85rem;
                }
                
                button {
                    font-size: 1rem;
                    padding: 0.85rem 2rem;
                }
                
                .additional-info {
                    padding: 1rem;
                    font-size: 0.9rem;
                }
            }

            @media (max-width: 400px) {
                h1 {
                    font-size: 2rem;
                }
                
                .motivational-elements {
                    flex-direction: column;
                    align-items: center;
                    gap: 1rem;
                }
                
                .motivational-card {
                    max-width: 250px;
                }
            }
        </style>

        <main class="card" role="main" aria-labelledby="error-title" aria-describedby="error-desc">
            <div class="icon-container">
                <div class="animated-icon" aria-hidden="true">💫</div>
            </div>
            
            <h1 id="error-title">¡Oops! No pudimos recuperar tu contraseña</h1>
            <p id="error-desc">Pero no te preocupes, todo tiene solución. <strong>¡Mantén una actitud positiva!</strong></p>
            
            <section class="motivational-elements" aria-label="Elementos motivacionales">
                <div class="motivational-card" tabindex="0">
                    <span class="card-icon">🌟</span>
                    <div class="card-text">Los errores son oportunidades para aprender</div>
                </div>
                <div class="motivational-card" tabindex="0">
                    <span class="card-icon">🚀</span>
                    <div class="card-text">Cada reinicio es un nuevo comienzo</div>
                </div>
            </section>
            
            <button type="button" onclick="location.reload()" aria-label="Reintentar recuperación de contraseña">
                🔄 Intentar de nuevo
            </button>
            
            <div class="additional-info">
                <strong>💡 Consejo:</strong> Los enlaces de recuperación de contraseña expiran por seguridad. Si necesitas ayuda adicional, contacta al administrador del sistema.
            </div>
        </main>
   `;
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

async function actualizar_password() {
    const id = document.getElementById('data').value;
    const token = document.getElementById('data2').value;
    const password = document.getElementById('password').value;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('token', token);
    formData.append('password', password);
    formData.append('sesion', '');
    
    try {
        let respuesta = await fetch(base_url_server + 'src/control/Usuario.php?tipo=actualizar_password_reset', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        
        let json = await respuesta.json();
        
        if (json.status) {
            await Swal.fire({
                type: 'success',
                title: '¡Contraseña actualizada!',
                text: 'Tu contraseña ha sido actualizada correctamente. Serás redirigido al login.',
                confirmButtonClass: 'btn btn-confirm mt-2',
                timer: 3000,
                timerProgressBar: true
            });
            
            // Redirigir al login después de 3 segundos
            setTimeout(() => {
                location.replace(base_url + "login");
            }, 2000);
            
        } else {
            throw new Error(json.mensaje || 'Error al actualizar la contraseña');
        }
        
    } catch (error) {
        console.log("Error al actualizar contraseña: " + error);
        throw error;
    }
}

    //enviar informacin de password y id al controlador   usuario
    //recibir informacion y encriptar la nueva contraseña
    //guardar en la base  de datos y actualizar campo reset password =0 y token paswword=''
    //notificar a usuario sobre el estado del proceso
    


// ------------------------------------------- FIN DE DATOS DE CARGA PARA FILTRO DE BUSQUEDA -----------------------------------------------