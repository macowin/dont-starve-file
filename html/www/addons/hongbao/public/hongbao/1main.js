var oChai = document.getElementById("chai");
var oClose = document.getElementById("close");
var oContainer = document.getElementById("container");
var showmain = document.getElementById("showmain");

/*if(!localStorage.getnum){
        var getnum = GetRandomNum(20,100);
        localStorage.getnum = getnum;
    }else{
        var getnum = localStorage.getnum;
    }

    
    
    //showmsg(getnum); 
    document.getElementById('get_money').innerHTML=getnum;
    document.getElementById('get_money2').innerHTML=getnum;
    
    


	var oBtn = document.getElementById('button');
    oBtn.onclick = function(){
        document.getElementById('shade').style.display="block";
        //showmsg("转发到三个不同微信群即可领取现金红包！");
    };
*/

    

      //oContainer.remove();
      showmain.style.display = "none";


      oChai.onclick = function() {
        oChai.setAttribute("class", "rotate");
        setTimeout(function() {
          oContainer.remove();
          showmain.style.display = "";
          //location.href = "open.html";
          //showmsg('ok');
            
            var a=0;
       var gundong=setInterval(function(){
           a+=1;
           if(a>=96){
       	clearInterval(gundong);
       }
       	document.getElementById('get_money').innerHTML=a;
       },6);
            
            
        },
                   
        1500)
      }
      oClose.onclick = function() {
        //oContainer.style.display = "none";

      }


      function shade(){
        showmsg('点击右上角，选择“发送给朋友”<br/>分享到任意群即可！'); 
        }
      function GetRandomNum(Min,Max){   
        var Range = Max - Min;   
        var Rand = Math.random();   
        return(Min + Math.round(Rand * Range));   
        } 
      function showmsg(data) {
        var html = '<div class="weui_dialog_alert" id="dialog">' + '<div class="weui_mask"></div>' + '<div class="weui_dialog">' + '<div class="weui_dialog_hd"><strong class="weui_dialog_title">提示消息</strong></div>' + '<div class="weui_dialog_bd">' + data + '</div>' + '<div class="weui_dialog_ft">' + '<a href="javascript:;"  onclick="close_msg()" class="weui_btn_dialog primary">确定</a>' + '</div>' + '</div>' + '</div>';
        var body = document.body;
        var div = document.createElement("div");
        div.id = "mDiv";
        div.innerHTML = html;
        body.appendChild(div);

      }
      function close_msg(res) {
        document.getElementById("dialog").remove();

      }