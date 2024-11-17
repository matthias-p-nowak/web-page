
function mediaPreventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleFileDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    const fi = document.getElementById('files');
    fi.files = files;
}

function addDropHandlers() {
    const dropArea = document.getElementById('drop_Area');
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, mediaPreventDefaults, false)
        document.body.addEventListener(eventName, mediaPreventDefaults, false)
    });
    // Highlight drop area when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('dragHigh'), false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragHigh'), false);
    });
    dropArea.addEventListener('drop', handleDrop, false);
    console.log('picture.js loaded');
};
