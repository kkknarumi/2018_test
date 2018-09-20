var LEFT_DIR = 0;
var RIGHT_DIR = 1;

function Mario(posX,posY){
	// 表示X座標
	this.posX = posX;
	// 表示Y座標
    this.posY = posY;
    // どのタイミングでアニメーションを切り替えるか
    this.animCnt = 0;
    // 切り出す始点のX
    this.animX = 0;
    // 方向を切り替えるための変数
    this.direction = RIGHT_DIR;
}
/*
	描画関数
	ctx:context
	texture:img class
*/
Mario.prototype.draw = function(ctx,right,left){
    //左右画像判定
    var direction_img;
    if(this.direction == 1){
        direction_img = right;
    }else{
        direction_img = left;
    }
    //枠からはみ出さない処理
    if(this.posX <= 0){
        this.posX = 0;
    }else if(this.posX > 560){
        this.posX = 560;
    }
    // context.drawImage(img,sx,sy,swidth,sheight,x,y,width,height);
    ctx.drawImage(direction_img,this.animX * 80,0,80,80,this.posX,this.posY,80,80);
}

Mario.prototype.moveX = function(moveX){
    // 移動方向を変える
    Number(moveX);
    if(moveX > 0){
        this.direction = RIGHT_DIR;
    }else if(moveX == 0){
        this.animCnt = 1;
        this.animX = 0;
    }else{
        this.direction = LEFT_DIR;
    }
    this.posX += moveX;
    // animation
    if(this.animCnt++ % 6 == 0){
        // 一定以上に達したらアニメーション更新する
        if(++this.animX > 2){
            this.animX = 0;
        }
    }
}