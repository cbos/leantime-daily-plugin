<?php

defined('RESTRICTED') or exit('Restricted access');
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$habits = $tpl->get('habits');
$habitRecords = $tpl->get('habitRecords');
$selectedDate = $tpl->get('selectedDate');
?>


<div style="min-width:90%">
    <h1><?= $tpl->__('mydaily.title') ?><?php echo $selectedDate ?></h1>

    <?php echo app('blade.compiler')::render('@include("daily::partials.myDaily", [
                                                "habits" => $habits,
                                                "habitRecords" => $habitRecords,
                                                "selectedDate" => $selectedDate
                                            ])', [
            'habits' => $habits,
            'habitRecords' => $habitRecords,
            'selectedDate' => $selectedDate
    ]); ?>

</div>




