<?php
require_once __DIR__ . '/helpers.php';
require_login();

$rol = $_SESSION['rol'] ?? '';

function nodeLink(string $label, string $href): string {
    $label = htmlspecialchars($label);
    $href  = htmlspecialchars($href);
    return "<a class=\"node link\" href=\"{$href}\">{$label}</a>";
}
function nodeText(string $label): string {
    $label = htmlspecialchars($label);
    return "<span class=\"node\">{$label}</span>";
}
?>
<?php include __DIR__ . '/header.php'; ?>

<h2>Mapa del sitio</h2>
<p style="text-align:center; font-weight:700; opacity:.85; margin-top:-10px;">
  Diagrama jerárquico de navegación (secciones principales y secundarias)
</p>

<div class="sitemap-tree">

  <!-- NIVEL 0 -->
  <div class="tree-level level-0">
    <div class="tree-item">
      <span class="tree-box root">SIGERUTT</span>
    </div>
  </div>

  <!-- CONECTOR -->
  <div class="tree-connector">
    <span class="v-line"></span>
  </div>

  <!-- NIVEL 1 -->
  <div class="tree-level level-1">
    <div class="tree-item">
      <span class="tree-box">Usuarios</span>
      <div class="tree-children">
        <?php if ($rol === 'admin'): ?>
          <?= nodeLink("Nuevo usuario", BASE_URL . "registrar_usuario.php") ?>
          <?= nodeLink("Usuarios (Listado)", BASE_URL . "ver_usuarios.php") ?>
        <?php else: ?>
          <?= nodeText("Acceso restringido (solo admin)") ?>
        <?php endif; ?>
        <span class="node sub">Editar usuario (desde listado)</span>
        <span class="node sub">Eliminar usuario (desde listado)</span>
      </div>
    </div>

    <div class="tree-item">
      <span class="tree-box">Rutas</span>
      <div class="tree-children">
        <?= nodeLink("Nueva ruta", BASE_URL . "registrar_ruta_dinamica.php") ?>
        <?= nodeLink("Rutas (Listado)", BASE_URL . "ver_rutas.php") ?>
        <span class="node sub">Editar ruta (desde listado)</span>
        <span class="node sub">Eliminar ruta (desde listado)</span>
      </div>
    </div>

    <div class="tree-item">
      <span class="tree-box">Vehículos</span>
      <div class="tree-children">
        <?= nodeLink("Nuevo vehículo", BASE_URL . "registrar_vehiculo.php") ?>
        <?= nodeLink("Vehículos (Listado)", BASE_URL . "ver_vehiculos.php") ?>
        <span class="node sub">Editar vehículo (desde listado)</span>
        <span class="node sub">Eliminar vehículo (desde listado)</span>
      </div>
    </div>

    <div class="tree-item">
      <span class="tree-box">Operadores</span>
      <div class="tree-children">
        <?= nodeLink("Nuevo operador", BASE_URL . "registrar_operador.php") ?>
        <?= nodeLink("Operadores (Listado)", BASE_URL . "ver_operadores.php") ?>
        <span class="node sub">Editar operador (desde listado)</span>
        <span class="node sub">Eliminar operador (desde listado)</span>
      </div>
    </div>

    <div class="tree-item">
      <span class="tree-box">Asignaciones</span>
      <div class="tree-children">
        <?= nodeLink("Asignar ruta", BASE_URL . "asignaciones.php") ?>
        <?= nodeLink("Asignaciones (Listado)", BASE_URL . "ver_asignaciones.php") ?>
        <span class="node sub">Editar asignación (desde listado)</span>
        <span class="node sub">Eliminar asignación (desde listado)</span>
      </div>
    </div>

    <div class="tree-item">
      <span class="tree-box">Utilidades</span>
      <div class="tree-children">
        <?= nodeLink("Búsqueda global", BASE_URL . "buscar.php") ?>
        <?= nodeLink("Mapa del sitio", BASE_URL . "sitemap.php") ?>
        <span class="node sub">Login (público): login.php</span>
        <?= nodeLink("Cerrar sesión", BASE_URL . "php/logout.php") ?>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/footer.php'; ?>
