{{-- Header --}}
<div class="header">
    <h1 class="title is-1">Item erstellen</h1>
</div>

<div class="form-wrapper">
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Name --}}
        <div class="field">
            <label class="label">Name</label>
            <div class="control">
                <input type="text" class="input @error('name') is-danger @enderror" name="name"
                    value="{{ old('name') }}" required>
            </div>

            @error('name')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- category --}}
        <div class="field">
            <label class="label">Kategorie</label>
            <div class="control">
                <select name="category_id">
                    <option selected>Kategorie auswählen...</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" @if(old('category_id')==$category->id) selected @endif>{{
                        $category->name }}</option>
                    @endforeach
                </select>
            </div>

            @error('category_id')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- tags --}}
        <div class="field">
            <label class="label">Tags</label>
            <div class="control">
                <select name="tags[]" multiple>
                    @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
            </div>

            @error('tags')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- is_waterproof --}}
        <div class="field">
            <label class="label">Wasserdicht</label>
            <div class="control">
                <input type="checkbox" class="checkbox" name="is_waterproof" value="1" {{ old('is_waterproof')
                    ? 'checked' : '' }}>
            </div>
        </div>

        {{-- min_temperature --}}
        <div class="field">
            <label class="label">Minimale Temperatur</label>
            <div class="control">
                <input type="text" class="input @error('min_temperature') is-danger @enderror" name="min_temperature"
                    value="{{ old('min_temperature') }}">
            </div>

            @error('min_temperature')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- max_temperature --}}
        <div class="field">
            <label class="label">Maximale Temperatur</label>
            <div class="control">
                <input type="text" class="input @error('max_temperature') is-danger @enderror" name="max_temperature"
                    value="{{ old('max_temperature') }}">
            </div>

            @error('max_temperature')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- min_uv_index --}}
        <div class="field">
            <label class="label">Minimaler UV-Index</label>
            <div class="control">
                <input type="text" class="input @error('min_uv_index') is-danger @enderror" name="min_uv_index"
                    value="{{ old('min_uv_index') }}">
            </div>

            @error('min_uv_index')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- max_uv_index --}}
        <div class="field">
            <label class="label">Maximaler UV-Index</label>
            <div class="control">
                <input type="text" class="input @error('max_uv_index') is-danger @enderror" name="max_uv_index"
                    value="{{ old('max_uv_index') }}">
            </div>

            @error('max_uv_index')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>


        {{-- cloud_cover_threshold --}}
        <div class="field">
            <label class="label">Bewölkungsschwelle</label>
            <div class="control">
                <input type="text" class="input @error('cloud_cover_threshold') is-danger @enderror"
                    name="cloud_cover_threshold" value="{{ old('cloud_cover_threshold') }}">
            </div>

            @error('cloud_cover_threshold')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- image --}}
        <div class="field">
            <label class="label">Bild hochladen</label>
            <div class="control">
                <input type="file" class="input @error('filepath') is-danger @enderror" name="filepath" required>
            </div>

            @error('filepath')
            <p class="has-text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- submit button --}}
        <div class="control submit-button">
            <button class="button is-info">Item erstellen</button>
        </div>

    </form>
</div>