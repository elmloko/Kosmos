/* -----------------------------------------------
/* Author : Vincent Garreau  - vincentgarreau.com
/* MIT license: http://opensource.org/licenses/MIT
/* Demo / Generator : vincentgarreau.com/particles.js
/* GitHub : github.com/VincentGarreau/particles.js
/* How to use? : Check the GitHub README
/* v2.0.0
/* ----------------------------------------------- */

var pafePJS = function(tag_id, params){ 

  var canvas_el = document.querySelector(tag_id + ' > .particles-js-canvas-el');

  /* particles.js variables with default values */
  this.pafePJS = { 
    canvas: {
      el: canvas_el,
      w: canvas_el.offsetWidth,
      h: canvas_el.offsetHeight
    },
    particles: {
      number: {
        value: 400,
        density: {
          enable: true,
          value_area: 800
        }
      },
      color: {
        value: '#2a802b'
      },
      shape: {
        type: 'circle',
        stroke: {
          width: 0,
          color: '#ff0000'
        },
        polygon: {
          nb_sides: 5
        },
        image: {
          src: '',
          width: 100,
          height: 100
        }
      },
      opacity: {
        value: 1,
        random: false,
        anim: {
          enable: false,
          speed: 2,
          opacity_min: 0,
          sync: false
        }
      },
      size: {
        value: 20,
        random: false,
        anim: {
          enable: false,
          speed: 20,
          size_min: 0,
          sync: false
        }
      },
      line_linked: {
        enable: true,
        distance: 100,
        color: '#fff',
        opacity: 1,
        width: 1
      },
      move: {
        enable: true,
        speed: 2,
        direction: 'none',
        random: false,
        straight: false,
        out_mode: 'out',
        bounce: false,
        attract: {
          enable: false,
          rotateX: 3000,
          rotateY: 3000
        }
      },
      array: []
    },
    interactivity: {
      detect_on: 'canvas',
      events: {
        onhover: {
          enable: true,
          mode: 'grab'
        },
        onclick: {
          enable: true,
          mode: 'push'
        },
        resize: true
      },
      modes: {
        grab:{
          distance: 100,
          line_linked:{
            opacity: 1
          }
        },
        bubble:{
          distance: 200,
          size: 80,
          duration: 0.4
        },
        repulse:{
          distance: 200,
          duration: 0.4
        },
        push:{
          particles_nb: 4
        },
        remove:{
          particles_nb: 2
        }
      },
      mouse:{}
    },
    retina_detect: false,
    fn: {
      interact: {},
      modes: {},
      vendors:{}
    },
    tmp: {}
  };

  var pafePJS = this.pafePJS;

  /* params settings */
  if(params){
    Object.deepExtend(pafePJS, params);
  }

  pafePJS.tmp.obj = {
    size_value: pafePJS.particles.size.value,
    size_anim_speed: pafePJS.particles.size.anim.speed,
    move_speed: pafePJS.particles.move.speed,
    line_linked_distance: pafePJS.particles.line_linked.distance,
    line_linked_width: pafePJS.particles.line_linked.width,
    mode_grab_distance: pafePJS.interactivity.modes.grab.distance,
    mode_bubble_distance: pafePJS.interactivity.modes.bubble.distance,
    mode_bubble_size: pafePJS.interactivity.modes.bubble.size,
    mode_repulse_distance: pafePJS.interactivity.modes.repulse.distance
  };


  pafePJS.fn.retinaInit = function(){

    if(pafePJS.retina_detect && window.devicePixelRatio > 1){
      pafePJS.canvas.pxratio = window.devicePixelRatio; 
      pafePJS.tmp.retina = true;
    } 
    else{
      pafePJS.canvas.pxratio = 1;
      pafePJS.tmp.retina = false;
    }

    pafePJS.canvas.w = pafePJS.canvas.el.offsetWidth * pafePJS.canvas.pxratio;
    pafePJS.canvas.h = pafePJS.canvas.el.offsetHeight * pafePJS.canvas.pxratio;

    pafePJS.particles.size.value = pafePJS.tmp.obj.size_value * pafePJS.canvas.pxratio;
    pafePJS.particles.size.anim.speed = pafePJS.tmp.obj.size_anim_speed * pafePJS.canvas.pxratio;
    pafePJS.particles.move.speed = pafePJS.tmp.obj.move_speed * pafePJS.canvas.pxratio;
    pafePJS.particles.line_linked.distance = pafePJS.tmp.obj.line_linked_distance * pafePJS.canvas.pxratio;
    pafePJS.interactivity.modes.grab.distance = pafePJS.tmp.obj.mode_grab_distance * pafePJS.canvas.pxratio;
    pafePJS.interactivity.modes.bubble.distance = pafePJS.tmp.obj.mode_bubble_distance * pafePJS.canvas.pxratio;
    pafePJS.particles.line_linked.width = pafePJS.tmp.obj.line_linked_width * pafePJS.canvas.pxratio;
    pafePJS.interactivity.modes.bubble.size = pafePJS.tmp.obj.mode_bubble_size * pafePJS.canvas.pxratio;
    pafePJS.interactivity.modes.repulse.distance = pafePJS.tmp.obj.mode_repulse_distance * pafePJS.canvas.pxratio;

  };



  /* ---------- pafePJS functions - canvas ------------ */

  pafePJS.fn.canvasInit = function(){
    pafePJS.canvas.ctx = pafePJS.canvas.el.getContext('2d');
  };

  pafePJS.fn.canvasSize = function(){

    pafePJS.canvas.el.width = pafePJS.canvas.w;
    pafePJS.canvas.el.height = pafePJS.canvas.h;

    if(pafePJS && pafePJS.interactivity.events.resize){

      window.addEventListener('resize', function(){

          pafePJS.canvas.w = pafePJS.canvas.el.offsetWidth;
          pafePJS.canvas.h = pafePJS.canvas.el.offsetHeight;

          /* resize canvas */
          if(pafePJS.tmp.retina){
            pafePJS.canvas.w *= pafePJS.canvas.pxratio;
            pafePJS.canvas.h *= pafePJS.canvas.pxratio;
          }

          pafePJS.canvas.el.width = pafePJS.canvas.w;
          pafePJS.canvas.el.height = pafePJS.canvas.h;

          /* repaint canvas on anim disabled */
          if(!pafePJS.particles.move.enable){
            pafePJS.fn.particlesEmpty();
            pafePJS.fn.particlesCreate();
            pafePJS.fn.particlesDraw();
            pafePJS.fn.vendors.densityAutoParticles();
          }

        /* density particles enabled */
        pafePJS.fn.vendors.densityAutoParticles();

      });

    }

  };


  pafePJS.fn.canvasPaint = function(){
    pafePJS.canvas.ctx.fillRect(0, 0, pafePJS.canvas.w, pafePJS.canvas.h);
  };

  pafePJS.fn.canvasClear = function(){
    pafePJS.canvas.ctx.clearRect(0, 0, pafePJS.canvas.w, pafePJS.canvas.h);
  };


  /* --------- pafePJS functions - particles ----------- */

  pafePJS.fn.particle = function(color, opacity, position){

    /* size */
    this.radius = (pafePJS.particles.size.random ? Math.random() : 1) * pafePJS.particles.size.value;
    if(pafePJS.particles.size.anim.enable){
      this.size_status = false;
      this.vs = pafePJS.particles.size.anim.speed / 100;
      if(!pafePJS.particles.size.anim.sync){
        this.vs = this.vs * Math.random();
      }
    }

    /* position */
    this.x = position ? position.x : Math.random() * pafePJS.canvas.w;
    this.y = position ? position.y : Math.random() * pafePJS.canvas.h;

    /* check position  - into the canvas */
    if(this.x > pafePJS.canvas.w - this.radius*2) this.x = this.x - this.radius;
    else if(this.x < this.radius*2) this.x = this.x + this.radius;
    if(this.y > pafePJS.canvas.h - this.radius*2) this.y = this.y - this.radius;
    else if(this.y < this.radius*2) this.y = this.y + this.radius;

    /* check position - avoid overlap */
    if(pafePJS.particles.move.bounce){
      pafePJS.fn.vendors.checkOverlap(this, position);
    }

    /* color */
    this.color = {};
    if(typeof(color.value) == 'object'){

      if(color.value instanceof Array){
        var color_selected = color.value[Math.floor(Math.random() * pafePJS.particles.color.value.length)];
        this.color.rgb = hexToRgb(color_selected);
      }else{
        if(color.value.r != undefined && color.value.g != undefined && color.value.b != undefined){
          this.color.rgb = {
            r: color.value.r,
            g: color.value.g,
            b: color.value.b
          }
        }
        if(color.value.h != undefined && color.value.s != undefined && color.value.l != undefined){
          this.color.hsl = {
            h: color.value.h,
            s: color.value.s,
            l: color.value.l
          }
        }
      }

    }
    else if(color.value == 'random'){
      this.color.rgb = {
        r: (Math.floor(Math.random() * (255 - 0 + 1)) + 0),
        g: (Math.floor(Math.random() * (255 - 0 + 1)) + 0),
        b: (Math.floor(Math.random() * (255 - 0 + 1)) + 0)
      }
    }
    else if(typeof(color.value) == 'string'){
      this.color = color;
      this.color.rgb = hexToRgb(this.color.value);
    }

    /* opacity */
    this.opacity = (pafePJS.particles.opacity.random ? Math.random() : 1) * pafePJS.particles.opacity.value;
    if(pafePJS.particles.opacity.anim.enable){
      this.opacity_status = false;
      this.vo = pafePJS.particles.opacity.anim.speed / 100;
      if(!pafePJS.particles.opacity.anim.sync){
        this.vo = this.vo * Math.random();
      }
    }

    /* animation - velocity for speed */
    var velbase = {}
    switch(pafePJS.particles.move.direction){
      case 'top':
        velbase = { x:0, y:-1 };
      break;
      case 'top-right':
        velbase = { x:0.5, y:-0.5 };
      break;
      case 'right':
        velbase = { x:1, y:-0 };
      break;
      case 'bottom-right':
        velbase = { x:0.5, y:0.5 };
      break;
      case 'bottom':
        velbase = { x:0, y:1 };
      break;
      case 'bottom-left':
        velbase = { x:-0.5, y:1 };
      break;
      case 'left':
        velbase = { x:-1, y:0 };
      break;
      case 'top-left':
        velbase = { x:-0.5, y:-0.5 };
      break;
      default:
        velbase = { x:0, y:0 };
      break;
    }

    if(pafePJS.particles.move.straight){
      this.vx = velbase.x;
      this.vy = velbase.y;
      if(pafePJS.particles.move.random){
        this.vx = this.vx * (Math.random());
        this.vy = this.vy * (Math.random());
      }
    }else{
      this.vx = velbase.x + Math.random()-0.5;
      this.vy = velbase.y + Math.random()-0.5;
    }

    // var theta = 2.0 * Math.PI * Math.random();
    // this.vx = Math.cos(theta);
    // this.vy = Math.sin(theta);

    this.vx_i = this.vx;
    this.vy_i = this.vy;

    

    /* if shape is image */

    var shape_type = pafePJS.particles.shape.type;
    if(typeof(shape_type) == 'object'){
      if(shape_type instanceof Array){
        var shape_selected = shape_type[Math.floor(Math.random() * shape_type.length)];
        this.shape = shape_selected;
      }
    }else{
      this.shape = shape_type;
    }

    if(this.shape == 'image'){
      var sh = pafePJS.particles.shape;
      this.img = {
        src: sh.image.src,
        ratio: sh.image.width / sh.image.height
      }
      if(!this.img.ratio) this.img.ratio = 1;
      if(pafePJS.tmp.img_type == 'svg' && pafePJS.tmp.source_svg != undefined){
        pafePJS.fn.vendors.createSvgImg(this);
        if(pafePJS.tmp.pushing){
          this.img.loaded = false;
        }
      }
    }

    

  };


  pafePJS.fn.particle.prototype.draw = function() {

    var p = this;

    if(p.radius_bubble != undefined){
      var radius = p.radius_bubble; 
    }else{
      var radius = p.radius;
    }

    if(p.opacity_bubble != undefined){
      var opacity = p.opacity_bubble;
    }else{
      var opacity = p.opacity;
    }

    if(p.color.rgb){
      var color_value = 'rgba('+p.color.rgb.r+','+p.color.rgb.g+','+p.color.rgb.b+','+opacity+')';
    }else{
      var color_value = 'hsla('+p.color.hsl.h+','+p.color.hsl.s+'%,'+p.color.hsl.l+'%,'+opacity+')';
    }

    pafePJS.canvas.ctx.fillStyle = color_value;
    pafePJS.canvas.ctx.beginPath();

    switch(p.shape){

      case 'circle':
        pafePJS.canvas.ctx.arc(p.x, p.y, radius, 0, Math.PI * 2, false);
      break;

      case 'edge':
        pafePJS.canvas.ctx.rect(p.x-radius, p.y-radius, radius*2, radius*2);
      break;

      case 'triangle':
        pafePJS.fn.vendors.drawShape(pafePJS.canvas.ctx, p.x-radius, p.y+radius / 1.66, radius*2, 3, 2);
      break;

      case 'polygon':
        pafePJS.fn.vendors.drawShape(
          pafePJS.canvas.ctx,
          p.x - radius / (pafePJS.particles.shape.polygon.nb_sides/3.5), // startX
          p.y - radius / (2.66/3.5), // startY
          radius*2.66 / (pafePJS.particles.shape.polygon.nb_sides/3), // sideLength
          pafePJS.particles.shape.polygon.nb_sides, // sideCountNumerator
          1 // sideCountDenominator
        );
      break;

      case 'star':
        pafePJS.fn.vendors.drawShape(
          pafePJS.canvas.ctx,
          p.x - radius*2 / (pafePJS.particles.shape.polygon.nb_sides/4), // startX
          p.y - radius / (2*2.66/3.5), // startY
          radius*2*2.66 / (pafePJS.particles.shape.polygon.nb_sides/3), // sideLength
          pafePJS.particles.shape.polygon.nb_sides, // sideCountNumerator
          2 // sideCountDenominator
        );
      break;

      case 'image':

        function draw(){
          pafePJS.canvas.ctx.drawImage(
            img_obj,
            p.x-radius,
            p.y-radius,
            radius*2,
            radius*2 / p.img.ratio
          );
        }

        if(pafePJS.tmp.img_type == 'svg'){
          var img_obj = p.img.obj;
        }else{
          var img_obj = pafePJS.tmp.img_obj;
        }

        if(img_obj){
          draw();
        }

      break;

    }

    pafePJS.canvas.ctx.closePath();

    if(pafePJS.particles.shape.stroke.width > 0){
      pafePJS.canvas.ctx.strokeStyle = pafePJS.particles.shape.stroke.color;
      pafePJS.canvas.ctx.lineWidth = pafePJS.particles.shape.stroke.width;
      pafePJS.canvas.ctx.stroke();
    }
    
    pafePJS.canvas.ctx.fill();
    
  };


  pafePJS.fn.particlesCreate = function(){
    for(var i = 0; i < pafePJS.particles.number.value; i++) {
      pafePJS.particles.array.push(new pafePJS.fn.particle(pafePJS.particles.color, pafePJS.particles.opacity.value));
    }
  };

  pafePJS.fn.particlesUpdate = function(){

    for(var i = 0; i < pafePJS.particles.array.length; i++){

      /* the particle */
      var p = pafePJS.particles.array[i];

      // var d = ( dx = pafePJS.interactivity.mouse.click_pos_x - p.x ) * dx + ( dy = pafePJS.interactivity.mouse.click_pos_y - p.y ) * dy;
      // var f = -BANG_SIZE / d;
      // if ( d < BANG_SIZE ) {
      //     var t = Math.atan2( dy, dx );
      //     p.vx = f * Math.cos(t);
      //     p.vy = f * Math.sin(t);
      // }

      /* move the particle */
      if(pafePJS.particles.move.enable){
        var ms = pafePJS.particles.move.speed/2;
        p.x += p.vx * ms;
        p.y += p.vy * ms;
      }

      /* change opacity status */
      if(pafePJS.particles.opacity.anim.enable) {
        if(p.opacity_status == true) {
          if(p.opacity >= pafePJS.particles.opacity.value) p.opacity_status = false;
          p.opacity += p.vo;
        }else {
          if(p.opacity <= pafePJS.particles.opacity.anim.opacity_min) p.opacity_status = true;
          p.opacity -= p.vo;
        }
        if(p.opacity < 0) p.opacity = 0;
      }

      /* change size */
      if(pafePJS.particles.size.anim.enable){
        if(p.size_status == true){
          if(p.radius >= pafePJS.particles.size.value) p.size_status = false;
          p.radius += p.vs;
        }else{
          if(p.radius <= pafePJS.particles.size.anim.size_min) p.size_status = true;
          p.radius -= p.vs;
        }
        if(p.radius < 0) p.radius = 0;
      }

      /* change particle position if it is out of canvas */
      if(pafePJS.particles.move.out_mode == 'bounce'){
        var new_pos = {
          x_left: p.radius,
          x_right:  pafePJS.canvas.w,
          y_top: p.radius,
          y_bottom: pafePJS.canvas.h
        }
      }else{
        var new_pos = {
          x_left: -p.radius,
          x_right: pafePJS.canvas.w + p.radius,
          y_top: -p.radius,
          y_bottom: pafePJS.canvas.h + p.radius
        }
      }

      if(p.x - p.radius > pafePJS.canvas.w){
        p.x = new_pos.x_left;
        p.y = Math.random() * pafePJS.canvas.h;
      }
      else if(p.x + p.radius < 0){
        p.x = new_pos.x_right;
        p.y = Math.random() * pafePJS.canvas.h;
      }
      if(p.y - p.radius > pafePJS.canvas.h){
        p.y = new_pos.y_top;
        p.x = Math.random() * pafePJS.canvas.w;
      }
      else if(p.y + p.radius < 0){
        p.y = new_pos.y_bottom;
        p.x = Math.random() * pafePJS.canvas.w;
      }

      /* out of canvas modes */
      switch(pafePJS.particles.move.out_mode){
        case 'bounce':
          if (p.x + p.radius > pafePJS.canvas.w) p.vx = -p.vx;
          else if (p.x - p.radius < 0) p.vx = -p.vx;
          if (p.y + p.radius > pafePJS.canvas.h) p.vy = -p.vy;
          else if (p.y - p.radius < 0) p.vy = -p.vy;
        break;
      }

      /* events */
      if(isInArray('grab', pafePJS.interactivity.events.onhover.mode)){
        pafePJS.fn.modes.grabParticle(p);
      }

      if(isInArray('bubble', pafePJS.interactivity.events.onhover.mode) || isInArray('bubble', pafePJS.interactivity.events.onclick.mode)){
        pafePJS.fn.modes.bubbleParticle(p);
      }

      if(isInArray('repulse', pafePJS.interactivity.events.onhover.mode) || isInArray('repulse', pafePJS.interactivity.events.onclick.mode)){
        pafePJS.fn.modes.repulseParticle(p);
      }

      /* interaction auto between particles */
      if(pafePJS.particles.line_linked.enable || pafePJS.particles.move.attract.enable){
        for(var j = i + 1; j < pafePJS.particles.array.length; j++){
          var p2 = pafePJS.particles.array[j];

          /* link particles */
          if(pafePJS.particles.line_linked.enable){
            pafePJS.fn.interact.linkParticles(p,p2);
          }

          /* attract particles */
          if(pafePJS.particles.move.attract.enable){
            pafePJS.fn.interact.attractParticles(p,p2);
          }

          /* bounce particles */
          if(pafePJS.particles.move.bounce){
            pafePJS.fn.interact.bounceParticles(p,p2);
          }

        }
      }


    }

  };

  pafePJS.fn.particlesDraw = function(){

    /* clear canvas */
    pafePJS.canvas.ctx.clearRect(0, 0, pafePJS.canvas.w, pafePJS.canvas.h);

    /* update each particles param */
    pafePJS.fn.particlesUpdate();

    /* draw each particle */
    for(var i = 0; i < pafePJS.particles.array.length; i++){
      var p = pafePJS.particles.array[i];
      p.draw();
    }

  };

  pafePJS.fn.particlesEmpty = function(){
    pafePJS.particles.array = [];
  };

  pafePJS.fn.particlesRefresh = function(){

    /* init all */
    cancelRequestAnimFrame(pafePJS.fn.checkAnimFrame);
    cancelRequestAnimFrame(pafePJS.fn.drawAnimFrame);
    pafePJS.tmp.source_svg = undefined;
    pafePJS.tmp.img_obj = undefined;
    pafePJS.tmp.count_svg = 0;
    pafePJS.fn.particlesEmpty();
    pafePJS.fn.canvasClear();
    
    /* restart */
    pafePJS.fn.vendors.start();

  };


  /* ---------- pafePJS functions - particles interaction ------------ */

  pafePJS.fn.interact.linkParticles = function(p1, p2){

    var dx = p1.x - p2.x,
        dy = p1.y - p2.y,
        dist = Math.sqrt(dx*dx + dy*dy);

    /* draw a line between p1 and p2 if the distance between them is under the config distance */
    if(dist <= pafePJS.particles.line_linked.distance){

      var opacity_line = pafePJS.particles.line_linked.opacity - (dist / (1/pafePJS.particles.line_linked.opacity)) / pafePJS.particles.line_linked.distance;

      if(opacity_line > 0){        
        
        /* style */
        var color_line = pafePJS.particles.line_linked.color_rgb_line;
        pafePJS.canvas.ctx.strokeStyle = 'rgba('+color_line.r+','+color_line.g+','+color_line.b+','+opacity_line+')';
        pafePJS.canvas.ctx.lineWidth = pafePJS.particles.line_linked.width;
        //pafePJS.canvas.ctx.lineCap = 'round'; /* performance issue */
        
        /* path */
        pafePJS.canvas.ctx.beginPath();
        pafePJS.canvas.ctx.moveTo(p1.x, p1.y);
        pafePJS.canvas.ctx.lineTo(p2.x, p2.y);
        pafePJS.canvas.ctx.stroke();
        pafePJS.canvas.ctx.closePath();

      }

    }

  };


  pafePJS.fn.interact.attractParticles  = function(p1, p2){

    /* condensed particles */
    var dx = p1.x - p2.x,
        dy = p1.y - p2.y,
        dist = Math.sqrt(dx*dx + dy*dy);

    if(dist <= pafePJS.particles.line_linked.distance){

      var ax = dx/(pafePJS.particles.move.attract.rotateX*1000),
          ay = dy/(pafePJS.particles.move.attract.rotateY*1000);

      p1.vx -= ax;
      p1.vy -= ay;

      p2.vx += ax;
      p2.vy += ay;

    }
    

  }


  pafePJS.fn.interact.bounceParticles = function(p1, p2){

    var dx = p1.x - p2.x,
        dy = p1.y - p2.y,
        dist = Math.sqrt(dx*dx + dy*dy),
        dist_p = p1.radius+p2.radius;

    if(dist <= dist_p){
      p1.vx = -p1.vx;
      p1.vy = -p1.vy;

      p2.vx = -p2.vx;
      p2.vy = -p2.vy;
    }

  }


  /* ---------- pafePJS functions - modes events ------------ */

  pafePJS.fn.modes.pushParticles = function(nb, pos){

    pafePJS.tmp.pushing = true;

    for(var i = 0; i < nb; i++){
      pafePJS.particles.array.push(
        new pafePJS.fn.particle(
          pafePJS.particles.color,
          pafePJS.particles.opacity.value,
          {
            'x': pos ? pos.pos_x : Math.random() * pafePJS.canvas.w,
            'y': pos ? pos.pos_y : Math.random() * pafePJS.canvas.h
          }
        )
      )
      if(i == nb-1){
        if(!pafePJS.particles.move.enable){
          pafePJS.fn.particlesDraw();
        }
        pafePJS.tmp.pushing = false;
      }
    }

  };


  pafePJS.fn.modes.removeParticles = function(nb){

    pafePJS.particles.array.splice(0, nb);
    if(!pafePJS.particles.move.enable){
      pafePJS.fn.particlesDraw();
    }

  };


  pafePJS.fn.modes.bubbleParticle = function(p){

    /* on hover event */
    if(pafePJS.interactivity.events.onhover.enable && isInArray('bubble', pafePJS.interactivity.events.onhover.mode)){

      var dx_mouse = p.x - pafePJS.interactivity.mouse.pos_x,
          dy_mouse = p.y - pafePJS.interactivity.mouse.pos_y,
          dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse),
          ratio = 1 - dist_mouse / pafePJS.interactivity.modes.bubble.distance;

      function init(){
        p.opacity_bubble = p.opacity;
        p.radius_bubble = p.radius;
      }

      /* mousemove - check ratio */
      if(dist_mouse <= pafePJS.interactivity.modes.bubble.distance){

        if(ratio >= 0 && pafePJS.interactivity.status == 'mousemove'){
          
          /* size */
          if(pafePJS.interactivity.modes.bubble.size != pafePJS.particles.size.value){

            if(pafePJS.interactivity.modes.bubble.size > pafePJS.particles.size.value){
              var size = p.radius + (pafePJS.interactivity.modes.bubble.size*ratio);
              if(size >= 0){
                p.radius_bubble = size;
              }
            }else{
              var dif = p.radius - pafePJS.interactivity.modes.bubble.size,
                  size = p.radius - (dif*ratio);
              if(size > 0){
                p.radius_bubble = size;
              }else{
                p.radius_bubble = 0;
              }
            }

          }

          /* opacity */
          if(pafePJS.interactivity.modes.bubble.opacity != pafePJS.particles.opacity.value){

            if(pafePJS.interactivity.modes.bubble.opacity > pafePJS.particles.opacity.value){
              var opacity = pafePJS.interactivity.modes.bubble.opacity*ratio;
              if(opacity > p.opacity && opacity <= pafePJS.interactivity.modes.bubble.opacity){
                p.opacity_bubble = opacity;
              }
            }else{
              var opacity = p.opacity - (pafePJS.particles.opacity.value-pafePJS.interactivity.modes.bubble.opacity)*ratio;
              if(opacity < p.opacity && opacity >= pafePJS.interactivity.modes.bubble.opacity){
                p.opacity_bubble = opacity;
              }
            }

          }

        }

      }else{
        init();
      }


      /* mouseleave */
      if(pafePJS.interactivity.status == 'mouseleave'){
        init();
      }
    
    }

    /* on click event */
    else if(pafePJS.interactivity.events.onclick.enable && isInArray('bubble', pafePJS.interactivity.events.onclick.mode)){


      if(pafePJS.tmp.bubble_clicking){
        var dx_mouse = p.x - pafePJS.interactivity.mouse.click_pos_x,
            dy_mouse = p.y - pafePJS.interactivity.mouse.click_pos_y,
            dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse),
            time_spent = (new Date().getTime() - pafePJS.interactivity.mouse.click_time)/1000;

        if(time_spent > pafePJS.interactivity.modes.bubble.duration){
          pafePJS.tmp.bubble_duration_end = true;
        }

        if(time_spent > pafePJS.interactivity.modes.bubble.duration*2){
          pafePJS.tmp.bubble_clicking = false;
          pafePJS.tmp.bubble_duration_end = false;
        }
      }


      function process(bubble_param, particles_param, p_obj_bubble, p_obj, id){

        if(bubble_param != particles_param){

          if(!pafePJS.tmp.bubble_duration_end){
            if(dist_mouse <= pafePJS.interactivity.modes.bubble.distance){
              if(p_obj_bubble != undefined) var obj = p_obj_bubble;
              else var obj = p_obj;
              if(obj != bubble_param){
                var value = p_obj - (time_spent * (p_obj - bubble_param) / pafePJS.interactivity.modes.bubble.duration);
                if(id == 'size') p.radius_bubble = value;
                if(id == 'opacity') p.opacity_bubble = value;
              }
            }else{
              if(id == 'size') p.radius_bubble = undefined;
              if(id == 'opacity') p.opacity_bubble = undefined;
            }
          }else{
            if(p_obj_bubble != undefined){
              var value_tmp = p_obj - (time_spent * (p_obj - bubble_param) / pafePJS.interactivity.modes.bubble.duration),
                  dif = bubble_param - value_tmp;
                  value = bubble_param + dif;
              if(id == 'size') p.radius_bubble = value;
              if(id == 'opacity') p.opacity_bubble = value;
            }
          }

        }

      }

      if(pafePJS.tmp.bubble_clicking){
        /* size */
        process(pafePJS.interactivity.modes.bubble.size, pafePJS.particles.size.value, p.radius_bubble, p.radius, 'size');
        /* opacity */
        process(pafePJS.interactivity.modes.bubble.opacity, pafePJS.particles.opacity.value, p.opacity_bubble, p.opacity, 'opacity');
      }

    }

  };


  pafePJS.fn.modes.repulseParticle = function(p){

    if(pafePJS.interactivity.events.onhover.enable && isInArray('repulse', pafePJS.interactivity.events.onhover.mode) && pafePJS.interactivity.status == 'mousemove') {

      var dx_mouse = p.x - pafePJS.interactivity.mouse.pos_x,
          dy_mouse = p.y - pafePJS.interactivity.mouse.pos_y,
          dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse);

      var normVec = {x: dx_mouse/dist_mouse, y: dy_mouse/dist_mouse},
          repulseRadius = pafePJS.interactivity.modes.repulse.distance,
          velocity = 100,
          repulseFactor = clamp((1/repulseRadius)*(-1*Math.pow(dist_mouse/repulseRadius,2)+1)*repulseRadius*velocity, 0, 50);
      
      var pos = {
        x: p.x + normVec.x * repulseFactor,
        y: p.y + normVec.y * repulseFactor
      }

      if(pafePJS.particles.move.out_mode == 'bounce'){
        if(pos.x - p.radius > 0 && pos.x + p.radius < pafePJS.canvas.w) p.x = pos.x;
        if(pos.y - p.radius > 0 && pos.y + p.radius < pafePJS.canvas.h) p.y = pos.y;
      }else{
        p.x = pos.x;
        p.y = pos.y;
      }
    
    }


    else if(pafePJS.interactivity.events.onclick.enable && isInArray('repulse', pafePJS.interactivity.events.onclick.mode)) {

      if(!pafePJS.tmp.repulse_finish){
        pafePJS.tmp.repulse_count++;
        if(pafePJS.tmp.repulse_count == pafePJS.particles.array.length){
          pafePJS.tmp.repulse_finish = true;
        }
      }

      if(pafePJS.tmp.repulse_clicking){

        var repulseRadius = Math.pow(pafePJS.interactivity.modes.repulse.distance/6, 3);

        var dx = pafePJS.interactivity.mouse.click_pos_x - p.x,
            dy = pafePJS.interactivity.mouse.click_pos_y - p.y,
            d = dx*dx + dy*dy;

        var force = -repulseRadius / d * 1;

        function process(){

          var f = Math.atan2(dy,dx);
          p.vx = force * Math.cos(f);
          p.vy = force * Math.sin(f);

          if(pafePJS.particles.move.out_mode == 'bounce'){
            var pos = {
              x: p.x + p.vx,
              y: p.y + p.vy
            }
            if (pos.x + p.radius > pafePJS.canvas.w) p.vx = -p.vx;
            else if (pos.x - p.radius < 0) p.vx = -p.vx;
            if (pos.y + p.radius > pafePJS.canvas.h) p.vy = -p.vy;
            else if (pos.y - p.radius < 0) p.vy = -p.vy;
          }

        }

        // default
        if(d <= repulseRadius){
          process();
        }

        // bang - slow motion mode
        // if(!pafePJS.tmp.repulse_finish){
        //   if(d <= repulseRadius){
        //     process();
        //   }
        // }else{
        //   process();
        // }
        

      }else{

        if(pafePJS.tmp.repulse_clicking == false){

          p.vx = p.vx_i;
          p.vy = p.vy_i;
        
        }

      }

    }

  }


  pafePJS.fn.modes.grabParticle = function(p){

    if(pafePJS.interactivity.events.onhover.enable && pafePJS.interactivity.status == 'mousemove'){

      var dx_mouse = p.x - pafePJS.interactivity.mouse.pos_x,
          dy_mouse = p.y - pafePJS.interactivity.mouse.pos_y,
          dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse);

      /* draw a line between the cursor and the particle if the distance between them is under the config distance */
      if(dist_mouse <= pafePJS.interactivity.modes.grab.distance){

        var opacity_line = pafePJS.interactivity.modes.grab.line_linked.opacity - (dist_mouse / (1/pafePJS.interactivity.modes.grab.line_linked.opacity)) / pafePJS.interactivity.modes.grab.distance;

        if(opacity_line > 0){

          /* style */
          var color_line = pafePJS.particles.line_linked.color_rgb_line;
          pafePJS.canvas.ctx.strokeStyle = 'rgba('+color_line.r+','+color_line.g+','+color_line.b+','+opacity_line+')';
          pafePJS.canvas.ctx.lineWidth = pafePJS.particles.line_linked.width;
          //pafePJS.canvas.ctx.lineCap = 'round'; /* performance issue */
          
          /* path */
          pafePJS.canvas.ctx.beginPath();
          pafePJS.canvas.ctx.moveTo(p.x, p.y);
          pafePJS.canvas.ctx.lineTo(pafePJS.interactivity.mouse.pos_x, pafePJS.interactivity.mouse.pos_y);
          pafePJS.canvas.ctx.stroke();
          pafePJS.canvas.ctx.closePath();

        }

      }

    }

  };



  /* ---------- pafePJS functions - vendors ------------ */

  pafePJS.fn.vendors.eventsListeners = function(){

    /* events target element */
    if(pafePJS.interactivity.detect_on == 'window'){
      pafePJS.interactivity.el = window;
    }else{
      pafePJS.interactivity.el = pafePJS.canvas.el;
    }


    /* detect mouse pos - on hover / click event */
    if(pafePJS.interactivity.events.onhover.enable || pafePJS.interactivity.events.onclick.enable){

      /* el on mousemove */
      pafePJS.interactivity.el.addEventListener('mousemove', function(e){

        if(pafePJS.interactivity.el == window){
          var pos_x = e.clientX,
              pos_y = e.clientY;
        }
        else{
          var pos_x = e.offsetX || e.clientX,
              pos_y = e.offsetY || e.clientY;
        }

        pafePJS.interactivity.mouse.pos_x = pos_x;
        pafePJS.interactivity.mouse.pos_y = pos_y;

        if(pafePJS.tmp.retina){
          pafePJS.interactivity.mouse.pos_x *= pafePJS.canvas.pxratio;
          pafePJS.interactivity.mouse.pos_y *= pafePJS.canvas.pxratio;
        }

        pafePJS.interactivity.status = 'mousemove';

      });

      /* el on onmouseleave */
      pafePJS.interactivity.el.addEventListener('mouseleave', function(e){

        pafePJS.interactivity.mouse.pos_x = null;
        pafePJS.interactivity.mouse.pos_y = null;
        pafePJS.interactivity.status = 'mouseleave';

      });

    }

    /* on click event */
    if(pafePJS.interactivity.events.onclick.enable){

      pafePJS.interactivity.el.addEventListener('click', function(){

        pafePJS.interactivity.mouse.click_pos_x = pafePJS.interactivity.mouse.pos_x;
        pafePJS.interactivity.mouse.click_pos_y = pafePJS.interactivity.mouse.pos_y;
        pafePJS.interactivity.mouse.click_time = new Date().getTime();

        if(pafePJS.interactivity.events.onclick.enable){

          switch(pafePJS.interactivity.events.onclick.mode){

            case 'push':
              if(pafePJS.particles.move.enable){
                pafePJS.fn.modes.pushParticles(pafePJS.interactivity.modes.push.particles_nb, pafePJS.interactivity.mouse);
              }else{
                if(pafePJS.interactivity.modes.push.particles_nb == 1){
                  pafePJS.fn.modes.pushParticles(pafePJS.interactivity.modes.push.particles_nb, pafePJS.interactivity.mouse);
                }
                else if(pafePJS.interactivity.modes.push.particles_nb > 1){
                  pafePJS.fn.modes.pushParticles(pafePJS.interactivity.modes.push.particles_nb);
                }
              }
            break;

            case 'remove':
              pafePJS.fn.modes.removeParticles(pafePJS.interactivity.modes.remove.particles_nb);
            break;

            case 'bubble':
              pafePJS.tmp.bubble_clicking = true;
            break;

            case 'repulse':
              pafePJS.tmp.repulse_clicking = true;
              pafePJS.tmp.repulse_count = 0;
              pafePJS.tmp.repulse_finish = false;
              setTimeout(function(){
                pafePJS.tmp.repulse_clicking = false;
              }, pafePJS.interactivity.modes.repulse.duration*1000)
            break;

          }

        }

      });
        
    }


  };

  pafePJS.fn.vendors.densityAutoParticles = function(){

    if(pafePJS.particles.number.density.enable){

      /* calc area */
      var area = pafePJS.canvas.el.width * pafePJS.canvas.el.height / 1000;
      if(pafePJS.tmp.retina){
        area = area/(pafePJS.canvas.pxratio*2);
      }

      /* calc number of particles based on density area */
      var nb_particles = area * pafePJS.particles.number.value / pafePJS.particles.number.density.value_area;

      /* add or remove X particles */
      var missing_particles = pafePJS.particles.array.length - nb_particles;
      if(missing_particles < 0) pafePJS.fn.modes.pushParticles(Math.abs(missing_particles));
      else pafePJS.fn.modes.removeParticles(missing_particles);

    }

  };


  pafePJS.fn.vendors.checkOverlap = function(p1, position){
    for(var i = 0; i < pafePJS.particles.array.length; i++){
      var p2 = pafePJS.particles.array[i];

      var dx = p1.x - p2.x,
          dy = p1.y - p2.y,
          dist = Math.sqrt(dx*dx + dy*dy);

      if(dist <= p1.radius + p2.radius){
        p1.x = position ? position.x : Math.random() * pafePJS.canvas.w;
        p1.y = position ? position.y : Math.random() * pafePJS.canvas.h;
        pafePJS.fn.vendors.checkOverlap(p1);
      }
    }
  };


  pafePJS.fn.vendors.createSvgImg = function(p){

    /* set color to svg element */
    var svgXml = pafePJS.tmp.source_svg,
        rgbHex = /#([0-9A-F]{3,6})/gi,
        coloredSvgXml = svgXml.replace(rgbHex, function (m, r, g, b) {
          if(p.color.rgb){
            var color_value = 'rgba('+p.color.rgb.r+','+p.color.rgb.g+','+p.color.rgb.b+','+p.opacity+')';
          }else{
            var color_value = 'hsla('+p.color.hsl.h+','+p.color.hsl.s+'%,'+p.color.hsl.l+'%,'+p.opacity+')';
          }
          return color_value;
        });

    /* prepare to create img with colored svg */
    var svg = new Blob([coloredSvgXml], {type: 'image/svg+xml;charset=utf-8'}),
        DOMURL = window.URL || window.webkitURL || window,
        url = DOMURL.createObjectURL(svg);

    /* create particle img obj */
    var img = new Image();
    img.addEventListener('load', function(){
      p.img.obj = img;
      p.img.loaded = true;
      DOMURL.revokeObjectURL(url);
      pafePJS.tmp.count_svg++;
    });
    img.src = url;

  };


  pafePJS.fn.vendors.destroypJS = function(){
    cancelAnimationFrame(pafePJS.fn.drawAnimFrame);
    canvas_el.remove();
    pJSDom = null;
  };


  pafePJS.fn.vendors.drawShape = function(c, startX, startY, sideLength, sideCountNumerator, sideCountDenominator){

    // By Programming Thomas - https://programmingthomas.wordpress.com/2013/04/03/n-sided-shapes/
    var sideCount = sideCountNumerator * sideCountDenominator;
    var decimalSides = sideCountNumerator / sideCountDenominator;
    var interiorAngleDegrees = (180 * (decimalSides - 2)) / decimalSides;
    var interiorAngle = Math.PI - Math.PI * interiorAngleDegrees / 180; // convert to radians
    c.save();
    c.beginPath();
    c.translate(startX, startY);
    c.moveTo(0,0);
    for (var i = 0; i < sideCount; i++) {
      c.lineTo(sideLength,0);
      c.translate(sideLength,0);
      c.rotate(interiorAngle);
    }
    //c.stroke();
    c.fill();
    c.restore();

  };

  pafePJS.fn.vendors.exportImg = function(){
    window.open(pafePJS.canvas.el.toDataURL('image/png'), '_blank');
  };


  pafePJS.fn.vendors.loadImg = function(type){

    pafePJS.tmp.img_error = undefined;

    if(pafePJS.particles.shape.image.src != ''){

      if(type == 'svg'){

        var xhr = new XMLHttpRequest();
        xhr.open('GET', pafePJS.particles.shape.image.src);
        xhr.onreadystatechange = function (data) {
          if(xhr.readyState == 4){
            if(xhr.status == 200){
              pafePJS.tmp.source_svg = data.currentTarget.response;
              pafePJS.fn.vendors.checkBeforeDraw();
            }else{
              console.log('Error pafePJS - Image not found');
              pafePJS.tmp.img_error = true;
            }
          }
        }
        xhr.send();

      }else{

        var img = new Image();
        img.addEventListener('load', function(){
          pafePJS.tmp.img_obj = img;
          pafePJS.fn.vendors.checkBeforeDraw();
        });
        img.src = pafePJS.particles.shape.image.src;

      }

    }else{
      console.log('Error pafePJS - No image.src');
      pafePJS.tmp.img_error = true;
    }

  };


  pafePJS.fn.vendors.draw = function(){

    if(pafePJS.particles.shape.type == 'image'){

      if(pafePJS.tmp.img_type == 'svg'){

        if(pafePJS.tmp.count_svg >= pafePJS.particles.number.value){
          pafePJS.fn.particlesDraw();
          if(!pafePJS.particles.move.enable) cancelRequestAnimFrame(pafePJS.fn.drawAnimFrame);
          else pafePJS.fn.drawAnimFrame = requestAnimFrame(pafePJS.fn.vendors.draw);
        }else{
          //console.log('still loading...');
          if(!pafePJS.tmp.img_error) pafePJS.fn.drawAnimFrame = requestAnimFrame(pafePJS.fn.vendors.draw);
        }

      }else{

        if(pafePJS.tmp.img_obj != undefined){
          pafePJS.fn.particlesDraw();
          if(!pafePJS.particles.move.enable) cancelRequestAnimFrame(pafePJS.fn.drawAnimFrame);
          else pafePJS.fn.drawAnimFrame = requestAnimFrame(pafePJS.fn.vendors.draw);
        }else{
          if(!pafePJS.tmp.img_error) pafePJS.fn.drawAnimFrame = requestAnimFrame(pafePJS.fn.vendors.draw);
        }

      }

    }else{
      pafePJS.fn.particlesDraw();
      if(!pafePJS.particles.move.enable) cancelRequestAnimFrame(pafePJS.fn.drawAnimFrame);
      else pafePJS.fn.drawAnimFrame = requestAnimFrame(pafePJS.fn.vendors.draw);
    }

  };


  pafePJS.fn.vendors.checkBeforeDraw = function(){

    // if shape is image
    if(pafePJS.particles.shape.type == 'image'){

      if(pafePJS.tmp.img_type == 'svg' && pafePJS.tmp.source_svg == undefined){
        pafePJS.tmp.checkAnimFrame = requestAnimFrame(check);
      }else{
        //console.log('images loaded! cancel check');
        cancelRequestAnimFrame(pafePJS.tmp.checkAnimFrame);
        if(!pafePJS.tmp.img_error){
          pafePJS.fn.vendors.init();
          pafePJS.fn.vendors.draw();
        }
        
      }

    }else{
      pafePJS.fn.vendors.init();
      pafePJS.fn.vendors.draw();
    }

  };


  pafePJS.fn.vendors.init = function(){

    /* init canvas + particles */
    pafePJS.fn.retinaInit();
    pafePJS.fn.canvasInit();
    pafePJS.fn.canvasSize();
    pafePJS.fn.canvasPaint();
    pafePJS.fn.particlesCreate();
    pafePJS.fn.vendors.densityAutoParticles();

    /* particles.line_linked - convert hex colors to rgb */
    pafePJS.particles.line_linked.color_rgb_line = hexToRgb(pafePJS.particles.line_linked.color);

  };


  pafePJS.fn.vendors.start = function(){

    if(isInArray('image', pafePJS.particles.shape.type)){
      pafePJS.tmp.img_type = pafePJS.particles.shape.image.src.substr(pafePJS.particles.shape.image.src.length - 3);
      pafePJS.fn.vendors.loadImg(pafePJS.tmp.img_type);
    }else{
      pafePJS.fn.vendors.checkBeforeDraw();
    }

  };




  /* ---------- pafePJS - start ------------ */


  pafePJS.fn.vendors.eventsListeners();

  pafePJS.fn.vendors.start();
  


};

