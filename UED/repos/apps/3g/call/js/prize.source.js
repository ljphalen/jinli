(function(){    
    $(function(){
        function parseAjaxUrl(url){
            var reg = /^dev.|demo.*/g;
            return reg.test(location.host) === true ? '/3g/' + url + '.php' : url;
        }

        var prize_stat = true;

        // 定义数组
        function GetSide(m,n){
            //初始化数组
            var arr = [];
            for(var i=0;i<m;i++){
                arr.push([]);
                for(var j=0;j<n;j++){
                    arr[i][j]=i*n+j;
                }
            }
            //获取数组最外圈
            var resultArr=[];
            var tempX=0,
             tempY=0,
             direction="Along",
             count=0;
            while(tempX>=0 && tempX<n && tempY>=0 && tempY<m && count<m*n){
                count++;
                resultArr.push([tempY,tempX]);
                if(direction=="Along"){
                    if(tempX==n-1)
                        tempY++;
                    else
                        tempX++;
                    if(tempX==n-1&&tempY==m-1)
                        direction="Inverse"
                }
                else{
                    if(tempX==0)
                        tempY--;
                    else
                        tempX--;
                    if(tempX==0&&tempY==0)
                        break;
                }
            }
            return resultArr;
        }

        var currIndex = 0, //当前开始转动位置
            prevIndex = 0, //前一位置
            endIndex=0, //决定在哪一格变慢
            speed = 300, //初始速度
            timeid, //定义对象
            arr = GetSide(3,3), //初始化数组
            cycle=0, //转动圈数  
            EndCycle=0, //计算圈数
            flag= false, //结束转动标志 
            quick=0, //加速
            btn = document.getElementById("prize-btn"),
            popmsg = {};
        
        function startPrize(hit){
            // 清除定时器
            clearInterval(timeid);
            cycle = 0;
            flag = false;
            // 
            endIndex=Math.floor(Math.random()*8);
            console.log(endIndex);
            //EndCycle=Math.floor(Math.random()*4);
            EndCycle = 1;
            timeid = setInterval(function(){Star(hit)},speed);
        }

        function Star(num){
            //跑马灯变速
            if(flag == false){
                //走3格开始加速
                if(quick == 3){
                    clearInterval(timeid);
                    speed = 50;
                    timeid = setInterval(function(){Star(num)},speed);
                }
                //跑N圈减速
                if(cycle == 4+Math.floor(Math.random()*4)/*EndCycle+1 && index == parseInt(endIndex)*/){
                    clearInterval(timeid);
                    speed=300;
                    flag=true; //触发结束         
                    timeid=setInterval(function(){Star(num)},speed);
                }
            }
            
            if(currIndex >= arr.length){
                currIndex=0;
                cycle++;
            }

            
           //结束转动并选中号码
           var hit = document.getElementById('prize_sel'+currIndex).getAttribute('data-id');
            if(flag == true && hit == num){
                quick=0;
                document.getElementById('prize-btn').className = 'prize-btn';
                showpoptips(popmsg.txt);
                $('#prize-score').html(popmsg.score);
                clearInterval(timeid);
            }
            if(hit == num){
                document.getElementById('prize_sel'+currIndex).className = 'prize-curr prize-hit';
            } else {
                document.getElementById('prize_sel'+currIndex).className = 'prize-curr';
            }

            if(currIndex > 0){
                prevIndex = currIndex-1;
            }
            else{
                prevIndex = arr.length-1;
            }

            document.getElementById('prize_sel'+prevIndex).className = 'prize-prev';

            currIndex++;
            quick++;
        }

        function showpoptips(str){
            $('.poptips .content').html(str);
            if(popmsg.code == -2){
                if(!$('.poptips .btn-link')[0]){
                    $('.poptips .button').append('<a class="btn-link" href="'+popmsg.link+'">获取积分</a>');
                }
            }
            $('.poptips').show();
        }

        $('.poptips .btn-ok').on('click', function(){
            if(popmsg.code == -400){
                location.reload();
            }
            $('.poptips').hide();
        });

        $('#prize-btn').on('click',function(){
            if(parseInt($('#prize-num').html()) > 0){
                if(prize_stat === true && !$(this).hasClass('prize-btn-active')){
                    $(this).addClass('prize-btn-active');
                    prize_stat = false;
                    $.ajax({
                        url: parseAjaxUrl('/user/lottery/ajaxDrawing'),
                        type: 'get',
                        dataType: 'json',
                        success: function(data){
                            popmsg = {};
                            prize_stat = true;
                            if(data.success){
                                if(data.data.redirect){
                                    location.href = data.data.redirect;
                                    return;
                                }
                                $('#prize-num').html(data.data.prize_num);
                                var pscore = parseInt($('#prize-score').html());
                                $('#prize-score').html(pscore-data.data.prize_costs);
                                startPrize(data.data.prize_hit);
                                popmsg.txt = data.data.prize_msg;
                                popmsg.score = data.data.score;
                            } else {
                                if(data.data.prize_error_code == -2){
                                    popmsg.code = -2;
                                    popmsg.link = data.data.prize_link;
                                    showpoptips(data.data.prize_msg);
                                } else {
                                    showpoptips(data.data.prize_msg);
                                }
                                $('.prize-btn').removeClass('prize-btn-active');
                            }

                        },
                        error: function(){
                            popmsg.code = -400;
                            showpoptips('数据请求失败，请刷新页面！');
                            $('.prize-btn').removeClass('prize-btn-active');
                        }
                    });
                }
            } else {
                showpoptips('今日抽奖次数已用完！');
            }
        });
    });
})();
