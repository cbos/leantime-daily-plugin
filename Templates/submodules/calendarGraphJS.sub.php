<?php
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
?>

<script language="javascript" type="text/javascript">
    const DEFAULT_SETTINGS = {
        year: new Date().getFullYear(),
        // https://colordesigner.io/gradient-generator
        colors: {
            'Yes/no':        ["#edffe8", "#266803"],
            'Numeric':     ["#edffe8", "#c9ffb9", "#a7fe8a", "#86fd5c", "#67fc2d", "#4cf505", "#41c604", "#349704", "#266803", "#163a02"],
            'Enum/list':        [ "#eb7900", "#0121dc", "#ffee68", "#02f3a0", "#286800", "#a35fff", "#ba008d", "#96fcff", "#0294f0", "#ff9de0"]
        },
        entries: [{date: "1900-01-01", color: "#7bc96f", intensity: 5, content: ""}],
        showCurrentDayBorder: true,
        defaultEntryIntensity: 4,
        intensityScaleStart: 1,
        intensityScaleEnd: 5,
        weekStartDay: 1,
    };

    leantime.heatmapCalendar = (function () {


        /**
         * Returns a number representing how many days into the year the supplied date is.
         * Example: first of january is 1, third of february is 34 (31+3)
         * @param {Date} date
         */
        var getHowManyDaysIntoYear = function (date) {
            return (
                (Date.UTC(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate()) -
                    Date.UTC(date.getUTCFullYear(), 0, 0)) / 24 / 60 / 60 / 1000
            );
        }

        var getHowManyDaysIntoYearLocal = function (date) {
            return (
                (Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()) -
                    Date.UTC(date.getFullYear(), 0, 0)) / 24 / 60 / 60 / 1000
            );
        }

        /**
         * Removes HTMLElements passed as entry.content and outside of the displayed year from rendering above the calendar
         */
        var removeHtmlElementsNotInYear = function (entries, year) {
            const calEntriesNotInDisplayedYear = entries.filter(e => new Date(e.date).getFullYear() !== year) ?? DEFAULT_SETTINGS.entries;
            calEntriesNotInDisplayedYear.forEach(e => e.content instanceof HTMLElement && e.content.remove());
        }

        var clamp = function (input, min, max) {
            return input < min ? min : input > max ? max : input;
        }

        var map = function (current, inMin, inMax, outMin, outMax) {
            const mapped = ((current - inMin) * (outMax - outMin)) / (inMax - inMin) + outMin;
            return clamp(mapped, outMin, outMax);
        }

        var getWeekdayShort = function (dayNumber) {
            return new Date(1970, 0, dayNumber + DEFAULT_SETTINGS.weekStartDay + 4).toLocaleDateString('en-US', {weekday: 'short'});
        }

        var renderHeatmapCalendar = function (el, calendarData) {
            const year = calendarData.year ?? DEFAULT_SETTINGS.year;
            const colors = typeof calendarData.colors === "string"
                ? DEFAULT_SETTINGS.colors[calendarData.colors]
                    ? {[calendarData.colors]: DEFAULT_SETTINGS.colors[calendarData.colors]}
                    : DEFAULT_SETTINGS.colors
                : calendarData.colors ?? DEFAULT_SETTINGS.colors;

            removeHtmlElementsNotInYear(calendarData.entries, year);

            const calEntries = calendarData.entries.filter(e => new Date(e.date + "T00:00").getFullYear() === year) ?? DEFAULT_SETTINGS.entries;

            const showCurrentDayBorder = calendarData.showCurrentDayBorder ?? DEFAULT_SETTINGS.showCurrentDayBorder;
            const defaultEntryIntensity = calendarData.defaultEntryIntensity ?? DEFAULT_SETTINGS.defaultEntryIntensity;

            const intensities = calEntries.filter(e => e.intensity).map(e => e.intensity);
            const minimumIntensity = intensities.length ? Math.min(...intensities) : DEFAULT_SETTINGS.intensityScaleStart;
            const maximumIntensity = intensities.length ? Math.max(...intensities) : DEFAULT_SETTINGS.intensityScaleEnd;
            const intensityScaleStart = calendarData.intensityScaleStart ?? minimumIntensity;
            const intensityScaleEnd = calendarData.intensityScaleEnd ?? maximumIntensity;

            const mappedEntries = [];
            calEntries.forEach(e => {
                const newEntry = {
                    intensity: defaultEntryIntensity,
                    ...e,
                };
                const colorIntensities = typeof colors === "string"
                    ? DEFAULT_SETTINGS.colors[colors]
                    : colors[e.color] ?? colors[Object.keys(colors)[0]];

                const numOfColorIntensities = Object.keys(colorIntensities).length;

                if (minimumIntensity === maximumIntensity && intensityScaleStart === intensityScaleEnd) {
                    newEntry.intensity = numOfColorIntensities;
                } else {
                    newEntry.intensity = Math.round(map(newEntry.intensity, intensityScaleStart, intensityScaleEnd, 1, numOfColorIntensities));
                }

                mappedEntries[getHowManyDaysIntoYear(new Date(e.date))] = newEntry;
            });

            const firstDayOfYear = new Date(Date.UTC(year, 0, 1));
            let numberOfEmptyDaysBeforeYearBegins = (firstDayOfYear.getUTCDay() + 7 - DEFAULT_SETTINGS.weekStartDay) % 7;

            const boxes = [];

            while (numberOfEmptyDaysBeforeYearBegins) {
                boxes.push({backgroundColor: "transparent"});
                numberOfEmptyDaysBeforeYearBegins--;
            }

            const lastDayOfYear = new Date(Date.UTC(year, 11, 31));
            const numberOfDaysInYear = getHowManyDaysIntoYear(lastDayOfYear);
            const todaysDayNumberLocal = getHowManyDaysIntoYearLocal(new Date());

            for (let day = 1; day <= numberOfDaysInYear; day++) {
                const box = {
                    classNames: [],
                };

                const currentDate = new Date(year, 0, day);
                const month = currentDate.toLocaleString('en-us', {month: 'short'});

                box.classNames.push(`month-${month.toLowerCase()}`);

                if (day === todaysDayNumberLocal && showCurrentDayBorder) {
                    box.classNames.push("today");
                }


                if (mappedEntries[day]) {
                    box.classNames.push("hasData");
                    const entry = mappedEntries[day];
                    box.date = entry.date;

                    if (entry.content) box.content = entry.content;
                    if (entry.contentValue) box.contentValue = entry.contentValue;

                    const currentDayColors = entry.color ? colors[entry.color] : colors[Object.keys(colors)[0]];
                    box.backgroundColor = currentDayColors[entry.intensity - 1];
                } else {
                    box.classNames.push("isEmpty");
                }
                boxes.push(box);
            }

            const heatmapCalendarGraphDiv = createDiv({
                cls: "heatmap-calendar-graph",
                parent: el,
            });

            createDiv({
                cls: "heatmap-calendar-year",
                text: String(year).slice(2),
                parent: heatmapCalendarGraphDiv,
            });

            const heatmapCalendarMonthsUl = createEl("ul", {
                cls: "heatmap-calendar-months",
                parent: heatmapCalendarGraphDiv,
            });

            ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"].forEach(month => {
                createEl("li", {text: month, parent: heatmapCalendarMonthsUl});
            });

            const heatmapCalendarDaysUl = createEl("ul", {
                cls: "heatmap-calendar-days",
                parent: heatmapCalendarGraphDiv,
            });

            for (let i = 0; i < 7; i++) {
                createEl("li", {text: getWeekdayShort(i), parent: heatmapCalendarDaysUl});
            }

            const heatmapCalendarBoxesUl = createEl("ul", {
                cls: "heatmap-calendar-boxes",
                parent: heatmapCalendarGraphDiv,
            });

            boxes.forEach(e => {
                let entry = createEl("li", {
                    attr: {
                        ...(e.backgroundColor && {style: `background-color: ${e.backgroundColor};`}),
                        ...(e.date && {"data-date": e.date}),
                    },
                    cls: e.classNames,
                    parent: heatmapCalendarBoxesUl,
                });

                if (calendarData.urlTemplate && e.date) {
                    entry = createEl("a", {
                        cls: "infoTooltip",
                        attr: {
                            href: calendarData.urlTemplate({date: e.date}),
                            style: "width: 100%; height: 100%; display: block;",
                            // 'data-placement': "left",
                            // 'data-toggle': "tooltip",
                            'data-tippy-content': calendarData.tooltipTemplate?.({date: e.date, contentValue: e.contentValue}),
                            //title: "Click to open link in new tab",

                        },
                        parent: entry,
                    })
                }

                createSpan({
                    cls: "heatmap-calendar-content",
                    parent: entry,
                    text: e.content,
                });
            });

            tippy('[data-tippy-content]');

        }


        //option are:
        // interface DomElementInfo {
        //        /**
        //         * The class to be assigned. Can be a space-separated string or an array of strings.
        //         */
        //        cls?: string | string[];
        //        /**
        //         * The textContent to be assigned.
        //         */
        //        text?: string | DocumentFragment;
        //        /**
        //         * HTML attributes to be added.
        //         */
        //        attr?: {
        //            [key: string]: string | number | boolean | null;
        //        };
        //        /**
        //         * HTML title (for hover tooltip).
        //         */
        //        title?: string;
        //        /**
        //         * The parent element to be assigned to.
        //         */
        //        parent?: Node;
        //        value?: string;
        //        type?: string;
        //        prepend?: boolean;
        //        placeholder?: string;
        //        href?: string;
        //    }

        /**
         * Generic function to create any HTML element with options.
         * @param {string} tag - The HTML tag name (e.g., 'div', 'li', 'span').
         * @param {Object} options - Configuration for the element (DomElementInfo).
         * @returns {HTMLElement}
         */
        var createEl = function (tag, options = {}) {
            const el = document.createElement(tag);

            // Handle CSS classes (cls)
            if (options.cls) {
                if (Array.isArray(options.cls)) {
                    el.classList.add(...options.cls.filter(Boolean));
                } else if (typeof options.cls === 'string') {
                    // Support space-separated strings
                    options.cls.split(' ').filter(Boolean).forEach(c => el.classList.add(c));
                }
            }

            // Handle textContent or DocumentFragment (text)
            if (options.text !== undefined) {
                if (options.text instanceof DocumentFragment) {
                    el.appendChild(options.text);
                } else {
                    el.textContent = String(options.text);
                }
            }

            // Handle specific HTML attributes (attr)
            if (options.attr) {
                for (const [key, value] of Object.entries(options.attr)) {
                    if (value === null) {
                        el.removeAttribute(key);
                    } else {
                        el.setAttribute(key, String(value));
                    }
                }
            }

            // Handle common properties directly
            if (options.title) el.title = options.title;
            if (options.placeholder) el.placeholder = options.placeholder;
            if (options.value !== undefined) el.value = options.value;
            if (options.type) el.type = options.type;
            if (options.href) el.setAttribute('href', options.href);

            // Handle parent attachment
            if (options.parent instanceof Node) {
                if (options.prepend) {
                    options.parent.prepend(el);
                } else {
                    options.parent.appendChild(el);
                }
            }

            return el;
        }

        /**
         * Creates a <div> element with the provided options.
         */
        var createDiv = function (options = {}) {
            return createEl('div', options);
        }

        /**
         * Creates a <span> element with the provided options.
         */
        var createSpan = function (options = {}) {
            return createEl('span', options);
        }

        // from: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals#tagged_templates
        var template = function (strings, ...keys) {
            return (...values) => {
                const dict = values[values.length - 1] || {};
                const result = [strings[0]];
                keys.forEach((key, i) => {
                    const value = Number.isInteger(key) ? values[key] : dict[key];
                    result.push(value, strings[i + 1]);
                });
                return result.join("");
            };
        }

        // Make public what you want to have public, everything else is private
        return {
            renderHeatmapCalendar: renderHeatmapCalendar,
            template: template
        };
    })();
