let itemForUpload = {
    name: null,
    is_waterproof: null,
    min_temperature: null,
    max_temperature: null,
    min_uv_index: null,
    max_uv_index: null,
    cloud_cover_threshold: null,
    category_id: null,
    tags:  [] 
}
let currentPage = 0;
const imageInput = document.getElementById('clothingImage');

const nextPageButton = document.getElementById('nextButton');
const previousPageButton = document.getElementById('previousButton');
const submitButton = document.getElementById('submitButton');

const generalAttributesDiv = document.getElementById('general-attributes');
const specialAttributesDiv = document.getElementById('special-attributes');

const ignoreWaterproofness = document.getElementById('ignore-waterproofness-checkbox');
const waterproofnessSwitch = document.getElementById('is-waterproof-switch');

const waterproofnessDiv = document.getElementById('waterproofness');
const tempDiv = document.getElementById('temp-range');
const uvDiv = document.getElementById('uv-range');
const cloudDiv = document.getElementById('cloud-range');

const errorDiv = document.getElementById('upload-modal-error');

const modalEl = document.getElementById('uploadModal');

document.addEventListener('DOMContentLoaded', function() {
    const imagePreview = document.getElementById('imagePreview');
    const uploadTagSelection = document.getElementById('uploadTagSelection');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                imagePreview.classList.add('d-none');
            }
        });
    }


    if (cloudDiv) {
        const cloudCoverSampleImage = cloudDiv.querySelector('img');
        const cloudCoverSlider = cloudDiv.querySelector('#cloud-cover-range');
        if(cloudCoverSampleImage && cloudCoverSlider) {
            cloudCoverSampleImage.src = '/storage/cloud-examples/' + (parseInt(cloudCoverSlider.value / 10) * 10) + '.png';
            cloudCoverSlider.addEventListener("input", calculateCloudCoverImage);
        }
    }

    nextPageButton.addEventListener("click", nextPage);
    previousPageButton.addEventListener("click", previousPage);
    submitButton.addEventListener("click", uploadItem);

    ignoreWaterproofness.addEventListener("change", () => {
        if (ignoreWaterproofness.checked) {
            waterproofnessSwitch.disabled = true;
            waterproofnessSwitch.checked = false;
        } else {
            waterproofnessSwitch.disabled = false;
        }
    });

    bindRangeOutput("temp-min", "rangeValue-min-temp");
    bindRangeOutput("temp-max", "rangeValue-max-temp");
    bindRangeOutput("uv-min", "rangeValue-min-uv");
    bindRangeOutput("uv-max", "rangeValue-max-uv");
    bindRangeOutput("cloud-cover-range", "rangeValue-clouds");
})

modalEl.addEventListener('show.bs.modal', function () {
    const uploadTagSelection = document.getElementById('uploadTagSelection');

    if (uploadTagSelection && window.tags && window.tags.length > 0) {
        uploadTagSelection.innerHTML = '';
        window.tags.forEach((tag) => {
            addNewTagToItemModal(tag.name, tag.id);
        });
    }
});

export function addNewTagToItemModal(name, tagId) {
    const uploadTagSelection = document.getElementById('uploadTagSelection');
    const tagCheckbox = document.createElement('div');
    tagCheckbox.className = 'form-check';
    tagCheckbox.innerHTML = `
        <input class="form-check-input" type="checkbox" value="${name}" id="upload-tag-${tagId}" data-id="${tagId}">
        <label class="form-check-label" for="upload-tag-${tagId}">${name}</label>
    `;
    uploadTagSelection.appendChild(tagCheckbox);
    const newCheckboxElement = uploadTagSelection.querySelector(`#upload-tag-${tagId}`);
    newCheckboxElement.addEventListener("change", () => {
        if (newCheckboxElement.checked) {
            itemForUpload.tags.push(tagId);
        } else {

            const i = itemForUpload.tags.indexOf(tagId)
            if (i > -1) itemForUpload.tags.splice(i, 1)
        }
    });
}

