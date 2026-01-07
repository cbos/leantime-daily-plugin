<?php

defined('RESTRICTED') or exit('Restricted access');
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$habits = $tpl->get('habits');
$habitRecords = $tpl->get('habitRecords');
$year = $tpl->get('year');

?>

<div class="pageheader">
    <div class="pageicon"><span class="fa fa-bullseye"></span></div>
    <div class="pagetitle">
        <h1>Habits</h1>
    </div>
</div>

<?php $tpl->displaySubmodule('daily-calendarGraphJS') ?>

<div class="maincontent">
    <div class="maincontentinner" style="text-align: center;">
        <a href="#/daily/newHabit" class="btn btn-link action-link pull-right" style="margin-top:-7px;"><i class="fa fa-plus"></i> <?php echo $tpl->__('habits.create_habit'); ?></a>
        <h5 class="subtitle"><?php echo $tpl->__('habits.title'); ?></h5>
        <br/>

        <?php foreach ($habits as $habit) {?>
            <a href="#/daily/showHabit?id=<?php echo $habit->id; ?>"><?php echo $habit->name; ?></a><br>

            <div class="calendar-placeholder<?php echo $habit->id; ?>"></div>

            <script type="text/javascript">
                jQuery(document).ready(function(){

                    let intensityScaleStart= 1;
                    let intensityScaleEnd= 10;
                    let color = "Numeric";
                    let tooltipTemplate = leantime.heatmapCalendar.template`${"date"}: ${"contentValue"}`
                    let calculateIntensity = function(value){
                        return parseInt(value);
                    }
                    let calculateContentValue = function(value){
                        return parseInt(value);
                    }

                    <?php
                    if ($habit->habitType == 0) {
                        echo "intensityScaleStart = 0;";
                        echo "intensityScaleEnd = 1;";
                        echo "color = 'Yes/no';";
                        echo "calculateIntensity = function(value){return value === \"1\" ? 1 : 0;};";
                        echo "calculateContentValue = function(value){return value === \"1\" ? 'Yes' : 'No';};";
                    } elseif ($habit->habitType == 1) {
                        echo "color = 'Numeric';";
                        echo "intensityScaleStart = ".$habit->numMinValue.";";
                        echo "intensityScaleEnd = ".$habit->numMaxValue.";";
                    } elseif ($habit->habitType == 2) {
                        echo "color = 'Enum/list';";


                        echo "const valueMapping = new Map();";
                        $selectValues = explode("," ,$habit->enumValues);
                        foreach($selectValues as $key=>$value) {
                            echo "valueMapping.set(\"".$value."\", ".$key.");";
                        }

                        echo "calculateIntensity = function(value){return valueMapping.get(value);};";
                        echo "calculateContentValue = function(value){return value;};";
                        echo "intensityScaleStart = 0;";
                        echo "intensityScaleEnd = valueMapping.size;";
                    }
                    ?>

                    const calendarData<?php echo $habit->id; ?> = {
                        year: <?php echo $year ?>,  // (optional) defaults to current year
                        colors: color,
                        showCurrentDayBorder: true,
                        defaultEntryIntensity: 1,
                        intensityScaleStart: intensityScaleStart,
                        intensityScaleEnd: intensityScaleEnd,
                        urlTemplate: leantime.heatmapCalendar.template`#/daily/showDaily?selectedDate=${"date"}`,
                        tooltipTemplate: tooltipTemplate,
                        entries: [
                            <?php
                                $hr = collect($habitRecords)->where('habitId', $habit->id);
                                foreach ($hr as $habitRecord) { ?>
                                    {
                                        date: "<?php echo $habitRecord->date ?>",
                                        intensity: calculateIntensity("<?php echo $habitRecord  ->value ?>"),
                                        contentValue: calculateContentValue("<?php echo $habitRecord  ->value ?>")
                                    },
                            <?php } ?>
                        ]
                    }

                    leantime.heatmapCalendar.renderHeatmapCalendar(document.querySelector('.calendar-placeholder<?php echo $habit->id; ?>'), calendarData<?php echo $habit->id; ?>)

                });
            </script>
        <?php } ?>

    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){

        <?php if (isset($_GET['closeModal'])) { ?>
            jQuery.nmTop().close();
        <?php } ?>
    });
</script>

