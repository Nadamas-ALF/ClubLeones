<?php

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion        = $_POST['accion']        ?? '';
    $cod_distrito  = $_POST['cod_distrito']  ?? null;
    $cod_provincia = $_POST['cod_provincia'] ?? null;
    $cod_canton    = $_POST['cod_canton']    ?? null;
    $nombre        = $_POST['nombre_distrito'] ?? null;

    if ($accion === 'crear' || $accion === 'actualizar') {

        if ($accion === 'crear' || empty($cod_distrito)) {


            //guardar
            $sql = "BEGIN insertar_distrito(:p_cod_provincia, :p_cod_canton, :p_nombre_distrito); END;";
            $stid = oci_parse($conn, $sql);
            oci_bind_by_name($stid, ":p_cod_provincia", $cod_provincia);
            oci_bind_by_name($stid, ":p_cod_canton", $cod_canton);
            oci_bind_by_name($stid, ":p_nombre_distrito", $nombre);

            if (!@oci_execute($stid)) {
                $e = oci_error($stid);
                $mensaje = "Error al insertar distrito: " . $e['message'];
                $tipo_mensaje = 'danger';
            } else {
                $mensaje = "Distrito insertado correctamente.";
                $tipo_mensaje = 'success';
            }
        } else {


            //Actualizar 
            $sql = "BEGIN actualizar_distrito(
                        :p_cod_distrito,
                        :p_cod_provincia,
                        :p_cod_canton,
                        :p_nombre_distrito
                    ); END;";
            $stid = oci_parse($conn, $sql);
            oci_bind_by_name($stid, ":p_cod_distrito", $cod_distrito);
            oci_bind_by_name($stid, ":p_cod_provincia", $cod_provincia);
            oci_bind_by_name($stid, ":p_cod_canton", $cod_canton);
            oci_bind_by_name($stid, ":p_nombre_distrito", $nombre);

            if (!@oci_execute($stid)) {
                $e = oci_error($stid);
                $mensaje = "Error al actualizar distrito: " . $e['message'];
                $tipo_mensaje = 'danger';
            } else {
                $mensaje = "Distrito actualizado correctamente.";
                $tipo_mensaje = 'success';
            }
        }
    } elseif ($accion === 'eliminar') {


        //eliminar
        $sql = "BEGIN eliminar_distrito(:p_cod_distrito); END;";
        $stid = oci_parse($conn, $sql);
        oci_bind_by_name($stid, ":p_cod_distrito", $cod_distrito);

        if (!@oci_execute($stid)) {
            $e = oci_error($stid);
            $mensaje = "Error al eliminar distrito: " . $e['message'];
            $tipo_mensaje = 'danger';
        } else {
            $mensaje = "Distrito eliminado correctamente.";
            $tipo_mensaje = 'success';
        }
    }
}

//listado distritos
$sql_list = "SELECT d.cod_distrito,
                    d.cod_provincia,
                    d.cod_canton,
                    d.nombre_distrito,
                    c.nombre_canton
             FROM distritos d
             JOIN cantones c ON c.cod_canton = d.cod_canton
             ORDER BY d.cod_distrito";

//listad provincias
$sql_prov = "SELECT cod_provincia, nombre_provincia
             FROM provincias
             ORDER BY cod_provincia";