function nextPage() {
    const itemName = document.getElementById('clothingName').value;
    const itemCategory = document.getElementById('clothingCategory').value;
    const category = window.categories.find(category => category.id === parseInt(itemCategory));

    if(!imageInput.files || !imageInput.files[0] || itemName == '' || !category) {
        errorDiv.innerHTML = 'Es müssen zuerst alle Felder ausgefüllt werden.';
        errorDiv.classList.remove('d-none');
        return;
    }

    if(currentPage === 0) {
        const minTempInput = tempDiv.querySelector("#temp-min");
        const maxTempInput = tempDiv.querySelector("#temp-max");
        const rangeValueSliderMax = tempDiv.querySelector("#rangeValue-max-temp");
        const rangeValueSliderMin = tempDiv.querySelector("#rangeValue-min-temp");

        errorDiv.classList.add('d-none');
        errorDiv.innerHTML = '';

        itemForUpload.name = itemName;
        itemForUpload.category_id = parseInt(itemCategory);

        if(itemForUpload.category_id === 10) { //Sonnenbrille
            cloudDiv.classList.remove("d-none");
        } else if(itemForUpload.category_id === 11) { //Sonnencreme
            uvDiv.classList.remove("d-none");
        } else if(category.is_impacted_by_rain === 1) { //Wasserfest
            tempDiv.classList.remove("d-none");
            waterproofnessDiv.classList.remove("d-none");

            if(itemForUpload.category_id === 4) { //Jacke
                minTempInput.value = 0;
                maxTempInput.value = 15;
            } else if(itemForUpload.category_id === 5) { //Hose
                minTempInput.value = 10;
                maxTempInput.value = 25;
            }
        } else { //Nicht wasserfest
            if(itemForUpload.category_id === 2) { //T-Shirt
                minTempInput.value = 15;
                maxTempInput.value = 35;
            } else if(itemForUpload.category_id === 3) { //Pullover
                minTempInput.value = 0;
                maxTempInput.value = 15;
            } else if(itemForUpload.category_id === 6) { //Strumpfhose
                minTempInput.value = 15;
                maxTempInput.value = 25;
            }
            tempDiv.classList.remove("d-none");
        }

        rangeValueSliderMax.value = maxTempInput.value;
        rangeValueSliderMin.value = minTempInput.value;

        currentPage++;
        generalAttributesDiv.classList.add("d-none");
        specialAttributesDiv.classList.remove("d-none");
        previousPageButton.classList.remove("d-none");
        submitButton.classList.remove("d-none");
        nextPageButton.classList.add("d-none");
    }
}

function previousPage() {
    if(currentPage === 1) {
        currentPage--;
        hideAllAttributes();
        generalAttributesDiv.classList.remove("d-none");
        specialAttributesDiv.classList.add("d-none");
        previousPageButton.classList.add("d-none");
        submitButton.classList.add("d-none");
        nextPageButton.classList.remove("d-none");
    }
}

