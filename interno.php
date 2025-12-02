<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistemas internos</title>
    <link rel="icon" href="assest/img/favicon.png">
    <link rel="stylesheet" type="text/css" href="assest/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="assest/css/style.css" />
    <link rel="stylesheet" href="api/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="api/cryptioadmin10/html/main/css/vendors_css.css">
    <link rel="stylesheet" href="api/cryptioadmin10/html/main/css/style.css">
    <link rel="stylesheet" href="api/cryptioadmin10/html/main/css/skin_color.css">
    <style>
        body,
        html {
            height: 100%;
        }

        .portal-wrapper {
            min-height: 100vh;
        }

        .module-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.22);
            padding: 24px 22px 10px;
            height: 100%;
        }

        .module-card h3 {
            color: #1f2d3d;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .module-card p {
            color: #4b5563;
            margin-bottom: 16px;
        }

        .module-grid {
            gap: 14px;
        }

        .module-card .btn {
            padding: 11px 12px;
            font-weight: 600;
        }
    </style>
</head>
<body class="hold-transition theme-primary bg-gradient-primary">
    <div class="container portal-wrapper py-5">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8 text-center">
                <h2 class="text-white mb-2">Sistemas internos</h2>
                <p class="text-white-50 mb-0">Accede a cada módulo manteniendo los colores y funciones originales.</p>
            </div>
        </div>
        <div class="row module-grid justify-content-center">
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                <div class="module-card w-100">
                    <h3>Fruta</h3>
                    <p class="mb-3">Gestión completa de fruta.</p>
                    <a class="btn bg-gradient-primary btn-block w-100" href="./fruta/">Entrar</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                <div class="module-card w-100">
                    <h3>Materiales</h3>
                    <p class="mb-3">Control y disponibilidad.</p>
                    <a class="btn bg-gradient-secondary btn-block w-100" href="./material/">Entrar</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                <div class="module-card w-100">
                    <h3>Exportadora</h3>
                    <p class="mb-3">Operaciones de exportación.</p>
                    <a class="btn bg-gradient-secondary btn-block w-100" href="./exportadora/">Entrar</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                <div class="module-card w-100">
                    <h3>Estadísticas</h3>
                    <p class="mb-3">Indicadores y reportes.</p>
                    <a class="btn bg-gradient-secondary btn-block w-100" href="./estadistica/">Entrar</a>
                </div>
                <p class="grid-section__text">Navega por cada módulo sin modificar su funcionamiento.</p>
            </header>
            <div class="card-grid">
                <a class="card" href="./fruta/">
                    <div class="card__label">Módulo</div>
                    <h3 class="card__title">Fruta</h3>
                    <p class="card__text">Gestiona la información y los procesos de fruta.</p>
                </a>
                <a class="card" href="./exportadora/">
                    <div class="card__label">Módulo</div>
                    <h3 class="card__title">Exportadora</h3>
                    <p class="card__text">Administra las operaciones de exportación.</p>
                </a>
                <a class="card" href="./estadistica/">
                    <div class="card__label">Módulo</div>
                    <h3 class="card__title">Estadísticas</h3>
                    <p class="card__text">Consulta y analiza indicadores clave.</p>
                </a>
                <a class="card" href="./material/">
                    <div class="card__label">Módulo</div>
                    <h3 class="card__title">Materiales</h3>
                    <p class="card__text">Controla materiales y recursos disponibles.</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
