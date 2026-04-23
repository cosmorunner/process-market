@php

    $numberOfBlocks = [
        1 => [0, 14],
        2 => [15, 24],
        3 => [25, 34],
        4 => [35, 44],
        5 => [45, 54],
        6 => [55, 64],
        7 => [65, 74],
        8 => [75, 84],
        9 => [85, 94],
        10 => [95, 100],

    ];

    $colors = [
        '#33a1f4' => [0, 34],
        '#dc57ff' => [35, 74],
        '#7756fa' => [75, 100],
    ];

    $getNumberOfBlocks = function($score) use($numberOfBlocks){
        foreach ($numberOfBlocks as $number => $range) {
            if(in_array($score, range(...$range))) {
                return $number;
            }
        }

        return 0;
    };

    $getColor = function($score) use($colors){
        foreach ($colors as $hex => $range) {
            if(in_array($score, range(...$range))) {
                return $hex;
            }
        }

        return '#ffffff';
    };

    /** @noinspection PhpUndefinedVariableInspection */
    $scoreToHundred = (int) ($score * 10);
    $color = $getColor($scoreToHundred);
    $blocks = $getNumberOfBlocks($scoreToHundred);

@endphp

<div class="bg-white px-1 border rounded d-flex" style="width: 90px; padding-top: 1px;"
     data-toggle="tooltip" data-placement="top" title="Komplexitäts-Wert">
    <small class="font-weight-bold d-inline-block mr-1 align-content-center" style="color: {{$color}}; ">{{max(as_decimal($score, 0), 1)}}</small>
    @for ($i = 0; $i < $blocks; $i++)
        <span style="width:4px; height:10px; margin-left: 2px; background-color: {{$color}}" class="my-1 opacity-3"></span>
    @endfor
</div>
