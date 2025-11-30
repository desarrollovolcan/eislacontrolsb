<?php
include_once "../../assest/config/validarUsuarioFruta.php";
$ARRAYEMPRESA = $EMPRESA_ADO->listarEmpresaCBX();
$nombreEmpresas = [];
if ($ARRAYEMPRESA) {
    foreach ($ARRAYEMPRESA as $empresa) {
        $nombreEmpresas[$empresa['ID_EMPRESA']] = $empresa['NOMBRE_EMPRESA'];
    }
}

$empresaSeleccionForm = $EMPRESAS;
if (isset($_POST['EMPRESA']) && $_POST['EMPRESA'] !== '') {
    $empresaSeleccionForm = intval($_POST['EMPRESA']);
}

$empresaFiltroTablaRaw = isset($_POST['EMPRESA_FILTRO']) ? $_POST['EMPRESA_FILTRO'] : $EMPRESAS;
$empresaFiltroTabla = $empresaFiltroTablaRaw === 'ALL' ? 'ALL' : ($empresaFiltroTablaRaw !== '' ? intval($empresaFiltroTablaRaw) : $EMPRESAS);

$temporadaFiltro = $TEMPORADAS;

if (!isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'])) {
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'] = [];
}

// Normaliza registros antiguos sin bandera de habilitación
foreach ($_SESSION['INFORME_GERENCIAL_PROYECCIONES'] as &$proyeccionNormalizada) {
    if (!isset($proyeccionNormalizada['habilitado'])) {
        $proyeccionNormalizada['habilitado'] = true;
    }
    if (!isset($proyeccionNormalizada['tipo_materia_prima'])) {
        $proyeccionNormalizada['tipo_materia_prima'] = !empty($proyeccionNormalizada['es_bulk']) ? 'Bulk' : 'Granel';
    }
}
unset($proyeccionNormalizada);

$mensajeExito = "";
$indiceEditar = null;
$datosEdicion = [
    'empresa' => $empresaSeleccionForm,
    'semana' => '',
    'kg_proyectado' => '',
    'tipo_mp' => ''
];

if (isset($_POST['REFRESCAR_EMPRESA'])) {
    $datosEdicion['empresa'] = $empresaSeleccionForm;
    $datosEdicion['semana'] = isset($_POST['SEMANA']) ? intval($_POST['SEMANA']) : '';
    $datosEdicion['kg_proyectado'] = isset($_POST['KG_PROYECTADO']) ? floatval(str_replace([",", " "], [".", ""], $_POST['KG_PROYECTADO'])) : '';
    $tipoMPSeleccionado = isset($_POST['TIPO_MP']) ? $_POST['TIPO_MP'] : '';
    $datosEdicion['tipo_mp'] = in_array($tipoMPSeleccionado, ['Granel', 'Bulk']) ? $tipoMPSeleccionado : '';
}

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

if (isset($_POST['ELIMINAR'])) {
    $indice = intval($_POST['ELIMINAR']);
    if (isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice])) {
        $registro = $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice];
        if ($registro['temporada'] == $temporadaFiltro && ($empresaFiltroTabla === 'ALL' || $registro['empresa'] == $empresaFiltroTabla)) {
            unset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]);
            $_SESSION['INFORME_GERENCIAL_PROYECCIONES'] = array_values($_SESSION['INFORME_GERENCIAL_PROYECCIONES']);
            $mensajeExito = "Proyección eliminada.";
        }
    }
}

if (isset($_POST['EDITAR'])) {
    $indiceEditar = intval($_POST['EDITAR']);
    if (isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indiceEditar])) {
        $registro = $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indiceEditar];
        if ($registro['temporada'] == $temporadaFiltro && ($empresaFiltroTabla === 'ALL' || $registro['empresa'] == $empresaFiltroTabla)) {
            $empresaSeleccionForm = $registro['empresa'];

            $datosEdicion['empresa'] = $empresaSeleccionForm;
            $datosEdicion['semana'] = $registro['semana'];
            $datosEdicion['kg_proyectado'] = $registro['kg_proyectado'];
            $datosEdicion['tipo_mp'] = isset($registro['tipo_materia_prima']) ? $registro['tipo_materia_prima'] : ($registro['es_bulk'] ? 'Bulk' : 'Granel');
        } else {
            $indiceEditar = null;
        }
    }
}

