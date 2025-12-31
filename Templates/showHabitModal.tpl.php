<?php

defined('RESTRICTED') or exit('Restricted access');
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$habit = $tpl->get('habit');

?>

<div style="min-width:90%">
    <h1><?= $tpl->__('habits.show.title') ?></h1>

    <?php echo $tpl->displayNotification(); ?>


    <form class="formModal" action="<?= BASE_URL ?>/daily/showHabit" method="post">
        <?php $tpl->displaySubmodule('daily-habitDetails') ?>
    </form>

</div>
<br />

<script type="text/javascript">

    jQuery(document).ready(function(){

        <?php if (isset($_GET['closeModal'])) { ?>
        jQuery.nmTop().close();
        <?php } ?>
    });

</script>
