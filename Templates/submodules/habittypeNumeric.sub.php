<?php
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$habit = $tpl->get('habit');
?>


<!-- Min value -->
<div class="form-group">
    <label class="control-label"><?php echo $tpl->__('label.habit.minvalue'); ?></label>
    <div class="">
        <input type="number" class="form-control" name="numMinValue" value="<?php echo $habit->numMinValue; ?>">
        <a href="javascript:void(0)" class="infoToolTip" data-placement="left" data-toggle="tooltip" data-tippy-content="<?php echo $tpl->__('tooltip.habit.minvalue'); ?>">
            &nbsp;<i class="fa fa-question-circle"></i>&nbsp;
        </a>
    </div>
</div>

<!-- Max value -->
<div class="form-group">
    <label class="control-label"><?php echo $tpl->__('label.habit.maxvalue'); ?></label>
    <div class="">
        <input type="number" class="form-control" name="numMaxValue" value="<?php echo $habit->numMaxValue; ?>">
        <a href="javascript:void(0)" class="infoToolTip" data-placement="left" data-toggle="tooltip" data-tippy-content="<?php echo $tpl->__('tooltip.habit.maxvalue'); ?>">
            &nbsp;<i class="fa fa-question-circle"></i>&nbsp;
        </a>
    </div>
</div>