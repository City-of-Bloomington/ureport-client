"use strict";
function validate() {
    const media = document.getElementById('media'),
          files = media.files,
          max   = 10 * 1024 * 1024;
    if (files.length > 0) {
        if (files[0].size > max) {
            media.setCustomValidity('The image must not be larger than 10MB');
            media.reportValidity();
            return false;
        }
    }
    media.setCustomValidity('');
    return true;
}


function saveInput(event) {
    let input = event.target;
    localStorage.setItem(input.getAttribute('id'), input.value);
}

for (const f of ['firstname', 'lastname', 'email', 'phone']) {
    let i = document.getElementById(f);

    i.value = localStorage.getItem(f);
    i.addEventListener('change', saveInput);
}
