var g_Canvas;
var g_Ctx;
// fps
var g_LastAnimationFrameTime = 0;
var g_LastFpsUpdateTime = 0;
var gMarioTexRight;
var gMarioTexLeft;
// key
var gSpacePush = false; // space
var gLeftPush = false;	// left
var gRightPush = false;	// right
var gUpPush = false;	// up
var gDownPush = false;	// down
// keyの定義
var SPACE_KEY = 32;
var LEFT_KEY = 37;
var RIGHT_KEY = 39;
var UP_KEY = 38;
var DOWN_KEY = 40;
//マリオ
var gMario;

/**
	onload
	最初に呼び出される関数
*/
onload = function () {
    // キャンバスに代入
    g_Canvas = document.getElementById('id_canvas'); 
    // cavasに対応していない
    if (!g_Canvas || !g_Canvas.getContext) {
        alert("html5に対応していないので、実行できません");
        return false;
    }
    g_Ctx = g_Canvas.getContext('2d');  // cox
    //マリオ画像設定
    loadTexture();
    gMario = new Mario(0,350);
    // キーの登録
    window.addEventListener('keydown', keyDown, true);
    window.addEventListener('keyup', keyUp, true);
    // loopスタート
    requestNextAnimationFrame(animate);
};

/*
   テクスチャのロード
*/
function loadTexture(){
    gMarioTexRight = new Image();
    gMarioTexRight.src = 'images/mario_right.png';
    gMarioTexLeft = new Image();
    gMarioTexLeft.src = 'images/mario_left.png';
 }

function animate(now) {
    move();
    // 描画
    Draw();
    requestNextAnimationFrame(animate);
}

/*
	60fps毎に処理を実行
*/
window.requestNextAnimationFrame =
(function () {
   var originalWebkitRequestAnimationFrame = undefined,
       wrapper = undefined,
       callback = undefined,
       geckoVersion = 0,
       userAgent = navigator.userAgent,
       index = 0,
       self = this;

   // Workaround for Chrome 10 bug where Chrome
   // does not pass the time to the animation function
   if (window.webkitRequestAnimationFrame) {
      // Define the wrapper
      wrapper = function (time) {
        if (time === undefined) {
           time = +new Date();
        }
        self.callback(time);
      };
      // Make the switch
      originalWebkitRequestAnimationFrame = window.webkitRequestAnimationFrame;
      window.webkitRequestAnimationFrame = function (callback, element) {
         self.callback = callback;
         // Browser calls the wrapper and wrapper calls the callback
         originalWebkitRequestAnimationFrame(wrapper, element);
      }
   }
   // Workaround for Gecko 2.0, which has a bug in
   // mozRequestAnimationFrame() that restricts animations
   // to 30-40 fps.
   if (window.mozRequestAnimationFrame) {
      // Check the Gecko version. Gecko is used by browsers
      // other than Firefox. Gecko 2.0 corresponds to
      // Firefox 4.0.
      index = userAgent.indexOf('rv:');
      if (userAgent.indexOf('Gecko') != -1) {
         geckoVersion = userAgent.substr(index + 3, 3);
         if (geckoVersion === '2.0') {
            // Forces the return statement to fall through
            // to the setTimeout() function.
            window.mozRequestAnimationFrame = undefined;
         }
      }
   }
   return window.requestAnimationFrame   ||
      window.webkitRequestAnimationFrame ||
      window.mozRequestAnimationFrame    ||
      window.oRequestAnimationFrame      ||
      window.msRequestAnimationFrame     ||
      function (callback, element) {
         var start,
             finish;
             
         window.setTimeout( function () {
            start = +new Date();
            callback(start);
            finish = +new Date();

            self.timeout = 1000 / 60 - (finish - start);

         }, self.timeout);
      };
   }
)
();

function move(){
	if(gLeftPush){
        // 左キーが押されている状態
        gMario.moveX(-4);
    }else if(gRightPush){
        // 右キーが押されている状態
        gMario.moveX(4);
	}else{
        // キーが押されていない状態
        gMario.moveX(0);
    }
}
/*
	Draw
	描画
*/
function Draw(){
    g_Ctx.fillStyle = "#66CCFF";    // 空
    g_Ctx.fillRect(0,0,640,430);
    g_Ctx.fillStyle = "#800000";    // 地面
    g_Ctx.fillRect(0,430,640,50);
    gMario.draw(g_Ctx,gMarioTexRight,gMarioTexLeft);
}

/*
	キーを押した時のイベント
*/
function keyDown(event) {
	var code = event.keyCode;       // どのキーが押されたか
	switch(code) {
	    // スペースキー
	    case SPACE_KEY:
            	// スクロールさせないため
            	event.returnValue = false;		// ie
            	event.preventDefault();		// firefox
	        gSpacePush = true;
	        break;
	    // 左キー
	    case LEFT_KEY:
	        gLeftPush = true;
	        break;
	    // 右キー
	    case RIGHT_KEY:
	        gRightPush = true;
	        break;
	    // 上キー
	    case UP_KEY:
            	event.returnValue = false;	// ie
            	event.preventDefault();	// firefox
		gUpPush = true;
	        break;
            // 下キー
	    case DOWN_KEY:
            	event.returnValue = false;	// ie
            	event.preventDefault();	// firefox
	    	gDownPush = true;
	        break;
	}
}
/*
	キーを離した時のイベント
*/
function keyUp(event){
	// どのキーが押されたか
	var code = event.keyCode;
	switch(code){
		case SPACE_KEY:
		gSpacePush = false;
		break;
		// 左キー
		case LEFT_KEY:
			gLeftPush = false;
			break;
		// 右キー
		case RIGHT_KEY:
			gRightPush = false;
			break;
		//　上キー
		case UP_KEY:
			gUpPush = false;
			break;
		// 下キー
		case DOWN_KEY:
			gDownKey = false;
			break;
	}
}