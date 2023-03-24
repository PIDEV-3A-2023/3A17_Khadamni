
const ajoutBtn = document.getElementById('ajBtn');
const modifBtns = document.querySelectorAll('.modBtn');
const leftPanel = document.querySelector('.leftp');
const rightPanel = document.querySelector('.rightp');
const titre = document.getElementById('rptitle');

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
});

modifBtns.forEach( (btn) => {
    btn.addEventListener('click', () =>{
        if (rightPanel.classList.contains('hidden') ) {
            rightPanel.classList.remove('hidden');
            leftPanel.classList.remove('active');
            ajoutBtn.textContent = 'Cacher le panneau';
            titre.textContent="Modifier une formation";
            // to do fill form
        }
        else {

            // fill form
            titre.textContent="Modifier une formation";
        }
    });
})
