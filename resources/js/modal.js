//tags werden daweil dynamisch mit js geladen, später wahrscheinlich aus db

let currentTags = window.tags || [];

document.addEventListener('DOMContentLoaded', function() {

  
  const saveTagButton = document.getElementById('saveTagButton');
  const newTagInput = document.getElementById('newTag');
  const tagList = document.getElementById('tagList');
  const uploadTagSelection = document.getElementById('uploadTagSelection');
  const tagError = document.getElementById('tagError');

  function renderTags(){
    tagList.innerHTML='';
    currentTags.forEach(function(tag){
      const tagElement = document.createElement('span');
      const tagName = document.createElement('span');
      tagName.textContent = tag.name ?? tag;
      tagElement.className = 'badge bg-primary me-2 d-inline-flex align-items-center gap-1';
      const deleteButton=document.createElement('button');
      deleteButton.className = 'tag-delete-button';
      deleteButton.type = 'button';
      deleteButton.textContent='x';
      tagElement.appendChild(tagName);
      tagElement.appendChild(deleteButton);
      tagList.appendChild(tagElement);
    });
  }

  if (saveTagButton && newTagInput && tagList && uploadTagSelection) {

    saveTagButton.addEventListener('click', async function() {
     
      const newTag = newTagInput.value.trim();

     if (!newTag) {
        return;
    }

    if (currentTags.some(tag => tag.name === newTag)) {
        return;
    }

    const response = await fetch('/tags', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ name: newTag })
    });

    const data = await response.json();
    if (!response.ok || data.status == 'error') {
        tagError.textContent = data.message || 'Fehler beim Speichern des Tags.';
        return;
    }
    currentTags.push(data.tag);
    renderTags();

    tagError.textContent = '';

    newTagInput.value = ''; 

       
        const tagCheckbox = document.createElement('div');
        tagCheckbox.className = 'form-check';
        tagCheckbox.innerHTML = `
          <input class="form-check-input" type="checkbox" name="tags[]" value="${newTag}" id="upload-tag-${currentTags.length - 1}">
          <label class="form-check-label" for="upload-tag-${currentTags.length - 1}">${newTag}</label>
        `;
        uploadTagSelection.appendChild(tagCheckbox);

        
        newTagInput.value = '';
      
    });
  }
});