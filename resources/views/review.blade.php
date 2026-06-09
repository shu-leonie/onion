@extends('layouts.app')

@section('content')
<main class="wrapper">
    
    <aside class="side-area branding" style="align-items: flex-start; text-align: left;">
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
            $groups = [
                'head' => [],
                'upper' => [],
                'lower' => [],
                'feet' => [],
            ];

            $layerOrder = [
                'Kopfbedeckung' => 1,
                'Sonnenbrille' => 2,
                'T-Shirt' => 10,
                'Pullover' => 20,
                'Jacke' => 30,
                'Strumpfhose' => 40,
                'Hose' => 50,
                'Socken' => 60,
                'Schuhe' => 70,
            ];

            $placeholderMap = [
                'Kopfbedeckung' => 'kopfbedeckung',
                'Sonnenbrille' => 'sonnenbrille',
                'T-Shirt' => 't-shirt',
                'Pullover' => 'pullover',
                'Jacke' => 'jacke',
                'Hose' => 'hose',
                'Strumpfhose' => 'strumpfhose',
                'Socken' => 'socken',
                'Schuhe' => 'schuhe',
                'Accessoires' => 'accessoires',
                'Sonnencreme' => 'sonnencreme'
            ];

            $hasCategory = [];

            foreach($items as $item) {
                $cat = $item['category']['name'] ?? '';
                $hasCategory[$cat] = true; 

                if (empty($item['filepath'])) {
                    $item['is_placeholder'] = true;
                } else {
                     $item['is_placeholder'] = false;
                }

                if(in_array($cat, ['Kopfbedeckung', 'Sonnenbrille'])) {
                    $groups['head'][] = $item;
                }
                elseif(in_array($cat, ['T-Shirt', 'Pullover', 'Jacke'])) {
                    $groups['upper'][] = $item;
                }
                elseif(in_array($cat, ['Hose', 'Strumpfhose'])) {
                    $groups['lower'][] = $item;
                }
                elseif(in_array($cat, ['Schuhe', 'Socken'])) {
                    $groups['feet'][] = $item;
                }
            }

            if (empty($groups['upper'])) {
                $groups['upper'][] = ['category' => ['name' => 'T-Shirt'], 'is_placeholder' => true];
            }
            if (empty($groups['lower'])) {
                $groups['lower'][] = ['category' => ['name' => 'Hose'], 'is_placeholder' => true];
            }
            if (empty($groups['feet'])) {
                $groups['feet'][] = ['category' => ['name' => 'Schuhe'], 'is_placeholder' => true];
            }

            foreach($groups as &$group) {
                usort($group, function($a, $b) use ($layerOrder) {
                    return ($layerOrder[$a['category']['name']] ?? 99)
                        <=> ($layerOrder[$b['category']['name']] ?? 99);
                });
            }


            function getImagePath($item, $placeholderMap) {

                if (($item['is_placeholder'] ?? false) || empty($item['filepath'])) {
                    $catName = $item['category']['name'] ?? 't-shirt';
                    $fileName = $placeholderMap[$catName] ?? 't-shirt';

                    return asset('/img/placeholders/platzhalter_' . $fileName . '.png');
                }

                return asset($item['filepath']);
            }
        @endphp

        <div class="outfit-preview">
            {{-- Kopf --}}
            <div class="category-group">
                @foreach($groups['head'] as $index => $item)
                    <img src="{{ getImagePath($item, $placeholderMap) }}" class="layer" style="z-index: {{ $index }}" alt="{{ $item['category']['name'] ?? 'Layer' }}">
                @endforeach
            </div>

            {{-- Oberkörper --}}
            <div class="category-group">
                @foreach($groups['upper'] as $index => $item)
                    <img src="{{ getImagePath($item, $placeholderMap) }}" class="layer" style="z-index: {{ $index }}" alt="{{ $item['category']['name'] ?? 'Layer' }}">
                @endforeach
            </div>

            {{-- Unterkörper --}}
            <div class="category-group">
                @foreach($groups['lower'] as $index => $item)
                    <img src="{{ getImagePath($item, $placeholderMap) }}" class="layer" style="z-index: {{ $index }}" alt="{{ $item['category']['name'] ?? 'Layer' }}">
                @endforeach
            </div>

            {{-- Füße --}}
            <div class="category-group">
                @foreach($groups['feet'] as $index => $item)
                    <img src="{{ getImagePath($item, $placeholderMap) }}" class="layer" style="z-index: {{ $index }}" alt="{{ $item['category']['name'] ?? 'Layer' }}">
                @endforeach
            </div>
        </div>
        
    </aside>

</main>

@endsection