</script>


<!-- Based on https://github.com/Richardsl/heatmap-calendar-obsidian/blob/master/styles.css -->
<style lang="css">
    .heatmap-calendar-graph > * {
        padding: 0px;
        margin: 0px;
        list-style: none;
    }

    .heatmap-calendar-graph {
        font-size: 0.65em;
        display: grid;
        grid-template-columns: auto 1fr;
        grid-template-areas:
    'year months'
    'days boxes';
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica,
        Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol';
        width: 100%;
        padding: 5px; /* 0px caused overflow and scrollbars */
    }

    .heatmap-calendar-graph ul {
        padding-inline-start: 0;
        margin-block-start: 0;
        margin-block-end: 0;
    }

    .heatmap-calendar-months {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        grid-area: months;
        margin-top: 0.1em;
        margin-bottom: 0.3em;
        grid-gap: 0.3em;
    }

    .heatmap-calendar-days {
        grid-area: days;
        margin-left: 0.1em;
        margin-right: 0.3em;
        white-space: nowrap;
    }

    .heatmap-calendar-boxes {
        grid-auto-flow: column;
        grid-template-columns: repeat(53, minmax(0, 1fr));
        grid-area: boxes;
    }

    .heatmap-calendar-days,
    .heatmap-calendar-boxes {
        display: grid;
        grid-gap: 0.3em;
        grid-template-rows: repeat(7, minmax(0, 1fr));
    }

    .heatmap-calendar-year {
        grid-area: year;
        font-weight: bold;
        font-size: 1.2em;
    }

    /* only label three days of the week */
    .heatmap-calendar-days li:nth-child(odd) {
        visibility: hidden;
    }

    .heatmap-calendar-boxes li {
        position: relative;
        font-size: 0.75em;
        background-color: #ebedf0;
        width: 100%;
        margin-inline-start: auto !important;
    }

    .theme-dark .heatmap-calendar-boxes .isEmpty {
        background: #333;
    }

    .heatmap-calendar-boxes li:not(.task-list-item)::before {
        content: unset;
    }

    .heatmap-calendar-boxes .internal-link {
        text-decoration: none;
        position: absolute;
        width: 100%;
        height: 100%;
        text-align: center;
    }

    .heatmap-calendar-boxes .today {
        border: solid 1px rgb(61, 61, 61);
    }

    /* Settings */

    .heatmap-calendar-settings-colors__color-box {
        width: 10px;
        height: 10px;
        display: inline-block;
        margin: 0 5px;
    }

    .heatmap-calendar-settings-colors__color-box:first-child {
        margin-left: 0;
    }

    .heatmap-calendar-settings-colors__color-name {
        display: inline-block;
    }

    .heatmap-calendar-settings-colors__container {
        align-items: center;
        border-top: 1px solid var(--background-modifier-border);
        display: flex;
        justify-content: space-between;
        padding: 0.5em 0;
    }

    .heatmap-calendar-settings-colors__container h4 {
        margin: 0.5em 0;
    }

    .heatmap-calendar-settings-colors__new-color-input-container {
        display: flex;
        justify-content: space-between;
    }

    .heatmap-calendar-settings-colors__new-color-input-container input {
        margin-right: 1em;
    }

    .heatmap-calendar-settings-colors__new-color-input-container input {
        margin-right: 1em;
    }

    .heatmap-calendar-settings-colors__new-color-input-value {
        flex-grow: 1;
    }
</style>