if (isset($_POST['GUARDAR_CAMBIOS'])) {
    $indice = intval($_POST['INDEX']);
    $empresaSeleccionada = isset($_POST['EMPRESA']) ? intval($_POST['EMPRESA']) : $empresaSeleccionForm;
    $semana = isset($_POST['SEMANA']) ? intval($_POST['SEMANA']) : 0;
    $kgProyectado = isset($_POST['KG_PROYECTADO']) ? floatval(str_replace([",", " "], [".", ""], $_POST['KG_PROYECTADO'])) : 0;
    $tipoMateriaPrima = isset($_POST['TIPO_MP']) ? $_POST['TIPO_MP'] : '';
    $tipoEmbalaje = $tipoMateriaPrima === 'Bulk' ? 'Bulk' : 'Granel';

    if (isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]) && $semana > 0 && $kgProyectado > 0 && $empresaSeleccionada && in_array($tipoMateriaPrima, ['Granel', 'Bulk'])) {
        $esBulk = $tipoMateriaPrima === 'Bulk' || stripos($tipoEmbalaje, 'bulk') !== false;

        $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice] = [
            'empresa' => $empresaSeleccionada,
            'temporada' => $temporadaFiltro,
            'semana' => $semana,
            'kg_proyectado' => $kgProyectado,
            'tipo_embalaje' => $tipoEmbalaje,
            'descripcion_estandar' => '',
            'tipo_materia_prima' => $tipoMateriaPrima,
            'es_bulk' => $esBulk,
            'creado' => $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]['creado'],
            'habilitado' => $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][$indice]['habilitado']
        ];

        $mensajeExito = "Proyección actualizada para la semana " . $semana . ".";
        $indiceEditar = null;
    }
} elseif (isset($_POST['AGREGAR_PROYECCION'])) {
    $empresaSeleccionada = isset($_POST['EMPRESA']) ? intval($_POST['EMPRESA']) : $empresaSeleccionForm;
    $semana = isset($_POST['SEMANA']) ? intval($_POST['SEMANA']) : 0;
    $kgProyectado = isset($_POST['KG_PROYECTADO']) ? floatval(str_replace([",", " "], [".", ""], $_POST['KG_PROYECTADO'])) : 0;
    $tipoMateriaPrima = isset($_POST['TIPO_MP']) ? $_POST['TIPO_MP'] : '';
    $tipoEmbalaje = $tipoMateriaPrima === 'Bulk' ? 'Bulk' : 'Granel';

    if ($semana > 0 && $kgProyectado > 0 && $empresaSeleccionada && in_array($tipoMateriaPrima, ['Granel', 'Bulk'])) {
        $esBulk = $tipoMateriaPrima === 'Bulk' || stripos($tipoEmbalaje, 'bulk') !== false;

        $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][] = [
            'empresa' => $empresaSeleccionada,
            'temporada' => $temporadaFiltro,
            'semana' => $semana,
            'kg_proyectado' => $kgProyectado,
            'tipo_embalaje' => $tipoEmbalaje,
            'descripcion_estandar' => '',
            'tipo_materia_prima' => $tipoMateriaPrima,
            'es_bulk' => $esBulk,
            'creado' => date('Y-m-d H:i'),
            'habilitado' => true
        ];

        $mensajeExito = "Proyección agregada para la semana " . $semana . ".";
    }
}

$proyeccionesFiltradas = array_values(array_filter(
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'],
    function ($proyeccion) use ($empresaFiltroTabla, $temporadaFiltro) {
        return $proyeccion['temporada'] == $temporadaFiltro && ($empresaFiltroTabla === 'ALL' || $proyeccion['empresa'] == $empresaFiltroTabla);
    }
));

