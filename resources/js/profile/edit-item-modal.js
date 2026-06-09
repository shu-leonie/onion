let editingItemId = null;
let editingItemCategoryId = null;
const allTags = window.tags || [];

window.openEditItemModal = function(item) {
    editingItemId = item.id;
    editingItemCategoryId = item.category_id;
    document.getElementById('editItemName').value = item.name;

    const tagContainer = document.getElementById('editItemTagSelection');
    tagContainer.innerHTML = '';

    allTags.forEach(function(tag) {
        let checked = '';

        if (item.tags) {
            item.tags.forEach(function(itemTag) {
                if (itemTag.id === tag.id) {
                    checked = 'checked';
                }
            });
        }

        tagContainer.innerHTML += `
            <div class="form-check">
                <input 
                    class="form-check-input"
                    type="checkbox"
                    value="${tag.id}"
                    id="edit-tag-${tag.id}"
                    ${checked}
                >
                <label class="form-check-label" for="edit-tag-${tag.id}">
                    ${tag.name}
                </label>
            </div>
        `;
    });

    const editItemTempMin = document.getElementById('editItemTempMin');
    const editItemTempMax = document.getElementById('editItemTempMax');
    const editItemUvMin = document.getElementById('editItemUvMin');
    const editItemUvMax = document.getElementById('editItemUvMax');
    const editItemCloudCoverRange = document.getElementById('editItemCloudCoverRange');
    const editItemWaterproofSwitch = document.getElementById('editItemWaterproofSwitch');
    const editItemCategory = document.getElementById('editItemCategory');


    editItemTempMin.value = item.min_temperature != null ? item.min_temperature : 0;
    editItemTempMax.value = item.max_temperature != null ? item.max_temperature : 10;
    editItemUvMin.value = item.min_uv_index != null ? item.min_uv_index : 1;
    editItemUvMax.value = item.max_uv_index != null ? item.max_uv_index : 7;
    editItemCloudCoverRange.value = item.cloud_cover_threshold != null ? item.cloud_cover_threshold : 50;
    editItemWaterproofSwitch.checked = item.is_waterproof != null ? item.is_waterproof : false;

    document.getElementById('editItemRangeValueMinTemp').textContent = editItemTempMin.value;
    document.getElementById('editItemRangeValueMaxTemp').textContent = editItemTempMax.value;
    document.getElementById('editItemRangeValueMinUv').textContent = editItemUvMin.value;
    document.getElementById('editItemRangeValueMaxUv').textContent = editItemUvMax.value;
    document.getElementById('editItemRangeValueClouds').textContent = editItemCloudCoverRange.value;

    const editItemMinUvRange = document.getElementById('editItemMinUvGroup');
    const editItemMaxUvRange = document.getElementById('editItemMaxUvGroup');
    const editItemMinTempRange = document.getElementById('editItemMinTempGroup');
    const editItemMaxTempRange = document.getElementById('editItemMaxTempGroup');
    const editItemCloudRange = document.getElementById('editItemCloudRange');
    const editItemWaterproofness = document.getElementById('editItemWaterproofness');


    if (item.min_temperature == null) {
        editItemMinTempRange.classList.add('d-none');
    } else {
        editItemMinTempRange.classList.remove('d-none');
    }

    if (item.max_temperature == null) {
        editItemMaxTempRange.classList.add('d-none');
    } else {
        editItemMaxTempRange.classList.remove('d-none');
    }

    if (item.min_uv_index == null ) {
        editItemMinUvRange.classList.add('d-none');
    } else {
        editItemMinUvRange.classList.remove('d-none');
    }

    if (item.max_uv_index == null ) {
        editItemMaxUvRange.classList.add('d-none');
    } else {
        editItemMaxUvRange.classList.remove('d-none');
    }

    if (item.cloud_cover_threshold == null) {
        editItemCloudRange.classList.add('d-none');
    } else {
        editItemCloudRange.classList.remove('d-none');
    }

    if (item.is_waterproof == null) {
        editItemWaterproofness.classList.add('d-none');
    } else {
        editItemWaterproofness.classList.remove('d-none');
    }

    const modal = new bootstrap.Modal(document.getElementById('editItemModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function () {


    function connectRangeValue(rangeId, outputId) {
        const range = document.getElementById(rangeId);
        const output = document.getElementById(outputId);

        if (!range || !output) return;

        const update = () => {
            output.value = range.value;
        };
        const update2 = () => {
            const max = Number(output.max);
            const min = Number(output.min);
            const value = Number(output.value);

            if(value > max) output.value = max;
            if(value < min) output.value = min;
            range.value = output.value;
            calculateCloudCoverImage();
        };

        range.addEventListener("input", update);
        output.addEventListener("input", update2);
        update();
    }

    function calculateCloudCoverImage() {
        const cloudDiv = document.getElementById('editItemCloudRange');
        if(!cloudDiv) return;
        const cloudCoverSampleImage = cloudDiv.querySelector('img');
        const cloudCoverSlider = cloudDiv.querySelector('#editItemCloudCoverRange');
        if(cloudCoverSampleImage && cloudCoverSlider) {
            cloudCoverSampleImage.src = '/storage/cloud-examples/' + (parseInt(cloudCoverSlider.value / 10) * 10) + '.png';
        }
    }

    connectRangeValue('editItemTempMin', 'editItemRangeValueMinTemp');
    connectRangeValue('editItemTempMax', 'editItemRangeValueMaxTemp');
    connectRangeValue('editItemUvMin', 'editItemRangeValueMinUv');
    connectRangeValue('editItemUvMax', 'editItemRangeValueMaxUv');
    connectRangeValue('editItemCloudCoverRange', 'editItemRangeValueClouds');

    const cloudDiv = document.getElementById('editItemCloudRange');
    if (cloudDiv) {
        const cloudCoverSampleImage = cloudDiv.querySelector('img');
        const cloudCoverSlider = cloudDiv.querySelector('#editItemCloudCoverRange');
        if (cloudCoverSampleImage && cloudCoverSlider) {
            cloudCoverSampleImage.src = '/storage/cloud-examples/' + (parseInt(cloudCoverSlider.value / 10) * 10) + '.png';
            cloudCoverSlider.addEventListener("input", calculateCloudCoverImage);
        }
    }

    const editItemSubmitButton = document.getElementById('editItemSubmitButton');
    const editItemModalError = document.getElementById('editItemModalError');

    if (editItemSubmitButton) {
        editItemSubmitButton.addEventListener('click', async function () {
            const updatedItem = {
                name: document.getElementById('editItemName').value,
                category_id: editingItemCategoryId,
                is_waterproof: document.getElementById('editItemWaterproofSwitch').checked,
                min_temperature: document.getElementById('editItemMinTempGroup').classList.contains('d-none')
                    ? null : Number(document.getElementById('editItemTempMin').value),
                max_temperature: document.getElementById('editItemMaxTempGroup').classList.contains('d-none')
                    ? null : Number(document.getElementById('editItemTempMax').value),
                min_uv_index: document.getElementById('editItemMinUvGroup').classList.contains('d-none')
                    ? null : Number(document.getElementById('editItemUvMin').value),
                max_uv_index: document.getElementById('editItemMaxUvGroup').classList.contains('d-none')
                    ? null : Number(document.getElementById('editItemUvMax').value),
                cloud_cover_threshold: document.getElementById('editItemCloudRange').classList.contains('d-none')
                    ? null : Number(document.getElementById('editItemCloudCoverRange').value),
                tags: Array.from(
                    document.querySelectorAll('#editItemTagSelection input:checked')
                ).map(function(input) {
                    return Number(input.value);
                })
            };

            const response = await fetch(`/items/${editingItemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(updatedItem)
            });

            const data = await response.json();

            if (!response.ok || data.status === 'error') {
                console.log("Laravel Fehler:", data);
                editItemModalError.textContent = 'Kleidungsstück konnte nicht gespeichert werden.';
                editItemModalError.classList.remove('d-none');
                return;
            }

            const modalElement = document.getElementById('editItemModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            editItemModalError.textContent = '';
            editItemModalError.classList.add('d-none');
            modal.hide();


            window.location.reload();
        });
    }

    const deleteItemButton = document.getElementById('deleteItemButton');

    if (deleteItemButton) {
        deleteItemButton.addEventListener('click', async function () {

            const isConfirmed = confirm('Bist du dir ganz ganz sicher, dass du dieses Kleidungsstück löschen möchtest?\n(Das kann nicht rückgängig gemacht werden.)');
            
            if (!isConfirmed) {
                return; 
            }

            const response = await fetch(`/items/${editingItemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            window.location.reload();
        });
    }


    
});