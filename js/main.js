var resultDiv = document.getElementById('container');
var userInfo = {};


// 设置全局参数
mdui.$.ajaxSetup({
    global : true,
    url    : './api.php',
    method : 'POST'
});

mdui.$(document).ajaxError(function() {
    mdui.alert('向服务器请求数据时失败', '出错啦！');
});


// 初始化用户数据和项目数据
mdui.$(function () {
    mdui.$.ajax({
        data : {
            mod : 'getUserId'
        },
        success : function (data) {
            let json = undefined;

            try {
                json = JSON.parse(data)
            } catch (e) {
                mdui.alert('向服务器请求数据时失败', '出错啦！');
                console.error('解析服务器返回信息时失败：', e);
                resultDiv.innerHTML = `<center class="mdui-m-t-2 mdui-m-b-2">
                    <p class="mdui-text-color-theme-disabled"><span class="mdui-m-b-1" style="font-size:xx-large">|(*′口\`)</span><br><br>
                    请求数据失败了</p>
                    </center>`;
                
                return;
            }

            if (json.code != 200) {
                mdui.alert(json.msg, '出错啦！');
                resultDiv.innerHTML = `<center class="mdui-m-t-2 mdui-m-b-2">
                    <p class="mdui-text-color-theme-disabled"><span class="mdui-m-b-1" style="font-size:xx-large">|(*′口\`)</span><br><br>
                    请求数据失败了</p>
                    </center>`;
                
                return;
            }
            
            if (getCookie('userid') != json.data.userid) {
                setCookie('userid', json.data.userid);
            }

            userInfo = json.data;

            mdui.$.ajax({
                data : {
                    mod : 'getItemList'
                },
                success : function (data) {
                    let json = undefined;
                    let result = '';
    
                    try {
                        json = JSON.parse(data)
                    } catch (e) {
                        mdui.alert('向服务器请求数据时失败', '出错啦！');
                        console.error('解析服务器返回信息时失败：', e);
                        resultDiv.innerHTML = `<center class="mdui-m-t-2 mdui-m-b-2">
                            <p class="mdui-text-color-theme-disabled"><span class="mdui-m-b-1" style="font-size:xx-large">|(*′口\`)</span><br><br>
                            请求数据失败了</p>
                            </center>`;

                        return;
                    }
    
                    if (json.code != 200) {
                        mdui.alert(json.msg, '出错啦！');
                        resultDiv.innerHTML = `<center class="mdui-m-t-2 mdui-m-b-2">
                            <p class="mdui-text-color-theme-disabled"><span class="mdui-m-b-1" style="font-size:xx-large">|(*′口\`)</span><br><br>
                            请求数据失败了</p>
                            </center>`;
    
                        return;
                    }
    
                    result = `<div class="mdui-m-t-2 mdui-m-b-1 mdui-row-xs-1 mdui-row-sm-2 mdui-row-md-3">`;
                    
                    for (let i = 0; i < json.data.length; i++) {
                        let itemInfo = json.data[i];

                        result += `<div class="mdui-col">
                            <section class="mdui-card mdui-m-b-1 mdui-hoverable">
                            <div class="mdui-card-media" style="background-image: url('` + itemInfo.picture + `')"></div>`;
                        
                        if (totalPerson > 0) {
                            result += `<div class="mdui-progress">
                            <div class="mdui-progress-determinate" style="width: ` + (itemInfo.votes / (totalPerson * votesPerPerson)).toFixed(2) * 100 + `%;" id="progress_bar_` + itemInfo.id + `"></div>
                            </div>`;
                        }
                            
                        result += `<div class="mdui-card-primary">
                            <h2 class="mdui-text-truncate mdui-m-t-0 mdui-m-b-0" mdui-tooltip="{content:'` + itemInfo.name + `'}">` + itemInfo.name + `</h2>
                            <p class="mdui-text-truncate" id="vote_text_` + itemInfo.id + `" mdui-tooltip="{content:'` + itemInfo.votes + ` 次投票'}">` + itemInfo.votes + ` 次投票`;
                        
                        if (totalPerson > 0) {
                            result += '&nbsp;&nbsp;' + (itemInfo.votes / (totalPerson * votesPerPerson)).toFixed(2) * 100 + '%';
                        }

                        result += `</p>`;
                        
                        if (userInfo.votes.length >= 1) {
                            let isVoted = false;

                            for (let x = 0; x < userInfo.votes.length; x++) {
                                if (userInfo.votes[x] == itemInfo.id) {
                                    isVoted = true;
                                    break;
                                }
                            }
                            
                            if (isVoted) {
                                result += `<button onclick="UnvoteItem(this, ` + itemInfo.id + `)" class="mdui-btn mdui-ripple mdui-color-red-600">取消投票</button>`;
                            } else {
                                result += `<button onclick="VoteItem(this, ` + itemInfo.id + `)"  class="mdui-btn mdui-ripple mdui-color-green-600">投票</button>`;
                            }

                        } else {
                            result += `<button onclick="VoteItem(this, ` + itemInfo.id + `)"  class="mdui-btn mdui-ripple mdui-color-green-600">投票</button>`;
                        }
                        
                        result += `</div>
                            </section>
                            </div>`;
                    }

                    result += `</div>`;
                    
                    resultDiv.innerHTML = result;
                }
            });
        }
    });
});

