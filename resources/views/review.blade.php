@extends('layouts.app')

@section('content')
<main class="wrapper">
    
    <aside class="side-area branding" style="align-items: flex-start; text-align: left;">
        <a href="{{ route('onion.home') }}" class="back-to-config" style="margin-bottom: 3rem; font-size: 1.2rem; color: #444; text-decoration: none;">
            &larr; zurück
        </a>
        <div class="brand-content">
            <h1 class="onion-title">on¿on</h1>
            <p class="onion-subtitle" style="font-size: 2rem; line-height: 1.2; margin-top: 10px;">
                dein heutiges<br>outfit war...
            </p>
        </div>
    </aside>

    <section class="main-configurator" style="justify-content: flex-start; padding-top: 100px; gap: 20px;">
        
        @php
            $offsets = [
                ['label' => 'genau richtig!', 'value' => 0],
                ['label' => 'zu kalt...', 'value' => 1],
                ['label' => 'zu warm...', 'value' => -1],
            ];
        @endphp

        @foreach($offsets as $offset)
        <form action="{{ route('user.updateOffset') }}" method="POST" class="w-100 d-flex justify-content-center">
            @csrf
            @method('PUT')
            <input type="hidden" name="temperature_offset" value="{{ $offset['value'] }}">
            
            @foreach($items as $item)
                <input type="hidden" name="outfit_ids[]" value="{{ $item['outfit_entry_id'] }}">
            @endforeach

            <button type="submit" class="outfit-capsule review-btn">
                {{ $offset['label'] }}
            </button>
        </form>
        @endforeach
        
    </section>

    <aside class="side-area info" style="align-items: center; justify-content: flex-start; padding-top: 20px;">
        
        @php
            $upper = []; $lower = []; $feet = []; $other = [];
            
            // Wir sortieren die Items in logische Körper-Gruppen
            foreach($items as $item) {
                $cat = $item['category']['name'] ?? '';
                if(in_array($cat, ['T-Shirt', 'Pullover', 'Jacke'])) {
                    $upper[] = $item;
                } elseif(in_array($cat, ['Hose', 'Strumpfhose'])) {
                    $lower[] = $item;
                } elseif(in_array($cat, ['Schuhe', 'Socken'])) {
                    $feet[] = $item;
                } else {
                    $other[] = $item;
                }
            }
        @endphp

        <div class="outfit-display d-flex flex-column align-items-center">
            
            @if(count($upper) > 0)
            <div class="d-flex flex-row justify-content-center" style="z-index: 10;">
                @foreach($upper as $index => $item)
                    <img src="{{ asset($item['filepath']) }}" class="review-item" style="z-index: {{ $index + 10 }}; {{ $index > 0 ? 'margin-left: -90px;' : '' }}">
                @endforeach
            </div>
            @endif

            @if(count($lower) > 0)
            <div class="d-flex flex-row justify-content-center" style="margin-top: -30px; z-index: 5;">
                @foreach($lower as $index => $item)
                    <img src="{{ asset($item['filepath']) }}" class="review-item" style="z-index: {{ $index + 5 }}; {{ $index > 0 ? 'margin-left: -60px;' : '' }}">
                @endforeach
            </div>
            @endif

            @if(count($feet) > 0)
            <div class="d-flex flex-row justify-content-center" style="margin-top: -10px; z-index: 1;">
                @foreach($feet as $index => $item)
                    <img src="{{ asset($item['filepath']) }}" class="review-item" style="z-index: {{ $index + 1 }}; {{ $index > 0 ? 'margin-left: -40px;' : '' }}">
                @endforeach
            </div>
            @endif

            @if(count($other) > 0)
            <div class="d-flex flex-row justify-content-center" style="margin-top: 10px;">
                @foreach($other as $item)
                    <img src="{{ asset($item['filepath']) }}" class="review-item" style="margin-left: 10px;">
                @endforeach
            </div>
            @endif

        </div>
    </aside>

</main>

@endsection