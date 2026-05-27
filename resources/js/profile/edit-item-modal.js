let editingItemId = null;
const allTags = window.tags || [];
// später durch echtes Item ersetzen
const testItem = {
    id: 1,
    name: "Test Jacke",
    waterproof: true,
    cloudcoverthreshold: 60,
    maxuv: 3,
    minuv: 8,
    maxtemp: 18,
    mintemp: null,
    tags: [
        { id: 1, name: "rain" }
    ]
};

function openEditItemModal(item) {

    editingItemId = item.id;

    document.getElementById('editItemName').value = item.name;

    const tagContainer = document.getElementById('editItemTagSelection');

    tagContainer.innerHTML = '';

    allTags.forEach(function(tag) {

        let checked = '';

        item.tags.forEach(function(itemTag) {
            if (itemTag.id === tag.id) {
                checked = 'checked';
            }
        });

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

    editItemTempMin.value = item.mintemp;
    editItemTempMax.value = item.maxtemp;
    editItemUvMin.value = item.minuv;
    editItemUvMax.value = item.maxuv;
    editItemCloudCoverRange.value = item.cloudcoverthreshold;
    editItemWaterproofSwitch.checked = item.waterproof;

    document.getElementById('editItemRangeValueMinTemp').textContent = item.mintemp;

    document.getElementById('editItemRangeValueMaxTemp').textContent = item.maxtemp;

    document.getElementById('editItemRangeValueMinUv').textContent = item.minuv;

    document.getElementById('editItemRangeValueMaxUv').textContent = item.maxuv;

    document.getElementById('editItemRangeValueClouds').textContent = item.cloudcoverthreshold;

    const editItemMinUvRange = document.getElementById('editItemMinUvGroup');
    const editItemMaxUvRange = document.getElementById('editItemMaxUvGroup');
    const editItemMinTempRange = document.getElementById('editItemMinTempGroup');
    const editItemMaxTempRange = document.getElementById('editItemMaxTempGroup');
    const editItemCloudRange = document.getElementById('editItemCloudRange');
    const editItemWaterproofness = document.getElementById('editItemWaterproofness');

    if (item.mintemp === null) {
        editItemMinTempRange.classList.add('d-none');
    } else {
        editItemMinTempRange.classList.remove('d-none');
    }

    if (item.maxtemp === null) {
        editItemMaxTempRange.classList.add('d-none');
    } else {
        editItemMaxTempRange.classList.remove('d-none');
    }

    if (item.minuv === null ) {
        editItemMinUvRange.classList.add('d-none');
    } else {
        editItemMinUvRange.classList.remove('d-none');
    }

    if (item.maxuv === null ) {
        editItemMaxUvRange.classList.add('d-none');
    } else {
        editItemMaxUvRange.classList.remove('d-none');
    }

    if (item.cloudcoverthreshold === null) {
        editItemCloudRange.classList.add('d-none');
    } else {
        editItemCloudRange.classList.remove('d-none');
    }

    if (item.waterproof === null) {
        editItemWaterproofness.classList.add('d-none');
    } else {
        editItemWaterproofness.classList.remove('d-none');
    }

    const modal = new bootstrap.Modal(document.getElementById('editItemModal'));

    modal.show();
}

document.addEventListener('DOMContentLoaded', function () {

    const testCard = document.getElementById('test-item-card');

    if (testCard) {
        testCard.addEventListener('click', function () {
            openEditItemModal(testItem);
        });
    }

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
        const cloudCoverSampleImage = cloudDiv.querySelector('img');
        const cloudCoverSlider = cloudDiv.querySelector('#editItemCloudCoverRange');
        cloudCoverSampleImage.src = '/storage/cloud-examples/' + (parseInt(cloudCoverSlider.value / 10) * 10) + '.png';
    }

    connectRangeValue('editItemTempMin', 'editItemRangeValueMinTemp');
    connectRangeValue('editItemTempMax', 'editItemRangeValueMaxTemp');
    connectRangeValue('editItemUvMin', 'editItemRangeValueMinUv');
    connectRangeValue('editItemUvMax', 'editItemRangeValueMaxUv');
    connectRangeValue('editItemCloudCoverRange', 'editItemRangeValueClouds');

    const cloudDiv = document.getElementById('editItemCloudRange');
    const cloudCoverSampleImage = cloudDiv.querySelector('img');
    const cloudCoverSlider = cloudDiv.querySelector('#editItemCloudCoverRange');
    cloudCoverSampleImage.src = '/storage/cloud-examples/' + (parseInt(cloudCoverSlider.value / 10) * 10) + '.png';
    cloudCoverSlider.addEventListener("input", calculateCloudCoverImage);

    const editItemSubmitButton = document.getElementById('editItemSubmitButton');

    if (editItemSubmitButton) {

        editItemSubmitButton.addEventListener('click', async function () {

            const updatedItem = {

                name: document.getElementById('editItemName').value,

                is_waterproof: document.getElementById('editItemWaterproofSwitch').checked,

                min_temperature: document.getElementById('editItemMinTempGroup').classList.contains('d-none')
                    ? null
                    : Number(document.getElementById('editItemTempMin').value),

                max_temperature: document.getElementById('editItemMaxTempGroup').classList.contains('d-none')
                    ? null
                    : Number(document.getElementById('editItemTempMax').value),

                min_uv_index: document.getElementById('editItemMinUvGroup').classList.contains('d-none')
                    ? null
                    : Number(document.getElementById('editItemUvMin').value),

                max_uv_index: document.getElementById('editItemMaxUvGroup').classList.contains('d-none')
                    ? null
                    : Number(document.getElementById('editItemUvMax').value),

                cloud_cover_threshold: document.getElementById('editItemCloudRange').classList.contains('d-none')
                    ? null
                    : Number(document.getElementById('editItemCloudCoverRange').value),

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
                console.log(data.message || 'Fehler beim Speichern');
                return;
            }

            const modalElement = document.getElementById('editItemModal');
            const modal = bootstrap.Modal.getInstance(modalElement);

            modal.hide();
        });
    }
});