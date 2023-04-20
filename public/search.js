
const dropBtns = document.querySelectorAll('.dbtns');
const mainBtn = document.getElementById('menu-btn');
const mainBtnTxt = document.querySelector('.Btntxt');
const dropMenu = document.querySelector('.drop');

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

$("#search-dropdown").keyup(function () {
    let value = $(this).val();
    let critere = $('#menu-btn').val();
    console.log(value + value.length)
    if (value.length === 0) {

        $('#new-tab').empty();
        $('#new-tab').hide().fadeOut('fast');
        $('#prev-tab').show().fadeIn('fast');

    } else {

        $.ajax({
            url: "/formation/search",
            type: 'GET',
            data: {
                'searchValue': value,
                'critere': critere
            },
            success: function (retour) {
                let data = JSON.parse(retour);
                if (Object.keys(data).length === 0) {
                    $('#new-tab').empty();
                    $('#new-tab').hide().fadeOut('fast');
                    $('#prev-tab').show().fadeIn('fast');
                } else {
                    $('#prev-tab').hide().fadeOut('fast');
                    $('#new-tab').empty();
                    $('#new-tab').show().fadeIn('fast');
                    $.each(data, function (i, obj) {
                        $('#new-tab').append(
                            '<tr>' +
                            '<td>' + obj.nomFormation.toUpperCase() + '</td>' +
                            '<td style="min-width: 250px;">' + obj.description + '</td>' +
                            '<td>' + obj.duree + ' semaines</td>' +
                            '<td>' + obj.prix + ' TND</td>' +
                            '<td><div class="mod"></div></td>' +
                            '<td><div class="sup"></div></td>' +
                            '</tr>');
                        $('.sup').html(
                            "<div>\n" +
                            " <form method=\"post\" action=\"{{ path('app_formation_delete', {'idFormation': f.idFormation}) }}\" onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer cet élément ??');\">\n" +
                            "  <input type=\"hidden\" name=\"_token\" value=\"{{ csrf_token('delete' ~ f.idFormation) }}\">\n" +
                            "  <button class=\"font-bold text-blue-800 hover:underline expand-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded\">Supprimer</button>\n" +
                            "  </form>\n" +
                            "  </div>"
                        );
                        $('.mod').html(
                            " <a href=\"{{ path('app_formation_edit',{'idFormation':f.idFormation}) }}\" class=\"font-bold text-blue-800 hover:underline expand-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded\">Modifier</a>\n"
                        )
                    });

                }
            },
            error: function (xhr, status, error) {
                console.log("Error: " + error + ' ' + status);
                $('#prev-tab').show();
                $('#new-tab').empty();
            }

        });
    }
});





