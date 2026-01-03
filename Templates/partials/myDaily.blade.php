@props([
'habits',
'habitRecords',
'selectedDate',
'tpl'
])

<div class="mydaily-widget">

    <div class="htmx-indicator full-width-loader">
        <div class="indeterminate"></div>
    </div>

    <div class="mydaily">
        <div class="row">
            <div class="col-md-12">
                <div class="row marginBottom">
                    <div class="col-md-12">
                        @foreach ($habits as $habit)
                        @php
                            $habitRecord = collect($habitRecords)->where('habitId', $habit->id)->first();
                        @endphp
                        @include('daily::partials.habit', ['habit' => $habit, 'tpl' => $tpl, 'habitRecord' => $habitRecord,
                        'selectedDate' => $selectedDate])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .mydaily {
            background: var(--kanban-card-bg);
            border: 3px solid transparent;
            border-bottom: none;
            border-radius: var(--box-radius);
            border-top: none;
            box-shadow: var(--regular-shadow);
            cursor: move;
            margin-bottom: 10px;
            padding: 10px
        }

        .mydaily input, .mydaily select {
        !important;
            width: 200px;
        }

        .mydaily .habit-checkbox {
            font-size: 18px;
            height: auto;
            line-height: 21px;
        }
    </style>
</div>