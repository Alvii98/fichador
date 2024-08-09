alto_pantalla = window.innerHeight
borrar_log = 0
document.addEventListener('click', function (event) { 
  if (event.target.id == 'screen') fullScreen()
})
setTimeout(() => {
  if (document.querySelector('#defaultCanvas0') == null && document.querySelector('#reconocimiento_activo') != null) {
    location.reload()
  }
}, 3000)

function fullScreen() {
  console.log('Pantalla completa')
  if (alto_pantalla != window.innerHeight) {
    document.querySelector('#screen').setAttribute('class', 'bi bi-arrows-fullscreen')
    if (document.cancelFullScreen) {
      document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
      document.webkitCancelFullScreen();
    }
  }else{
    document.querySelector('#screen').setAttribute('class', 'bi bi-fullscreen-exit')
    if (document.documentElement.requestFullScreen) {
      document.documentElement.requestFullScreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullScreen) {
      document.documentElement.webkitRequestFullScreen();
    }
  }
}

document.addEventListener('DOMContentLoaded', function (event) {
  var zoomedDiv = document.getElementById('container')
  
  if (document.documentElement.clientHeight > 1000) {
    zoomedDiv.style.zoom = '160%' 
  }else if (document.documentElement.clientHeight > 552 && document.documentElement.clientHeight < 674) {
      zoomedDiv.style.zoom = '90%'
  }else if (document.documentElement.clientHeight > 500 && document.documentElement.clientHeight < 552) {
      zoomedDiv.style.zoom = '80%'
  }else if (document.documentElement.clientHeight > 440 && document.documentElement.clientHeight < 500 ) {
      zoomedDiv.style.zoom = '70%'
  }else if (document.documentElement.clientHeight < 440) {
      zoomedDiv.style.zoom = '60%'
  }
})

function mueveReloj(){
  if (window.onerror != null) location.reload()

  let horaActual = new Date(),
  hora = horaActual.getHours(),
  minuto = horaActual.getMinutes(),
  segundo = horaActual.getSeconds()

  hora = String(hora).length == 1 ? '0'+hora : hora
  minuto = String(minuto).length == 1 ? '0'+minuto : minuto
  segundo = String(segundo).length == 1 ? '0'+segundo : segundo

  document.querySelector('#hora_actual').textContent = hora+":"+minuto+":"+segundo
  
  if (hora+":"+minuto == '10:00' && borrar_log == 0) {
    borrar_log = 1
    borrar_logs()
  }
  if (hora+":"+minuto == '10:02') borrar_log = 0

  setTimeout("mueveReloj()",1000)
}

// SOLO NUMERO A LOS CAMPOS DOCUMENTO
function valideKey(evt){
    var code = (evt.which) ? evt.which : evt.keyCode;
    if(code==8) {
      return true;
    } else if(code>=48 && code<=57) {
      return true;
    } else{
      return false;
    }
}

function teclado(event) {
  const audio = new Audio('wav/teclado.mp3')
  audio.play()
  const input = document.getElementById('documento')
  if (event == 'borrar') {
    input.value = input.value.slice(0, -1)
  }else {
    if (input.value.length <= 8) {  
      input.value = input.value+event.textContent
    }
  }
  input.focus()
}

// document.addEventListener('contextmenu', function (event) {event.preventDefault()})

function borrar_logs() {
  fetch('ajax/ajax_borrar_logs.php')
  .then(response => {
    if (response.ok) console.log('Se ejecuto el borrador de logs.');
    else console.error('Error al ejecutar el borrador de logs');
  }).catch(error => {
    console.error('Error al ejecutar el borrador de logs:', error);
  })
}