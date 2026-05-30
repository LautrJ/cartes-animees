<div
    x-data="{
        started: false,
        showReal: false,
        finished: false,
        duration: {{ ($card->duration ?? 3) * 1000 }},
        start() {
            this.started = true;
            this.finished = false;
            this.showReal = false;
            const sound = document.getElementById('preview-sound');
            const drawnVideo = document.getElementById('preview-drawn-player');
            if (sound) { sound.currentTime = 0; sound.play(); }
            if (drawnVideo) drawnVideo.play();
            setTimeout(() => {
                this.showReal = true;
                this.$nextTick(() => {
                    const realVideo = document.getElementById('preview-real-player');
                    if (sound) { sound.currentTime = 0; sound.play(); }
                    if (realVideo) realVideo.play();
                });
                setTimeout(() => {
                    this.finished = true;
                    this.started = false;
                    const realVideo = document.getElementById('preview-real-player');
                    const sound = document.getElementById('preview-sound');
                    if (realVideo) realVideo.pause();
                    if (sound) sound.pause();
                }, this.duration);
            }, this.duration);
        },
        replay() {
            this.finished = false;
            this.showReal = false;
            this.$nextTick(() => this.start());
        }
    }"
    style="display:flex; flex-direction:column; align-items:center; padding:1rem; gap:1rem;"
>
    @php
        $drawnUrl = $card->drawn_animation_path ? asset('storage/cards/' . $card->drawn_animation_path) : null;
        $realUrl  = $card->real_animation_path  ? asset('storage/cards/' . $card->real_animation_path)  : null;
        $soundUrl = $card->sound_path           ? asset('storage/cards/' . $card->sound_path)           : null;
        $width    = $card->width  ?? 480;
        $height   = $card->height ?? 270;
    @endphp

    {{-- Placeholder avant lancement --}}
    <div x-show="!started && !finished && !showReal"
         style="width:{{ $width }}px; height:{{ $height }}px; border-radius:8px; background:#1a1a2e; display:flex; align-items:center; justify-content:center;">
        <span style="color:#a896d8; font-size:3rem;">🎴</span>
    </div>

    {{-- Animation dessinée --}}
    <div x-show="started && !showReal" x-cloak>
        @if($drawnUrl)
            @if(str_ends_with($drawnUrl, '.gif'))
                <img src="{{ $drawnUrl }}"
                     alt="Animation dessinée"
                     style="width:{{ $width }}px; height:{{ $height }}px; border-radius:8px; object-fit:cover;">
            @else
                <video id="preview-drawn-player" loop
                       style="width:{{ $width }}px; height:{{ $height }}px; border-radius:8px; object-fit:cover;">
                    <source src="{{ $drawnUrl }}" type="video/mp4">
                </video>
            @endif
        @else
            <p style="color:gray;">Aucune animation dessinée disponible</p>
        @endif
    </div>

    {{-- Animation réelle --}}
    <div x-show="showReal && !finished" x-cloak>
        @if($realUrl)
            @if(str_ends_with($realUrl, '.gif'))
                <img src="{{ $realUrl }}"
                     alt="Animation réelle"
                     style="width:{{ $width }}px; height:{{ $height }}px; border-radius:8px; object-fit:cover;">
            @else
                <video id="preview-real-player"
                       style="width:{{ $width }}px; height:{{ $height }}px; border-radius:8px; object-fit:cover;">
                    <source src="{{ $realUrl }}" type="video/mp4">
                </video>
            @endif
        @else
            <p style="color:gray;">Aucune animation réelle disponible</p>
        @endif
    </div>

    {{-- Son --}}
    @if($soundUrl)
        <audio id="preview-sound" preload="auto">
            <source src="{{ $soundUrl }}" type="audio/mpeg">
        </audio>
    @endif

    {{-- Label --}}
    <p x-show="started"
       x-cloak
       style="color:#a896d8; font-weight:600; font-size:0.9rem;"
       x-text="showReal ? 'Animation réelle...' : 'Animation dessinée...'">
    </p>

    {{-- Bouton Lancer --}}
    <button x-show="!started && !finished"
            type="button"
            @click="start()"
            style="background:#a896d8; color:white; border:none; border-radius:8px; padding:0.75rem 2rem; font-size:1rem; cursor:pointer; font-weight:600;">
        ▶ Lancer
    </button>

    {{-- Bouton Rejouer --}}
    <button x-show="finished"
            x-cloak
            type="button"
            @click="replay()"
            style="background:#a896d8; color:white; border:none; border-radius:8px; padding:0.75rem 2rem; font-size:1rem; cursor:pointer; font-weight:600;">
        🔁 Rejouer
    </button>

</div>
