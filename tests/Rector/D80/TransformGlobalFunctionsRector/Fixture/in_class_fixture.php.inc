<?php

function db_select($taxonomy_index, $param) {};

class MyClass
{
    public function myMethod() {
        $nid = 123;
        $tids = db_select('taxonomy_index', 'ti')
            ->fields('ti', ['tid', 'nid'])
            ->condition('ti.nid', $nid)
            ->execute()->fetchAllKeyed();
    }
}

?>
-----
<?php

function db_select($taxonomy_index, $param) {};

class MyClass
{
    public function myMethod() {
        $nid = 123;
        $tids = \Drupal::database()->select('taxonomy_index', 'ti')
            ->fields('ti', ['tid', 'nid'])
            ->condition('ti.nid', $nid)
            ->execute()->fetchAllKeyed();
    }
}

?>
