//tags werden daweil dynamisch mit js geladen, später wahrscheinlich aus db

let currentTags = [];

document.addEventListener('DOMContentLoaded', function() {
  
  const tagsData = document.body.dataset.tags;
  if (tagsData) {
    currentTags = JSON.parse(tagsData);
  }

  
  const saveTagButton = document.getElementById('saveTagButton');
  const newTagInput = document.getElementById('newTag');
  const tagList = document.getElementById('tagList');
  const uploadTagSelection = document.getElementById('uploadTagSelection');

  if (saveTagButton && newTagInput && tagList && uploadTagSelection) {
    saveTagButton.addEventListener('click', function() {
      const newTag = newTagInput.value.trim();
      if (newTag && !currentTags.includes(newTag)) {
        
        currentTags.push(newTag);

        
        const tagElement = document.createElement('span');
        tagElement.className = 'badge bg-primary me-2';
        tagElement.textContent = newTag;
        tagList.appendChild(tagElement);

       
        const tagCheckbox = document.createElement('div');
        tagCheckbox.className = 'form-check';
        tagCheckbox.innerHTML = `
          <input class="form-check-input" type="checkbox" name="tags[]" value="${newTag}" id="upload-tag-${currentTags.length - 1}">
          <label class="form-check-label" for="upload-tag-${currentTags.length - 1}">${newTag}</label>
        `;
        uploadTagSelection.appendChild(tagCheckbox);

        
        newTagInput.value = '';
      }
    });
  }
});