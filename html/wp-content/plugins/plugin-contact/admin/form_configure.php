<?php
global $wpdb;

if (isset($_POST['btnguardar'])) {
  if (
    !empty($_POST)
    and $_POST['title_form'] != ''
  ) {
    $tabla_configure = $wpdb->prefix . 'form_configure';
    $title_form = sanitize_text_field($_POST['title_form']);
    $email_admin = $_POST['email_admin'];
    $field_name  = isset($_POST['field_name'])? 1 : 0;
    $field_email  = isset( $_POST['field_email'])? 1 : 0;
    $field_phone  = isset($_POST['field_phone'])? 1 : 0;
    $field_checkbox  = isset($_POST['field_checkbox'])? 1 : 0;
    $drop_down  = isset($_POST['drop_down'])? 1 : 0;
    $paragraph_text  = isset( $_POST['paragraph_text'])? 1 : 0;
    $update=$wpdb->update(
      $tabla_configure,
      array(
        'title_form' => $title_form,
        'email_admin' => $email_admin,
        'field_name' => $field_name,
        'field_email' => $field_email,
        'field_phone' => $field_phone,
        'field_checkbox' => $field_checkbox,
        'drop_down' => $drop_down,
        'paragraph_text' => $paragraph_text,

      ),
      array('id'=> 1)
    );
    if(!$update){
      $wpdb->insert(
        $tabla_configure,
        array(
          'title_form' => $title_form,
          'email_admin' => $email_admin,
          'field_name' => $field_name,
          'field_email' => $field_email,
          'field_phone' => $field_phone,
          'field_checkbox' => $field_checkbox,
          'drop_down' => $drop_down,
          'paragraph_text' => $paragraph_text,

        )
      );
    }
    
    echo "<p class=''><mark>Tu formulario esta creado, pega este short code [ntp_plugin_form] </mark>.<p>";
  }
}
?>

<div class="wrap">
  <?php
  echo "<h1>" . get_admin_page_title() . "</h1>";
  ?>
</div>
<form action="<?php get_the_permalink(); ?>" method="post" id="form_home" class="form">
  <div class="mb-3">
    <label for='Titulo formulario' class="form-label">Título del formulario</label>
    <input type="text" class="form-control" name="title_form" id="title_form" required>
  </div>
  <div class="mb-3">
    <label for='email_admin' class="form-label">Enviar al correo:</label>
    <input type="email" class="form-control" name="email_admin" id="email_admin" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
    <label for="text_box" class="form-label">Campos Estándar</label>
  </div>
  <div class="checkbox-inline">
    <input class="form-check-input" type="checkbox" name="field_name" value="1">
    <label class="form-check-label" for="flexCheckDefault">Nombre</label>
  </div>
  <div class="checkbox-inline">
    <input class="form-check-input" type="checkbox" name="field_email" value="1">
    <label class="form-check-label" for="flexCheckDefault">Correo</label>
  </div>
  <div class="checkbox-inline">
    <input class="form-check-input" type="checkbox" name="field_phone" value="1">
    <label class="form-check-label" for="flexCheckDefault">Teléfono</label>
  </div>
  <div class="checkbox-inline">
    <input class="form-check-input" type="checkbox" name="field_checkbox" value="1">
    <label class="form-check-label" for="flexCheckDefault">Casilla de verificación</label>
  </div>
  <div class="checkbox-inline">
    <input class="form-check-input" type="checkbox" name="drop_down" value="1">
    <label class="form-check-label" for="flexCheckDefault">Desplegable</label>
  </div>
  <div class="checkbox-inline mb-3">
    <input class="form-check-input" type="checkbox" name="paragraph_text" value="1">
    <label class="form-check-label" for="flexCheckDefault">Texto de Párrafo</label>
  </div>
  <div>
    <input type="submit" class="btn btn-primary" name="btnguardar" value="Crear formulario">
  </div>
</form>