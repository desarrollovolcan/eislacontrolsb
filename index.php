<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistemas</title>
    <link rel="icon" href="assest/img/favicon.png">
    <link rel="stylesheet" href="assest/css/portal.css">
</head>
<body>
    <main class="page">
        <section class="hero">
            <div class="hero__content">
                <p class="hero__eyebrow">Intranet</p>
                <h1 class="hero__title">Bienvenido al portal de sistemas</h1>
                <p class="hero__subtitle">
                    Accede rápidamente a las vistas internas y de productor con una interfaz ordenada y ligera.
                </p>
            </div>
            <div class="hero__actions">
                <a class="card card--accent" href="./interno.php">
                    <div class="card__label">Acceso interno</div>
                    <h2 class="card__title">Sistemas internos</h2>
                    <p class="card__text">Fruta, Exportadora, Estadísticas y Materiales.</p>
                </a>
                <a class="card" href="./estadistica/">
                    <div class="card__label">Acceso productor</div>
                    <h2 class="card__title">Productor</h2>
                    <p class="card__text">Consulta los indicadores y reportes disponibles.</p>
                </a>
            </div>
        </section>

        <section class="grid-section">
            <header class="grid-section__header">
                <div>
                    <p class="hero__eyebrow">Sistemas internos</p>
                    <h2 class="grid-section__title">Vistas disponibles</h2>
                </div>
                <p class="grid-section__text">Explora cada módulo en un solo lugar con colores unificados.</p>
            </header>
            <div class="card-grid">
                <a class="card" href="./fruta/">
                    <div class="card__label">Módulo</div>
                    <h3 class="card__title">Fruta</h3>
                    <p class="card__text">Gestión y seguimiento de fruta.</p>
                </a>
                <a class="card" href="./exportadora/">
                    <div class="card__label">Módulo</div>
                    <h3 class="card__title">Exportadora</h3>
                    <p class="card__text">Operaciones de exportación ordenadas.</p>
                </a>
                <a class="card" href="./estadistica/">
                    <div class="card__label">Módulo</div>
                    <h3 class="card__title">Estadísticas</h3>
                    <p class="card__text">Indicadores y reportabilidad en línea.</p>
                </a>
                <a class="card" href="./material/">
                    <div class="card__label">Módulo</div>
                    <h3 class="card__title">Materiales</h3>
                    <p class="card__text">Control de materiales y recursos.</p>
                </a>
            </div>
        </section>
    </main>
</body>
</html>
