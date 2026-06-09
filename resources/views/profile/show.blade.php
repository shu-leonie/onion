@extends('layouts.app')

@section('content')
<main class="wrapper profile-page">
    
    <aside class="side-area branding">
        <div class="brand-content">
            <a href="/" class="back-to-config">← zurück zum Kleiderschrank</a>
            <h1 class="onion-title">Einstellungen</h1>
        </div>
    </aside>

    <section class="main-configurator" style="justify-content: flex-start; padding-top: 40px; overflow-y: auto;"> 
        <div style="width: 100%; max-width: 800px; margin: 0 auto;">
            
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div style="background: rgba(255,255,255,0.9); padding: 30px; border-radius: 15px; margin-bottom: 30px;">
                    @livewire('profile.update-profile-information-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div style="background: rgba(255,255,255,0.9); padding: 30px; border-radius: 15px; margin-bottom: 50px;">
                    @livewire('profile.update-password-form')
                </div>
            @endif

        </div>
    </section>

</main>
@endsection