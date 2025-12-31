<?php
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$habit = $tpl->get('habit');
?>


<!-- Enum / list values -->
<div class="form-group">
    <label class="control-label"><?php echo $tpl->__('label.tags'); ?></label>
    <div class="">
        <input type="text" value="<?php $tpl->e($habit->enumValues); ?>" name="enumValues" id="tags" />
    </div>
</div>



<script type="text/javascript">
    jQuery(document).ready(function(){
        leantime.ticketsController.initTagsInput();
    });
</script>