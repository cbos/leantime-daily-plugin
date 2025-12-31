<?php

defined('RESTRICTED') or exit('Restricted access');
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$habit = $tpl->get('habit');

?>

<?php $tpl->displaySubmodule($selectedHabitType->template)?>
