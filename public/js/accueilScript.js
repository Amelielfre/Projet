const checkbox_orga = document.getElementById("orga");
const checkbox_inscrit = document.getElementById("inscrit");
const checkbox_pas_inscrit = document.getElementById("pasInscrit");


checkbox_orga.addEventListener('change', function () {
    if (this.checked) {
        checkbox_pas_inscrit.disabled = true;
        checkbox_pas_inscrit.checked = false;
        if (!checkbox_inscrit.checked) {
            checkbox_inscrit.disabled = true;
            checkbox_inscrit.checked = false;
        }
    } else {
        checkbox_pas_inscrit.disabled = false;
        checkbox_pas_inscrit.checked = false;
        if (!checkbox_inscrit.checked) {
            checkbox_inscrit.disabled = false;
            checkbox_inscrit.checked = false;
        }
    }
});

checkbox_inscrit.addEventListener('change', function () {
    if (this.checked) {
        checkbox_pas_inscrit.disabled = true;
        checkbox_pas_inscrit.checked = false;
    } else {
        checkbox_pas_inscrit.disabled = false;
        checkbox_pas_inscrit.checked = false;
    }
});

checkbox_pas_inscrit.addEventListener('change', function () {
    if (this.checked) {
        checkbox_orga.disabled = true;
        checkbox_orga.checked = false;
        checkbox_inscrit.disabled = true;
        checkbox_inscrit.checked = false;
    } else {
        checkbox_orga.disabled = false;
        checkbox_orga.checked = false;
        checkbox_inscrit.disabled = false;
        checkbox_inscrit.checked = false;
    }
});