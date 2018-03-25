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
<body>
  <head>
    <meta charset="UTF-8">
    <script type="text/javascript" src="js/jQuery.min.1.7.js"></script>
    <script type="text/javascript" src="js/app.js"></script>
    <script type="text/javascript" src="js/animator.js"></script>
    <script type="text/javascript" src="js/spriteslib.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/background_gl.js"></script>
    <script>
      $(document).ready(
        function(){
          app.bg = new BackgroundGL({context:$("#fondo")[0].getContext("2d")});
          app.bg.init();
        }
      );
    </script>
    <link rel="stylesheet" href="css/general.css" type="text/css" />
  </head>
<canvas id="fondo"></canvas>
<div style="text-align:center; position:relative;">
  
  <p id="titulo">Feels Theory</p>
  <p>Desde la posverdad, hacia la sentimentalidad artificial.</p><br/>

</div>


</body>
