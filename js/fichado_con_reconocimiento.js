arranca_fichar = 0
tiempo_para_fichar = 0
documento = ''
videoImg = ''
capturaimg = 0
intentos = 0
tiempo_camara = 0
primeravez = 0
audioGirarCara = 0
statusGirarCara = 0
distancia = 0
distancia2 = 0
distancia_suma = 0
distancia_suma2 = 0
datos_persona = ''
document.addEventListener('DOMContentLoaded', function (event) {crearLoader();limpiarPorTiempo();})
// VER ESTADO DE LA CAMARA CUANDO INICIA 
navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
  console.log('La camara esta conectada');
  // console.clear();
}).catch(error => {
  console.log(error)
  arranca_fichar = 1
  console.log('La camara no esta conectada');
  setTimeout(() => {
    document.querySelector('#error_modal').setAttribute('style','display:block;')
    document.querySelector('#documento').setAttribute('disabled', true)
  }, 1000)
})
// CARGA LOS MODELOS DE FACE-API
Promise.all([
  faceapi.nets.faceRecognitionNet.loadFromUri('./libs/face-api/models'),
  faceapi.nets.faceLandmark68Net.loadFromUri('./libs/face-api/models'),
  faceapi.nets.ssdMobilenetv1.loadFromUri('./libs/face-api/models'),
  faceapi.nets.tinyFaceDetector.loadFromUri('./libs/face-api/models')
//   faceapi.nets.faceExpressionNet.loadFromUri('./libs/face-api/weights'),
]).then(setTimeout(() => {cargar_api_face()}, 2000))
.catch((error) => {
    location.reload()
})

document.addEventListener('keyup', function (event) {
  if (event.keyCode == 27) limpiarCampos()
  if (arranca_fichar == 1) return
  if(event.target.id == 'documento') {
    if (event.keyCode == 13) iniciarFichado(event.target.value.trim(),1)
  }
})

// op = 1 Va al reconocimiento op = 2 paso el reconocimiento y va directo a fichar
// doc = documento ingresado
function iniciarFichado(doc,op) {

  if (doc == '') {
    alertify.success("Ingrese el documento.")
    limpiarCampos()
    return
  }
  arranca_fichar = 1
  
  if (op == 1) {
    documento = doc
    const audio = new Audio('wav/comparacion_facial.mp3'),
    hora = new Date();
    audio.play()
    minutos = hora.getMinutes()
    document.querySelector('main').style.display = 'block'
    setTimeout(() => {
      tiempo_para_fichar = 1
      capturaimg = 1
      primeravez = 0
      tiempo_camara = 0
      audioGirarCara = 0
      statusGirarCara = 0
      videoImg = ''
      mensajes('Estamos haciendo el reconocimiento..')
    }, 2000)
    return
  }
  
  audio = new Audio('wav/fichado_entrada.wav')
  audio.play()

  alertify.success('Fichado correctamente.')
  guardar_log('Fichado correctamente '+documento)
  guardar_imagen(1)
  setTimeout("limpiarCampos()",2000)
}
    
function limpiarCampos() {
  arranca_fichar = 0
  tiempo_para_fichar = 0
  documento = ''
  videoImg = ''
  capturaimg = 0
  intentos = 0
  tiempo_camara = 0
  primeravez = 0
  audioGirarCara = 0
  statusGirarCara = 0
  distancia = 0
  distancia2 = 0
  mensajes('')
  datos_persona = ''
  document.querySelector('#documento').value = ''
  document.querySelector('#foto_persona').src = 'img/foto.bmp'
  document.querySelector('#ape_nom').innerHTML = ''
  document.querySelector('main').style.display = 'none'
  document.querySelector('#ultimos_registros').innerHTML = '<tr style="height:36px"><td></td><td></td></tr><tr style="height:36px"><td></td><td></td></tr>'
  document.getElementById('documento').focus()
  setTimeout(() => {
    document.querySelector('#reconocimiento_modal').style.display = 'none'
  }, 2000)
}

