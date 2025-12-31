<?php
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$habit = $tpl->get('habit');
$habitTypes = $tpl->get('habitTypes');
$selectedHabitType = $tpl->get('selectedHabitType');

?>
<input type="hidden" value="<?php $tpl->e($habit->id); ?>" name="id" autocomplete="off" readonly/>

<div class="row">
    <div class="col-md-12">
        <div class="row marginBottom">
            <div class="col-md-12">

                <div class="form-group">
                    <label class="control-label"><?php echo $tpl->__('label.habit.name'); ?></label>
                    <div class="">
                        <input type="text" class="form-control" name="name" value="<?php $tpl->e($habit->name); ?>" placeholder="<?= $tpl->__('input.placeholders.enter_title_of_habit')?>">
                    </div>
                </div>

                <?php if (isset($habit->id) && !empty($habit->id)) { ?>
                    <div class="form-group">
                        <label class="control-label"><?php echo $tpl->__('label.habittype'); ?></label>
                        <div class="">
                            <?php echo $selectedHabitType->name; ?>
                            <a href="javascript:void(0)" class="infoToolTip" data-placement="left" data-toggle="tooltip" data-tippy-content="<?php echo $tpl->__('tooltip.habit.not_able_to_change'); ?>">
                                &nbsp;<i class="fa fa-question-circle"></i>&nbsp;
                            </a>
                        </div>
                    </div>
                <?php } else { ?>
                    <!-- Habit Type -->
                    <div class="form-group">
                        <label class="control-label"><?php echo $tpl->__('label.habittype'); ?></label>
                        <div class="">
                            <select
                                    id="habittype-select"
                                    class=""
                                    name="habitType"
                                    hx-post="/daily/habittypeDetails"
                                    hx-target="#habittype-specific-fields"
                            >
                                <?php foreach ($habitTypes as $habitType) {?>
                                    <option value="<?php echo $habitType->id; ?>"
                                            <?php if ($habit->habitType == $habitType->id) {
                                                echo "selected='selected'";
                                            } ?>
                                    ><?php echo $tpl->escape($habitType->name); ?></option>
                                <?php } ?>
                            </select>
                            <a href="javascript:void(0)" class="infoToolTip" data-placement="left" data-toggle="tooltip" data-tippy-content="<?php echo $tpl->__('tooltip.habittype'); ?>">
                                &nbsp;<i class="fa fa-question-circle"></i>&nbsp;
                            </a>
                        </div>
                    </div>
                <?php } ?>

                <div id="habittype-specific-fields">
                    <?php $tpl->displaySubmodule($selectedHabitType->template)?>
                </div>
            </div>
        </div>

        <div class="sticky-modal-footer">
            <div class="row">
                <div class="col-md-12" style="margin-top:15px;">
                    <input type="hidden" name="saveTicket" value="1" />
                    <input type="hidden" id="saveAndCloseButton" name="saveAndCloseTicket" value="0" />

                    <input type="submit" name="saveTicket" class="saveTicketBtn" value="<?php echo $tpl->__('buttons.save'); ?>"/>
                    <input type="submit" name="saveAndCloseTicket" class="btn btn-outline" onclick="jQuery('#saveAndCloseButton').val('1');" value="<?php echo $tpl->__('buttons.save_and_close'); ?>"/>
                </div>
            </div>
        </div>


    </div>
</div>

