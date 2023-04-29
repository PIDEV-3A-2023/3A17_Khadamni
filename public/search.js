
const dropBtns = document.querySelectorAll('.dbtns');
const mainBtn = document.getElementById('menu-btn');
const mainBtnTxt = document.querySelector('.Btntxt');
const dropMenu = document.querySelector('.drop');
const searchField = document.getElementById('search-dropdown');



if (mainBtn !== null) {
    mainBtn.addEventListener("click",() =>{
        dropMenu.classList.toggle('hidden')
    })
    mainBtn.addEventListener('blur', function(event) {
        if (!event.relatedTarget || !event.relatedTarget.classList.contains('dbtns')) {
            dropMenu.classList.toggle('hidden');
        }
    });
    dropBtns.forEach( (btn) => {
        btn.addEventListener('click', (e)=>{
            let v = btn.value;
            let c = btn.textContent;
            btn.value = mainBtn.value;

            btn.textContent = mainBtn.textContent;

            mainBtn.value = v;
            mainBtnTxt.textContent = c;
            dropMenu.classList.toggle('hidden');
        })
    })
}






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


        //searchField.style = 'color:darkblue';
       // reset()



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
                const referer = window.location.href
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

                   if (referer.indexOf('admin') < 0)
                    FillUserArray(data);
                   else FillAdminArray(data);

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
    $.each(data, function (i, obj) {
        $('#new-tab').append(
            '<tr>' +
            '<th scope="row">' + obj.nomFormation.toUpperCase() + '</th>' +
            '<td style="min-width: 250px;">' + obj.description + '</td>' +
            '<td>' + obj.duree + ' semaines</td>' +
            '<td>' + obj.prix + ' TND</td>' +
            '<td><div class="act"></div></td>' +
            '</tr>');
            $('.act').html(' <button id="{{ \'dropdownDefaultButton\' ~ f.idFormation }}" data-dropdown-toggle="{{ \'dropdown\' ~ f.idFormation }}" class="text-white bg-blue-700 hover:bg-blue-800 mb-0 focus:outline-none  font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center " type="button">Actions<svg class="w-4 h-4 ml-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button>\n' +
                '                                                <!-- Dropdown menu -->\n' +
                '                                                <div id="{{ \'dropdown\' ~ f.idFormation }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow">\n' +
                '                                                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="{{ \'dropdownDefaultButton\' ~ f.idFormation }}">\n' +
                '                                                        <li>\n' +
                '                                                            <a href="{{ path(\'app_show_formation\',{\'id\':f.idFormation}) }}" class="block text-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Consulter</a>\n' +
                '                                                        </li>\n' +
                '                                                        <li>\n' +
                '                                                            <a href="{{ path(\'app_formation_edit\',{\'idFormation\':f.idFormation}) }}" class="block text-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Modifier</a>\n' +
                '                                                        </li>\n' +
                '                                                        <li>\n' +
                '                                                                <form method="post" action="{{ path(\'app_formation_delete\', {\'idFormation\': f.idFormation}) }}" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cet élément ??\');">\n' +
                '                                                                    <input type="hidden" name="_token" value="{{ csrf_token(\'delete\' ~ f.idFormation) }}">\n' +
                '                                                                    <button class="block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Supprimer</button>\n' +
                '                                                                </form>\n' +
                '                                                        </li>\n' +
                '\n' +
                '                                                    </ul>\n' +
                '                                                </div>')
    });

}

function FillAdminArray(data) {
    $.each(data, function (i, obj) {
        $('#new-tab').append(
            '<tr>' +
            '<td>' + obj.nomFormation.toUpperCase() + '</th>' +
            '<td>' + obj.description + '</td>' +
            '<td>' + obj.duree + ' semaines</td>' +
            '<td>' + obj.prix + ' TND</td>' +
            '<td><div class="sup"></div></td>' +
            '</tr>');
        $('.sup').html(
           '<form method="post" action="{{ path(\'app_formation_delete\', {\'idFormation\': formation.idFormation}) }}" onsubmit="return confirm(\'Êtes-vous sûr(e) de vouloir supprimer cet élément ?\');">\n' +
            '                                                    <input type="hidden" name="_token" value="{{ csrf_token(\'delete\' ~ formation.idFormation) }}">\n' +
            '                                                    <button class="btn">\n' +
            '                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 24 24">\n' +
            '                                                            <path d="M0 0h24v24H0V0z" fill="none"/>\n' +
            '                                                            <path d="M19 7h-3V6c0-1.1-.9-2-2-2H10c-1.1 0-2 .9-2 2v1H5v2h14V7zm-9-1h4v1h-4V6zM4.5 9h15l-1.39 12.63c-.08.69-.67 1.37-1.36 1.37H7.35c-.69 0-1.28-.68-1.36-1.37L4.5 9zm7.5 10c.92 0 1.67.75 1.67 1.67S13.92 22 13 22s-1.67-.75-1.67-1.67.75-1.67 1.67-1.67zm4.92-7l-.71-4H9.79l-.71 4h9.34z"/>\n' +
            '                                                        </svg>\n' +
            '                                                    </button>\n' +
            '                                                </form>'
        );

    });

}



