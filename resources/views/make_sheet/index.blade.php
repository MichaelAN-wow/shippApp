@extends('layouts.admin_master')
@section('content')

<style>
.table-scroll-wrapper {
    overflow-x: auto;
    width: 100%;
    display: block;
}

.make-sheet-table {
    font-family: "Arial Narrow", Arial, sans-serif;
    font-size: 10px;
    text-align: center;
    margin-top: 60px;
    border: 2px solid #000;
    border-collapse: collapse;
    table-layout: auto;
    width: max-content;
    min-width: 100%;
}

.make-sheet-table th,
.make-sheet-table td {
    padding: 2px 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.total-bar-wrapper {
    position: sticky;
    top: 60px;
    z-index: 100;
    background: white;
    padding-top: 8px;
}

.total-bar {
    background-color: #FFD700;
    font-weight: bold;
    font-size: 14px;
    padding: 6px;
    text-align: center;
    margin-bottom: 8px;
    border: 2px solid #ccc;
}

.dark-header { background-color: #A3A3A3; font-weight: bold; }
.medium-header { background-color: #f0f0f0; }
.location-font th { font-size: 13px; }
.product-type-font th { font-size: 10px; }

input[type="number"] {
    border: 1px solid #ccc;
    width: 38px;
    height: 24px;
    text-align: center;
    font-size: 10px;
    font-weight: bold;
    appearance: none;
    -moz-appearance: textfield;
    padding: 1px 2px;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    appearance: none;
    margin: 0;
}

.bold-divider-left { border-left: 3px solid #000 !important; }
.bold-divider-right { border-right: 3px solid #000 !important; }
.location-total {
    font-weight: bold;
    background-color: #FFD700;
}

.btn-purple {
    background-color: #5C3FA5;
    color: white;
    font-weight: bold;
    border: none;
    padding: 10px 18px;
    border-radius: 4px;
}

.make-sheet-table th {
    font-size: 9px;
    line-height: 1.1;
    padding: 2px 2px;
    max-width: 50px;
    word-wrap: break-word;
    white-space: normal;
}

.make-sheet-table .medium-header th {
    font-size: 9px;
    padding: 2px 2px;
}

.make-sheet-table td:first-child,
.make-sheet-table th:first-child {
    max-width: 150px;
    min-width: 120px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: keep-all;
    text-align: left;
    padding-left: 6px;
    font-weight: bold;
    font-size: 12px;
    color: #000;
}
</style>

<div class="container-fluid">
    <div class="total-bar-wrapper">
        <div class="total-bar" id="masterTotal">Total Items to Make: 0</div>
    </div>

    <form id="makeSheetForm">
        <div id="makeSheetWrapper">
            <table class="table make-sheet-table bg-white">
                <thead>
                    <tr class="dark-header location-font">
                        <th>Product</th>
                        <th colspan="10" class="bold-divider-right">BACKSTOCK</th>
                        <th colspan="10" class="bold-divider-right bold-divider-left">STORE FRONT</th>
                        <th colspan="10" class="bold-divider-left">MARKETS</th>
                    </tr>
                    <tr class="medium-header product-type-font">
                        <th></th> <!-- Placeholder for Product column -->

                        {{-- Backstock --}}
                        <th class="bold-divider-left">Wax Melt</th>
                        <th>XL Wax Melt</th>
                        <th>Single Wick</th>
                        <th>Double Wick</th>
                        <th>Triple Wick</th>
                        <th>Dough Bowl</th>
                        <th>Room Spray</th>
                        <th>Car Diffuser</th>
                        <th>Soap</th>
                        <th class="bold-divider-right">Soap Refill</th>

                        {{-- Store Front --}}
                        <th class="bold-divider-left">Wax Melt</th>
                        <th>XL Wax Melt</th>
                        <th>Single Wick</th>
                        <th>Double Wick</th>
                        <th>Triple Wick</th>
                        <th>Dough Bowl</th>
                        <th>Room Spray</th>
                        <th>Car Diffuser</th>
                        <th>Soap</th>
                        <th class="bold-divider-right">Soap Refill</th>

                        {{-- Markets --}}
                        <th class="bold-divider-left">Wax Melt</th>
                        <th>XL Wax Melt</th>
                        <th>Single Wick</th>
                        <th>Double Wick</th>
                        <th>Triple Wick</th>
                        <th>Dough Bowl</th>
                        <th>Room Spray</th>
                        <th>Car Diffuser</th>
                        <th>Soap</th>
                        <th class="bold-divider-right">Soap Refill</th>
                    </tr>
                </thead>
                @php
                $products = [
                    'Clean',
                    'New Car Vibes',

                    '--- Divider Row ---',
                    'Berry Crunch',
                    'Cinnamilk',
                    'Coffee Break',

                    '--- Divider Row ---',
                    'Capricorn',
                    'Aquarius',
                    'Pisces',
                    'Aries',
                    'Taurus',
                    'Gemini',
                    'Cancer',
                    'Leo',
                    'Virgo',
                    'Libra',
                    'Scorpio',
                    'Sagittarius',

                    '--- Divider Row ---',
                    'Banana Nut Bread',
                    'Breeze',
                    'Cellar',
                    'Cloud Wine',
                    'Cozy',
                    'Dapper',
                    'Fallon',
                    'Home',
                    'Laundry Vibes',
                    'Mine',
                    'Saturday Morning Vibes',
                    'Spill The Tea',
                    'Woodsy',

                    '--- Divider Row ---',
                    'Black Magic',
                    'Brew',
                    'Clarity',
                    'Fearless',
                    'Golden',
                    'Healer',
                    'Lore',
                    'Mirage',
                    'Nag Champa',
                    'Renew',
                    'Unwind',
                    'White Magic',
                    'Wisdom',

                    '--- Divider Row ---',
                    'Bloom',
                    'Crisp',
                    'Garden Party',
                    'Hello Spring',
                    'Just Lavender',
                    'Light Jacket',
                    'Limoncello Energy',
                    'Lover',
                    'Orchard',
                    'Perfect Day',
                    'Rose',
                    'Spring Evenings',
                    'Spring Vibes',

                    '--- Divider Row ---',
                    'Beach Day',
                    'Burst',
                    'Cabana',
                    'Coastal Crush',
                    'Desert Bloom',
                    'Dragon Fruit Dream',
                    'Drift Away',
                    'Happy',
                    'Pineapple Sage',
                    'Pink Pineapple',
                    'Summer Vibes',
                    'Sunrise',
                    'Sunset',

                    '--- Divider Row ---',
                    'Apple Cinnamon Chai',
                    'Apple Pickin',
                    'Autumn Souffle',
                    'Backwoods',
                    'Bobbing',
                    'Bourbon Pumpkin',
                    'Cranberry Oak',
                    'Fall AF',
                    'Harvest Moon',
                    'Hoodie Vibes',
                    'Pumpkin Patch',
                    'Smokey Oak',
                    'Thankful',
                    'Warm & Comfy',
                    'White Pumpkin',

                    '--- Divider Row ---',
                    'Candy Cane Dream',
                    'Cashmere',
                    'Evergreen Forest',
                    'Gather',
                    "Granny's Cookies",
                    'Holiday Season',
                    'Meet Me',
                    'Nordic Embers',
                    'Red',
                    'Snow Vibes',
                    'Under The Tree',
                    'Warm Winter',
                    'Winter Day',

                    '--- Divider Row ---',
                    'Ice Cream Shoppe',
                    'Orange Sherbert',
                    'Raspberry Sorbet',

                    '--- Divider Row ---',
                    'Made In Omaha',
                    'White Label',
                ];
                @endphp

                @php
                $excludedCells = [
                'Clean' => [0, 2, 3, 4, 5, 6, 10, 12, 13, 14, 15, 16, 20, 22, 23, 24, 25],
                'New Car Vibes' => [0,1, 2, 3, 4, 5, 8, 9, 10, 11, 12, 13, 14, 15, 18, 19, 20, 22, 23, 24, 25, 28, 29],
                    
                'Berry Crunch' => [ 1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Cinnamilk' => [ 1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Coffee Break' => [6, 7, 8, 9, 16, 17, 18, 19, 26, 27, 28, 29],

                'Capricorn' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Aquarius' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Pisces' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Aries' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Taurus' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Gemini' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Cancer' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Leo' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Virgo' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Libra' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Scorpio' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Sagittarius' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],

                'Banana Nut Bread' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Breeze' => [1, 6, 8, 9, 11, 16,18,21, 26, 28, 29],
                'Cellar' => [1, 8, 9, 11, 18, 19, 21, 28, 29],
                'Cloud Wine' => [6, 7, 8, 9, 16, 17, 18, 19, 26, 27, 28, 29],
                'Cozy' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Dapper' => [1, 8, 9, 11, 18, 19, 21, 28, 29],
                'Fallon' => [1, 8, 9, 11, 18, 19, 21, 28, 29],
                'Home' => [6, 7, 8, 9, 16, 17, 18, 19, 26, 27, 28, 29],
                'Laundry Vibes' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 21, 26, 27, 28, 29],
                'Mine' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 21, 26, 27, 28, 29],
                'Saturday Morning Vibes' => [6, 7, 8, 9, 16, 17, 18, 19, 26, 27, 28, 29],
                'Spill The Tea' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 21, 26, 27, 28, 29],
                'Woodsy' => [1, 8, 9, 11, 18, 19, 21, 28, 29],

                'Brew' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Clarity' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Fearless' => [1, 7, 8, 9, 11, 17, 18, 19, 21, 27, 28, 29],
                'Golden' => [1, 6, 7, 11, 16, 17, 21, 26, 27],
                'Healer' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Lore' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Mirage' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Nag Champa' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Renew' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Unwind' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'White Magic' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Wisdom' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],

                'Bloom' => [ 6, 7, 8, 9, 16, 17, 18, 19, 26, 27, 28, 29],
                'Crisp' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Garden Party' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Hello Spring' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Just Lavender' => [1, 6, 7, 11, 16, 19, 21, 26, 27],
                'Light Jacket' => [ 6, 7, 8, 9, 16, 17, 18, 19, 26, 27, 28, 29],
                'Limoncello Energy' => [ 6, 7, 16, 17, 26, 27],
                'Lover' => [1, 7, 8, 9, 11, 17, 18, 19, 21, 27, 28, 29],
                'Orchard' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Perfect Day' => [1, 8, 9, 11, 18, 19, 21, 28, 29],
                'Rose' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Spring Evenings' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Spring Vibes' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],

                'Beach Day' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Burst' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Cabana' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Coastal Crush' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Desert Bloom' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Dragon Fruit Dream' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Drift Away' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Pineapple Sage' => [1, 8, 9, 11, 18, 19, 21, 28, 29],
                'Pink Pineapple' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Summer Vibes' => [1, 7, 8, 9, 11, 17, 18, 19, 21, 26, 27, 28, 29],
                'Sunrise' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 27, 28, 29],
                'Sunset' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],

                'Apple Cinnamon Chai' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Apple Pickin' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Autumn Souffle' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Backwoods' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Bobbing' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Bourbon Pumpkin' => [ 6, 7, 8, 9, 16, 17, 18, 19, 26, 27, 28, 29],
                'Cranberry Oak' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Fall AF' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Harvest Moon' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Pumpkin Patch' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Smokey Oak' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Thankful' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Warm & Comfy' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'White Pumpkin' => [1, 6, 8, 9, 11, 16, 18, 19, 21, 26, 28, 29],

                'Candy Cane Dream' => [1, 6, 7, 11, 16, 17, 21, 26, 27],
                'Cashmere' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Evergreen Forest' => [ 6, 7, 8, 9, 16, 17, 18, 19, 26, 27, 28, 29],
                'Gather' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                "Granny's Cookies" => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Holiday Season' => [1, 6, 8, 9, 11, 16, 18, 19, 21, 26, 28, 29],
                'Meet Me' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Nordic Embers' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Red' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Snow Vibes' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Under The Tree' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Warm Winter' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],
                'Winter Day' => [1, 6, 7, 8, 9, 11, 16, 17, 18, 19, 21, 26, 27, 28, 29],

                'Ice Cream Shoppe' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Orange Sherbert' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                'Raspberry Sorbet' => [1, 4, 6, 7, 8, 9, 11, 14, 16, 17, 18, 19, 21, 24, 26, 27, 28, 29],
                ];
                @endphp

                @foreach($products as $pIndex => $p)
                    @if(Str::contains($p, '---'))
                        <tr>
                            <td colspan="31" style="background-color: #eee; height: 12px; border-top: 2px solid #ccc;"></td>
                        </tr>
                    @else
                        <tr>
                            <!-- Product Name Column -->
                            <td class="bold-divider-right" style="text-align: left;">{{ $p }}</td>

                            <!-- 30 Data Columns -->
                            @for($i = 0; $i < 30; $i++)
                                @php
                                    $isLeftDivider = ($i % 10 === 0 && $i !== 0);
                                    $isRightDivider = (($i + 1) % 10 === 0);
                                    $isDisabled = isset($excludedCells[$p]) && in_array($i, $excludedCells[$p]);
                                @endphp

                                <td class="@if($isLeftDivider) bold-divider-left @endif @if($isRightDivider) bold-divider-right @endif">
                                    @if($isDisabled)
                                        <input type="number" disabled style="background-color:#aaa;" />
                                    @else
                                        <input type="number" name="data[{{ $pIndex }}][{{ $i }}]" class="input-field font-weight-bold" min="0" />
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endif
                @endforeach

                <tfoot>
                    <tr class="location-total">
                        <td>Location Totals</td>
                        @for($i = 0; $i < 30; $i++)
                            <td id="locTotal{{ $i }}" class="@if($i % 10 == 0 && $i != 0) bold-divider-left @endif @if(($i + 1) % 10 == 0) bold-divider-right @endif">0</td>
                        @endfor
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div style="display: flex; gap: 10px; justify-content: right; margin-top: 10px; margin-bottom: 40px;">
            <button type="button" class="btn btn-purple" onclick="exportToExcel()">📄 Export to Excel</button>
            <button type="button" class="btn btn-danger" onclick="clearMakeSheet()">Clear All</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    const updateTotals = () => {
        let masterTotal = 0;
        let columnTotals = new Array(30).fill(0);

        document.querySelectorAll('tbody tr').forEach(row => {
            row.querySelectorAll('input[type="number"]').forEach((input, index) => {
                let val = parseInt(input.value) || 0;
                columnTotals[index] += val;
                masterTotal += val;
            });
        });

        columnTotals.forEach((total, index) => {
            document.getElementById(`locTotal${index}`).textContent = total;
        });

        document.getElementById('masterTotal').textContent = 'Total Items to Make: ' + masterTotal;
    };

    // Auto-save to database with debouncing
    let autoSaveTimeout;
    const autoSaveData = () => {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            const formData = new FormData(document.getElementById('makeSheetForm'));
            
            fetch("{{ route('make_sheet.auto_save') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: formData
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    console.log('Data auto-saved to database');
                }
            }).catch(error => {
                console.error('Auto-save failed:', error);
            });
        }, 1000); // Save after 1 second of no changes
    };

    document.querySelectorAll('.input-field').forEach((input, idx) => {
        input.addEventListener('input', () => {
            updateTotals();
            autoSaveData(); // Auto-save to database instead of localStorage
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const inputs = Array.from(document.querySelectorAll('.input-field'));
        
        // Load saved data from database if available
        @if(isset($latestEntry) && $latestEntry && $latestEntry->data)
            const savedData = @json($latestEntry->data);
            if (savedData) {
                inputs.forEach((input, idx) => {
                    const name = input.getAttribute('name');
                    if (name) {
                        // Parse name like "data[0][1]" to get productIndex (0) and columnIndex (1)
                        const matches = name.match(/data\[(\d+)\]\[(\d+)\]/);
                        if (matches) {
                            const productIndex = matches[1];
                            const columnIndex = matches[2];
                            
                            // Check if we have data for this product and column
                            if (savedData[productIndex] && savedData[productIndex][columnIndex] !== undefined) {
                                const value = savedData[productIndex][columnIndex];
                                if (value !== null && value !== '') {
                                    input.value = value;
                                }
                            }
                        }
                    }
                });
            }
        @endif

        // Create a mapping of table positions for better navigation
        function createNavigationMap() {
            const rows = Array.from(document.querySelectorAll('tbody tr'));
            const inputMap = new Map();
            const positionMap = new Map();
            
            let inputIndex = 0;
            
            rows.forEach((row, rowIndex) => {
                const rowInputs = Array.from(row.querySelectorAll('.input-field'));
                
                rowInputs.forEach((input, colIndex) => {
                    // Find the actual column position by counting all td elements before this input
                    const allCells = Array.from(row.querySelectorAll('td'));
                    let actualCol = -1;
                    
                    for (let i = 0; i < allCells.length; i++) {
                        if (allCells[i].querySelector('.input-field') === input) {
                            actualCol = i - 1; // -1 because first column is product name
                            break;
                        }
                    }
                    
                    inputMap.set(input, { row: rowIndex, col: actualCol, index: inputIndex });
                    positionMap.set(`${rowIndex}-${actualCol}`, input);
                    inputIndex++;
                });
            });
            
            return { inputMap, positionMap };
        }

        const { inputMap, positionMap } = createNavigationMap();

        // Arrow key navigation with better logic
        inputs.forEach((input) => {
            input.addEventListener('keydown', function (e) {
                const currentPos = inputMap.get(input);
                if (!currentPos) return;

                let targetInput = null;

                if (e.key === 'ArrowRight') {
                    // Find next input in same row
                    for (let col = currentPos.col + 1; col < 30; col++) {
                        const key = `${currentPos.row}-${col}`;
                        if (positionMap.has(key)) {
                            targetInput = positionMap.get(key);
                            break;
                        }
                    }
                } else if (e.key === 'ArrowLeft') {
                    // Find previous input in same row
                    for (let col = currentPos.col - 1; col >= 0; col--) {
                        const key = `${currentPos.row}-${col}`;
                        if (positionMap.has(key)) {
                            targetInput = positionMap.get(key);
                            break;
                        }
                    }
                } else if (e.key === 'ArrowDown') {
                    // Find input in same column, next available row
                    for (let row = currentPos.row + 1; row < 100; row++) { // 100 is max rows estimate
                        const key = `${row}-${currentPos.col}`;
                        if (positionMap.has(key)) {
                            targetInput = positionMap.get(key);
                            break;
                        }
                    }
                } else if (e.key === 'ArrowUp') {
                    // Find input in same column, previous available row
                    for (let row = currentPos.row - 1; row >= 0; row--) {
                        const key = `${row}-${currentPos.col}`;
                        if (positionMap.has(key)) {
                            targetInput = positionMap.get(key);
                            break;
                        }
                    }
                }

                if (targetInput) {
                    e.preventDefault();
                    targetInput.focus();
                }
            });
        });

        updateTotals();
    });

    function clearMakeSheet() {
        if (!confirm('Are you sure you want to clear all data? This will remove all saved information.')) {
            return;
        }
        
        const inputs = document.querySelectorAll('.make-sheet-table input[type="number"]');
        inputs.forEach((input) => {
            if (!input.disabled) {
                input.value = '';
            }
        });
        
        // Clear from database
        const formData = new FormData(document.getElementById('makeSheetForm'));
        fetch("{{ route('make_sheet.auto_save') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.success) {
                console.log('Data cleared from database');
            }
        }).catch(error => {
            console.error('Clear failed:', error);
        });
        
        updateTotals();
    }

    function printMakeSheet() {
        const element = document.getElementById('makeSheetWrapper');
        const opt = {
            margin: 10,
            filename: 'make-sheet.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' }
        };

        html2pdf().from(element).set(opt).save();
    }

    document.getElementById('makeSheetForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("{{ route('make_sheet.store') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: formData
        }).then(res => res.json()).then(data => {
            alert("Make Sheet saved!");
        });
    });
    
    window.exportToExcel = function () {
        const table = document.querySelector('.make-sheet-table');
        const clone = table.cloneNode(true);
    
        // Convert input fields to plain text and mark disabled
        clone.querySelectorAll('td').forEach(td => {
            const input = td.querySelector('input[type="number"]');
            if (input) {
                if (input.disabled) {
                    td.textContent = ""; // empty but will remain as a cell
                    td.setAttribute('data-grey', '1');
                } else {
                    td.textContent = input.value;
                }
            }
        });
    
        // Create a worksheet
        const ws = XLSX.utils.table_to_sheet(clone);
    
        // Style disabled cells with grey background
        Object.keys(ws).forEach(addr => {
            if (!addr.startsWith('!')) {
                const col = XLSX.utils.decode_cell(addr).c;
                const row = XLSX.utils.decode_cell(addr).r;
                const domTrs = Array.from(clone.querySelectorAll('tr'));
                if (domTrs[row]) {
                    const domTds = domTrs[row].querySelectorAll('td, th');
                    if (domTds[col] && domTds[col].getAttribute('data-grey') === '1') {
                        ws[addr].s = {
                            fill: {
                                patternType: "solid",
                                fgColor: { rgb: "AAAAAA" }
                            }
                        };
                    }
                }
            }
        });
    
        // Export
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Make Sheet");
        XLSX.writeFile(wb, "make-sheet.xlsx");
    };
</script>
@endsection