<?php
  //Detección de browser, para poder bannear a Internet Explorer.
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  if (
    (preg_match('/MSIE/i',$user_agent) && !preg_match('/Opera/i',$user_agent))
    ||
    (preg_match('/EDGE/i',$user_agent) && !preg_match('/Opera/i',$user_agent))
  )
  {
    //El usuario usa Internet Explorer.
    //Esto es intolerable.
    header ("Location: ./noie.html"); 
    die("");
  }
?>
<html>
  <head>
    <meta charset="utf-8">
    <style>
      
      @font-face {
        font-family:"Aroania";
        src:  url('./css/Aroania_R.woff') format('woff'),
            url('./css/Aroania_R.ttf') format('truetype'),
            url('./css/Aroania_R.svg#Aroania') format('svg');
        font-weight: normal;
        font-style: normal;
      }

      @font-face {
        font-family:"Ubuntu";
        src:  url('css/Ubuntu-Bold.woff') format('woff'),
            url('css/Ubuntu-Bold.ttf') format('truetype'),
            url('css/Ubuntu-Bold.svg#Ubuntu-Bold') format('svg');
        font-weight: normal;
        font-style: normal;
      }
      
      .total {
        position: absolute;
        left:     0px;
        top:      0px;
        margin:   0px;
        padding:  0px;
        width:    100%;
        height:   100%;
      }
      
      body {
        z-index:  1;
        background-color:#000000; 
        color:#ffffff;
        margin:0 auto;
        text-align:center;
        vertical-align: center;
        font-family: "Aroania";
        overflow:hidden;
      }
      
      canvas {
        position: absolute;
        left:     0px;
        top:      0px;
        z-index:  1;
      }
      
      #glcanvas {
        width:100%;
        height: 100%;
      }
      
      #loading-image {
        z-index: 100;
        cursor: wait;
      }
      
      #titulo{
        font-family: "Ubuntu";
        font-size: 10vw;
        -moz-text-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        -webkit-text-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        text-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        margin-top: 0.3vw; 
        margin-bottom: 0.3vw;
        display: block;
        position: absolute;
        left: 0px;
        top: 0px;
        height: 25%;
        width:100%;
        overflow:visible;
        text-align:center;
      }
      
      #subtitulo{
        font-family: "Aroania";
        font-size: 2vw;
        display: block;
        width: 100%;
        bottom: 0px;
        height: 25%;
        margin: auto;
        left: 0px;
        position: absolute;
        -moz-text-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        -webkit-text-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        text-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
      }
      
      #main-container {
        display : none;
        z-index : 2;
      }
      
      #opciones {
        position:absolute;
        z-index:2;
        width:100%;
        height:50%;
        top:25%;
        margin: 0px;
        padding:0px;
        text-align:center;
      }
      
      .opcion-grande {
        border            : white solid 0.2em;
        border-radius     : 50%;
        -moz-box-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        -webkit-box-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        box-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        margin            : auto;
        display           : inline-block;
        position          : relative;
        float             : middle;
        background-color  : rgba(255,255,255,0.6);
        color             : white;
        font-size         : 3vw;
        cursor            : pointer;
        height            : 10vw;
        width             : 10vw;
        top               : 25%;
        opacity           : 0.5;
        background-repeat:no-repeat;
        background-attachment:unset;
        background-position:center;
        background-size: 50%;
      }
      
      .opcion-grande:hover ,
      .opcion-grande:focus {
        opacity:  1;
        background-size: 85%;
      }
      
      .opcion-grande > img {
        position: absolute;
        width   : 80%;
        height  : 80%;
        left    : 5%;
        top     : 5%;
        cursor  : pointer;
        display : inline-block;
        margin  : auto;
      }
      
      .seccion {
        /*transition: opacity 0.5s ease-in-out;*/
        z-index: 2;
        display:none;
      }
      
      .out { 
        opacity: 0; 
      }
      .in { opacity: 1; }
      
      .cuerpo-seccion {
        position    : absolute;
        width       : 80%;
        left        : 10%;
        top         : 5%;
        height      : 80%;
        overflow    : auto;
        color       : black;
        text-align  : justify;
        
        font-family : sans, verdana, helvetica;
        font-size   : 2vw;
        background-color  : rgba(255,255,255,0.7);
        border-radius     : 25px;
        border            : white solid 0.2em;
        -moz-box-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        -webkit-box-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
        box-shadow: 1px 1px 2px white, 0 0 1em white, 0 0 0.2em white;
      }
      
      .cuerpo-seccion button{
        width: 15vw;
        font-size: 2vw;
        border-radius: 25px;
      }
      
      .cuerpo-seccion > p {
        position: relative;
        margin-left: 5%;
        margin-right: 5%;
        width: 90%;
      }
      
      .cuerpo-seccion  img {
        position: relative;
        margin: auto;
        max-width: 90%;
      }
    </style>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
  </head>
  <body class="total" onresize="resize">
    <img src="images/loading.png" id="loading-image" class="total loading-image"/>
    <script id="vertex-shader-webgl1" type="x-shader/x-vertex">
      attribute vec4 Vertex;
      void main()
      {
          gl_Position = Vertex;
      }
    </script>
    <script id="fragment-shader-webgl1" type="x-shader/x-fragment">
      precision highp float;
      precision highp int;
      
      uniform sampler2D iChannel0;
      uniform sampler2D iChannel1;
      uniform vec3  iResolution;
      uniform int   iFrame;
      uniform float iTime;
      uniform float iTimeDelta;
      
      // Canta's polyfills
      
        int mod(int a, int b) 
        {
          return int(a - b * (a / b));
        }
      
      vec4 getRand(vec2 pos)
      {
          //vec2 rres=vec2(textureSize(iChannel1,0));
          vec2 rres=vec2(iResolution.xy);
          //return textureLod(iChannel1,pos/rres,0.);
          return vec4(0.,0.,0.,0.);
      }

      vec4 getRand(int idx)
      {
          //ivec2 rres=textureSize(iChannel1,0);
          ivec2 rres=ivec2(iResolution.xy);
          //idx=idx%(rres.x*rres.y);
          idx = mod(idx , int(rres.x*rres.y));
          //return texelFetch(iChannel1,ivec2(idx%rres.x,idx/rres.x),0);
          return texture2D(iChannel1, vec2(float(mod(idx,int(rres.x))),float(idx/rres.x)));
      }


      // change these values to 0.0 to turn off individual effects
      float vertJerkOpt = 1.0;
      float vertMovementOpt = 1.0;
      float bottomStaticOpt = 1.0;
      float scalinesOpt = 1.0;
      float rgbOffsetOpt = 1.0;
      float horzFuzzOpt = 1.0;

      // Noise generation functions borrowed from: 
      // https://github.com/ashima/webgl-noise/blob/master/src/noise2D.glsl

      vec3 mod289(vec3 x) {
        return x - floor(x * (1.0 / 289.0)) * 289.0;
      }

      vec2 mod289(vec2 x) {
        return x - floor(x * (1.0 / 289.0)) * 289.0;
      }

      vec3 permute(vec3 x) {
        return mod289(((x*34.0)+1.0)*x);
      }

      float snoise(vec2 v)
        {
        const vec4 C = vec4(0.211324865405187,  // (3.0-sqrt(3.0))/6.0
                            0.366025403784439,  // 0.5*(sqrt(3.0)-1.0)
                           -0.577350269189626,  // -1.0 + 2.0 * C.x
                            0.024390243902439); // 1.0 / 41.0
      // First corner
        vec2 i  = floor(v + dot(v, C.yy) );
        vec2 x0 = v -   i + dot(i, C.xx);

      // Other corners
        vec2 i1;
        //i1.x = step( x0.y, x0.x ); // x0.x > x0.y ? 1.0 : 0.0
        //i1.y = 1.0 - i1.x;
        i1 = (x0.x > x0.y) ? vec2(1.0, 0.0) : vec2(0.0, 1.0);
        // x0 = x0 - 0.0 + 0.0 * C.xx ;
        // x1 = x0 - i1 + 1.0 * C.xx ;
        // x2 = x0 - 1.0 + 2.0 * C.xx ;
        vec4 x12 = x0.xyxy + C.xxzz;
        x12.xy -= i1;

      // Permutations
        i = mod289(i); // Avoid truncation effects in permutation
        vec3 p = permute( permute( i.y + vec3(0.0, i1.y, 1.0 ))
          + i.x + vec3(0.0, i1.x, 1.0 ));

        vec3 m = max(0.5 - vec3(dot(x0,x0), dot(x12.xy,x12.xy), dot(x12.zw,x12.zw)), 0.0);
        m = m*m ;
        m = m*m ;

      // Gradients: 41 points uniformly over a line, mapped onto a diamond.
      // The ring size 17*17 = 289 is close to a multiple of 41 (41*7 = 287)

        vec3 x = 2.0 * fract(p * C.www) - 1.0;
        vec3 h = abs(x) - 0.5;
        vec3 ox = floor(x + 0.5);
        vec3 a0 = x - ox;

      // Normalise gradients implicitly by scaling m
      // Approximation of: m *= inversesqrt( a0*a0 + h*h );
        m *= 1.79284291400159 - 0.85373472095314 * ( a0*a0 + h*h );

      // Compute final noise value at P
        vec3 g;
        g.x  = a0.x  * x0.x  + h.x  * x0.y;
        g.yz = a0.yz * x12.xz + h.yz * x12.yw;
        return 130.0 * dot(m, g);
      }

      float staticV(vec2 uv) {
          float staticHeight = snoise(vec2(9.0,iTime*1.2+3.0))*0.3+5.0;
          float staticAmount = snoise(vec2(1.0,iTime*1.2-6.0))*0.1+0.3;
          float staticStrength = snoise(vec2(-9.75,iTime*0.6-3.0))*2.0+2.0;
        return (1.0-step(snoise(vec2(5.0*pow(iTime,2.0)+pow(uv.x*7.0,1.2),pow((mod(iTime,100.0)+100.0)*uv.y*0.3+3.0,staticHeight))),staticAmount))*staticStrength;
      }


      vec4 mainImage4( vec4 fragColor, in vec2 fragCoord )
      {

        vec2 uv =  fragCoord.xy/iResolution.xy;
        
        float jerkOffset = (1.0-step(snoise(vec2(iTime*1.3,5.0)),0.8))*0.05;
        
        float fuzzOffset = snoise(vec2(iTime*15.0,uv.y*80.0))*0.003;
        float largeFuzzOffset = snoise(vec2(iTime*1.0,uv.y*25.0))*0.004;
          
          float vertMovementOn = (1.0-step(snoise(vec2(iTime*0.2,8.0)),0.4))*vertMovementOpt;
          float vertJerk = (1.0-step(snoise(vec2(iTime*1.5,5.0)),0.6))*vertJerkOpt;
          float vertJerk2 = (1.0-step(snoise(vec2(iTime*5.5,5.0)),0.2))*vertJerkOpt;
          float yOffset = abs(sin(iTime)*4.0)*vertMovementOn+vertJerk*vertJerk2*0.3;
          float y = mod(uv.y+yOffset,1.0);
          
        
        float xOffset = (fuzzOffset + largeFuzzOffset) * horzFuzzOpt;
          
          float staticVal = 0.0;
         
          for (float y = -1.0; y <= 1.0; y += 1.0) {
              float maxDist = 5.0/200.0;
              float dist = y/200.0;
            staticVal += staticV(vec2(uv.x,uv.y+dist))*(maxDist-abs(dist))*1.5;
          }
              
          staticVal *= bottomStaticOpt;
        
        float red   =   texture2D(  iChannel0,  vec2(uv.x + xOffset -0.01*rgbOffsetOpt,y)).r+staticVal;
        float green =   texture2D(  iChannel0,  vec2(uv.x + xOffset, y)).g+staticVal;
        float blue  =   texture2D(  iChannel0,  vec2(uv.x + xOffset +0.01*rgbOffsetOpt,y)).b+staticVal;
        
        float time = floor(iTime * 16.0) / 16.0;
        vec3 color = vec3(red,green,blue) ;
        float scanline = sin(uv.y*800.0)* 0.1 *scalinesOpt;
        color -= scanline;
        
        vec2 pos = ( fragCoord.xy / iResolution.xy );
        color *= sin(pos.x*3.15);
        color *= sin(pos.y*3.);
        color *= .9;
        
        fragColor = vec4(color,1.0);
        return fragColor;
      }

      //out vec4 myFragColor;
      void main()
      {
          //myFragColor = mainImage2(myFragColor,gl_FragCoord.xy);
          //myFragColor = mainImage3(myFragColor,gl_FragCoord.xy);
          //myFragColor = vec4(myFragColor.xyz, 1.);
          gl_FragColor = mainImage4(gl_FragColor,gl_FragCoord.xy);
          //myFragColor = mainImage5(myFragColor,gl_FragCoord.xy);
          //myFragColor = vec4(myFragColor.xy, getRand(int(myFragColor.x)));
          //mainImage(myFragColor,gl_FragCoord.xy);
          //myFragColor = texture(iChannel0,gl_FragCoord.xy/iResolution.xy);
      }
    </script>
    <script id="vertex-shader-webgl2" type="x-shader/x-vertex">#version 300 es
      precision mediump sampler3D;
      in vec4 Vertex;
      void main()
      {
          gl_Position = Vertex;
      }
    </script>
    <script id="fragment-shader-webgl2" type="x-shader/x-fragment">#version 300 es
      precision mediump sampler3D;
      precision highp float;
      precision highp int;
      
      uniform sampler2D iChannel0;
      uniform sampler2D iChannel1;
      uniform vec3  iResolution;
      uniform int   iFrame;
      uniform float iTime;
      uniform float iTimeDelta;
      
      vec4 getRand(vec2 pos)
      {
          vec2 rres=vec2(textureSize(iChannel1,0));
          return textureLod(iChannel1,pos/rres,0.);
      }
      vec4 getRand(int idx)
      {
          ivec2 rres=textureSize(iChannel1,0);
          idx=idx%(rres.x*rres.y);
          return texelFetch(iChannel1,ivec2(idx%rres.x,idx/rres.x),0);
      }
      // change these values to 0.0 to turn off individual effects
      float vertJerkOpt = 1.0;
      float vertMovementOpt = 1.0;
      float bottomStaticOpt = 1.0;
      float scalinesOpt = 1.0;
      float rgbOffsetOpt = 1.0;
      float horzFuzzOpt = 1.0;
      // Noise generation functions borrowed from: 
      // https://github.com/ashima/webgl-noise/blob/master/src/noise2D.glsl
      vec3 mod289(vec3 x) {
        return x - floor(x * (1.0 / 289.0)) * 289.0;
      }
      vec2 mod289(vec2 x) {
        return x - floor(x * (1.0 / 289.0)) * 289.0;
      }
      vec3 permute(vec3 x) {
        return mod289(((x*34.0)+1.0)*x);
      }
      float snoise(vec2 v)
        {
        const vec4 C = vec4(0.211324865405187,  // (3.0-sqrt(3.0))/6.0
                            0.366025403784439,  // 0.5*(sqrt(3.0)-1.0)
                           -0.577350269189626,  // -1.0 + 2.0 * C.x
                            0.024390243902439); // 1.0 / 41.0
      // First corner
        vec2 i  = floor(v + dot(v, C.yy) );
        vec2 x0 = v -   i + dot(i, C.xx);
      // Other corners
        vec2 i1;
        //i1.x = step( x0.y, x0.x ); // x0.x > x0.y ? 1.0 : 0.0
        //i1.y = 1.0 - i1.x;
        i1 = (x0.x > x0.y) ? vec2(1.0, 0.0) : vec2(0.0, 1.0);
        // x0 = x0 - 0.0 + 0.0 * C.xx ;
        // x1 = x0 - i1 + 1.0 * C.xx ;
        // x2 = x0 - 1.0 + 2.0 * C.xx ;
        vec4 x12 = x0.xyxy + C.xxzz;
        x12.xy -= i1;
      // Permutations
        i = mod289(i); // Avoid truncation effects in permutation
        vec3 p = permute( permute( i.y + vec3(0.0, i1.y, 1.0 ))
          + i.x + vec3(0.0, i1.x, 1.0 ));
        vec3 m = max(0.5 - vec3(dot(x0,x0), dot(x12.xy,x12.xy), dot(x12.zw,x12.zw)), 0.0);
        m = m*m ;
        m = m*m ;
      // Gradients: 41 points uniformly over a line, mapped onto a diamond.
      // The ring size 17*17 = 289 is close to a multiple of 41 (41*7 = 287)
        vec3 x = 2.0 * fract(p * C.www) - 1.0;
        vec3 h = abs(x) - 0.5;
        vec3 ox = floor(x + 0.5);
        vec3 a0 = x - ox;
      // Normalise gradients implicitly by scaling m
      // Approximation of: m *= inversesqrt( a0*a0 + h*h );
        m *= 1.79284291400159 - 0.85373472095314 * ( a0*a0 + h*h );
      // Compute final noise value at P
        vec3 g;
        g.x  = a0.x  * x0.x  + h.x  * x0.y;
        g.yz = a0.yz * x12.xz + h.yz * x12.yw;
        return 130.0 * dot(m, g);
      }
      float staticV(vec2 uv) {
          float staticHeight = snoise(vec2(9.0,iTime*1.2+3.0))*0.3+5.0;
          float staticAmount = snoise(vec2(1.0,iTime*1.2-6.0))*0.1+0.3;
          float staticStrength = snoise(vec2(-9.75,iTime*0.6-3.0))*2.0+2.0;
        return (1.0-step(snoise(vec2(5.0*pow(iTime,2.0)+pow(uv.x*7.0,1.2),pow((mod(iTime,100.0)+100.0)*uv.y*0.3+3.0,staticHeight))),staticAmount))*staticStrength;
      }
      vec4 mainImage4( vec4 fragColor, in vec2 fragCoord )
      {
        vec2 uv =  fragCoord.xy/iResolution.xy;
        
        float jerkOffset = (1.0-step(snoise(vec2(iTime*1.3,5.0)),0.8))*0.05;
        
        float fuzzOffset = snoise(vec2(iTime*15.0,uv.y*80.0))*0.003;
        float largeFuzzOffset = snoise(vec2(iTime*1.0,uv.y*25.0))*0.004;
          
          float vertMovementOn = (1.0-step(snoise(vec2(iTime*0.2,8.0)),0.4))*vertMovementOpt;
          float vertJerk = (1.0-step(snoise(vec2(iTime*1.5,5.0)),0.6))*vertJerkOpt;
          float vertJerk2 = (1.0-step(snoise(vec2(iTime*5.5,5.0)),0.2))*vertJerkOpt;
          float yOffset = abs(sin(iTime)*4.0)*vertMovementOn+vertJerk*vertJerk2*0.3;
          float y = mod(uv.y+yOffset,1.0);
          
        
        float xOffset = (fuzzOffset + largeFuzzOffset) * horzFuzzOpt;
          
          float staticVal = 0.0;
         
          for (float y = -1.0; y <= 1.0; y += 1.0) {
              float maxDist = 5.0/200.0;
              float dist = y/200.0;
            staticVal += staticV(vec2(uv.x,uv.y+dist))*(maxDist-abs(dist))*1.5;
          }
              
          staticVal *= bottomStaticOpt;
        
        float red 	=   texture(	iChannel0, 	vec2(uv.x + xOffset -0.01*rgbOffsetOpt,y)).r+staticVal;
        float green = 	texture(	iChannel0, 	vec2(uv.x + xOffset,	  y)).g+staticVal;
        float blue 	=	texture(	iChannel0, 	vec2(uv.x + xOffset +0.01*rgbOffsetOpt,y)).b+staticVal;
        
        float time = floor(iTime * 16.0) / 16.0;
        vec3 color = vec3(red,green,blue) ;
        float scanline = sin(uv.y*800.0)* 0.1 *scalinesOpt;
        color -= scanline;
        
        vec2 pos = ( fragCoord.xy / iResolution.xy );
        color *= sin(pos.x*3.15);
        color *= sin(pos.y*3.);
        color *= .9;
        
        fragColor = vec4(color,1.0);
        return fragColor;
      }
      out vec4 myFragColor;
      void main()
      {
          //myFragColor = mainImage2(myFragColor,gl_FragCoord.xy);
          //myFragColor = mainImage3(myFragColor,gl_FragCoord.xy);
          //myFragColor = vec4(myFragColor.xyz, 1.);
          myFragColor = mainImage4(myFragColor,gl_FragCoord.xy);
          //myFragColor = mainImage5(myFragColor,gl_FragCoord.xy);
          //myFragColor = vec4(myFragColor.xy, getRand(int(myFragColor.x)));
          //mainImage(myFragColor,gl_FragCoord.xy);
          //myFragColor = texture(iChannel0,gl_FragCoord.xy/iResolution.xy);
      }
    </script>
    <canvas id="glcanvas">
      Oh no! Your browser doesn't support canvas!
    </canvas>
    <script type="text/javascript">
      gl                    = null;
      glCanvas              = null;
      let subtitulo_default = "Desde la posverdad, hacia la sentimentalidad artificial.";
      
      <?php
        $imagenes = glob("images/???.???");
        echo "let imagenes = [";
        foreach ($imagenes as $img) {
          echo "\"" . $img . "\",";
        }
        echo "];\n"
      ?>
      
      let aspectRatio;
      let previousTime  = 0.0;
      let frame         = 0;
      
      window.addEventListener("load", function() { setTimeout(startup, 3000) }, false);

      function resize() {
        glCanvas        = document.getElementById("glcanvas");
        glCanvas.width  = glCanvas.offsetWidth;
        glCanvas.height = glCanvas.offsetHeight;
      }
      
      function startup() {
        resize();
        gl = null;
        
        let contexts = [
          { name: "webgl2", meta: "webgl2"},
          { name: "experimental-webgl2", meta: "webgl2"},
          { name: "webgl", meta: "webgl1"},
          { name: "experimental-webgl", meta: "webgl1"}
        ];
        
        let cont = null;
        for (let i = 0; i < contexts.length; i++) {
          cont = contexts[i];
          try{
            gl = glCanvas.getContext(cont.name);
            if ( gl !== null) {
              break;
            }
          } catch (e) {
            console.log("No hay contexto " + cont.name + " en este dispositivo.");
          }
        }
      
        if (gl !== null) {
          console.log("Contexto para los shaders: ", cont);
          shaderProgram = initShaderProgram(
            gl, 
            document.getElementById("vertex-shader-" + cont.meta).innerHTML, 
            document.getElementById("fragment-shader-" + cont.meta).innerHTML
          );
          
          programas = [
            {
              programa    : shaderProgram,
              context     : gl,
              canvas      : glCanvas,
              iResolution : gl.getUniformLocation(shaderProgram, "iResolution"),
              iTime       : gl.getUniformLocation(shaderProgram, "iTime"),
              iTimeDelta  : gl.getUniformLocation(shaderProgram, "iTimeDelta"),
              iChannel0   : gl.getUniformLocation(shaderProgram, "iChannel0"),
              iChannel1   : gl.getUniformLocation(shaderProgram, "iChannel1"),
              fragColor   : gl.getUniformLocation(shaderProgram, "myFragColor"),
              iFrame      : gl.getUniformLocation(shaderProgram, "iFrame")
            },
          ];
          
          gl.useProgram(shaderProgram);
          
          texture       = loadTexture(gl, 'images/black_square.gif', true);
          noise         = loadTexture(gl, 'images/noise.jpg', true);
          framebuffer   = gl.createFramebuffer();
          renderbuffer  = gl.createRenderbuffer();
          
          
          // Vinculo la textura a un framebuffer
          gl.activeTexture(gl.TEXTURE0);
          gl.bindTexture( gl.TEXTURE_2D, texture );
          gl.bindFramebuffer( gl.FRAMEBUFFER, framebuffer );
          gl.framebufferTexture2D( gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, texture, 0 );
          // Configuro el renderbuffer
          gl.bindRenderbuffer( gl.RENDERBUFFER, renderbuffer );
          gl.renderbufferStorage( gl.RENDERBUFFER, gl.DEPTH_COMPONENT16, glCanvas.width, glCanvas.height );
          gl.framebufferRenderbuffer( gl.FRAMEBUFFER, gl.DEPTH_ATTACHMENT, gl.RENDERBUFFER, renderbuffer );
          // Y limpio
          gl.bindTexture( gl.TEXTURE_2D, null );
          gl.bindRenderbuffer( gl.RENDERBUFFER, null );
          gl.bindFramebuffer( gl.FRAMEBUFFER, null);
          
          
          // quad vbuffer
          vertBuffer = gl.createBuffer();
          gl.bindBuffer(gl.ARRAY_BUFFER, vertBuffer);
          vertices = [
            -1.0, -1.0,  0.0, 1.0,
             1.0, -1.0,  0.0, 1.0,
            -1.0,  1.0,  0.0, 1.0,
             1.0, -1.0,  0.0, 1.0,
             1.0,  1.0,  0.0, 1.0,
            -1.0,  1.0,  0.0, 1.0
          ];
          gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertices), gl.STATIC_DRAW);
          
          gl.activeTexture(gl.TEXTURE0);
          gl.bindTexture(gl.TEXTURE_2D, texture);
          gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
          gl.activeTexture(gl.TEXTURE1);
          gl.bindTexture(gl.TEXTURE_2D, noise);
          
          gl.uniform1i(gl.getUniformLocation(shaderProgram, "iChannel0"), 0);
          gl.uniform1i(gl.getUniformLocation(shaderProgram, "iChannel1"), 1);
          gl.uniform4fv(gl.getUniformLocation(shaderProgram, "myFragColor"), [0.1, 0.7, 0.2, 1.0]);
          
          aspectRatio = glCanvas.width/glCanvas.height;
          actTime     = 0.0;
          t0          = Date.now()/1000.0;
          
        }
        document.getElementById("loading-image").style.display = "none";
        setTimeout(showUI, 1000);
        
        animateScene();
      }
      
      function change_image(){
        gl.useProgram(shaderProgram);
        
        texture = loadRandomTexture(gl);
        
        // Vinculo la textura al framebuffer
        gl.activeTexture(gl.TEXTURE0);
        gl.bindTexture( gl.TEXTURE_2D, texture );
        gl.bindFramebuffer( gl.FRAMEBUFFER, framebuffer );
        gl.framebufferTexture2D( gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, texture, 0 );
        gl.activeTexture(gl.TEXTURE0);
        gl.bindTexture(gl.TEXTURE_2D, texture);
        gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
        gl.activeTexture(gl.TEXTURE1);
        gl.bindTexture(gl.TEXTURE_2D, noise);
        
        gl.uniform1i(gl.getUniformLocation(shaderProgram, "iChannel0"), 0);
        gl.uniform1i(gl.getUniformLocation(shaderProgram, "iChannel1"), 1);
        gl.uniform4fv(gl.getUniformLocation(shaderProgram, "myFragColor"), [0.1, 0.7, 0.2, 1.0]);
        
        gl.bindTexture( gl.TEXTURE_2D, null );
        gl.bindRenderbuffer( gl.RENDERBUFFER, null );
        gl.bindFramebuffer( gl.FRAMEBUFFER, null);
      }
      
      function showUI() {
        document.body.style.cursor = "default";
        if (gl !== null ) {
          document.getElementById("main-container").style.display = "inline-block";
          window.image_timer = setInterval(change_image,5000);
        }
        let tmpfnc = function() {
          document.querySelectorAll(".opcion-grande")
          .forEach(
            item => {
              item.addEventListener("mouseover"   , subtitulo_handler);
              item.addEventListener("touchstart"  , subtitulo_handler);
              item.addEventListener("mouseout"    , _ => subtitulo_handler());
              item.addEventListener("touchcancel" , _ => subtitulo_handler());
              item.addEventListener("touchleave"  , _ => subtitulo_handler());
              item.addEventListener("click"       , seccion_handler);
            });
        };
        setTimeout( tmpfnc , 100);
      }
      
      function initShaderProgram(gl, vsSource, fsSource) {
        const fragmentShader = loadShader(gl, gl.FRAGMENT_SHADER, fsSource);
        const vertexShader = loadShader(gl, gl.VERTEX_SHADER, vsSource);

        // Create the shader program

        let shaderProgram = gl.createProgram();
        gl.attachShader(shaderProgram, vertexShader);
        gl.attachShader(shaderProgram, fragmentShader);
        gl.linkProgram(shaderProgram);

        // If creating the shader program failed, alert

        if (!gl.getProgramParameter(shaderProgram, gl.LINK_STATUS)) {
          console.log('Unable to initialize the shader program: ' + gl.getProgramInfoLog(shaderProgram));
          return null;
        }

        return shaderProgram;
      }
      
      function no_tenes_webgl() {
        if (glCanvas.getContext) {
          resize();
          var ctx = glCanvas.getContext('2d');
          var img = new Image(); 
          img.addEventListener('load', function() {
            ctx.drawImage(img, 0, 0, glCanvas.width, glCanvas.height);
            setTimeout( function() {
              ctx.fillStyle = "rgba(0, 0, 0, 0.6)";
              ctx.fillRect(0, 0, glCanvas.width,glCanvas.height);
              $(".seccion[id!='no-tenes-webgl-container']").fadeOut(500,
                _ => $("#no-tenes-webgl-container").fadeIn(500)
              );
            }, 1000);
            
          }, false);
          img.src = 'images/compu-vieja-reparando.jpg';
        }
        
      }
      
      function animateScene( ) {
        if (gl === null) {
          no_tenes_webgl();
          return;
        }
        gl.viewport(0.0,0.0, glCanvas.width, glCanvas.height);
        gl.clearColor(0.0,0.5,0.5,1.0);     // Set clear color 
        
        //gl.clearDepth(1.0);                 // Clear everything
        //gl.enable(gl.DEPTH_TEST);           // Enable depth testing
        //gl.depthMask(1);                    // enable depth write
        gl.depthFunc(gl.LEQUAL);            // Near things obscure far things
        gl.disable(gl.BLEND);               // disable blending
        
        frame++;
        var newTime       = Date.now()/1000.0-t0;
        var actTimeDelta  = (actTime==0.0)?0.0:newTime-actTime;
        actTime           = newTime;
        
        // quad vbuffer
        var location = gl.getAttribLocation(gl.getParameter(gl.CURRENT_PROGRAM), 'Vertex');
        gl.bindBuffer(gl.ARRAY_BUFFER, vertBuffer);
        gl.enableVertexAttribArray(location);
        gl.vertexAttribPointer(location, 4, gl.FLOAT, false, 0, 0);
        
        item = programas[0];
        gl.useProgram(item.programa);
        gl.uniform3fv(item.iResolution, [glCanvas.width, glCanvas.height, 0.0]);
        gl.uniform1f(item.iTime, actTime);
        gl.uniform1f(item.iTimeDelta, actTimeDelta);
        gl.uniform1f(item.iFrame, frame);
        
        // Mando lo de un shader al framebuffer
        gl.bindFramebuffer( gl.FRAMEBUFFER, framebuffer );
        gl.drawArrays( gl.TRIANGLES, 0, 6 );
        
        // Y mando todo al canvas
        gl.bindFramebuffer( gl.FRAMEBUFFER, null );
        gl.clear( gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT );
        gl.drawArrays( gl.TRIANGLES, 0, 6 );
        
        window.requestAnimationFrame(function(currentTime) {
          previousTime = currentTime;
          animateScene();
        });
      }
      
      //
      // creates a shader of the given type, uploads the source and
      // compiles it.
      //
      function loadShader(gl, type, source) {
        const shader = gl.createShader(type);

        // Send the source to the shader object

        gl.shaderSource(shader, source);

        // Compile the shader program

        gl.compileShader(shader);

        // See if it compiled successfully

        if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
          console.log('An error occurred compiling the shaders: ' + gl.getShaderInfoLog(shader));
          gl.deleteShader(shader);
          return null;
        }

        return shader;
      }
      
      function loadTexture(gl, url, mipmap) {
        const local_texture = gl.createTexture();
        gl.bindTexture(gl.TEXTURE_2D, local_texture);

        // Because images have to be download over the internet
        // they might take a moment until they are ready.
        // Until then put a single pixel in the texture so we can
        // use it immediately. When the image has finished downloading
        // we'll update the texture with the contents of the image.
        const level = 0;
        const internalFormat = gl.RGBA;
        const width = 1;
        const height = 1;
        const border = 0;
        const srcFormat = gl.RGBA;
        const srcType = gl.UNSIGNED_BYTE;
        const pixel = new Uint8Array([0, 0, 255, 255]);  // opaque blue
        gl.texImage2D(gl.TEXTURE_2D, level, internalFormat,
                      width, height, border, srcFormat, srcType,
                      pixel);

        const image = new Image();
        image.onload = function() {
          gl.bindTexture(gl.TEXTURE_2D, local_texture);
          gl.texImage2D(gl.TEXTURE_2D, level, internalFormat,
                        srcFormat, srcType, image);

          if ( mipmap ) {
             gl.generateMipmap(gl.TEXTURE_2D);
          } else {
             // No, it's not a power of 2. Turn of mips and set
             // wrapping to clamp to edge
             gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
             gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
             gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.NEAREST);
             gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.NEAREST);
          }
          
        };
        image.src = url;
        
        return local_texture;
      }

      function loadRandomTexture(gl) {
        if (gl === null) {
          return;
        }
        var index = randomIntFromInterval(0, imagenes.length);
        var url   = imagenes[index];
        return loadTexture(gl, url);
      }

      function isPowerOf2(value) {
        return (value & (value - 1)) == 0;
      }
      
      function randomIntFromInterval(min,max) {
          return Math.floor(Math.random()*(max-min+1)+min);
      }
      
      function subtitulo_handler( evt ) {
        if (typeof evt === "undefined") {
          document.getElementById("subtitulo").innerHTML = subtitulo_default;
          return;
        }
        document.getElementById("subtitulo").innerHTML = evt.target.getAttribute("subtitulo");

      }
      
      function seccion_handler( evt ) {
        if (evt === "atras") {
          // click en "atrás"
          $(".seccion[id!='main-container']").fadeOut(500);
          $("#main-container").removeClass("out").fadeIn(500);
        } else {
          // Se eligió una sección.
          $(".seccion[id!='" + evt.target.getAttribute("seccion") + "-container']").fadeOut(
            500,
            function() {
              $("#" + $(evt.target).attr("seccion") + "-container")
              .removeClass("out").fadeIn(500);
            }
          );
          
        }
        
      }
      
    </script>
    <div id="no-tenes-webgl-container" class="total seccion">
      <div class="cuerpo-seccion">
        <p>Hola.</p>
        <p>Este sitio usa WebGL. Es una tecnología para hacer cosas tridimensionales, y con eso le hice un fondo animado re bonito a la página.</p>
        <p>Pero tu compu no tiene esa tecnología. Puede ser porque es viejita, o puede ser porque no la actualizás, o bien puede ser porque los dueños de la compu (que son los dueños del software y el hardware, no vos) no te la quieren actualizar.</p>
        <p>Por esa razón, estás viendo en el fondo una imagen triste y gris de un señor que trabaja sólo con una computadora vieja, perdido en el tiempo, y sin esperanzas.</p>
        <p></p>
        <p>En caso de tener dudas sobre qué le puede estar pasando a tu compu, te recomiendo arrancar por este link: <a target="_blank" href="https://get.webgl.org">https://get.webgl.org</a>.</p>
        <p></p>
        <p style="text-align:center;"><button onclick="seccion_handler('atras');"> OK </button></p>
      </div>
    </div>
    <div id="que-container" class="total seccion">
      <div class="cuerpo-seccion">
        <p><i>Feels Theory</i> es una línea de investigación que planteo en mi reciente libro. También resulta ser el nombre del libro.</p>
        <p>La primer pregunta suele ser "de qué se trata". Y francamente ya no tengo mucha idea. De epistemología seguro, pero todo lo demás en lo que se mete supongo que también deberían contar como temas.</p>
        <p></p>
        <p>Como decía, a partir de algunas reflexiones históricas sobre cuestiones vinculadas a la verdad, mediadas mayormente por problemas de la tecnología y de la política, me permito plantear algunos aspectos de la verdad que con el paso de los años no veo a nadie planteándolo con mucho énfasis por ningún lado.</p>
        <p>Existen discursos afines. Pero lo que planteo formalmente en este texto, no tengo registro de que se tome como principio en ninguna disciplina actual.</p>
        <p></p>
        <p>Las ideas en este libro datan de mucho tiempo. En <a target="_blank" href="https://blog.canta.com.ar">mi blog</a> pueden encontrar múltiples recortes de las cosas que planteo con mejor estructura en este libro. Y, como mencionara anteriormente, el vínculo entre tecnología, verdad, y política, está muy vivo en este momento de la historia, de la mano de la idea de "posverdad".</p>
        <p>De modo que aprovecho ese envión que me tocó vivir, y arranco desmenuzando de a poquito la posverdad, para eventualmente comenzar a explorar aspectos positivos de la verdad.</p>
        <p>Así, usando a la posverdad, en este texto voy a poder unir verdad y sentimentalidad. El objetivo es seguir un camino de reflexiones que en algún futuro permita integrar la sentimentalidad a un modelo de inteligencia artificial; o cuanto menos sumar un granito de arena a algunas causas decentes, como la comprensión del ser humano.</p>
        <p></p>
        <p>El libro está dividido en 7 capítulos. Seis de ellos exploran periferias de la verdad, relatando detalles sobre algunos actores sociales de nuestro mundo contemporaneo. Recién en el último capítulo se proponen líneas de lecturas autónomas y se da pie así a una nueva teoría de la verdad, que no se pretende revolucionaria sino apenas un item mas en una larga serie de discursos críticos epistemológicos.</p>
        <p></p>
        <p style="text-align:center;"><button onclick="seccion_handler('atras');">Volver</button></p>
      </div>
    </div>
    <div id="quien-container" class="total seccion">
      <div class="cuerpo-seccion">
        <p>Canta es Daniel Cantarín. Es este de acá, de la foto:</p>
        <p style="text-align:center;"><img src="https://i.imgur.com/POVPcBS.jpg" /></p>
        <p>Es la primer imagen que aparece buscando en internet. La señorita de la foto es mi señora esposa.</p>
        <p>Muy probablemente me crucen más a menudo con esta otra imagen:</p>
        <p style="text-align:center;"><img src="https://secure.gravatar.com/avatar/dad89752e807971706ad199bd586b0ed?size=400" /></p>
        <p>Aprendí computación desde chiquito, y también me gustaba escribir; aunque como eso último no parecía servir para nada, no le presté tanta atención como a la computación.</p>
        <p>Estudié Letras en la UBA durante unos años, hasta que deserté.</p>
        <p>Más tarde estudié la carrera de Técnico Superior en Robótica, donde me recibí junto con mi esposa. Y en un año y medio confiamos en ser ambos Licenciados en Automatización.</p>
        <p>En el camino de esas cosas tuve mucho contacto con problemas de diferentes filosofías, corrientes políticas, psicologías, y tecnologías.</p>
        <p>Podría ponerme a hablar detalles... pero no lo voy a hacer. Si por alguna bizarra razón a alguien le interesa, mi currículum es de público acceso, acá: <a target="_blank" href="https://canta.com.ar/cv.pdf">https://canta.com.ar/cv.pdf</a></p>
        <p style="text-align:center;"><button onclick="seccion_handler('atras');">Volver</button></p>
      </div>
    </div>
    <div id="contacto-container" class="total seccion">
      <div class="cuerpo-seccion">
        <p>A Canta se lo puede encontrar por varios lados.</p>
        <p>En primer lugar, vía correo electrónico: canta arroba canta punto com punto ar. No escribo la dirección para que no la boteen. Y si no sabés lo que es un bot, o qué es botear, buscalo en internet.</p>
        <p>Luego, está el blog, donde de vez en cuando escribe cosas: <a target="_blank" href="https://blog.canta.com.ar">https://blog.canta.com.ar</a>.</p>
        <p>También solía andar por <a target="_blank" href="http://www.forofyl.com.ar">ForoFyL</a>, pero hace rato que no entra porque medio que se enculó.</p>
        <p>Esporádicamente, cuando sus tiempos le permiten dedicarse a sus proyectos de tecnología, suele subir cualquier porquería de código a su espacio en github: <a target="_blank" href="https://github.com/Canta">https://github.com/Canta</a>.</p>
        <p>En rigor, uno debería poder contactarlo en su página personal: <a target="_blank" href="https://canta.com.ar">https://canta.com.ar</a>. Pero nunca se puso las pilas para hacerla. De una manera u otra, hace un tiempo está la versión soviética: <a target="_blank" href="http://canta.su">http://canta.su</a>. Esa anda, y no se me ocurren muchas razones por las que pudiera cambiar.</p>
        <p>Y después de eso, muy de vez en cuando entra en <a target="_blank" href="https://webchat.freenode.net/">IRC (freenode)</a>, donde difícilmente lo vayas a encontrar porque le gusta cambiar de nick y de canal.</p>
        <p>Es sumamente extraño que lo vayas a ver por Facebook, Twitter, o cualquiera de esas porquerías. Pero como hay gente que sólo habla por ahí, a veces les concedo un mensaje para no ser un ogro. Igualmente, eso sucede una vez cada muerte de obispo.</p>
        <p style="text-align:center;"><button onclick="seccion_handler('atras');">Volver</button></p>
      </div>
    </div>
    <div id="descargar-container" class="total seccion">
      <div class="cuerpo-seccion">
        <p>Para descargar el libro completo, siga el siguiente link, o bien hágale un click a la imagen: </p>
        <p style="text-align:center;"><a target="_blank" href="https://canta.com.ar/feels_theory/feels_theory.pdf">https://canta.com.ar/feels_theory/feels_theory.pdf</a></p>
        <p style="text-align:center;"><a target="_blank" href="https://canta.com.ar/feels_theory/feels_theory.pdf"><img src="images/book-canta.png" style="width:150px;"/></a></p>
        <p>Actualmente sólo está online en formato PDF. Esto no es así por voluntad propia, sino por detalles técnicos y legales de publicación; básicamente, hay que hacer más trámites para tener más formatos, y no los tengo tan estudiados como a mí me gustaría (ni a los trámites, ni a los formatos).</p>
        <p></p>
        <p>Periódicamente iré publicando en esta misma sección actualizaciones.</p>
        
        <p style="text-align:center;"><button onclick="seccion_handler('atras');">Volver</button></p>
      </div>
    </div>
    <div id="main-container" class="total seccion">
      <div id="titulo">Feels Theory</div>
      <div id="subtitulo">Desde la posverdad, hacia la sentimentalidad artificial.</div>
      
      <div id="opciones">
        <div class="opcion-grande" style="background-image:url('images/icono-que-onda.png');" subtitulo="¿Qué?" seccion="que"></div>
        <div class="opcion-grande" style="background-image:url('images/icono-quien.png');" subtitulo="¿Quién?" seccion="quien"></div>
        <div class="opcion-grande" style="background-image:url('images/icono-descargar.png');" subtitulo="Descargar el libro completo, gratis." seccion="descargar"></div>
        <div class="opcion-grande" style="background-image:url('images/icono-contacto.png');" subtitulo="Contacto" seccion="contacto"></div>
      </div>
    </div>
  </body>
</html>
