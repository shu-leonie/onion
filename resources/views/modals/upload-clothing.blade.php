<!-- Button für das Upload-Modal -->
<button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadModal">
  <i class="bi bi-upload"></i> KLEIDUNGSSTÜCK HOCHLADEN
</button>

<!-- Upload-Modal mit Tag-Auswahl -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true"
     style="position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title" id="uploadModalLabel">Kleidungsstück erstellen</strong>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="general-attributes">
          <div class="mb-3">
            <label for="clothingImage" class="form-label">Bild auswählen</label>
            <input type="file" class="form-control" id="clothingImage" accept="image/*" required>
          </div>
          <div class="text-center">
            <img id="imagePreview" src="#" alt="Vorschau" class="img-fluid rounded d-none" style="max-height: 300px; max-width: 100%;">
          </div>

          <div class="mt-3">
            <label for="clothingName" class="form-label">Name</label>
            <input type="text" class="form-control" id="clothingName" required>
          </div>
        
          <div class="mt-3">
            <label class="form-label">Tags auswählen</label>
            <div id="uploadTagSelection" class="d-flex flex-wrap gap-2">

            </div>
          </div>

          <div class="mt-3">
            <label for="clothingCategory" class="form-label">Kategorie</label>
            <select class="form-select" id="clothingCategory">
              <option selected>Kategorie auswählen...</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div id="special-attributes" class="d-none">

          <div id="waterproofness" class="d-none">
            <br>
            <div class="form-check" id="is-waterproof">
              <input class="form-check-input" type="checkbox" value="" id="is-waterproof-switch">
              <label class="form-check-label" for="is-waterproof-switch">Gegenstand ist Wasserfest</label>
            </div>
            <div id="ignore-waterproofness" class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="ignore-waterproofness-checkbox">
              <label class="form-check-label" for="ignore-waterproofness-checkbox">Wasserfestigkeit bei diesem Gegenstand nicht relevant</label>
            </div>
          </div>

          <div id="temp-range" class="d-none">
            <br>
            <div>
              <label for="temp-min" class="form-label">Minimale Temperatur</label>
              <input type="range" class="form-range" min="-90" max="60" value="0" id="temp-min">
              <output for="temp-min" id="rangeValue-min-temp"></output>
            </div>
            <br>
            <div>
              <label for="temp-max" class="form-label">Maximale Temperatur</label>
              <input type="range" class="form-range" min="-90" max="60" value="10" id="temp-max">
              <output for="temp-max" id="rangeValue-max-temp"></output>
            </div>
          </div>
          
          <div id="uv-range" class="d-none">
            <br>
            <div>
              <label for="uv-min" class="form-label">Minimale UV-Index</label>
              <input type="range" class="form-range" min="0" max="60" value="1" id="uv-min">
              <output for="uv-min" id="rangeValue-min-uv"></output>
            </div>
            <br>
            <div>
              <label for="uv-max" class="form-label">Maximale UV-Index</label>
              <input type="range" class="form-range" min="0" max="60" value="7" id="uv-max">
              <output for="uv-max" id="rangeValue-max-uv"></output>
            </div>
          </div>

          <div id="cloud-range" class="d-none">
            <br>
            <label for="cloud-cover-range" class="form-label">Bis zu welcher Bewölkung soll die Sonnenbrille empfohlen werden?</label>
            <input type="range" class="form-range" min="0" max="100" value="50" id="cloud-cover-range">
            <output for="cloud-cover-range" id="rangeValue-clouds"></output>
          </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="nextButton">Weiter</button>
        <button type="button" class="btn btn-primary d-none" id="previousButton">Zurück</button>
        <button type="button" class="btn btn-primary d-none" id="submitButton">Speichern</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@vite(['resources/js/profile/create-item-modal.js'])