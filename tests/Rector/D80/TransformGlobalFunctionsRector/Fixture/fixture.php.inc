<?php

function drupal_set_message($a, $b) {};

drupal_set_message('Hello world', 'custom');

?>
-----
<?php

function drupal_set_message($a, $b) {};

\Drupal::messenger()->addMessage('Hello world', 'custom');

?>
