


const searchField = document.getElementById('search-dropdown');



$("#price-slider").on('slidestop', callback)
$("#duree-slider").on('slidestop', callback)
$("#search-dropdown").keyup( callback );
$("#reset-btn").on('click',function () {
    reset();
    $('#res').text('');
    $('#search-dropdown').val('');
})

function callback() {
    let value = $('#search-dropdown').val();
    let prix= [1,5000]
    let duree = [1,30]
    if ($('#price-slider').length)
        prix = $('#price-slider').slider("values");
    if ($('#duree-slider').length)
        duree =  $('#duree-slider').slider("values");

    $.ajax({
        url: "/formation/search",
        type: 'GET',
        data: {
            'searchValue': value,
            'minPrix' : prix[0],
            'maxPrix' : prix[1],
            'minDuree' : duree[0],
            'maxDuree' : duree[1]
        },
        success: function (retour) {
            let data = JSON.parse(retour);
            if (Object.keys(data).length === 0) {
                searchField.style = 'color:red';
                if ($('#res').length) {
                    $('#res').css({
                        'color': 'red',
                    })
                    $('#res').text('Aucune résultat trouvée');
                }

                reset();
            } else {
                searchField.style = 'color:green';
                if ($('#res').length) {
                    $('#res').css({
                        'color': 'green',
                    })
                    $('#res').text(data.length + ' formations trouvées');
                }

                $('#prev-tab').hide().fadeOut('fast');
                $('#new-tab').empty();
                $('#new-tab').show().fadeIn('fast');

                FillUserArray(data);
            }

        },
        error: function (xhr, status, error) {
            console.log("Error: " + error + ' ' + status);
            $('#prev-tab').show();
            $('#new-tab').empty();
        }

    });

}

function reset() {
    $('#new-tab').empty();
    $('#new-tab').hide().fadeOut('fast');
    $('#prev-tab').show().fadeIn('fast');
}
function FillUserArray(data) {
    let idForma = 0
    $.each(data, function (i, obj) {
        idForma = obj.idFormation
        let uniqueClassName = 'actu-' + idForma;
        $('#new-tab').append(
            '<tr class="ligne">' +
            '<th scope="row">' + obj.nomFormation.toUpperCase() + '</th>' +
            '<td style="min-width: 250px;">' + obj.description + '</td>' +
            '<td>' + obj.duree + ' semaines</td>' +
            '<td>' + obj.prix + ' TND</td>' +
            '<td class="' + uniqueClassName + '"></td>' +
            '</tr>');

        let token = document.getElementById(`t_${obj.idFormation}`);
        $('.' + uniqueClassName).html()

        $('.' + uniqueClassName).append(`
    <button id="d-btn${obj.idFormation}" data-dropdown-toggle="dropmenu${obj.idFormation}" class="text-white bg-blue-700 hover:bg-blue-800 mb-0 focus:outline-none font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center" type="button">
        Actions
        <svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <!-- Dropdown menu -->
    <div id="dropmenu${obj.idFormation}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow">
        <ul class="py-2 text-sm text-gray-700" aria-labelledby="d-btn${obj.idFormation}">
            <li>
                <a href="/formation/show/${obj.idFormation}" class="block text-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Consulter</a>
            </li>
            <li>
                <a href="/formation/${obj.idFormation}/edit" class="block text-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Modifier</a>
            </li>
            <li>
                <form method="post" action="/formation/${obj.idFormation}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ??');">
                            <input type="hidden" name="_token" value="${token.value}">
                    <button class="block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Supprimer</button>
                </form>
            </li>
        </ul>
    </div>
`);

        let button = document.getElementById(`d-btn${obj.idFormation}`);

        let dropdown = document.getElementById(`dropmenu${obj.idFormation}`);

        const options = {
            placement: 'bottom',
            triggerType: 'click',
            offsetSkidding: 0,
            offsetDistance: 10,
            delay: 300,
        };

new Dropdown(dropdown, button, options);












    });

}




