
const ajoutBtn = document.getElementById('ajBtn');
const modifBtns = document.querySelectorAll('.modBtn');
const leftPanel = document.querySelector('.leftp');
const rightPanel = document.querySelector('.rightp');
const titre = document.getElementById('rptitle');
const nomf = document.getElementById('formation_nomFormation');
const dureef = document.getElementById('formation_duree');
const prixf = document.getElementById('formation_prix');
const descrf = document.getElementById('formation_description');
ajoutBtn.addEventListener('click', () => {
    if (rightPanel.classList.contains('hidden')) {

            rightPanel.classList.remove('hidden');
            leftPanel.classList.remove('active');
            ajoutBtn.textContent = 'Cacher le panneau';
            titre.textContent="Ajouter une formation";


    }
    else {
        rightPanel.classList.add('hidden');
        leftPanel.classList.add('active');
        ajoutBtn.textContent = 'Ajouter une formation';


    }
    nomf.value = descrf.value =  dureef.value =   prixf.value= "";
});

modifBtns.forEach( (btn) => {
    btn.addEventListener('click', () =>{
        const parent = btn.parentElement.parentElement;
        const dataB = parent.querySelectorAll('td');
        const data = Array.prototype.slice.call(dataB,0,3);
        const name = parent.querySelector('th');


        if (rightPanel.classList.contains('hidden') ) {
            rightPanel.classList.remove('hidden');
            leftPanel.classList.remove('active');
            ajoutBtn.textContent = 'Cacher le panneau';
            titre.textContent="Modifier une formation";

        }
        else {
            nomf.textContent = nomf.value = "test";
            titre.textContent="Modifier une formation";
        }
        nomf.value = name.textContent.trim();
        descrf.value = data[0].innerText;
        dureef.value = data[1].innerText.match(/\d+/)[0];
        prixf.value = data[2].innerText.match(/\d+/)[0];
    });
})
