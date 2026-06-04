@extends('layouts.app')

@section('content')
<script>
    window.items = @json($items);
    window.tags = @json($tags);
    window.categories = @json($categories);
    window.unreviewedOutfitsByDay = @json($unreviewedOutfitsByDay);
</script>

<main class="wrapper profile-page">
    
    <aside class="side-area branding">
        <div class="brand-content">
            <a href="/" class="back-to-config">← zurück zum outfit konfigurator</a>
            
            <h1 class="onion-title">on¿on</h1>
            
            <div class="user-greeting">
                <p>hey <strong>{{ Auth::user()->name }}</strong>,<br>
                schön dass du on¿on nutzt!</p>
            </div>
        </div>
        
        <div class="profile-actions d-flex flex-column gap-3 mt-4" style="width: 100%; align-items: flex-end;">
    
            <a href="{{ route('settings') }}" class="side-pill text-center text-decoration-none">
                Einstellungen
            </a>
            
            <button type="button" class="side-pill border-0" data-bs-toggle="modal" data-bs-target="#uploadModal">
                neues Item hinzufügen
            </button>
            
            <button type="button" class="side-pill border-0" data-bs-toggle="modal" data-bs-target="#addTagModal">
                Tag hinzufügen
            </button>
        </div>
    </aside>

    <section class="main-configurator" style="justify-content: flex-start; padding-top: 40px; overflow-y: auto;"> 
        <h2 class="closet-header mb-5">Dein Kleiderschrank</h2>

        <div class="closet-list w-100">
            @foreach($categories as $category)
                @php
                    $categoryItems = collect($items)->where('category_id', $category['id'] ?? $category->id);
                @endphp

                @if($categoryItems->count() > 0)
                    <div class="category-row mb-5 w-100">
                        <h3 class="category-title" style="font-size: 1.2rem; color: #555; margin-bottom: 15px; font-weight: 400;">
                            {{ $category['name'] ?? $category->name }}
                        </h3>
                        
                        <div class="horizontal-scroll-container d-flex gap-3 pb-2" style="overflow-x: auto; flex-wrap: nowrap; scrollbar-width: thin;">
                            @foreach($categoryItems as $item)
                                @php
                                    $path = str_replace('/storage/', '', $item->filepath ?? $item['filepath'] ?? '');
                                @endphp
                                
                                <div class="closet-item flex-shrink-0" 
                                     style="width: 140px; height: 140px; cursor: pointer; background: white; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 15px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
                                     onmouseover="this.style.transform='scale(1.05)'" 
                                     onmouseout="this.style.transform='scale(1)'"
                                     onclick="window.openEditItemModal({{ json_encode($item) }})">
                                    
                                    <img src="{{ asset($path) }}" 
                                         alt="Kleidungsstück" 
                                         style="max-width: 100%; max-height: 100%; object-fit: contain; display: block;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; console.log('Fehler bei Bildpfad:', '{{ asset($path) }}');">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if(count($items) === 0)
                <p style="color: #888; text-align: center; margin-top: 50px;">Dein Kleiderschrank ist noch leer. Füge dein erstes Item hinzu!</p>
            @endif
        </div>
    </section>

    <div class="mt-3">
        @include('modals.upload-clothing')
        @include('modals.add-tag')
        @include('modals.edit-item')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@vite(['resources/js/modal.js'])

@endsection