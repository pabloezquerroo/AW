//funciones de control

function validEmail(){
    const campo = $("#email");
    if(campo[0] == null)return true;

    campo[0].setCustomValidity("");
    $("#errorEmail").hide();
    $("#errorEmail").html("");
    campo.css('color',"#029A7F");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");


    if(!campo[0].checkValidity()){
        campo.css('border-color',"red");
        $("#errorEmail").show();
        $("#errorEmail").html("&#x274c El correo no es correcto");
        campo[0].setCustomValidity("El correo no es correcto");
        campo[0].focus();
        return false;
    }
    else if(campo.val().length ==0){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("El correo no puede estar vacío");
        campo[0].focus();

        return false;
    }
    else if(campo.val().length > 100){
        campo.css('border-color',"red");
        $("#errorEmail").show();
        $("#errorEmail").html("&#x274c El correo no puede tener más de 100 caracteres");
        return false;
    }
    return true;
}
function validNombre(){
    const campo = $("#nombre");
    if(campo[0] == null)return true;
    $("#errorNombre").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity()){
        campo.css('border-color',"red");
        $("#errorNombre").show();
        $("#errorNombre").html("&#x274c El nombre no es válido");

        return false;
    }
    else if (campo.val().length <3 && campo.val().length >0){
        campo.css('border-color',"red");
        $("#errorNombre").show();
        $("#errorNombre").html("&#x274c El nombre tiene que tener más de 3 caracteres");
        return false;
    }
    else if(campo.val().length == 0 ){
        campo.css('border-width',"0px");
        campo[0].focus();
        campo[0].setCustomValidity("El nombre tiene que tener más de 3 caracteres");
        return false;
    }
    else if ( campo.val().length >30){
        campo.css('border-color',"red");
        $("#errorNombre").show();
        $("#errorNombre").html("&#x274c El nombre tiene que tener menos de 30 caracteres");
        return false;
    }
    return true;
}
function validPass(){
    const campo = $("#password");
    if(campo[0] == null)return true;
    $("#errorPass").hide();
    campo[0].setCustomValidity("");
    campo.css('color',"#029A7F");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(campo.val().length == 0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("la contraseña no puede estar vacía");
        campo[0].focus();
        return false;
    }
    else if(campo.val().length < 5 ){
        campo.css('border-color',"red");
        $("#errorPass").show();
        $("#errorPass").html("&#x274c La contraseña tiene que tener mínimo 5 caracteres");
        return false;
    }

    const campo2 = $("#password2");
    $("#errorPass").hide();
    campo2[0].setCustomValidity("");
    campo2.css('color',"#029A7F");
    campo2.css('border-width',"3px");
    campo2.css('border-style',"solid");
    campo2.css('border-color',"green");
    if(campo2.val().length == 0 ){
        campo2[0].setCustomValidity("Vuelva a introducir la contraseña");
        campo2.css('border-width',"0px");
        campo[0].focus();
        return false;
    }
    else if(campo2.val()!= campo.val()){
        campo2.css('border-color',"red");
        $("#errorPass").show();
        $("#errorPass").html("&#x274c Las contraseñas son distintas");
        return false;
    }

    const campo3 = $("#nuevaPassword");
    $("#errorPass").hide();
    campo3[0].setCustomValidity("");
    campo3.css('color',"#029A7F");
    campo3.css('border-width',"3px");
    campo3.css('border-style',"solid");
    campo3.css('border-color',"green");
    if(campo3.val().length == 0 ){
        campo3.css('border-width',"0px");
        campo3[0].setCustomValidity("la contraseña no puede estar vacía");
        campo[0].focus();
        return false;
    }
    else if(campo3.val().length < 5 && campo3.val().length >0){
        campo3.css('border-color',"red");
        $("#errorPass").show();
        $("#errorPass").html("&#x274c La contraseña es demasiado corta");
        return false;
    }

    const campo4 = $("#nuevaPassword2");
    $("#errorPass").hide();
    campo4[0].setCustomValidity("");
    campo4.css('color',"#029A7F");
    campo4.css('border-width',"3px");
    campo4.css('border-style',"solid");
    campo4.css('border-color',"green");
    if(campo4.val().length == 0 ){
        campo4.css('border-width',"0px");
        campo4[0].setCustomValidity("Vuelva a introducir la contraseña");
        campo[0].focus();
        return false;
    }
    else if(campo4.val().length < 5 && campo4.val().length >0){
        campo4.css('border-color',"red");
        $("#errorPass").show();
        $("#errorPass").html("&#x274c La contraseña es demasiado corta");
        return false;
    }


    return true;
}
function validDirec(){
    const campo = $("#direccion");
    if(campo[0] == null)return true;
    $("#errorDirec").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity()){
        campo.css('border-color',"red");
        $("#errorDirec").show();
        $("#errorDirec").html("&#x274c La dirección no es válida");
        return false;
    }
    else if(campo.val().length == 0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("La dirección no puede quedar vacía");
        campo[0].focus();
        return false;
    }
    else if(campo.val().length >100){
        campo.css('border-color',"red");
        $("#errorDirec").show();
        $("#errorDirec").html("&#x274c La dirección es demasiado larga. Máximo 100 caracteres");
        return false;
    }
    return true;
}
function validConv(){
    const campo = $("#num_convivientes");
    if(campo[0] == null)return true;
    $("#errorConv").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity()){
        campo.css('border-color',"red");
        $("#errorConv").show();
        $("#errorConv").html("&#x274c El número de convivientes no es válido");
        return false;
    }
    else if(campo.val()<0){
        campo.css('border-color',"red");
        $("#errorConv").show();
        $("#errorConv").html("&#x274c El número de convivientes no puede ser negativo");
        campo[0].focus();
        return false;
    }
    else if(campo.val().length == 0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("El número de convivientes no puede quedar vacío, en caso de no tener ponga: 0");
        campo[0].focus();
        return false
    }
    return true;
}
function validDedicacion(){
    const campo = $("#dedicacion");
    if(campo[0] == null)return true;
    $("#errorDedi").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity()){
        campo.css('border-color',"red");
        $("#errorDedi").show();
        campo[0].focus();
        $("#errorDedi").html("&#x274c La dedicación no es válida");
        return false;
    }
    else if(campo.val().length == 0 ){
        campo.css('border-width',"0px");
        campo[0].focus();
        campo[0].setCustomValidity("La dedicación no puede quedar vacía, en caso de no tener ponga: NADA");
        return false;
    }
    else if(campo.val().length > 30 ){
        campo.css('border-color',"red");
        $("#errorDedi").show();
        $("#errorDedi").html("&#x274c La dedicación es demasiado larga, Tiene que tener menos de 30 caracteres");
        return false;
    }
    return true;
}
function validNmasc(){
    const campo = $("#num_mascotas");
    if(campo[0] == null)return true;
    $("#errorMasc").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity() || campo.val()<0){
        campo.css('border-color',"red");
        $("#errorMasc").show();
        $("#errorMasc").html("&#x274c El número de mascotas no es válido");
        return false;
    }
    else if(campo.val().length ==0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("El número de mascotas no puede estar vacío");
        campo[0].focus();
        return false;
    }
    return true;
}
function validMviv(){
    const campo = $("#m2_vivienda");
    if(campo[0] == null)return true;
    $("#errorMviv").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity() || campo.val()<0){
        campo.css('border-color',"red");
        $("#errorMviv").show();
        $("#errorMviv").html("&#x274c El número de mascotas no es válido");
        return false;
    }
    else if(campo.val().length ==0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("El número de mascotas no puede estar vacío");
        campo[0].focus();
        return false;
    }
    return true;
}
function validTel(){
    const campo = $("#telefono");
    if(campo[0] == null)return true;
    $("#errorTel").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity() || campo.val()<0){
        campo.css('border-color',"red");
        $("#errorTel").show();
        $("#errorTel").html("&#x274c El teléfono no es válido");
        return false;
    }
    else if(campo.val().length ==0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("El teléfono no puede estar vacío");
        campo[0].focus();
        return false;
    }
    return true;
}
function validEdad(){
    const campo = $("#edad");
    if(campo[0] == null)return true;
    $("#errorEdad").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity() || campo.val()<0){
        campo.css('border-color',"red");
        $("#errorEdad").show();
        $("#errorEdad").html("&#x274c La edad no es válida");
        return false;
    }
    else if(campo.val().length ==0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("La edad no puede estar vacía");
        campo[0].focus();
        return false;
    }
    return true;
}
function validPeso(){
    const campo = $("#peso");
    if(campo[0] == null)return true;
    $("#errorPeso").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity() || campo.val()<0){
        campo.css('border-color',"red");
        $("#errorPeso").show();
        $("#errorPeso").html("&#x274c El peso no es válido");
        return false;
    }
    else if(campo.val().length ==0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("El peso no puede estar vacío");
        campo[0].focus();
        return false;
    }
    return true;
}
function validRaza(){
    const campo = $("#raza");
    if(campo[0] == null)return true;
    $("#errorRaza").hide();
    campo[0].setCustomValidity("");
    campo.css('border-width',"3px");
    campo.css('border-style',"solid");
    campo.css('border-color',"green");
    if(!campo[0].checkValidity() || campo.val().length<0){
        campo.css('border-color',"red");
        $("#errorRaza").show();
        $("#errorRaza").html("&#x274c La raza no es válida");
        return false;
    }
    else if(campo.val().length ==0 ){
        campo.css('border-width',"0px");
        campo[0].setCustomValidity("La raza no puede estar vacía");
        campo[0].focus();
        return false;
    }
    return true;
}
function validFile(file){
   /* if(file.length == 0){
        var parrafo = document.getElementById("errorFile");
        parrafo.style.color="red";
        parrafo.innerHTML = "El nombre no puede estar vacio";
        return false;
    }*/
    return true;
}
function validFiltro(){
    var salida = true;
    const campo = $("#edadmin");
    if(campo[0] == null)return true;
    const campo2 = $("#edadmax");
    const campo3 = $("#pesomin");
    const campo4 = $("#pesomax");
    $("errorEdad").hide();
    if(campo.val() > campo2.val()){
        $("errorEdad").html("&#x274c La edad máxima tiene que ser mayor que la mínima");
        $("errorEdad").show();
        salida = false;
    }
    $("errorPeso").hide();
    if(campo3.val() > campo4.val()){
        $("errorPeso").show();
        $("errorPeso").html("&#x274c El peso máximo tiene que ser mayor que el mínimo");
        salida = false;
    }

    return salida;
}
function validTitulo(){
    const campo = $("#titulo");
    var salida = true;
    if(campo[0] == null)return true;
    $("#errorTitulo").hide();
    campo[0].setCustomValidity("");
    if(!campo[0].checkValidity()){
        $("#errorTitulo").show();
        $("#errorTitulo").html("&#x274c El título no es válido");
        salida = false;
    }
    else if(campo.val().length<8 && campo.val().length>0){
        $("#errorTitulo").show();
        $("#errorTitulo").html("&#x274c El título tiene que tener 8 caracteres");
        salida = false;
    }
    else if(campo.val().length ==0 ){
        campo[0].setCustomValidity("El título no puede estar vacío");
        campo[0].focus();
        salida = false;
    }
    else if(campo.val().length >100 ){
        $("#errorTitulo").show();
        $("#errorTitulo").html("&#x274cEl título es demasiado largo. Tiene que tener menos de 100 caracteres");
        salida = false;
    }
    
    return salida;
}
function validDecr(){
    const campo2 = $("#descripcion");
    if(campo2[0] == null)return true;
    $("#errorDesc").hide();
    campo2[0].setCustomValidity("");
    if(!campo2[0].checkValidity()){
        $("#errorDesc").show();
        $("#errorDesc").html("&#x274c La descripción no es válida.");
        return false;
    }
    else if (campo2.val().length < 10 && campo2.val().length > 0){
        $("#errorDesc").show();
        $("#errorDesc").html("&#x274c La descripción no es válida. Debe tener más de 10 caracteres");
        return false;
    }
    else if (campo2.val().length >= 500){
        $("#errorDesc").show();
        $("#errorDesc").html("&#x274c La descripción no es válida. Debe tener menos de 500 caracteres");
        return false;
    }
    else if(campo2.val().length ==0 ){
        campo2[0].setCustomValidity("La descripción no puede estar vacía");
        campo2[0].focus();
        return false;
    }
    return true;
}

//controladores de formularios

$(document).change(function(){
    validMviv();
    validTel();
    validNmasc();
    validDedicacion();
    validConv();
    validDirec();
    validPass();
    validNombre();
    validEmail();   
    validFiltro();
    validTitulo();
    validDecr();
    validPeso();
    validEdad();
    validRaza();
});

$(document).submit(function(evento){
    if(!validFiltro())evento.preventDefault();
    if(!validEmail())evento.preventDefault();
    if(!validNombre()) evento.preventDefault();
    if(!validPass())evento.preventDefault();
    if(!validDirec()) evento.preventDefault();
    if(!validConv()) evento.preventDefault();
    if(!validNmasc()) evento.preventDefault();
    if(!validTel()) evento.preventDefault();
    if(!validMviv()) evento.preventDefault();
    if(!validDedicacion()) evento.preventDefault();
    if(!validTitulo()) evento.preventDefault();
    if(!validPeso()) evento.preventDefault();
    if(!validEdad()) evento.preventDefault();
    if(!validRaza()) evento.preventDefault();
    if(!validDecr()) evento.preventDefault();

})