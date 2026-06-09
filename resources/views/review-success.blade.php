@extends('layouts.app')

@section('content')
@php
    if ($offset == 1) {
        $image = 'img/oni-zu-kalt.png';
        $text = 'Brrr... Gemerkt!<br>Nächstes Mal packe ich dich wärmer ein...<br>' . 
                '<small>Falls du Anpassungen an deinem Kleiderschrank vornehmen möchtest,<br>kannst du das übrigens im Kleiderschrank tun!</small>';
    } elseif ($offset == -1) {
        $image = 'img/oni-zu-warm.png';
        $text = 'Ohje, zu warm!<br>Ich merke mir, dass dir schnell heiß ist!<br>' . 
                '<small>Falls du Anpassungen an deinem Kleiderschrank vornehmen möchtest,<br>kannst du das übrigens im Kleiderschrank tun!</small>';
    } else {
        $image = 'img/oni-genau-richtig.png';
        $text = 'Perfekt!<br>Hoffentlich bis morgen!!';
    }
@endphp

<main class="wrapper feedback-wrapper">
    <img src="{{ asset($image) }}" alt="Onion Feedback" class="feedback-onion-img">
    <div class="feedback-text-container">
        {!! $text !!}
    </div>
</main>

<script>
    setTimeout(function() {
        window.location.href = "{{ route('onion.home') }}";
    }, 3000);
</script>
@endsection