<?php
include_once "../../assest/config/validarUsuarioFruta.php";
include_once "../../assest/controlador/ERECEPCION_ADO.php";

$ERECEPCION_ADO = new ERECEPCION_ADO();

$empresaFiltro = $EMPRESAS;
$temporadaFiltro = $TEMPORADAS;

$ARRAYESTANDAR = [];
if ($empresaFiltro) {
    $ARRAYESTANDAR = $ERECEPCION_ADO->listarEstandarPorEmpresaCBX($empresaFiltro);
}

if (!isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'])) {
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'] = [];
}

// Normaliza registros antiguos sin bandera de habilitación
foreach ($_SESSION['INFORME_GERENCIAL_PROYECCIONES'] as &$proyeccionNormalizada) {
    if (!isset($proyeccionNormalizada['habilitado'])) {
        $proyeccionNormalizada['habilitado'] = true;
    }
}
unset($proyeccionNormalizada);

$nombreEstandares = [];
if ($ARRAYESTANDAR) {
    foreach ($ARRAYESTANDAR as $estandar) {
        $nombreEstandares[$estandar['ID_ESTANDAR']] = $estandar['NOMBRE_ESTANDAR'];
    }
}

$mensajeExito = "";
$indiceEditar = null;
$datosEdicion = [
    'semana' => '',
    'kg_proyectado' => '',
    'estandar' => ''
];

if (isset($_POST['DESHABILITAR'])) {
    $indice = intval($_POST['DESHABILITAR']);
    if (isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice])) {
        $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]['habilitado'] = false;
    }
}

if (isset($_POST['HABILITAR'])) {
    $indice = intval($_POST['HABILITAR']);
    if (isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice])) {
        $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]['habilitado'] = true;
    }
}

if (isset($_POST['EDITAR'])) {
    $indiceEditar = intval($_POST['EDITAR']);
    if (isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indiceEditar])) {
        $registro = $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indiceEditar];
        if ($registro['empresa'] == $empresaFiltro && $registro['temporada'] == $temporadaFiltro) {
            $datosEdicion['semana'] = $registro['semana'];
            $datosEdicion['kg_proyectado'] = $registro['kg_proyectado'];
            $datosEdicion['estandar'] = array_search($registro['descripcion_estandar'], $nombreEstandares) ?: '';
        } else {
            $indiceEditar = null;
        }
    }
}

if (isset($_POST['GUARDAR_CAMBIOS'])) {
    $indice = intval($_POST['INDEX']);
    $semana = isset($_POST['SEMANA']) ? intval($_POST['SEMANA']) : 0;
    $kgProyectado = isset($_POST['KG_PROYECTADO']) ? floatval(str_replace([",", " "], [".", ""], $_POST['KG_PROYECTADO'])) : 0;
    $estandarSeleccionado = isset($_POST['ESTANDAR']) ? $_POST['ESTANDAR'] : '';
    $descripcionEstandar = $estandarSeleccionado && isset($nombreEstandares[$estandarSeleccionado]) ? $nombreEstandares[$estandarSeleccionado] : '';
    $tipoEmbalaje = $descripcionEstandar;

    if (isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]) && $semana > 0 && $kgProyectado > 0) {
        $esBulk = stripos($tipoEmbalaje, 'bulk') !== false || stripos($descripcionEstandar, 'bulk') !== false;

        $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice] = [
            'empresa' => $empresaFiltro,
            'temporada' => $temporadaFiltro,
            'semana' => $semana,
            'kg_proyectado' => $kgProyectado,
            'tipo_embalaje' => $tipoEmbalaje,
            'descripcion_estandar' => $descripcionEstandar,
            'es_bulk' => $esBulk,
            'creado' => $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]['creado'],
            'habilitado' => $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]['habilitado']
        ];

        $mensajeExito = "Proyección actualizada para la semana " . $semana . ".";
        $indiceEditar = null;
    }
} elseif (isset($_POST['AGREGAR_PROYECCION'])) {
    $semana = isset($_POST['SEMANA']) ? intval($_POST['SEMANA']) : 0;
    $kgProyectado = isset($_POST['KG_PROYECTADO']) ? floatval(str_replace([",", " "], [".", ""], $_POST['KG_PROYECTADO'])) : 0;
    $estandarSeleccionado = isset($_POST['ESTANDAR']) ? $_POST['ESTANDAR'] : '';
    $descripcionEstandar = $estandarSeleccionado && isset($nombreEstandares[$estandarSeleccionado]) ? $nombreEstandares[$estandarSeleccionado] : '';
    $tipoEmbalaje = $descripcionEstandar;

    if ($semana > 0 && $kgProyectado > 0) {
        $esBulk = stripos($tipoEmbalaje, 'bulk') !== false || stripos($descripcionEstandar, 'bulk') !== false;

        $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][] = [
            'empresa' => $empresaFiltro,
            'temporada' => $temporadaFiltro,
            'semana' => $semana,
            'kg_proyectado' => $kgProyectado,
            'tipo_embalaje' => $tipoEmbalaje,
            'descripcion_estandar' => $descripcionEstandar,
            'es_bulk' => $esBulk,
            'creado' => date('Y-m-d H:i'),
            'habilitado' => true
        ];

        $mensajeExito = "Proyección agregada para la semana " . $semana . ".";
    }
}

$proyeccionesFiltradas = array_values(array_filter(
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'],
    function ($proyeccion) use ($empresaFiltro, $temporadaFiltro) {
        return $proyeccion['empresa'] == $empresaFiltro && $proyeccion['temporada'] == $temporadaFiltro;
    }
));