//################# RECONOCIMIENTO FACIAL #######################
let video
let detector
let boxes = []
function setup() {
  createCanvas(640, 480)
  video = createCapture(VIDEO)
  video.size(640, 480)
  video.hide()
  const detectionOptions = {
    withLandmarks: true,
    withExpressions: true,
    withDescriptors: false,
    maxResults: 2
  }
  detector = ml5.faceApi(video, detectionOptions, () => {
    detectFace()
  })
}

function draw() {
  image(video, 0, 0)
  drawBoxes()
}
// DETECTA LA CARA EN LA CAMARA Y VALIDO SEGUN EL ANCHO DE LA CARA LA DISTANCIA EN LA QUE ESTA
// CUANDO EL ANCHO ES ENTRE 150 Y 170 ENTRA A HACER EL RECONOCIMIENTO
function detectFace() {
  detector.detect((err, detections) => {
    if (err) return console.error(err)
    
    if (detections && detections.length > 0) {
      // caras detectadas!!
      if (document.querySelector('main').style.display == 'block' && capturaimg == 1) {
        // console.log('entro2')
        if (detections.length > 1) {

          statusGirarCara = 0
          distancia_suma = 0
          audioGirarCara = 0
          tiempo_camara = -5
          //eliminarFlechas()
          mensajes('Se detectaron mas de una cara en la imagen, vuelva a intentar.')
        }else {
          if (tiempo_camara < 0) {
            tiempo_camara++
          }else {
            for(let i = 0; i < detections.length; i++){
              let detection = detections[i].alignedRect;
                if (detection._box._width > 140) {
                  tiempo_camara = 0
                  boxes = createBoxes(detections)
                  // detectExpression(video.get(detection._box._x-100, detection._box._y-100, detection._box._width+200, detection._box._height+200).canvas.toDataURL())
                  //  PARA LA VALIDACION DESCOMENTAR EL DE ARRIBA Y PONER EN EL IF statusGirarCara = 2
                  statusGirarCara = 2
                  if (statusGirarCara == 2) {
                    videoImg = video.get(detection._box._x-100, detection._box._y-100, detection._box._width+200, detection._box._height+200).canvas.toDataURL()
                    //eliminarFlechas()
                    capturaimg = 0
                    statusGirarCara = 0
                    guardar_imagen()
                  }
              }else{
                boxes = []
              }

              tiempo_camara++
              if (tiempo_camara > 25 && detection._box._width < 140) {
                tiempo_camara = 0
                mensajes('Ac\xe9rquese un poco a la camara..')
                const audio = new Audio('wav/acercar_camara.wav')
                audio.play()
              }
            }
          }
        }
      }
    }else{
        tiempo_camara = 0
        boxes = []
    }
    // llamamos a este método continuamente
    detectFace()
  })
}

async function detectExpression(img) {
    try {
        const image1 = new Image()
        image1.src = img
        const detections = await faceapi.detectAllFaces(image1).withFaceLandmarks().withFaceDescriptors()
        
        detections.forEach((detection, i) => {
          if (detection.detection._box._width > 130 && detection.detection._box._width < 200) {
            
            const mouse = detection.landmarks.getMouth(),
            nose = detection.landmarks.getNose()

            distancia = parseInt(nose[0]._x) == 0 ? distancia : parseInt(nose[0]._x)
            distancia2 = parseInt(mouse[0]._x) == 0 ? distancia2 : parseInt(mouse[0]._x)

            if (distancia_suma == 0) distancia_suma = distancia
            if (distancia_suma2 == 0) distancia_suma2 = distancia2

            if (distancia > (distancia_suma+20) && statusGirarCara == 0) { // gira la cara hacia la izquierda
              statusGirarCara = 1
              audioGirarCara = 1
            }
            if (distancia2 > (distancia_suma2+20) && statusGirarCara == 0) { // gira la cara hacia la izquierda
              statusGirarCara = 1
              audioGirarCara = 1
            }
            if ((distancia_suma+20) > distancia && statusGirarCara == 1) { // centrar la cara
              statusGirarCara = 2
            }
            if ((distancia_suma2+20) > distancia2 && statusGirarCara == 1) { // centrar la cara
              statusGirarCara = 2
            }

          if ((audioGirarCara > 25 && statusGirarCara == 0) || audioGirarCara == 0) {
            flechas()
            const audio = new Audio('wav/gire_cara.mp3')
            audio.play()
            mensajes('Gire la cara levemente hacia su izquierda y vuelva a centrar..')
            audioGirarCara = 1
          }else if ((audioGirarCara > 25 && statusGirarCara == 1) || audioGirarCara == 1) {
            eliminarFlechas()
            const audio = new Audio('wav/centre_cara.mp3')
            audio.play()
            mensajes('Aleje y centre la cara por favor..')
            audioGirarCara = 1
          }
          audioGirarCara++
        }
        })
    } catch (error) {
        console.log(error)   
    }
}

