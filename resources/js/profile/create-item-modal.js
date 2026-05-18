let itemForUpload = {
    name: null,
    is_waterproof: null,
    min_temperature: null,
    max_temperature: null,
    min_uv_index: null,
    max_uv_index: null,
    cloud_cover_threshold: null,
    category: null,
    tags:  [
        null
    ] 
}
let currentPage = 0;
let itemUploadSuccess = false;
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

document.addEventListener('DOMContentLoaded', function() {
    const imagePreview = document.getElementById('imagePreview');
    const uploadTagSelection = document.getElementById('uploadTagSelection');
    const tagsData = document.body.dataset.tags;
    if (tagsData) {
        currentTags = JSON.parse(tagsData);
    }

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

    if (uploadTagSelection && currentTags.length > 0) {
        currentTags.forEach((tag, index) => {
            addNewTagToItemModal(tag, index);
        });
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

function addNewTagToItemModal(name, tagId) {
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
            itemForUpload.tags.push(index);
        } else {
            const i = itemForUploaditemForUpload.tags.indexOf(index)
            if (i > -1) itemForUploaditemForUpload.tags.splice(i, 1)
        }
    });
}

function nextPage() {
    if(currentPage === 0) {
        const itemName = document.getElementById('clothingName');
        const itemCategory = document.getElementById('clothingCategory');
        const category = categories.find(category => category.id === parseInt(itemCategory.value));

        itemForUpload.name = itemName.value;
        itemForUpload.category = parseInt(itemCategory.value);

        if(itemForUpload.category === 10) { //Sonnenbrille
            cloudDiv.classList.remove("d-none");
        } else if(itemForUpload.category === 11) { //Sonnencreme
            uvDiv.classList.remove("d-none");
        } else if(category.is_impacted_by_rain === 1) { //Wasserfest
            tempDiv.classList.remove("d-none");
            waterproofnessDiv.classList.remove("d-none");
        } else { //Nicht wasserfest
            tempDiv.classList.remove("d-none");
        }

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
    const category = categories.find(category => category.id === itemForUpload.category);

    if(itemForUpload.category === 10) { //Sonnenbrille
        itemForUpload.cloud_cover_threshold = cloudDiv.getElementById('cloud-cover-range').value;

        itemForUpload.min_temperature = null;
        itemForUpload.max_temperature = null;
        itemForUpload.min_uv_index = null;
        itemForUpload.max_uv_index = null;
        itemForUpload.is_waterproof = null;
    } else if(itemForUpload.category === 11) { //Sonnencreme
        itemForUpload.min_uv_index = uvDiv.querySelector("#uv-min").value;
        itemForUpload.min_uv_index = uvDiv.querySelector("#uv-max").value;

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
        itemForUpload.min_uv_index = tempDiv.querySelector("#temp-min").value;
        itemForUpload.min_uv_index = tempDiv.querySelector("#temp-max").value;

        itemForUpload.min_uv_index = null;
        itemForUpload.max_uv_index = null;
        itemForUpload.cloud_cover_threshold = null;
    } else { //Nicht wasserfest
        itemForUpload.min_uv_index = tempDiv.querySelector("#temp-min").value;
        itemForUpload.min_uv_index = tempDiv.querySelector("#temp-max").value;

        itemForUpload.min_uv_index = null;
        itemForUpload.max_uv_index = null;
        itemForUpload.cloud_cover_threshold = null;
        itemForUpload.is_waterproof = null;
    }

    const formData = new FormData();
    formData.append('image', imageInput.files[0]);
    formData.append('data', JSON.stringify(itemForUpload));

    fetch('/save-item', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        itemUploadSuccess = data.success;
    });


    if(itemUploadSuccess) {
        const modalEl = document.getElementById('uploadModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

        //KLEIDUNGSSTÜCK ÜBER FUNKTION ZU ITEMS AUF DER SEITE HINZUFÜGEN?
        modal.hide();
        generalAttributesDiv.classList.remove("d-none");
        specialAttributesDiv.classList.add("d-none");
        previousPageButton.classList.add("d-none");
        submitButton.classList.add("d-none");
        nextPageButton.classList.remove("d-none");
        currentPage = 0;
        hideAllAttributes();
        resetAllAttributes();
    }
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
    tagContainer.querySelectorAll("input[type='checkbox']").forEach(cb => {
        cb.checked = false;
    });


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
    output.textContent = range.value;
  };

  range.addEventListener("input", update);
  update(); // initialer Wert beim Laden
}