let currentTags = window.tags || [];
let editingTagId = null;

document.addEventListener('DOMContentLoaded', function () {
    const saveTagButton = document.getElementById('saveTagButton');
    const newTagInput = document.getElementById('newTag');
    const tagList = document.getElementById('tagList');
    const tagError = document.getElementById('tagError');

    function renderTags() {
        if (!tagList) return;

        tagList.innerHTML = '';

        currentTags.forEach(function (tag) {
            const tagElement = document.createElement('span');
            tagElement.className = 'badge bg-primary me-2 d-inline-flex align-items-center gap-1';

            const tagName = document.createElement('span');
            tagName.textContent = tag.name;
            tagName.style.cursor = 'pointer';

            tagName.addEventListener('click', function () {
                editingTagId = tag.id;
                newTagInput.value = tag.name;
                saveTagButton.textContent = 'Tag aktualisieren';
                tagError.textContent = '';
            });

            const deleteButton = document.createElement('button');
            deleteButton.className = 'tag-delete-button';
            deleteButton.type = 'button';
            deleteButton.textContent = 'x';

            deleteButton.addEventListener('click', async function () {
                const response = await fetch(`/tags/${tag.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (!response.ok || data.status === 'error') {
                    tagError.textContent = data.message || 'Tag konnte nicht gelöscht werden.';
                    return;
                }

                currentTags = currentTags.filter(function (currentTag) {
                    return currentTag.id !== tag.id;
                });

                tagError.textContent = '';
                renderTags();
            });

            tagElement.appendChild(tagName);
            tagElement.appendChild(deleteButton);
            tagList.appendChild(tagElement);
        });
    }

    renderTags();

    if (saveTagButton && newTagInput && tagList) {
        saveTagButton.addEventListener('click', async function () {
            const tagName = newTagInput.value.trim();

            if (!tagName) {
                return;
            }

            let response;

            if (editingTagId) {
                response = await fetch(`/tags/${editingTagId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: tagName
                    })
                });
            } else {
                if (currentTags.some(tag => tag.name === tagName)) {
                    return;
                }

                response = await fetch('/tags', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: tagName
                    })
                });
            }

            const data = await response.json();

            if (!response.ok || data.status === 'error') {
                tagError.textContent = data.message || 'Fehler beim Speichern des Tags.';
                return;
            }

            const savedTag = data.tag;

            if (editingTagId) {
                currentTags = currentTags.map(function (tag) {
                    if (tag.id === editingTagId) {
                        return savedTag;
                    }

                    return tag;
                });

                editingTagId = null;
                saveTagButton.textContent = 'Tag speichern';
            } else {
                currentTags.push(savedTag);
            }

            renderTags();

            tagError.textContent = '';
            newTagInput.value = '';
        });
    }
});