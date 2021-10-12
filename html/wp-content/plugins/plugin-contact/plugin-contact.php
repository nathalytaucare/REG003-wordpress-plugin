<?php

/**
 * Plugin Name:       Plugin Contact
 * Plugin URI:        
 * Description:       Plugin to create a contact form. Use the shortcode [ntp_plugin_form].
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Nathaly Taucare
 * Author URI:        https://github.com/nathalytaucare
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        
 * Text Domain:       plugin-contact
 * Domain Path:       /languages
 */

// Cuando el plugin se active se crea la tabla para recoger los datos si no existe
register_activation_hook(__FILE__, 'Ntp_Contacts_init');

function Ntp_Contacts_init()
{
  global $wpdb; // Este objeto global permite acceder a la base de datos de WP
  // Crea la tabla sólo si no existe
  // Utiliza el mismo prefijo del resto de tablas
  $tabla_contacts = $wpdb->prefix . 'contacts';
  $tabla_form_configure = $wpdb->prefix . 'form_configure';
  // Utiliza el mismo tipo de orden de la base de datos
  $charset_collate = $wpdb->get_charset_collate();
  // Prepara la consulta
  $query = "CREATE TABLE IF NOT EXISTS $tabla_contacts (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      nombre varchar(50) NOT NULL,
      correo varchar(100) NOT NULL,
      telefono varchar(100) NOT NULL,
      asunto varchar(100) NOT NULL,
      mensaje varchar(500) NOT NULL,
      aceptacion smallint(4) NOT NULL,
      created_at datetime NOT NULL,
      UNIQUE (id)
      ) $charset_collate;";
  $query2 = "CREATE TABLE IF NOT EXISTS $tabla_form_configure (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    title_form varchar(50) NOT NULL,
    email_admin varchar(100) NOT NULL,
    field_name smallint(4) NOT NULL,
    field_email smallint(4) NOT NULL,
    field_phone smallint(4) NOT NULL,
    field_checkbox smallint(4) NOT NULL,
    drop_down smallint(4) NOT NULL,
    paragraph_text smallint(4) NOT NULL,
    UNIQUE (id)
    ) $charset_collate;";
  // La función dbDelta permite crear tablas de manera segura se
  // define en el archivo upgrade.php que se incluye a continuación
  include_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta($query); // Lanza la consulta para crear la tabla de manera segura
  dbDelta($query2);
}

// Define el shortcode que muestra el formulario en pantalla.

add_shortcode('ntp_plugin_form', 'Ntp_Plugin_form');

/** 
 * Define la función que ejecutará el shortcode
 * De momento sólo pinta un formulario que no hace nada
 * @return string
 */