/* ---------- global functions - vendors ------------ */

Object.deepExtend = function(destination, source) {
  for (var property in source) {
    if (source[property] && source[property].constructor &&
     source[property].constructor === Object) {
      destination[property] = destination[property] || {};
      arguments.callee(destination[property], source[property]);
    } else {
      destination[property] = source[property];
    }
  }
  return destination;
};

window.requestAnimFrame = (function(){
  return  window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame    ||
    window.oRequestAnimationFrame      ||
    window.msRequestAnimationFrame     ||
    function(callback){
      window.setTimeout(callback, 1000 / 60);
    };
})();

window.cancelRequestAnimFrame = ( function() {
  return window.cancelAnimationFrame         ||
    window.webkitCancelRequestAnimationFrame ||
    window.mozCancelRequestAnimationFrame    ||
    window.oCancelRequestAnimationFrame      ||
    window.msCancelRequestAnimationFrame     ||
    clearTimeout
} )();

function hexToRgb(hex){
  // By Tim Down - http://stackoverflow.com/a/5624139/3493650
  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
  hex = hex.replace(shorthandRegex, function(m, r, g, b) {
     return r + r + g + g + b + b;
  });
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
      r: parseInt(result[1], 16),
      g: parseInt(result[2], 16),
      b: parseInt(result[3], 16)
  } : null;
};

