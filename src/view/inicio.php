<!-- start page title -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Rediseñado</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #434343 0%, #000000 100%);
            --orange-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            --purple-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-modern: 0 20px 40px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Tarjetas con formas hexagonales y glassmorphism */
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            box-shadow: var(--shadow-modern);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            border-radius: 30px 30px 0 0;
        }

        .card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: var(--shadow-hover);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Gradientes específicos para cada tarjeta */
        .card:nth-child(1)::before { background: var(--primary-gradient); }
        .card:nth-child(2)::before { background: var(--secondary-gradient); }
        .card:nth-child(3)::before { background: var(--success-gradient); }
        .card:nth-child(4)::before { background: var(--warning-gradient); }
        .card:nth-child(5)::before { background: var(--info-gradient); }
        .card:nth-child(6)::before { background: var(--dark-gradient); }

        .card-body {
            padding: 30px;
            color: white;
            position: relative;
        }

        /* Iconos flotantes con formas orgánicas */
        .card-icon {
            position: absolute;
            top: -10px;
            right: 20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
            opacity: 0.2;
            transition: all 0.3s ease;
        }

        .card:hover .card-icon {
            opacity: 0.4;
            transform: rotate(15deg) scale(1.1);
        }

        .card:nth-child(1) .card-icon { background: var(--primary-gradient); }
        .card:nth-child(2) .card-icon { background: var(--secondary-gradient); }
        .card:nth-child(3) .card-icon { background: var(--success-gradient); }
        .card:nth-child(4) .card-icon { background: var(--warning-gradient); }
        .card:nth-child(5) .card-icon { background: var(--info-gradient); }
        .card:nth-child(6) .card-icon { background: var(--dark-gradient); }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .card-number {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            background: linear-gradient(45deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Botones con formas fluidas */
        .btn {
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        /* Colores específicos para botones */
        .btn-usuarios { background: var(--primary-gradient); }
        .btn-instituciones { background: var(--secondary-gradient); }
        .btn-ambientes { background: var(--success-gradient); }
        .btn-bienes { background: var(--warning-gradient); }
        .btn-movimientos { background: var(--info-gradient); }
        .btn-reportes { background: var(--dark-gradient); }

        /* Efecto de partículas flotantes */
        .card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover::after {
            opacity: 1;
        }

        /* Formas geométricas de fondo */
        .geometric-shape {
            position: absolute;
            opacity: 0.1;
            pointer-events: none;
        }

        .shape-1 {
            top: 10px;
            left: 10px;
            width: 30px;
            height: 30px;
            background: white;
            border-radius: 50%;
        }

        .shape-2 {
            bottom: 20px;
            right: 40px;
            width: 20px;
            height: 20px;
            background: white;
            transform: rotate(45deg);
        }

        .shape-3 {
            top: 50%;
            left: 15px;
            width: 15px;
            height: 15px;
            background: white;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        }

        /* Animaciones suaves */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .card:hover .geometric-shape {
            animation: float 2s ease-in-out infinite;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }
            
            .card-number {
                font-size: 2.2rem;
            }
            
            .card-icon {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
        }

        /* Efectos de carga */
        .card {
            animation: slideInUp 0.6s ease-out;
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        .card:nth-child(5) { animation-delay: 0.5s; }
        .card:nth-child(6) { animation-delay: 0.6s; }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Efecto de ondas en hover */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Título principal */
        .dashboard-title {
            text-align: center;
            color: white;
            margin-bottom: 50px;
            font-size: 2.5rem;
            font-weight: 300;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="dashboard-title">Dashboard SIGI</h1>
        
        <div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="geometric-shape shape-1"></div>
                    <div class="geometric-shape shape-2"></div>
                    <div class="geometric-shape shape-3"></div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Usuarios</h5>
                        </div>
                        <div class="row d-flex align-items-center mb-4">
                            <div class="col-8">
                                <h2 class="card-number d-flex align-items-center mb-0">
                                    20
                                </h2>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="#usuarios" class="btn btn-usuarios text-white">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="geometric-shape shape-1"></div>
                    <div class="geometric-shape shape-2"></div>
                    <div class="geometric-shape shape-3"></div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Instituciones</h5>
                        </div>
                        <div class="row d-flex align-items-center mb-4">
                            <div class="col-8">
                                <h2 class="card-number d-flex align-items-center mb-0">
                                    10
                                </h2>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="#instituciones" class="btn btn-instituciones text-white">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="geometric-shape shape-1"></div>
                    <div class="geometric-shape shape-2"></div>
                    <div class="geometric-shape shape-3"></div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Ambientes</h5>
                        </div>
                        <div class="row d-flex align-items-center mb-4">
                            <div class="col-8">
                                <h2 class="card-number d-flex align-items-center mb-0">
                                    200
                                </h2>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="#ambientes" class="btn btn-ambientes text-white">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="geometric-shape shape-1"></div>
                    <div class="geometric-shape shape-2"></div>
                    <div class="geometric-shape shape-3"></div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Bienes</h5>
                        </div>
                        <div class="row d-flex align-items-center mb-4">
                            <div class="col-8">
                                <h2 class="card-number d-flex align-items-center mb-0">
                                    15,890
                                </h2>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="#bienes" class="btn btn-bienes text-white">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="geometric-shape shape-1"></div>
                    <div class="geometric-shape shape-2"></div>
                    <div class="geometric-shape shape-3"></div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Movimientos</h5>
                        </div>
                        <div class="row d-flex align-items-center mb-4">
                            <div class="col-8">
                                <h2 class="card-number d-flex align-items-center mb-0">
                                    20
                                </h2>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="#movimientos" class="btn btn-movimientos text-white">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="geometric-shape shape-1"></div>
                    <div class="geometric-shape shape-2"></div>
                    <div class="geometric-shape shape-3"></div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Reportes</h5>
                        </div>
                        <div class="row d-flex align-items-center mb-4">
                            <div class="col-8">
                                <h2 class="card-number d-flex align-items-center mb-0">
                                    01/04/2024
                                </h2>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="#reportes" class="btn btn-reportes text-white">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Efecto de ondas al hacer clic
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Animación de números
        function animateNumbers() {
            const numbers = document.querySelectorAll('.card-number');
            numbers.forEach(number => {
                const finalNumber = number.textContent.replace(/,/g, '');
                if (!isNaN(finalNumber) && finalNumber !== '') {
                    const increment = Math.ceil(parseInt(finalNumber) / 50);
                    let current = 0;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= parseInt(finalNumber)) {
                            current = parseInt(finalNumber);
                            clearInterval(timer);
                        }
                        number.textContent = current.toLocaleString();
                    }, 30);
                }
            });
        }

        // Ejecutar animación al cargar
        window.addEventListener('load', animateNumbers);
    </script>
</body>
</html>