if ($empresaFiltroTabla === 'ALL') {
    $nombreEmpresa = 'Todas';
} else {
    $empresaSeleccionada = $EMPRESA_ADO->verEmpresa($empresaFiltroTabla);
    $nombreEmpresa = $empresaSeleccionada ? $empresaSeleccionada[0]['NOMBRE_EMPRESA'] : '';
}
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
                                    <p class="mb-0 text-muted">Temporada activa: <?php echo htmlspecialchars($TEMPORADANOMBRE); ?></p>
                                </div>
                                <div class="box-body">
                                    <?php if ($mensajeExito) { ?>
                                        <div class="alert alert-soft"><?php echo $mensajeExito; ?></div>
                                    <?php } ?>
                                    <form method="post" class="row" id="form-proyeccion">
                                        <input type="hidden" name="INDEX" value="<?php echo $indiceEditar !== null ? $indiceEditar : ''; ?>">
                                        <div class="col-md-3 col-12">
                                            <label>Empresa</label>
                                            <select name="EMPRESA" class="form-control" required id="select-empresa">
                                                <option value="">Seleccione empresa</option>
                                                <?php foreach ($ARRAYEMPRESA as $empresa) { ?>
                                                    <option value="<?php echo $empresa['ID_EMPRESA']; ?>" <?php echo $empresaSeleccionForm == $empresa['ID_EMPRESA'] ? 'selected' : ''; ?>><?php echo $empresa['NOMBRE_EMPRESA']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <label>Tipo materia prima</label>
                                            <select name="TIPO_MP" class="form-control" required>
                                                <option value="">Seleccione tipo</option>
                                                <option value="Granel" <?php echo $datosEdicion['tipo_mp'] === 'Granel' ? 'selected' : ''; ?>>Granel</option>
                                                <option value="Bulk" <?php echo $datosEdicion['tipo_mp'] === 'Bulk' ? 'selected' : ''; ?>>Bulk</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-12">
                                            <label>Semana</label>
                                            <input type="number" name="SEMANA" class="form-control" min="1" max="53" required value="<?php echo htmlspecialchars($datosEdicion['semana']); ?>">
                                        </div>
                                        <div class="col-md-2 col-12">
                                            <label>Kg netos proyectados</label>
                                            <input type="number" step="0.01" name="KG_PROYECTADO" class="form-control" required value="<?php echo htmlspecialchars($datosEdicion['kg_proyectado']); ?>">
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
                                    <div class="box-controls pull-right">
                                        <form method="post" class="form-inline">
                                            <label class="mr-10">Empresa</label>
                                            <select name="EMPRESA_FILTRO" class="form-control mr-5">
                                                <option value="">Seleccione empresa</option>
                                                <option value="ALL" <?php echo $empresaFiltroTabla === 'ALL' ? 'selected' : ''; ?>>Mostrar todas</option>
                                                <?php foreach ($ARRAYEMPRESA as $empresa) { ?>
                                                    <option value="<?php echo $empresa['ID_EMPRESA']; ?>" <?php echo $empresaFiltroTabla == $empresa['ID_EMPRESA'] ? 'selected' : ''; ?>><?php echo $empresa['NOMBRE_EMPRESA']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                                        </form>
                                    </div>
                                    <p class="mb-0 text-muted">Solo se muestran proyecciones de la temporada activa. Empresa seleccionada: <?php echo htmlspecialchars($nombreEmpresa); ?></p>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered projection-table">
                                        <thead>
                                            <tr>
                                                <th>Empresa</th>
                                                <th class="text-center">Tipo materia prima</th>
                                                <th>Semana llegada</th>
                                                <th class="text-right">Kg Neto proyectados</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Operaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($proyeccionesFiltradas) { foreach ($proyeccionesFiltradas as $index => $proy) { ?>
                                                <tr>
                                                    <td><?php echo isset($nombreEmpresas[$proy['empresa']]) ? htmlspecialchars($nombreEmpresas[$proy['empresa']]) : $proy['empresa']; ?></td>
                                                    <td class="text-center">
                                                        <?php
                                                            $tipoMateriaPrima = isset($proy['tipo_materia_prima']) ? $proy['tipo_materia_prima'] : ($proy['es_bulk'] ? 'Bulk' : 'Granel');
                                                            if ($tipoMateriaPrima === 'Bulk') { ?>
                                                            <span class="tag tag-bulk">Bulk</span>
                                                        <?php } else { ?>
                                                            <span class="tag tag-envasado">Granel</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $proy['semana']; ?></td>
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
                                                            <input type="hidden" name="EMPRESA_FILTRO" value="<?php echo $empresaFiltroTabla === 'ALL' ? 'ALL' : $empresaFiltroTabla; ?>">
                                                            <button type="submit" name="EDITAR" value="<?php echo $index; ?>" class="btn btn-sm btn-outline-primary">Editar</button>
                                                        </form>
                                                        <?php if (!empty($proy['habilitado'])) { ?>
                                                            <form method="post" class="d-inline" onsubmit="return confirm('¿Deshabilitar registro?');">
                                                                <input type="hidden" name="EMPRESA_FILTRO" value="<?php echo $empresaFiltroTabla === 'ALL' ? 'ALL' : $empresaFiltroTabla; ?>">
                                                                <button type="submit" name="DESHABILITAR" value="<?php echo $index; ?>" class="btn btn-sm btn-outline-danger">Deshabilitar</button>
                                                            </form>
                                                        <?php } else { ?>
                                                            <form method="post" class="d-inline">
                                                                <input type="hidden" name="EMPRESA_FILTRO" value="<?php echo $empresaFiltroTabla === 'ALL' ? 'ALL' : $empresaFiltroTabla; ?>">
                                                                <button type="submit" name="HABILITAR" value="<?php echo $index; ?>" class="btn btn-sm btn-outline-success">Habilitar</button>
                                                            </form>
                                                        <?php } ?>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar registro definitivamente?');">
                                                            <input type="hidden" name="EMPRESA_FILTRO" value="<?php echo $empresaFiltroTabla === 'ALL' ? 'ALL' : $empresaFiltroTabla; ?>">
                                                            <button type="submit" name="ELIMINAR" value="<?php echo $index; ?>" class="btn btn-sm btn-outline-secondary">Eliminar</button>
                                                        </form>
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
