<?php include_once("../admin/lib/common/auth0_config.php"); ?>
var lock = new Auth0Lock('<?php echo $auth0_client_id ?>', '<?php echo $auth0_domain ?>', {

    initialScreen: 'login',
    allowSignUp: true,
    additionalSignUpFields: [
        {
            name: "full_name",
            placeholder: "Nombre y apellidos",
            validator: function(full_name) {
                return {
                    valid: full_name.length > 2 && full_name.length < 100,
                    hint: "Requerido"
                };
            }
        },
        {
            //ToDo: Lista de selección con las organizaciones existentes en SIDI
            name: "organization",
            placeholder: "Organización/Empresa",
            validator: function(organization) {
                return {
                    valid: organization.length > 2 && organization.length < 75,
                    hint: "Requerido"
                };
            }
        },
        {
            name: "phone",
            placeholder: "Teléfono de contacto",
            validator: function(phone) {
                return {
                    valid: phone.length == 0 || (phone.length == 7 &&  +phone == phone),
                    hint: "Opcional. Debe tener 7 dígitos"
                };
            }
        },
        {
            name: "cell",
            placeholder: "Teléfono celular",
            /*
            validator: function(cell) {
                return {
                    valid: cell.length == 10 && +cell == phone,
                    hint: "Requerido. Debe tener 10 dígitos"
                };
            }
            */
        }
    ],
    languageDictionary: {
        emailInputPlaceholder: "sucorreo@umaic.org",
        passwordInputPlaceholder: 'Contraseña',
        title: "Iniciar sesión"
    },
    theme: {
        logo: '<?php echo $auth0_logo ?>'
    },
    language: 'es',
    auth: {
        redirectUrl: '<?php echo $auth0_redirect_uri ?>',
        responseType: 'code',
        params: {
            scope: 'openid name email picture'
        }
    }
});
jQuery( document ).ready(function(){
    //lock.show();

});