// CREA LOS PUNTOS DONDE DETECTA LA CARA 
function createBoxes(detections) {
  boxes = []
  for(let i = 0; i < detections.length; i++){
    let detection = detections[i].alignedRect;
    if (detection._box._width > 180) {
      const box = {
        x: detection._box._x,
        y: detection._box._y,
        width: detection._box._width,
        height: detection._box._height,
        label: ""
      }
      boxes.push(box)
    }
  }
  return boxes
}
// PARA DIBUJAR/ESCRIBIR SOBRE LA IMAGEN DE LA CAMARA
function drawBoxes() {
  // Encuadra cara
  if (boxes.length > 0) {
    for (let i=0; i < boxes.length; i++) {
      const box = boxes[i]
      noFill()
      stroke(0, 255, 34)
      strokeWeight(4)
      rect(box.x, box.y, box.width, box.height)
    }
  }
}

// PARA HACER EL RECONOCIMIENTO FACIAL DE LAS IMAGENES GUARDADAS
async function reconocimiento_facial(foto) {

    const image1 = new Image()
    image1.src = videoImg
    const image2 = new Image()
    image2.src = foto == 'sinfoto' ? videoImg : 'data:image/png;base64,'+foto

    document.querySelector('#foto_reconocimiento').src = foto == 'sinfoto' ? videoImg : 'data:image/png;base64,'+foto
    document.querySelector('#foto_reconocimiento2').src = videoImg
    document.querySelector('#reconocimiento_modal').style.display = 'block'
    document.querySelector('main').style.display = 'none'

    // Detectar los rostros en las imagenes
    const detections1 = await faceapi.detectAllFaces(image1).withFaceLandmarks().withFaceDescriptors();
    const detections2 = await faceapi.detectAllFaces(image2).withFaceLandmarks().withFaceDescriptors();
    if (detections1.length == 0 || detections2.length == 0) {
        document.querySelector('#reconocimiento_modal').style.display = 'none'
        sin_coincidencias()
        return
    }
    // Comparar los descriptores de los rostros
    const faceMatcher = new faceapi.FaceMatcher(detections1);
    const results = detections2.map(descriptor =>
        faceMatcher.findBestMatch(descriptor.descriptor)
    )
    // Mostrar los resultados
    results.forEach((result, i) => {
        if (result.label == 'person 1') {
            document.querySelector('#reconocimiento_modal').style.display = 'none'
            iniciarFichado(documento,2)
        }else{
            sin_coincidencias()
        }
    })
}

// PARA GUARDAR LA IMAGEN CAPTURADA Y LUEGO HACER EL RECONOCIMIENTO 
function guardar_imagen(op = 0) {

    let datosPost = new FormData()
    datosPost.append('documento', documento)

    if (op == 1) {
      datosPost.append('foto_camara', videoImg)
    }
    fetch('ajax/ajax_capturar_imagenes.php', {
        method: "POST",
        // Set the post data
        body: datosPost
    })
    .then(response => response.json())
    .then(function (json) {
      if (json.foto_camara == 1) {
        console.log('Foto guardada.')
        return false
      }
      if (json.resp == 1) {
        reconocimiento_facial(json.foto)
      }else {
        guardar_log('Fichado pero revisar si tiene foto '+documento)
        reconocimiento_facial('sinfoto')
        // document.querySelector('#reconocimiento_modal').style.display = 'none'
        // document.querySelector('#reconocimiento_modal').style.display = 'block'
        // sin_coincidencias()
      }
    }).catch(function (error){
      console.log(error)
      document.querySelector('#reconocimiento_modal').style.display = 'block'
      document.querySelector('#reconocimiento_modal').style.display = 'none'
      sin_coincidencias()
    })
}

