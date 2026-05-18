@extends('layouts.app')

<script>
    const items = @json($items);
    const tags = @json($tags);
    const categories = @json($categories);
    const unreviewedOutfitsByDay = @json($unreviewedOutfitsByDay);
</script>

@section('content')
<main class="wrapper profile-page">
    
    <aside class="side-area branding">
        <div class="brand-content">
            <a href="/" class="back-to-config">← zurück zum outfit konfigurator</a>
            
            <h1 class="onion-title">on¿on</h1>
            
            <div class="user-greeting">
                <p>hey <strong>{{ Auth::user()->name }}</strong>,<br>
                schön dass du on¿on nutzt!</p>
            </div>
            
            <a href="{{ route('settings') }}" class="side-pill">Einstellungen</a>
            <a href="#" class="side-pill">neues Item hinzufügen</a>
        </div>
    </aside>

    <section class="main-configurator"> <h2 class="closet-header">Dein Kleiderschrank</h2>

        <div class="closet-list">
            <div class="outfit-capsule profile-capsule">
                <span class="capsule-label">Kopfbedeckung, ...</span>
                <div class="item-display">
                    <img src="/images/konfig/head1_cosi.png">
                </div>
            </div>

            <div class="outfit-capsule profile-capsule">
                <span class="capsule-label">Oberteile, ...</span>
                <div class="item-display">
                    <img src="/images/konfig/shirt2_cosi.png">
                </div>
            </div>

            <div class="outfit-capsule profile-capsule">
                <span class="capsule-label">Hosen, Röcke, ...</span>
                <div class="item-display">
                    <img src="/images/konfig/lower1_cosi.png">
                </div>
            </div>

            <div class="outfit-capsule profile-capsule">
                <span class="capsule-label">Schuhe</span>
                <div class="item-display">
                    <img src="/images/konfig/feet2_cosi.png">
                </div>
            </div>
        </div>
    </section>

    <div class="mt-3">
        @include('modals.upload-clothing')
    </div>

</main>
@endsection