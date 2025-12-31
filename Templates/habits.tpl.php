<?php

defined('RESTRICTED') or exit('Restricted access');
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$habits = $tpl->get('habits');

?>

<div class="pageheader">
    <div class="pageicon"><span class="fa fa-bullseye"></span></div>
    <div class="pagetitle">
        <h1>Habits</h1>
    </div>
</div>

<div class="maincontent">
    <div class="maincontentinner" style="text-align: center;">
        <a href="#/daily/newHabit" class="btn btn-link action-link pull-right" style="margin-top:-7px;"><i class="fa fa-plus"></i> <?php echo $tpl->__('habits.create_habit'); ?></a>
        <h5 class="subtitle"><?php echo $tpl->__('habits.title'); ?></h5>
        <br/>


        <?php foreach ($habits as $habit) {?>
            <a href="#/daily/showHabit?id=<?php echo $habit->id; ?>"><?php echo $habit->name; ?></a><br>
        <?php } ?>

    </div>
</div>


