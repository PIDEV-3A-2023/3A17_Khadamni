
const dropBtns = document.querySelectorAll('.dbtns');
const mainBtn = document.getElementById('menu-btn');
const mainBtnTxt = document.querySelector('.Btntxt');
const dropMenu = document.querySelector('.drop');


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

    $("#search-dropdown").keyup( function () {
    let value = $(this).val();
    let critere = $('#menu-btn').val();

    if(value.length === 0) {
        $('#prev-tab').show();
        $('#new-tab').empty();
        $('#new-tab').fadeIn('fast');
    }
    else {
        $.ajax({
            url: "/formation/search" ,
            type: 'GET',
            data: {
                'searchValue': value,
                'critere' : critere
            },
            success: function (retour) {
                let data = JSON.parse(retour);
                if (Object.keys(data).length === 0) {
                    $('#prev-tab').show();
                    $('#new-tab').empty();
                    $('#new-tab').fadeIn('fast');
                }
                else
                    $('#new-tab').empty();
                $.each(data, function(i, obj) {
                    $('#prev-tab').hide();
                    $('#new-tab').append(
                        '<tr>' +
                        '<td>' + obj.nomFormation + '</td>' +
                        '<td>' + obj.nomFormateur + '</td>' +
                        '<td><div class="rtg">' +
                        '</div></td>' +
                        '<td>' + obj.description + '</td>' +
                        '<td>' + obj.duree + ' semaines</td>' +
                        '<td>' + obj.prix + ' TND</td>' +
                        '<td><div class="inscr"></div></td>' +
                        '</tr>');

                    $('.rtg').html("<div class=\"flex items-center justify-center mt-2.5 mb-5\">\n" +
                        " <svg aria-hidden=\"true\" class=\"w-5 h-5 text-yellow-300\" fill=\"currentColor\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><title>First star</title><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"></path></svg>\n" +
                        " <svg aria-hidden=\"true\" class=\"w-5 h-5 text-yellow-300\" fill=\"currentColor\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><title>Second star</title><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"></path></svg>\n" +
                        " <svg aria-hidden=\"true\" class=\"w-5 h-5 text-yellow-300\" fill=\"currentColor\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><title>Third star</title><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"></path></svg>\n" +
                        " <svg aria-hidden=\"true\" class=\"w-5 h-5 text-yellow-300\" fill=\"currentColor\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><title>Fourth star</title><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"></path></svg>\n" +
                        " <svg aria-hidden=\"true\" class=\"w-5 h-5 text-yellow-300\" fill=\"currentColor\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><title>Fifth star</title><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"></path></svg>\n" +
                        " <span class=\"bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800 ml-3\">5.0</span>\n" +
                        " </div>");
                    $('.inscr').html("<a href=\"#\" class=\"font-extrabold flex content-center text-blue-800 hover:underline\">\n" +
                        " <svg aria-hidden=\"true\" class=\"w-5 h-5 mr-2 -ml-1\" fill=\"currentColor\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z\"></path></svg>\n" +
                        " S'inscrire</a>");
                });
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error +' '+status);
                $('#prev-tab').show();
                $('#new-tab').empty();
                $('#new-tab').fadeIn('fast');
            }

        });
    }
    });




