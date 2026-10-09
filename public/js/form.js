"use strict";
function validate() {
    const media    = document.getElementById('media'),
          files    = media.files,
          maxbytes = parseInt(media.dataset.maxbytes);


    if (files.length > 0) {
        if (files[0].size > maxbytes) {
            media.setCustomValidity('The image must not be larger than ' + media.dataset.maxsize);
            media.reportValidity();
            media.value = '';
            return false;
        }
    }
    media.setCustomValidity('');
    return true;
}
document.getElementById('media').addEventListener('change', validate);