function Ntp_Plugin_form()
{
  global $wpdb; // Este objeto global permite acceder a la base de datos de WP
  $tabla_form_configure = $wpdb->prefix . 'form_configure';
  $configurations = $wpdb->get_results("SELECT * FROM $tabla_form_configure");

  // Si viene del formulario  graba en la base de datos
  // Cuidado con el último igual de la condición del if que es doble
  if (
    !empty($_POST)
    // and $_POST['nombre'] != ''
    // and is_email($_POST['correo'])
    // and $_POST['asunto'] != ''
    // and $_POST['mensaje'] != ''
    // and $_POST['aceptacion'] == '1'
    and wp_verify_nonce($_POST['contact_nonce'], 'graba_contact')
  ) {
    $tabla_contacts = $wpdb->prefix . 'contacts';
    $nombre = isset($_POST['nombre'])?sanitize_text_field($_POST['nombre']):'';
    $correo = isset($_POST['correo'])?sanitize_email($_POST['correo']):'';
    $telefono = isset($_POST['phone'])?sanitize_text_field($_POST['phone']):'';
    $asunto = isset($_POST['asunto'])?sanitize_text_field($_POST['asunto']):'';
    $mensaje = isset($_POST['mensaje'])?sanitize_textarea_field($_POST['mensaje']):'';
    $aceptacion = isset($_POST['aceptacion'])?(int)($_POST['aceptacion']):'';
    $created_at = date('Y-m-d H:i:s');
    $wpdb->insert(
      $tabla_contacts,
      array(
        'nombre' => $nombre,
        'correo' => $correo,
        'telefono'=> $telefono,
        'asunto' => $asunto,
        'mensaje' => $mensaje,
        'aceptacion' => $aceptacion,
        'created_at' => $created_at,
      )
    );
    echo "<p class='exito'><b>Tus datos han sido registrados</b>. Gracias 
            por tu interés. En breve contactaré contigo.<p>";
  }

  // Esta función de PHP activa el almacenamiento en búfer de salida (output buffer)
  // Cuando termine el formulario lo imprime con la función ob_get_clean
  // Carga esta hoja de estilo para poner más bonito el formulario
   wp_enqueue_style('css_aspirante', plugins_url('style.css', __FILE__));
  ob_start();

?>
  <form action="<?php get_the_permalink(); ?>" method="post" id="form_aspirante" class="cuestionario">
    <?php wp_nonce_field('graba_contact', 'contact_nonce');
    foreach ($configurations as $configuration) {
      $title_form = $configuration->title_form;
      $field_name = esc_textarea($configuration->field_name);
      $field_email =  $configuration->field_email;
      $field_phone =  $configuration->field_phone;
      $drop_down =  $configuration->drop_down;
      $paragraph_text =  $configuration->paragraph_text;
      $field_checkbox =  $configuration->field_checkbox;
      echo " <h2>$title_form</h2>";

      if ($field_name) {
    ?>
        <div class="form-input">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>
      <?php
      }
      if ($field_email) {
      ?>
        <div class="form-input">
          <label for='correo' class="form-label">Correo</label>
          <input type="email" name="correo" id="correo" class="form-control" required>
        </div>
      <?php
      }
      if ($field_phone) {
      ?>
        <div class="form-input">
          <label for='phone' class="form-label">Teléfono</label>
          <input type="number" name="phone" id="phone" class="form-control" required>
        </div>

      <?php
      }
      if ($drop_down) {
      ?>
        <div class="form-input">
          <label for='asunto'>Asunto</label>
          <select name="asunto">
            <option value="Consulta en general" selected> Consulta en general</option>
            <option value="Consulta post venta">Consulta post venta</option> <!-- Opción por defecto -->
            <option value="Cotizar producto">Cotizar producto</option>
            <option value="Cotizar servicio">Cotizar servicio</option>
            <option value="Otros requerimientos">Otros requerimientos</option>
          </select>
        </div>
      <?php
      }
      if ($paragraph_text) {
      ?>
        <div class="form-input">
          <label for='mensaje'>Mensaje</label>
          <textarea name="mensaje" id="mensaje" cols="30" rows="10" required></textarea>
        </div>
      <?php
      }
      if ($field_checkbox) {
      ?>
        <div class="form-input">
          <p><input type="checkbox" id="aceptacion" value="1" name="aceptacion" required> Acepto los <a href="#">Términos y Condiciones</a></p>
        </div>
    <?php

      }
    }
    ?>
    <div class="form-input">
      <input type="submit" name="form_submit" value="Enviar">
    </div>
  </form>
<?php

  // Devuelve el contenido del buffer de salida
  return ob_get_clean();
}

// El hook "admin_menu" permite agregar un nuevo item al menú de administración
add_action("admin_menu", "Ntp_Contact_menu");

/**
 * Agrega el menú del plugin al escritorio de WordPress
 *
 * @return void
 */
function Ntp_Contact_menu()
{
  add_menu_page(
    'Formulario de Configuración', // Título de la pagina 
    'Contacto',            // Título del menu
    'manage_options',      // Rol
    plugin_dir_path(__FILE__) . 'admin/form_configure.php',    // slug en otra pagina
    null,   // función del contenido 
    'dashicons-feedback',  // icono
    '75'                     // prioridad
  );
}


// // Hooks admin-post
// add_action( 'wp_head', 'send_mail_data' );

// // Funcion callback
// function send_mail_data() {
//   if(isset($_POST['form_submit']))
//   {
//     $name = sanitize_text_field($_POST['nombre']);
//     $email = sanitize_email($_POST['correo']);
//     // $asunto = sanitize_text_field($_POST['asunto']);
//     $comments = sanitize_textarea_field($_POST['mensaje']);

//     $to = 'nathaly.tau@gmail.com';
//     $subject = 'TEST';
//     $menssage = ''.$name.'-'.$email.''.$comments;
//     wp_mail($to,$subject,$menssage);
//   }
// }

// incluir bootstrap

function incluirBootstrapJs($hook)
{
  // echo "<script>console.log('$hook')</script>";
  if ($hook != "plugin-contact/admin/form_configure.php") {
    return;
  }
  wp_enqueue_script('bootstrapJs', plugins_url('admin/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'incluirBootstrapJs');

function incluirBootstrapCss($hook)
{
  if ($hook != "plugin-contact/admin/form_configure.php") {
    return;
  }
  wp_enqueue_style('bootstrapCss', plugins_url('admin/bootstrap/css/bootstrap.min.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'incluirBootstrapCss');
?>