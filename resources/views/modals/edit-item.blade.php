<div id="test-item-card" style="cursor:pointer;">
    Test Kleidungsstück anklicken
</div>

<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true"
     style="position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <strong class="modal-title" id="editItemModalLabel">Kleidungsstück bearbeiten</strong>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="alert alert-danger d-none" role="alert" id="editItemModalError"></div>

        <div id="editItemGeneralAttributes">
          <div class="mt-3">
            <label for="editItemName" class="form-label">Name</label>
            <input type="text" class="form-control" id="editItemName" required>
          </div>

          <div class="mt-3">
            <label class="form-label">Tags auswählen</label>
            <div id="editItemTagSelection" class="d-flex flex-wrap gap-2"></div>
          </div>

          <div class="mt-3">
            <label for="editItemCategory" class="form-label">Kategorie</label>
            <select class="form-select" id="editItemCategory" disabled>
              <option selected>Kategorie bleibt unverändert</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div id="editItemSpecialAttributes">

          <div id="editItemWaterproofness">
            <br>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="editItemWaterproofSwitch">
              <label class="form-check-label" for="editItemWaterproofSwitch">Gegenstand ist wasserfest</label>
            </div>
          </div>

          <div id="editItemTempRange">
            <br>

            <div id="editItemMinTempGroup">
              <label for="editItemTempMin" class="form-label">Minimale Temperatur</label>
              <input type="range" class="form-range" min="-90" max="60" value="0" id="editItemTempMin">
              <output for="editItemTempMin" id="editItemRangeValueMinTemp"></output>
            </div>

            <br>

            <div id="editItemMaxTempGroup">
              <label for="editItemTempMax" class="form-label">Maximale Temperatur</label>
              <input type="range" class="form-range" min="-90" max="60" value="10" id="editItemTempMax">
              <output for="editItemTempMax" id="editItemRangeValueMaxTemp"></output>
            </div>
          </div>

          <div id="editItemUvRange">
            <br>

            <div id="editItemMinUvGroup">
              <label for="editItemUvMin" class="form-label">Minimaler UV-Index</label>
              <input type="range" class="form-range" min="0" max="60" value="1" id="editItemUvMin">
              <output for="editItemUvMin" id="editItemRangeValueMinUv"></output>
            </div>

            <br>

            <div id="editItemMaxUvGroup">
              <label for="editItemUvMax" class="form-label">Maximaler UV-Index</label>
              <input type="range" class="form-range" min="0" max="60" value="7" id="editItemUvMax">
              <output for="editItemUvMax" id="editItemRangeValueMaxUv"></output>
            </div>
          </div>

          <div id="editItemCloudRange">
            <br>
            <label for="editItemCloudCoverRange" class="form-label">Cloud-Cover-Threshold</label>
            <input type="range" class="form-range" min="0" max="100" value="50" id="editItemCloudCoverRange">
            <output for="editItemCloudCoverRange" id="editItemRangeValueClouds"></output>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="editItemSubmitButton">Änderungen speichern</button>
      </div>

    </div>
  </div>
</div>
<script>
    window.tags = @json($tags);
</script>
@vite(['resources/js/profile/edit-item-modal.js'])