function clamp(number, min, max) {
  return Math.min(Math.max(number, min), max);
};

function isInArray(value, array) {
  return array.indexOf(value) > -1;
}


/* ---------- particles.js functions - start ------------ */

window.pJSDom = [];

window.pafeParticlesJS = function(tag_id, params){

  //console.log(params);

  /* no string id? so it's object params, and set the id with default id */
  if(typeof(tag_id) != 'string'){
    params = tag_id;
    tag_id = 'particles-js';
  }

  /* no id? set the id to default id */
  if(!tag_id){
    tag_id = 'particles-js';
  }

  /* pafePJS elements */
  var pJS_tag = document.querySelector(tag_id),
      pJS_canvas_class = 'particles-js-canvas-el',
      exist_canvas = pJS_tag.getElementsByClassName(pJS_canvas_class);

  /* remove canvas if exists into the pafePJS target tag */
  if(exist_canvas.length){
    while(exist_canvas.length > 0){
      pJS_tag.removeChild(exist_canvas[0]);
    }
  }

  /* create canvas element */
  var canvas_el = document.createElement('canvas');
  canvas_el.className = pJS_canvas_class;

  /* set size canvas */
  canvas_el.style.width = "100%";
  canvas_el.style.height = "100%";

  /* append canvas */
  var canvas = document.querySelector(tag_id).appendChild(canvas_el);

  /* launch particle.js */
  if(canvas != null){
    pJSDom.push(new pafePJS(tag_id, params));
  }

};

