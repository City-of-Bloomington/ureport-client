"use strict";
const input         = document.getElementById('query'),
      results       = document.getElementById('searchResults'),
      minCharacters = 3,
      xhr           = new XMLHttpRequest();

const debounce = (f, d) => {
    let timer = null;

    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            f(...args);
        }, d);
    }
};
const doQuery = debounce((e) => {
    if (input.value.length > minCharacters) {
        switch (e.code) {
            case 'Enter':
                break;

            default:
                xhr.open('GET', BASE_URI + '/?partial=true&query=' + input.value);
                xhr.onload = (e) => { results.innerHTML = xhr.response; }
                xhr.send();
        }
    }
}, 300);

input.addEventListener('keyup', doQuery);