$empresaSeleccionada = $EMPRESA_ADO->verEmpresa($empresaFiltro);
$nombreEmpresa = $empresaSeleccionada ? $empresaSeleccionada[0]['NOMBRE_EMPRESA'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ingresar proyección</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Registro de proyecciones semanales por empresa" />
    <meta name="author" content="">
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <style>
        .metric-card {
            border: 0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            border-radius: 12px;
        }
        .metric-card .box-body { padding: 16px; }
        .tag { padding: 3px 8px; border-radius: 999px; font-size: 11px; }
        .tag-bulk { background: #fff4e5; color: #d9822b; }
        .tag-envasado { background: #e7f6ef; color: #2f855a; }
        .projection-table th, .projection-table td { font-size: 12px; }
        .section-title { font-weight: 600; font-size: 16px; }
        .badge-soft { padding: 6px 10px; border-radius: 10px; font-size: 12px; background: #f5f7fb; }
        .alert-soft { background: #f3f7ff; border: 1px solid #d4e2ff; color: #2d4b7a; }
    </style>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary" >
    <div class="wrapper">
        <?php include_once "../../assest/config/menuFruta.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <section class="content">
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Ingresar proyección</h3>
                                <p class="mb-0">Registro de proyecciones por empresa usando la temporada activa.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Registrar proyección semanal</h4>
                                    <p class="mb-0 text-muted">Empresa: <?php echo htmlspecialchars($nombreEmpresa); ?> | Temporada activa: <?php echo htmlspecialchars($TEMPORADANOMBRE); ?></p>
                                </div>
                                <div class="box-body">
                                    <?php if ($mensajeExito) { ?>
                                        <div class="alert alert-soft"><?php echo $mensajeExito; ?></div>
                                    <?php } ?>
                                    <form method="post" class="row">
                                        <input type="hidden" name="INDEX" value="<?php echo $indiceEditar !== null ? $indiceEditar : ''; ?>">
                                        <div class="col-md-3 col-12">
                                            <label>Semana</label>
                                            <input type="number" name="SEMANA" class="form-control" min="1" max="53" required value="<?php echo htmlspecialchars($datosEdicion['semana']); ?>">
                                        </div>
                                        <div class="col-md-5 col-12">
                                            <label>Kg netos proyectados</label>
                                            <input type="number" step="0.01" name="KG_PROYECTADO" class="form-control" required value="<?php echo htmlspecialchars($datosEdicion['kg_proyectado']); ?>">
                                        </div>
                                        <div class="col-md-4 col-12 mt-10">
                                            <label>Estandar de llegada</label>
                                            <select name="ESTANDAR" class="form-control" required>
                                                <option value="">Seleccione un estandar</option>
                                                <?php foreach ($ARRAYESTANDAR as $estandar) { ?>
                                                    <option value="<?php echo $estandar['ID_ESTANDAR']; ?>" <?php echo $datosEdicion['estandar'] == $estandar['ID_ESTANDAR'] ? 'selected' : ''; ?>><?php echo $estandar['NOMBRE_ESTANDAR']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-12 mt-15">
                                            <?php if ($indiceEditar !== null) { ?>
                                                <button type="submit" name="GUARDAR_CAMBIOS" class="btn btn-primary">Guardar cambios</button>
                                                <a href="ingresarProyeccionGerencial.php" class="btn btn-secondary ml-10">Cancelar</a>
                                            <?php } else { ?>
                                                <button type="submit" name="AGREGAR_PROYECCION" class="btn btn-primary">Agregar proyección</button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Proyecciones registradas</h4>
                                    <p class="mb-0 text-muted">Solo se muestran proyecciones de la temporada activa.</p>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered projection-table">
                                        <thead>
                                            <tr>
                                                <th>Semana</th>
                                                <th>Estandar</th>
                                                <th class="text-center">Tipo</th>
                                                <th class="text-right">Kg proyectados</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Operaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($proyeccionesFiltradas) { foreach ($proyeccionesFiltradas as $index => $proy) { ?>
                                                <tr>
                                                    <td><?php echo $proy['semana']; ?></td>
                                                    <td><?php echo htmlspecialchars($proy['descripcion_estandar']); ?></td>
                                                    <td class="text-center">
                                                        <?php if ($proy['es_bulk']) { ?>
                                                            <span class="tag tag-bulk">bulk</span>
                                                        <?php } else { ?>
                                                            <span class="tag tag-envasado">envasado</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-right"><?php echo number_format($proy['kg_proyectado'], 0, ',', '.'); ?></td>
                                                    <td class="text-center">
                                                        <?php if (!empty($proy['habilitado'])) { ?>
                                                            <span class="badge badge-success">Habilitado</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger">Deshabilitado</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="post" class="d-inline">
                                                            <button type="submit" name="EDITAR" value="<?php echo $index; ?>" class="btn btn-sm btn-outline-primary">Editar</button>
                                                        </form>
                                                        <?php if (!empty($proy['habilitado'])) { ?>
                                                            <form method="post" class="d-inline" onsubmit="return confirm('¿Deshabilitar registro?');">
                                                                <button type="submit" name="DESHABILITAR" value="<?php echo $index; ?>" class="btn btn-sm btn-outline-danger">Deshabilitar</button>
                                                            </form>
                                                        <?php } else { ?>
                                                            <form method="post" class="d-inline">
                                                                <button type="submit" name="HABILITAR" value="<?php echo $index; ?>" class="btn btn-sm btn-outline-success">Habilitar</button>
                                                            </form>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } } else { ?>
                                                <tr><td colspan="6" class="text-center text-muted">Todavía no hay datos guardados para esta temporada.</td></tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include_once "../../assest/config/urlBase.php"; ?>
</body>
</html>