window.pafeParticlesJS.load = function(tag_id, path_config_json, callback){

  /* load json config */
  var xhr = new XMLHttpRequest();
  xhr.open('GET', path_config_json);
  xhr.onreadystatechange = function (data) {
    if(xhr.readyState == 4){
      if(xhr.status == 200){
        var params = JSON.parse(data.currentTarget.response);
        window.pafeParticlesJS(tag_id, params);
        if(callback) callback();
      }else{
        console.log('Error pafePJS - XMLHttpRequest status: '+xhr.status);
        console.log('Error pafePJS - File config not found');
      }
    }
  };
  xhr.send();

};

jQuery(document).ready(function( $ ) {
	$('[data-pafe-particles]').each(function() { 

var particle_id = $(this).attr("data-pafe-particles");
var options_particles = JSON.parse(this.getAttribute('data-pafe-particles-options')),
	    quantity = options_particles.quantity,
      color = options_particles.particles_color,
      linked_color = options_particles.linked_color,
      hover_effect = options_particles.hover_effect,
      click_effect = options_particles.click_effect,
      particles_shape = options_particles.particles_shape,
      particles_size = options_particles.particles_size,
      particles_speed = options_particles.particles_speed,
      particles_image = options_particles.particles_image,
      particles_opacity = options_particles.particles_opacity;
      linked_opacity = options_particles.linked_opacity;


		pafeParticlesJS("[data-pafe-particles='"+ particle_id +"']", {
		  "particles": {
		    "number": {
		      "value": quantity,
		      "density": {
		        "enable": true,
		        "value_area": 800
		      }
		    },
		    "color": {
		      "value": color
		    },
		    "shape": {
		      "type": particles_shape,
		      "stroke": {
		        "width": 0,
		        "color": "#000000"
		      },
		      "polygon": {
		        "nb_sides": 5
		      },
		      "image": {
		        "src": particles_image,
		        "width": 100,
		        "height": 100
		      }
		    },
		    "opacity": {
		      "value": particles_opacity,
		      "random": false,
		      "anim": {
		        "enable": false,
		        "speed": 1,
		        "opacity_min": 0,
		        "sync": false
		      }
		    },
		    "size": {
		      "value": particles_size,
		      "random": true,
		      "anim": {
		        "enable": false,
		        "speed": 40,
		        "size_min": 0.1,
		        "sync": false
		      }
		    },
		    "line_linked": {
		      "enable": true,
		      "distance": 100,
		      "color": linked_color,
		      "opacity": linked_opacity,
		      "width": 1
		    },
		    "move": {
		      "enable": true,
		      "speed": particles_speed,
		      "direction": "none",
		      "random": false,
		      "straight": false,
		      "out_mode": "out",
		      "bounce": false,
		      "attract": {
		        "enable": false,
		        "rotateX": 600,
		        "rotateY": 1200
		      }
		    }
		  },
		  "interactivity": {
		    "detect_on": "canvas",
		    "events": {
		      "onhover": {
		        "enable": true,
		        "mode": hover_effect
		      },
		      "onclick": {
		        "enable": true,
		        "mode": click_effect
		      },
		      "resize": true
		    },
		    "modes": {
		      "grab": {
		        "distance": 140,
		        "line_linked": {
		          "opacity": 1
		        }
		      },
		      "bubble": {
		        "distance": 400,
		        "size": 10,
		        "duration": 2,
		        "opacity": 8,
		        "speed": 3
		      },
		      "repulse": {
		        "distance": 50,
		        "duration": 0.4
		      },
		      "push": {
		        "particles_nb": 10
		      },
		      "remove": {
		        "particles_nb": 10
		      }
		    }
		  },
		  "retina_detect": true
		});

	});  
});    