function sin_coincidencias() {
  guardar_log('Intento de fichado sin coincidencias '+documento)
  arranca_fichar = 0
  document.querySelector('main').style.display = 'none'
  const audio = new Audio('wav/error_coincidencia.wav')
  audio.play()
  alertify.error("No encontramos coincidencias, vuelva a intentar porfavor.")
  setTimeout("limpiarCampos()",2000)
}

// LOG PARA TENER UN REGISTRO
function guardar_log(msj) {
  let datosPost = new FormData()
  datosPost.append('msj', msj)

  fetch('ajax/ajax_log.php', {
    method: "POST",
    // Set the post data
    body: datosPost
  })
  .then(response => response.json())
  .then(function (json) {
    // console.log(json)
  }) 
}

function crearLoader() {
  const div_loader = document.createElement('div'),
  loader = document.createElement('div')

  document.getElementById('documento').setAttribute('disabled', true)
  div_loader.setAttribute('style', 'position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);')
  div_loader.id = 'loader_inicio'
  loader.classList.add('loader')

  div_loader.appendChild(loader)
  document.body.appendChild(div_loader)
}

function eliminarLoader() {  
  const loader = document.querySelector('#loader_inicio')
  document.getElementById('documento').disabled = false
  document.body.removeChild(loader)
}


// PRUEBA DE API AL CARGAR EN INICIO
async function cargar_api_face() {
  console.log('Modelos cargados.')
  let foto_guardada = 'img/alvaro.jpg'
  const image1 = new Image()
  image1.src = foto_guardada
  const image2 = new Image()
  image2.src = foto_guardada

  // Detectar los rostros en las imagenes
  const detections1 = await faceapi.detectAllFaces(image1).withFaceLandmarks().withFaceDescriptors();
  const detections2 = await faceapi.detectAllFaces(image2).withFaceLandmarks().withFaceDescriptors();

  if (detections1.length == 0 || detections2.length == 0) {
    eliminarLoader()
    return
  }
  // Comparar los descriptores de los rostros
  const faceMatcher = new faceapi.FaceMatcher(detections1);
  const results = detections2.map(descriptor =>
      faceMatcher.findBestMatch(descriptor.descriptor)
  )
  // Mostrar los resultados
  results.forEach((result, i) => {
    eliminarLoader()
  })
}

function flechas() {
  const main = document.querySelector('main'),
  flechas = document.getElementById('flechas')
  if (main && !flechas) { 
    main.insertAdjacentHTML("beforeend",`<div id="flechas">
    <div class="flecha-left"></div>
    <div class="flecha-left"></div>
    <div class="flecha-left"></div>      
    </div>`)
  }
}

function eliminarFlechas() {
  const flechas = document.getElementById('flechas')
  if (flechas) {
    flechas.remove()
  }
}


function mensajes(msj) {
  const main = document.querySelector('main'),
  mensaje = document.getElementById('mensajes')
  if (main && !mensaje) { 
    main.insertAdjacentHTML("beforeend",'<h4 id="mensajes">'+msj+'</h4>')
  }else{
    mensaje.innerText = msj
  }
}

function recuadro() {
  const main = document.querySelector('main'),
  recuadro = document.getElementById('recuadro')
  
  if (main && !recuadro && window.innerHeight > 1200) { 
    main.insertAdjacentHTML("beforeend",`<div id="recuadro"><div class="recuadro"></div><div class="recuadro2"></div>`)
  }
}

function limpiarPorTiempo(){
  if (tiempo_para_fichar > 0) {
    tiempo_para_fichar++
    if (tiempo_para_fichar > 60) {
      tiempo_para_fichar = 0
      sin_coincidencias()
    }
  }
  setTimeout("limpiarPorTiempo()",1000)
}