// 给项目投票
function VoteItem(button, id) {
    if (userInfo.votes.length >= votesPerPerson) {
        mdui.alert('你已经投完了所有的票，不能再投了！', '前方高能');
        return;
    }

    button.disabled = true;
    button.innerHTML = '<div class="mdui-spinner mdui-spinner-colorful mdui-m-t-1" style="width:20px;height:20px"></div>';

    mdui.mutation(button);

    mdui.$.ajax({
        data : {
            mod : 'voteItem',
            id  : id
        },
        success : function(data) {
            let json = undefined;

            try {
                json = JSON.parse(data);
            } catch (e) {
                mdui.alert('向服务器请求数据时失败', '出错啦！');
                console.error('解析服务器返回信息时失败：', e);
                button.disabled = false;
                button.innerHTML = '投票';

                return;
            }

            if (json.code != 200) {
                mdui.alert(json.msg, '出错啦！');
                button.disabled = false;
                button.innerHTML = '投票';

                return;
            }

            userInfo.votes.push(id.toString());

            mdui.$(button).removeClass('mdui-color-green-600');
            mdui.$(button).addClass('mdui-color-red-600');
            mdui.$(button).attr('onclick', 'UnvoteItem(this,' + id + ')');

            let _voteText = json.data.votes + ' 次投票';

            if (totalPerson > 0) {
                _voteText += '&nbsp;&nbsp;' + (json.data.votes / (totalPerson * votesPerPerson)).toFixed(2) * 100 + '%';
                mdui.$('#progress_bar_' + id).css('width', (json.data.votes / (totalPerson * votesPerPerson)).toFixed(2) * 100 + '%');
            }

            mdui.$('#vote_text_' + id).html(_voteText);
            
            button.disabled = false;
            button.innerHTML = '取消投票';

            mdui.snackbar({
                message : '投票成功'
            });
        }
    });
}

// 给项目取消投票
function UnvoteItem(button, id) {
    if (userInfo.votes.length <= 0) {
        mdui.alert('你还没有进行投票，无法取消投票！', '前方高能');
        return;
    }

    button.disabled = true;
    button.innerHTML = '<div class="mdui-spinner mdui-spinner-colorful mdui-m-t-1" style="width:20px;height:20px"></div>';

    mdui.mutation(button);

    mdui.$.ajax({
        data : {
            mod : 'unvoteItem',
            id  : id
        },
        success : function(data) {
            let json = undefined;

            try {
                json = JSON.parse(data);
            } catch (e) {
                mdui.alert('向服务器请求数据时失败', '出错啦！');
                console.error('解析服务器返回信息时失败：', e);
                button.disabled = false;
                button.innerHTML = '取消投票';

                return;
            }

            if (json.code != 200) {
                mdui.alert(json.msg, '出错啦！');
                button.disabled = false;
                button.innerHTML = '取消投票';

                return;
            }

            userInfo.votes.splice(userInfo.votes.indexOf(id.toString()), 1);

            mdui.$(button).removeClass('mdui-color-red-600');
            mdui.$(button).addClass('mdui-color-green-600');
            mdui.$(button).attr('onclick', 'VoteItem(this,' + id + ')');

            let _voteText = json.data.votes + ' 次投票';

            if (totalPerson > 0) {
                _voteText += '&nbsp;&nbsp;' + (json.data.votes / (totalPerson * votesPerPerson)).toFixed(2) * 100 + '%';
                mdui.$('#progress_bar_' + id).css('width', (json.data.votes / (totalPerson * votesPerPerson)).toFixed(2) * 100 + '%');
            }

            mdui.$('#vote_text_' + id).html(_voteText);

            button.disabled = false;
            button.innerHTML = '投票';

            mdui.snackbar({
                message : '取消投票成功'
            })
        }
    });

}

function setCookie(name,value) {
    var Days = 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days*24*60*60*1000);
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}

function getCookie(name) {
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
    if(arr=document.cookie.match(reg)){
        return unescape(arr[2]);

    }else{
        return null;

    }  
}

