@extends('layouts.app')

@section('content')
@php
    if ($offset == 1) {
        $image = 'img/oni-zu-kalt.png';
        $text = 'Brrr... Gemerkt!<br>Nächstes Mal packe ich dich wärmer ein...';
    } elseif ($offset == -1) {
        $image = 'img/oni-zu-warm.png';
        $text = 'Ohje, zu warm!<br>Ich empfehle dir ab jetzt luftigere Sachen!';
    } else {
        $image = 'img/oni-genau-richtig.png';
        $text = 'Perfekt!<br>Hoffentlich können wir dir auch Morgen wieder helfen!';
    }
@endphp

<main class="wrapper d-flex flex-column justify-content-start align-items-center feedback-wrapper">
    
    <img src="{{ asset($image) }}" alt="Onion Feedback" class="feedback-onion-img">
    
    <h2 class="feedback-text">
        {!! $text !!} 
    </h2>

</main>

<script>
    setTimeout(function() {
        window.location.href = "{{ route('onion.home') }}";
    }, 3000);
</script>
@endsection