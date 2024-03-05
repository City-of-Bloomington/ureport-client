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
document.getElementById('media').addEventListener('change', validate);