function uploadItem() {
    const category = window.categories.find(category => category.id === itemForUpload.category_id);
    let itemUploadSuccess = false;

    if(itemForUpload.category_id === 10) { //Sonnenbrille
        itemForUpload.cloud_cover_threshold = cloudDiv.querySelector('#cloud-cover-range').value;
        itemForUpload.min_temperature = null;
        itemForUpload.max_temperature = null;
        itemForUpload.min_uv_index = null;
        itemForUpload.max_uv_index = null;
        itemForUpload.is_waterproof = null;
    } else if(itemForUpload.category_id === 11) { //Sonnencreme
        itemForUpload.min_uv_index = uvDiv.querySelector("#uv-min").value;

        itemForUpload.max_uv_index = uvDiv.querySelector("#uv-max").value;

        itemForUpload.min_temperature = null;
        itemForUpload.max_temperature = null;
        itemForUpload.is_waterproof = null;
        itemForUpload.cloud_cover_threshold = null;
    } else if(category.is_impacted_by_rain === 1) { //Wasserfest
        if (ignoreWaterproofness.checked) {
            itemForUpload.is_waterproof = null;
        } else {
            itemForUpload.is_waterproof = waterproofnessSwitch.checked;
        }
        itemForUpload.min_temperature = tempDiv.querySelector("#temp-min").value;
        itemForUpload.max_temperature = tempDiv.querySelector("#temp-max").value;

        itemForUpload.min_uv_index = null;
        itemForUpload.max_uv_index = null;
        itemForUpload.cloud_cover_threshold = null;
    } else { //Nicht wasserfest
        itemForUpload.min_temperature = tempDiv.querySelector("#temp-min").value;
        itemForUpload.max_temperature = tempDiv.querySelector("#temp-max").value;

        itemForUpload.min_uv_index = null;
        itemForUpload.max_uv_index = null;
        itemForUpload.cloud_cover_threshold = null;
        itemForUpload.is_waterproof = null;
    }

    const formData = new FormData();
    formData.append('name', itemForUpload.name ?? '');
    formData.append('category_id', itemForUpload.category_id ?? '');
    formData.append('is_waterproof', itemForUpload.is_waterproof ? 1 : 0);
    if (imageInput.files[0]) formData.append('filepath', imageInput.files[0]);
    if (itemForUpload.min_temperature !== null) formData.append('min_temperature', itemForUpload.min_temperature);
    if (itemForUpload.max_temperature !== null) formData.append('max_temperature', itemForUpload.max_temperature);
    if (itemForUpload.min_uv_index !== null) formData.append('min_uv_index', itemForUpload.min_uv_index);
    if (itemForUpload.max_uv_index !== null) formData.append('max_uv_index', itemForUpload.max_uv_index);
    if (itemForUpload.cloud_cover_threshold !== null) formData.append('cloud_cover_threshold', itemForUpload.cloud_cover_threshold);
    itemForUpload.tags.forEach(tag => {
        formData.append('tags[]', tag);
    });

    fetch('/save-item', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if(data.success) {
            errorDiv.innerHTML = '';
            errorDiv.classList.add('d-none');
            const modalEl = document.getElementById('uploadModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            modal.hide();
        
            window.location.reload();
        } else {
            errorDiv.innerHTML = data.message;
            errorDiv.classList.remove('d-none');
        }
    });
}

function hideAllAttributes() {
    for (const child of specialAttributesDiv.children) {
        if (child.tagName === "DIV") {
            child.classList.add("d-none");
        }
    }
}

function resetAllAttributes() {
    document.getElementById("clothingImage").value = "";
    const imagePreview = document.getElementById("imagePreview");
    imagePreview.src = "#";
    imagePreview.classList.add("d-none");

    document.getElementById("clothingName").value = "";
    document.getElementById("clothingCategory").selectedIndex = 0;

    const tagContainer = document.getElementById("uploadTagSelection");
    if(tagContainer) {
        tagContainer.querySelectorAll("input[type='checkbox']").forEach(cb => {
            cb.checked = false;
        });
    }

    ignoreWaterproofness.checked = false;
    waterproofnessSwitch.checked = false;

    document.getElementById('temp-min').value = 0;
    document.getElementById('temp-max').value = 10;
    document.getElementById('uv-min').value = 1;
    document.getElementById('uv-max').value = 7;
    document.getElementById('cloud-cover-range').value = 50;

    document.getElementById('rangeValue-min-temp').textContent = 0;
    document.getElementById('rangeValue-max-temp').textContent = 10;
    document.getElementById('rangeValue-min-uv').textContent = 1;
    document.getElementById('rangeValue-max-uv').textContent = 7;
    document.getElementById('rangeValue-clouds').textContent = 50;
}

function bindRangeOutput(rangeId, outputId) {
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
    if(!cloudDiv) return;
    const cloudCoverSampleImage = cloudDiv.querySelector('img');
    const cloudCoverSlider = cloudDiv.querySelector('#cloud-cover-range');
    if(cloudCoverSampleImage && cloudCoverSlider) {
        cloudCoverSampleImage.src = '/storage/cloud-examples/' + (parseInt(cloudCoverSlider.value / 10) * 10) + '.png';
    }
}