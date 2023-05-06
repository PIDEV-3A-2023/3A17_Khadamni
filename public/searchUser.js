

    $("#search-dropdown").keyup( function () {
    let value = $(this).val();

    if(value.length === 0) {
        $('#prev-tab').show();
        $('#new-tab').empty();
        $('#new-tab').fadeIn('fast');
    }
    else {
        $.ajax({
            url: "/user/search" ,
            type: 'GET',
            data: {
                'searchValue': value,
                
            },
            success: function (retour) {
                let data = JSON.parse(retour);
                console.log(data)
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
                        '<td>' + obj.nom + '</td>' +
                        '<td>' + obj.prenom + '</td>' +
                        '<td>' + obj.email + '</td>' +
                        '<td>' + obj.numero + ' </td>' +
                        '<td>' + obj.adresse + ' </td>' +
                        '<td>' + obj.age + ' </td>' +
                        '<td class="mod"></td>' +
                        '<td class="sup"></td>' +
                        '</tr>');
                   $('.mod').html(  ' <form method="post" action="{{ path(\'app_admin_modify\',{\'id\': user.idUser}) }}">\n' +
                       '   <select name="my_select">\n' +
                       '            <option value=1 {% if user.idRole.idRole == 1 %}selected{% endif %}>Admin</option>\n' +
                       '            <option value=2 {% if user.idRole.idRole == 2 %}selected{% endif %}>User</option>\n' +
                       '            <option value=3 {% if user.idRole.idRole == 3 %}selected{% endif %}>Recruteur</option>\n' +
                       '        </select>\n' +
                       '    \n' +
                       '    <button type="submit">Modifier</button>\n' +
                       '</form>');

                   $('.sup').html ( '   <form method="post" action="{{ path(\'app_user_delete\', {\'idUser\': user.idUser}) }}" onsubmit="return confirm(\'Are you sure you want to delete this item?\');">\n' +
                       '                                                    <input type="hidden" name="_token" value="{{ csrf_token(\'delete\' ~ user.idUser) }}">\n' +
                       '                                                    <button class="btn">\n' +
                       '                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 24 24">\n' +
                       '                                                            <path d="M0 0h24v24H0V0z" fill="none"/>\n' +
                       '                                                            <path d="M19 7h-3V6c0-1.1-.9-2-2-2H10c-1.1 0-2 .9-2 2v1H5v2h14V7zm-9-1h4v1h-4V6zM4.5 9h15l-1.39 12.63c-.08.69-.67 1.37-1.36 1.37H7.35c-.69 0-1.28-.68-1.36-1.37L4.5 9zm7.5 10c.92 0 1.67.75 1.67 1.67S13.92 22 13 22s-1.67-.75-1.67-1.67.75-1.67 1.67-1.67zm4.92-7l-.71-4H9.79l-.71 4h9.34z"/>\n' +
                       '                                                        </svg>\n' +
                       '                                                    </button>\n' +
                       '                                                </form>');

                 
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
    })