$stid_prov = oci_parse($conn, $sql_prov);
oci_execute($stid_prov);
?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mantenimiento de Distritos – Club de Leones de Tibás</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <nav class="navbar-socio">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="navbar-brand d-flex align-items-center gap-2" href="menu_socio.html">
                    <img src="img/logotibas.png" alt="Logo" style="width: 30px; height: 30px;">
                    <span class="text-white">Área de Socios</span>
                </a>
                <ul class="menu-principal">


                    <li><a href="#">Configuración</a>
                        <ul class="submenu">
                            <li><a href="mantenimiento_provincias.html">Provincias</a></li>
                            <li><a href="mantenimiento_cantones.html">Cantones</a></li>
                            <li><a href="mantenimiento_distritos.php">Distritos</a></li>
                            <li><a href="mantenimiento_tipos_actividad.html">Tipos de Actividad</a></li>
                            <li><a href="mantenimiento_socios.html">Registro de Socios</a></li>
                            <li><a href="mantenimiento_tipos_pago.html">Tipos de Pago</a></li>
                        </ul>
                    </li>


                    <li><a href="#">Ingresos/Egresos</a>
                        <ul class="submenu">
                            <li><a href="registro_actividades.html">Registro de Actividades</a></li>
                            <li><a href="actividades_por_socio.html">Actividades por Socio</a></li>
                            <li><a href="ingresos_egresos_actividad.html">Ingresos/Egresos por actividad</a></li>
                        </ul>
                    </li>


                    <li><a href="#">Reportes</a>
                        <ul class="submenu">
                            <li><a href="reporte_socios.html">Reporte de Socios</a></li>
                            <li><a href="reporte_pagos_cuotas.html">Reporte de Pagos de cuotas por Socio</a></li>
                            <li><a href="reporte_ingresos_egresos.html">Reporte de Ingresos y egresos por Actividad</a></li>
                            <li><a href="recibo_pago.html">Recibo de Pago</a></li>
                        </ul>
                    </li>
                    <li><a href="login.html">Cerrar Sesión</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container my-5">
        <h1 class="h3 mb-4">Gestión de Distritos</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="card mb-5">
            <div class="card-body">
                <h5 class="card-title text-accent">Registrar Nuevo Distrito</h5>
                <form id="form-distrito" action="mantenimiento_distritos.php" method="POST">
                    <input type="hidden" name="cod_distrito" id="cod_distrito">
                    <input type="hidden" name="accion" id="accion" value="crear">
                    <div class="row g-3">

                        <div class="col-md-3">
                            <label for="cod_provincia" class="form-label">Provincia</label>
                            <select id="cod_provincia" name="cod_provincia" class="form-select" required>
                                <option value="" selected disabled>Seleccione.</option>
                                <?php while (($prov = oci_fetch_assoc($stid_prov)) != false): ?>
                                    <option value="<?php echo $prov['COD_PROVINCIA']; ?>">
                                        <?php echo htmlspecialchars($prov['NOMBRE_PROVINCIA']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="cod_canton" class="form-label">Cantón</label>
                            <select id="cod_canton" name="cod_canton" class="form-select" required>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="nombre_distrito" class="form-label">Nombre del Distrito</label>
                            <input type="text" class="form-control" id="nombre_distrito" name="nombre_distrito" required>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-azul w-100">Guardar</button>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary w-100" onclick="nuevoDistrito()">
                                Nuevo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h2 class="h4 mb-3">Distritos Registrados</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>ID</th>
                        <th>Cantón</th>
                        <th>Nombre del Distrito</th>
                        <th>Cod. Postal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>


                <tbody>
                    <?php while (($row = oci_fetch_assoc($stid_list)) != false): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['COD_DISTRITO']); ?></td>
                            <td><?php echo htmlspecialchars($row['NOMBRE_CANTON']); ?></td>
                            <td><?php echo htmlspecialchars($row['NOMBRE_DISTRITO']); ?></td>
                            <td>-</td>
                            <td>

                                <button type="button"
                                    class="btn btn-sm btn-amarillo"
                                    onclick="editarDistrito(
                        '<?php echo $row['COD_DISTRITO']; ?>',
                        '<?php echo $row['COD_PROVINCIA']; ?>',
                        '<?php echo $row['COD_CANTON']; ?>',
                        '<?php echo htmlspecialchars($row['NOMBRE_DISTRITO'], ENT_QUOTES); ?>'
                    )">
                                    Editar
                                </button>


                                <form method="post" action="mantenimiento_distritos.php"
                                    style="display:inline"
                                    onsubmit="return confirm('¿Eliminar este distrito?');">
                                    <input type="hidden" name="cod_distrito" value="<?php echo $row['COD_DISTRITO']; ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>


            </table>
        </div>
    </main>

    <footer class="text-center">
        <div class="container"><small>&copy; Club de Leones de Tibás — 2025</small></div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <!--JSON para cargar la lista de cantones-->
    <?php
    $cantonesPorProvincia = [];
    $sql_cant = "SELECT cod_canton, cod_provincia, nombre_canton FROM cantones ORDER BY cod_canton";
    $stid_cant = oci_parse($conn, $sql_cant);
    oci_execute($stid_cant);

    while ($row = oci_fetch_assoc($stid_cant)) {
        $prov = $row['COD_PROVINCIA'];

        if (!isset($cantonesPorProvincia[$prov])) {
            $cantonesPorProvincia[$prov] = [];
        }

        $cantonesPorProvincia[$prov][] = [
            'id' => $row['COD_CANTON'],
            'nombre' => $row['NOMBRE_CANTON']
        ];
    }
    ?>


    <!--Parte de JavaScript-->
    <script>
        const cantonesPorProvincia = <?php echo json_encode($cantonesPorProvincia); ?>;
        const provinciaSelect = document.getElementById('cod_provincia');
        const cantonSelect = document.getElementById('cod_canton');

        function actualizarCantones() {

            cantonSelect.innerHTML = '<option value="" selected disabled>Seleccione...</option>';
            cantonSelect.disabled = true;

            const provinciaId = provinciaSelect.value;
            const cantones = cantonesPorProvincia[provinciaId];

            if (cantones && cantones.length > 0) {
                // Habilitar el selector de Cantón
                cantonSelect.disabled = false;

                // Añadir las nuevas opciones
                cantones.forEach(canton => {
                    const option = document.createElement('option');
                    option.value = canton.id;
                    option.textContent = canton.nombre;
                    cantonSelect.appendChild(option);
                });
            }
        }

        provinciaSelect.addEventListener('change', actualizarCantones);
        actualizarCantones();


        function editarDistrito(cod, codProv, codCanton, nombre) {
            document.getElementById('cod_distrito').value = cod;
            document.getElementById('cod_provincia').value = codProv;
            document.getElementById('cod_canton').value = codCanton;
            document.getElementById('nombre_distrito').value = nombre;
            document.getElementById('accion').value = 'actualizar';

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function nuevoDistrito() {
            document.getElementById('form-distrito').reset();
            document.getElementById('cod_distrito').value = '';
            document.getElementById('accion').value = 'crear';
        }
    </script>


</body>

</html>