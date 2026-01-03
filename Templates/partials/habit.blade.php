@props([
    'habit',
    'habitRecord',
    'selectedDate',
    'tpl'
])

<form id="form-habit-{{ $habit->id }}">
        <input type="hidden" name="habitId" value="{{ $habit->id }}" />
        <input type="hidden" name="habitRecordId" value="{{ $habitRecord?->id ?? '' }}" />
        <input type="hidden" name="selectedDate" value="{{ $selectedDate }}" />

        <div class="form-group tw-flex">
            <label class="control-label tw-w-[200px]">{{ $habit->name }}</label>
            <div class="tw-w-full">
                @if ($habit->habitType === 0)
                    @if ($habitRecord?->value == 1)
                        <input type="hidden" name="habitValue" value="0" />
                    @else
                        <input type="hidden" name="habitValue" value="1" />
                    @endif
                    <a href="javascript:void(0)">
                        <i @class([
                            'habit-checkbox',
                            'fa-regular fa-circle-check' => $habitRecord?->value == 1,
                            'fa-regular fa-circle' => $habitRecord == NULL,
                            'fa-regular fa-circle' => $habitRecord?->value == 0,
                        ])
                        hx-post="/daily/recordHabit" hx-target="#form-habit-{{ $habit->id }}" hx-trigger="click" ></i>
                    </a>
                @elseif ($habit->habitType === 1)
                    <input
                            type="number"
                            class="form-control"
                            name="habitValue"
                            min="{{ $habit?->numMinValue }}"
                            max="{{ $habit?->numMaxValue }}"
                            hx-post="/daily/recordHabit"
                            hx-target="#form-habit-{{ $habit->id }}"
                            value="{{ $habitRecord?->value ?? '' }}">
                @elseif ($habit->habitType === 2)
                    <select
                            class="form-control"
                            name="habitValue"
                            hx-post="/daily/recordHabit"
                            hx-target="#form-habit-{{ $habit->id }}">
                        <option disabled selected value> -- select {{ $habit->name }} -- </option>
                        @php $selectValues = explode("," ,$habit->enumValues); @endphp
                        @foreach ($selectValues as $selectValue)
                            <option value="{{ $selectValue }}"
                                @if ($habitRecord?->value == $selectValue)
                                    selected='selected'
                                @endif
                            >{{ $selectValue }}</option>
                        @endforeach
                    </select>
                @else
                    Unknown habittype {{ $habit->habitType }}
                @endif
            </div>
        </div>
</form>