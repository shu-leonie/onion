<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<!-- Button für das Tag-Modal -->
<button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#addTagModal">
  <i class="bi bi-tag"></i> TAG HINZUFÜGEN
</button>

<!-- Tag-Modal -->
<div class="modal fade" id="addTagModal" tabindex="-1" aria-labelledby="addTagModalLabel" aria-hidden="true"
     style="position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addTagModalLabel">Neuen Tag hinzufügen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="newTag" class="form-label">Tag-Name</label>
          <input type="text" class="form-control" id="newTag" placeholder="z. B. 'Sommer'" required>
          <p id="tagError" class="text-danger mt-2"></p>
        </div>
        <!-- Liste der bereits hinzugefügten Tags -->
        <div id="tagPreview" class="mt-3">
          <p class="text-muted">Aktuelle Tags:</p>
          <div id="tagList" class="d-flex flex-wrap gap-2"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
        <button type="button" class="btn btn-success" id="saveTagButton">Tag speichern</button>
      </div>
    </div>
  </div>
</div>

<script>
  window.tags=@json($tags);
</script>
@vite(['resources/js/profile/tag-modal.js'])