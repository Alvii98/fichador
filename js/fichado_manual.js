arranca_fichar = 0
documento = ''
document.addEventListener('keyup', function (event) {
  if (arranca_fichar == 1) return
  if(event.target.id == 'documento') {
    if (event.keyCode == 13) iniciarFichado(event.target.value.trim())
  }
})

document.addEventListener('click', function (event) {
    if (arranca_fichar == 1) return
    if(event.target.id == 'enter') {
        let doc = document.getElementById('documento').value
        iniciarFichado(doc.trim())
    }
})

function iniciarFichado(doc) {
    if (doc == '') {
        alertify.error("Ingrese el documento.")
        limpiarCampos()
        return
    }
    arranca_fichar = 1
    let datosPost = new FormData()
    datosPost.append('documento', doc)
    datosPost.append('op', 1)
    
    fetch('ajax/ajax_fichar.php', {
        method: "POST",
        // Set the post data
        body: datosPost
    })
    .then(response => response.json())
    .then(function (json) {

        if (json.error == 1) {
            const audio = new Audio('wav/doc_error.wav')
            audio.play()
            alertify.error('No encontramos resultados con ese documento.')
            limpiarCampos()
            return
        }else if (json.error == 2) {
            const audio = new Audio('wav/parte_medico.wav')
            audio.play()
            alertify.error('Usted tiene un parte abierto, no puede fichar.')
            limpiarCampos()
            return
        }else if (json.error == 3) {
            const audio = new Audio('wav/no_habilitado.wav')
            audio.play()
            alertify.error('Usted no esta habilitado para fichar, consulte con superior.')
            limpiarCampos()
            return
        }else if (json.error == 4) {
            const audio = new Audio('wav/no_habilitado.wav')
            audio.play()
            alertify.error('Usted no esta habilitado para fichar.')
            limpiarCampos()
            return
        }else if (json.error == 5) {
            const audio = new Audio('wav/dos_intentos.wav')
            audio.play()
            alertify.error('Usted acaba de fichar, intente dentro de dos minutos.')
            limpiarCampos()
            return
        }
        
        let datos = json.datos[0],
        fichados = ''
        
        document.querySelector('#foto_persona').src = 'foto.php?dni='+datos.documento_nro
        document.querySelector('#ape_nom').innerHTML = datos.apellido+' '+datos.nombre
        
        datos.ultimos_fichados.forEach(element => {
        fichados += '<tr><td>'+element.cruce+'</td><td>'+element.hora_de_cruce+'</td></tr>'
        })
        if (datos.ultimos_fichados == '') {
        fichados = '<tr style="height:36px"><td></td><td></td></tr><tr style="height:36px"><td></td><td></td></tr>'
        }
        document.querySelector('#ultimos_registros').innerHTML = fichados

        if (datos.cruce == 'ENTRADA') {
        const audio = new Audio('wav/fichado_entrada.wav')
        audio.play()
        }else{
        const audio = new Audio('wav/fichado_salida.wav')
        audio.play()
        }
    
        alertify.success('Fichado correctamente.')
        setTimeout("limpiarCampos()",3000)
    }).catch(function (error){
        alertify.error('Ocurrio un error inesperado, vuelva a intentar.')
        setTimeout("location.reload()",3000)
    })
}
    
function limpiarCampos() {
    arranca_fichar = 0
    document.querySelector('#documento').value = ''
    document.querySelector('#foto_persona').src = 'img/foto.bmp'
    document.querySelector('#ape_nom').innerHTML = ''
    document.querySelector('#ultimos_registros').innerHTML = '<tr style="height:36px"><td></td><td></td></tr><tr style="height:36px"><td></td><td></td></tr>'
}





function agregarCookies(dato) {
    // console.log(document.cookie.split(';'))
    document.cookie.split(';').forEach(dat => {
        if (dat.toUpperCase().indexOf('DATO') > -1) {
        console.log(dat.split('=')[1])
        // dat.split('=').forEach(dat2 => {
        // })
        }
    })
    var expires = new Date();
    // console.log(expires)
    expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
    document.cookie = "doc="+dato+"; expires="+expires.toUTCString()+"; path=/";
    document.cookie = "fecha="+dato+"; expires="+expires.toUTCString()+"; path=/";
}
function borrarCookies() {
    document.cookie.split(";").forEach((c) => {
        document.cookie = c
        .replace(/^ +/, "")
        .replace(/=.*/, `=;expires=${new Date().toUTCString()};path=/`);
    })
}
