BackgroundGL.prototype = new Sprite();
function BackgroundGL($data){
  if ($data === undefined){
    $data = {};
  }
  
  this.context = (typeof $data.context == "undefined") ? false : $data.context;
  
  $c = new Canvas();
  try {
    delete this.lContext;
    this.lContext = null;
    var contexts = ["webgl2", "experimental-webgl2", "webgl", "experimental-webgl"];
    var i = 0;
    while (this.lContext === null) {
      this.lContext = $c.getContext( contexts[i], { preserveDrawingBuffer: true } );
      i++;
    }
  } catch( error ) { 
    console.log("Error trying to create context.", error);
  }
  if ( this.lContext == undefined || this.lContext == null ) {
    //No hay WebGL disponible :(
    return null;
  }
  
  this.is_visible = function(){
    return this.visible;
  }
  
  this.update = function(){
    if ( !this.lContext ) {
      //no hay contexto para laburar
      return;
    }
    
    if ( !this.is_visible() ) {
      //invisible. no hago nada.
      return;
    }
    
    if ( !this.currentProgram ) {
      //no hay programa. no hago nada.
      return;
    }
    
    this.parameters.time = Date.now() - this.parameters.startTime;
    
    // Set uniforms for custom shader
    this.lContext.useProgram( this.currentProgram );
    this.lContext.uniform3f( this.currentProgram.uniformsCache[ 'iResolution' ], this.parameters.screenWidth, this.parameters.screenHeight, 16);
    this.lContext.uniform1f( this.currentProgram.uniformsCache[ 'iTime' ], this.parameters.time / 1000 );
    this.lContext.uniform4f( this.currentProgram.uniformsCache[ 'iMouse' ], this.parameters.mouseX, this.parameters.mouseY, 0 , 0);
    
    /*
    this.cacheUniformLocation( program, 'iResolution');
    this.cacheUniformLocation( program, 'iTime');
    this.cacheUniformLocation( program, 'iTimeDelta');
    this.cacheUniformLocation( program, 'iFrame');
    this.cacheUniformLocation( program, 'iMouse');
    this.cacheUniformLocation( program, 'iChannel0');
    this.cacheUniformLocation( program, 'iChannel1');
    this.cacheUniformLocation( program, 'iChannel2');
    this.cacheUniformLocation( program, 'iChannel3');
    this.cacheUniformLocation( program, 'iDate');
    */
    
    this.lContext.bindBuffer( this.lContext.ARRAY_BUFFER, this.surface.buffer );
    if (this.surface.positionAttribute != -1) {
      this.lContext.vertexAttribPointer( this.surface.positionAttribute, 2, this.lContext.FLOAT, false, 0, 0 );
    }
    this.lContext.bindBuffer( this.lContext.ARRAY_BUFFER, this.buffer );
    this.lContext.vertexAttribPointer( this.vertexPosition, 2, this.lContext.FLOAT, false, 0, 0 );
    this.lContext.activeTexture( this.lContext.TEXTURE0 );
    this.lContext.bindTexture( this.lContext.TEXTURE_2D, this.backTarget.texture );

    // Render custom shader to front buffer
    this.lContext.bindFramebuffer( this.lContext.FRAMEBUFFER, this.frontTarget.framebuffer );
    this.lContext.clear( this.lContext.COLOR_BUFFER_BIT | this.lContext.DEPTH_BUFFER_BIT );
    this.lContext.drawArrays( this.lContext.TRIANGLES, 0, 6 );
    
    // Set uniforms for screen shader
    this.lContext.useProgram( this.screenProgram );
    this.lContext.uniform2f( this.screenProgram.uniformsCache[ 'resolution' ], this.parameters.screenWidth, this.parameters.screenHeight );
    this.lContext.uniform1i( this.screenProgram.uniformsCache[ 'texture' ], 1 );
    this.lContext.bindBuffer( this.lContext.ARRAY_BUFFER, this.buffer );
    this.lContext.vertexAttribPointer( this.screenVertexPosition, 2, this.lContext.FLOAT, false, 0, 0 );
    this.lContext.activeTexture( this.lContext.TEXTURE1 );
    this.lContext.bindTexture( this.lContext.TEXTURE_2D, this.frontTarget.texture );

    // Render front buffer to screen
    this.lContext.bindFramebuffer( this.lContext.FRAMEBUFFER, null );
    this.lContext.clear( this.lContext.COLOR_BUFFER_BIT | this.lContext.DEPTH_BUFFER_BIT );
    this.lContext.drawArrays( this.lContext.TRIANGLES, 0, 6 );

    // Swap buffers
    var tmp = this.frontTarget;
    this.frontTarget = this.backTarget;
    this.backTarget = tmp;
    
  }
  
  
  this.render = function(){
    if (this.context && this.context.canvas && this.is_visible()){
      this.context.canvas.width = this.context.canvas.width;
      this.context.drawImage(this.lContext.canvas, 0, 0, this.context.canvas.width, this.context.canvas.height);
    }
  }
  
  /* funciones y variables del motor de background GL */
  
  this.quality = ($data.calidad) ? $data.calidad : 2; 
  this.quality_levels = [ 0.5, 1, 2, 4, 8 ];
  
  this.buffer = null;
  this.currentProgram = null
  this.vertexPosition = null;
  this.screenVertexPosition= null;
  this.parameters = { startTime: Date.now(), time: 0, mouseX: 0.5, mouseY: 0.5, screenWidth: 0, screenHeight: 0 };
  this.surface = { centerX: 0, centerY: 0, width: 1, height: 1, isPanning: false, isZooming: false, lastX: 0, lastY: 0 };
  this.frontTarget = null;
  this.backTarget = null;
  this.screenProgram = null;
  this.getWebGL = null;
  this.resizer = {};
  this.compileOnChangeCode = true;
  
  this.fragmentShader = "#ifdef GL_ES\nprecision mediump float;\n#endif\nuniform vec2 resolution;\nuniform sampler2D texture;\nvoid main() {\nvec2 uv = gl_FragCoord.xy / resolution.xy;\ngl_FragColor = texture2D( texture, uv );\n}";
  
  this.vertexShader = "\
    attribute vec3 position;\
    void main() {\
      gl_Position = vec4( position, 1.0 );\
    }\n";
  
  this.surfaceVertexShader = "\
    attribute vec3 position;\
    attribute vec2 surfacePosAttrib;\
    varying vec2 fragCoord;\
    varying vec2 surfacePosition;\
    void main() {\
      surfacePosition = surfacePosAttrib;\
      gl_Position = vec4( position, 1.0 );\
      fragCoord=surfacePosition.xy;\
    }\n";
  
  this.activeShaders = Array(
    $("#vertex-shader").html()
  );
  
  this.load_random_shader = function() {
    $i = Math.round(Math.random() * this.activeShaders.length -1);
    if ($i > this.activeShaders.length - 1){
      $i = this.activeShaders.length -1;
    }
    if ($i < 0){
      $i = 0;
    }
    this.load_shader_from_lib($i);
  }
  
  this.load_shader_from_lib = function(indice) {
    codigo = this.activeShaders[indice];
    this.load_shader(codigo);
  }
  
  this.load_shader = function(codigo){
    this.resetSurface();
    this.compile(codigo);
  }
  
  this.init = function() {
    if (!document.addEventListener) {
      //Browser viejo o mala implementación de JS. 
      console.error("No se encontró document.addEvenListener");
      return;
    }
    
    if ( !this.lContext ) {
      console.warning("No hay contexto WebGL inicializado").
      return;
    } else {
      // Create vertex buffer (2 triangles)
      this.buffer = this.lContext.createBuffer();
      this.lContext.bindBuffer( this.lContext.ARRAY_BUFFER, this.buffer );
      this.lContext.bufferData( this.lContext.ARRAY_BUFFER, new Float32Array( [ - 1.0, - 1.0, 1.0, - 1.0, - 1.0, 1.0, 1.0, - 1.0, 1.0, 1.0, - 1.0, 1.0 ] ), this.lContext.STATIC_DRAW );
      // Create surface buffer (coordinates at screen corners)
      this.surface.buffer = this.lContext.createBuffer();
    }
    
    this.onWindowResize();
    window.addEventListener( 'resize', this.onWindowResize, false );
    this.load_random_shader();
    this.compileScreenProgram();
    
    this.animator.setParent(this);
    
    this.animator.addCallback( function(){this.parent.update();this.parent.render();}, null, false );
    this.handle_events("on_ready");
    this.animator.start();
    
  }
  
  this.computeSurfaceCorners = function() {
    if (this.lContext) {
      this.surface.width = this.surface.height * this.parameters.screenWidth / this.parameters.screenHeight;
      
      var halfWidth = this.surface.width * 0.5, halfHeight = this.surface.height * 0.5;
      
      this.lContext.bindBuffer( this.lContext.ARRAY_BUFFER, this.surface.buffer );
      this.lContext.bufferData( 
        this.lContext.ARRAY_BUFFER, new Float32Array( [
        this.surface.centerX - halfWidth, this.surface.centerY - halfHeight,
        this.surface.centerX + halfWidth, this.surface.centerY - halfHeight,
        this.surface.centerX - halfWidth, this.surface.centerY + halfHeight,
        this.surface.centerX + halfWidth, this.surface.centerY - halfHeight,
        this.surface.centerX + halfWidth, this.surface.centerY + halfHeight,
        this.surface.centerX - halfWidth, this.surface.centerY + halfHeight ] ), 
        this.lContext.STATIC_DRAW 
      );
    }
  }
  
  this.resetSurface = function() {
    this.surface.centerX = this.surface.centerY = 0;
    this.surface.height = 1;
    this.computeSurfaceCorners();
  }
  
  this.compile = function(codigo) {
    if (!this.lContext) {
      return;
    }
    var program   = this.lContext.createProgram();
    var fragment  = codigo;
    var vertex    = this.surfaceVertexShader;
    
    var vs = this.createShader( vertex, this.lContext.VERTEX_SHADER );
    var fs = this.createShader( fragment, this.lContext.FRAGMENT_SHADER );
    
    if ( vs == null || fs == null ) return null;
    
    this.lContext.attachShader( program, vs );
    this.lContext.attachShader( program, fs );

    this.lContext.deleteShader( vs );
    this.lContext.deleteShader( fs );
    
    this.lContext.linkProgram( program );

    if ( !this.lContext.getProgramParameter( program, this.lContext.LINK_STATUS ) ) {
      var error = this.lContext.getProgramInfoLog( program );
      console.error( error );
      console.error( 'VALIDATE_STATUS: ' + this.lContext.getProgramParameter( program, this.lContext.VALIDATE_STATUS ), 'ERROR: ' + this.lContext.getError() );
      return;
    }

    if ( this.currentProgram ) {
      this.lContext.deleteProgram( this.currentProgram );
      //setURL( fragment );
    }
    this.currentProgram = program;
    
    // Cache uniforms
    
    this.cacheUniformLocation( program, 'iResolution');
    this.cacheUniformLocation( program, 'iTime');
    this.cacheUniformLocation( program, 'iTimeDelta');
    this.cacheUniformLocation( program, 'iFrame');
    this.cacheUniformLocation( program, 'iMouse');
    this.cacheUniformLocation( program, 'iChannel0');
    this.cacheUniformLocation( program, 'iChannel1');
    this.cacheUniformLocation( program, 'iChannel2');
    this.cacheUniformLocation( program, 'iChannel3');
    this.cacheUniformLocation( program, 'iDate');
    // Load program into GPU
    this.lContext.useProgram( this.currentProgram );
    // Set up buffers
    this.surface.positionAttribute = this.lContext.getAttribLocation(this.currentProgram, "surfacePosAttrib");
    if (this.surface.positionAttribute != -1) {
      this.lContext.enableVertexAttribArray(this.surface.positionAttribute);
    }
    /*
    this.iChannel0 = this.lContext.getAttribLocation(this.currentProgram, "iChannel0");
    this.iChannel1 = this.lContext.getAttribLocation(this.currentProgram, "iChannel1");
    this.iChannel2 = this.lContext.getAttribLocation(this.currentProgram, "iChannel2");
    this.iChannel3 = this.lContext.getAttribLocation(this.currentProgram, "iChannel3");
    */
    
    /*
    var iChannel1 = this.lContext.createTexture();
    this.lContext.bindTexture(this.lContext.TEXTURE_2D, iChannel0);
    this.lContext.texParameteri(this.lContext.TEXTURE_2D, this.lContext.TEXTURE_WRAP_S, this.lContext.CLAMP_TO_EDGE);
    this.lContext.texParameteri(this.lContext.TEXTURE_2D, this.lContext.TEXTURE_WRAP_T, this.lContext.CLAMP_TO_EDGE);
    this.lContext.texParameteri(this.lContext.TEXTURE_2D, this.lContext.TEXTURE_MIN_FILTER, this.lContext.NEAREST);
    this.lContext.texParameteri(this.lContext.TEXTURE_2D, this.lContext.TEXTURE_MAG_FILTER, this.lContext.NEAREST);
    
    // Upload the image into the texture.
    this.lContext.texImage2D(this.lContext.TEXTURE_2D, 0, this.lContext.RGBA, this.lContext.RGBA, this.lContext.UNSIGNED_BYTE, $("#iChannel1")[0]);
    */
    this.vertexPosition = this.lContext.getAttribLocation(this.currentProgram, "position");
    this.lContext.enableVertexAttribArray( this.vertexPosition );
  }
  
  this.compileScreenProgram = function() {
    if (!this.lContext) { return; }
    
    var program = this.lContext.createProgram();
    var fragment = this.fragmentShader;
    var vertex = this.vertexShader;

    var vs = this.createShader( vertex, this.lContext.VERTEX_SHADER );
    var fs = this.createShader( fragment, this.lContext.FRAGMENT_SHADER );

    this.lContext.attachShader( program, vs );
    this.lContext.attachShader( program, fs );
    this.lContext.deleteShader( vs );
    this.lContext.deleteShader( fs );
    this.lContext.linkProgram( program );

    if ( !this.lContext.getProgramParameter( program, this.lContext.LINK_STATUS ) ) {
      console.error( 'VALIDATE_STATUS: ' + this.lContext.getProgramParameter( program, this.lContext.VALIDATE_STATUS ), 'ERROR: ' + this.lContext.getError() );
      return;
    }

    this.screenProgram = program;
    this.lContext.useProgram( this.screenProgram );

    this.cacheUniformLocation( program, 'resolution' );
    this.cacheUniformLocation( program, 'texture' );

    this.screenVertexPosition = this.lContext.getAttribLocation(this.screenProgram, "position");
    this.lContext.enableVertexAttribArray( this.screenVertexPosition );

  }
  
  this.cacheUniformLocation = function( program, label ) {
    if ( program.uniformsCache === undefined ) {
      program.uniformsCache = {};
    }
    program.uniformsCache[ label ] = this.lContext.getUniformLocation( program, label );
  }
  
  this.createTarget = function( width, height ) {
    var target = {};
    target.framebuffer = this.lContext.createFramebuffer();
    target.renderbuffer = this.lContext.createRenderbuffer();
    target.texture = this.lContext.createTexture();
    // set up framebuffer
    this.lContext.bindTexture( this.lContext.TEXTURE_2D, target.texture );
    this.lContext.texImage2D( this.lContext.TEXTURE_2D, 0, this.lContext.RGBA, width, height, 0, this.lContext.RGBA, this.lContext.UNSIGNED_BYTE, null );
    this.lContext.texParameteri( this.lContext.TEXTURE_2D, this.lContext.TEXTURE_WRAP_S, this.lContext.CLAMP_TO_EDGE );
    this.lContext.texParameteri( this.lContext.TEXTURE_2D, this.lContext.TEXTURE_WRAP_T, this.lContext.CLAMP_TO_EDGE );
    this.lContext.texParameteri( this.lContext.TEXTURE_2D, this.lContext.TEXTURE_MAG_FILTER, this.lContext.NEAREST );
    this.lContext.texParameteri( this.lContext.TEXTURE_2D, this.lContext.TEXTURE_MIN_FILTER, this.lContext.NEAREST );
    this.lContext.bindFramebuffer( this.lContext.FRAMEBUFFER, target.framebuffer );
    this.lContext.framebufferTexture2D( this.lContext.FRAMEBUFFER, this.lContext.COLOR_ATTACHMENT0, this.lContext.TEXTURE_2D, target.texture, 0 );
    // set up renderbuffer
    this.lContext.bindRenderbuffer( this.lContext.RENDERBUFFER, target.renderbuffer );
    this.lContext.renderbufferStorage( this.lContext.RENDERBUFFER, this.lContext.DEPTH_COMPONENT16, width, height );
    this.lContext.framebufferRenderbuffer( this.lContext.FRAMEBUFFER, this.lContext.DEPTH_ATTACHMENT, this.lContext.RENDERBUFFER, target.renderbuffer );
    // clean up
    this.lContext.bindTexture( this.lContext.TEXTURE_2D, null );
    this.lContext.bindRenderbuffer( this.lContext.RENDERBUFFER, null );
    this.lContext.bindFramebuffer( this.lContext.FRAMEBUFFER, null);
    return target;
  }
  
  this.createRenderTargets = function() {
    this.frontTarget = this.createTarget( this.parameters.screenWidth, this.parameters.screenHeight );
    this.backTarget = this.createTarget( this.parameters.screenWidth, this.parameters.screenHeight );
  }
  
  this.createShader = function( src, type ) {
    var shader = this.lContext.createShader( type );
    var line, lineNum, lineError, index = 0, indexEnd;
    
    this.lContext.shaderSource( shader, src );
    this.lContext.compileShader( shader );

    if ( !this.lContext.getShaderParameter( shader, this.lContext.COMPILE_STATUS ) ) {
      console.error( 
        "Error al intentar crear el shader.", 
        this.lContext.getShaderInfoLog(shader) 
      );
      return null;
    }
    return shader;
  }
  
  this.onWindowResize = function ( event ) {
    var isMaxWidth = ((this.resizer.currentWidth === this.resizer.maxWidth) || (this.resizer.currentWidth === this.resizer.minWidth)),
      isMaxHeight = ((this.resizer.currentHeight === this.resizer.maxHeight) || (this.resizer.currentHeight === this.resizer.minHeight));
    this.resizer.isResizing = false;
    this.resizer.maxWidth = window.innerWidth - 75;
    this.resizer.maxHeight = window.innerHeight - 125;
    if (isMaxWidth || (this.resizer.currentWidth > this.resizer.maxWidth)) {
      this.resizer.currentWidth = this.resizer.maxWidth;
    }
    if (isMaxHeight || (this.resizer.currentHeight > this.resizer.maxHeight)) {
      this.resizer.currentHeight = this.resizer.maxHeight;
    }
    if (this.resizer.currentWidth < this.resizer.minWidth) { this.resizer.currentWidth = this.resizer.minWidth; }
    if (this.resizer.currentHeight < this.resizer.minHeight) { this.resizer.currentHeight = this.resizer.minHeight; }

    this.context.canvas.width = window.innerWidth;
    this.context.canvas.height = window.innerHeight;

    this.lContext.canvas.width = window.innerWidth / this.quality;
    this.lContext.canvas.height = window.innerHeight / this.quality;
    
    this.context.canvas.style.width = window.innerWidth + 'px';
    this.context.canvas.style.height = window.innerHeight + 'px';
    this.lContext.canvas.style.width = window.innerWidth + 'px';
    this.lContext.canvas.style.height = window.innerHeight + 'px';

    this.parameters.screenWidth = this.lContext.canvas.width;
    this.parameters.screenHeight = this.lContext.canvas.height;
    this.computeSurfaceCorners();
    if (this.lContext) {
      this.lContext.viewport( 0, 0, this.lContext.canvas.width, this.lContext.canvas.height );
      this.createRenderTargets();
    }
  }
  
}

