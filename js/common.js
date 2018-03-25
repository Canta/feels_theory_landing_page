// Funciones tomadas de radiofyl. 

//Canta's
function Canvas($container){
  $tmp = $("<canvas id=\"canvas"+Math.random()+"\"></canvas>");
  if  (typeof $container != "undefined") {
    $container.append($tmp);
  } 
  return $tmp[0];
}

//Canta's
//Extiendo el objeto ImageData para manipular pixels de manera sencilla
ImageData.prototype.setPixel = function (x, y, r, g, b, a) {
    index = (x + y * this.width) * 4;
    this.data[index+0] = r;
    this.data[index+1] = g;
    this.data[index+2] = b;
    this.data[index+3] = a;
}
