let elements = document.querySelectorAll('.dm-box');
elements.forEach(function (item) {
    new Vue({
        el: item
    });
});

let taxonomyEl = document.getElementById('addtag');
if (taxonomyEl) {
    new Vue({
        el: taxonomyEl
    });
}

