var extend = [%params%];



var forum = {
    initviewthread : false,
    initforumdisplay: false,
    initpage: function() {
        if(extend.fid) {
            forum.initforumdisplay(extend.fid);
            if(extend.init) {
                forum.loadthreadlist(extend.fid, 1, true);
            }
        } else {
            forum.initviewthread(extend.tid);
        }
        window.onpopstate = function(event){
            if(event && event.state){
                if(event.state.fid) {
                    forum.initforumdisplay(event.state.fid);
                    forum.loadthreadlist(event.state.fid, 1, true);
                } else {
                    forum.initviewthread(event.state.tid);
                }
                
            } 
        }
    },
    loadthreadlist: function(fid, pnum, newforum) {
        if(newforum) {
            $('.forumdisplay-content').show();
            $('.view-content').hide();
            var bcolor = $('.forum-nav li[data-fid="' + fid + '"]').children().attr("bstyle"); // by dashen
            $('.forum-nav li[data-fid="' + fid + '"]').children().attr("style", bcolor); // by dashen
            $('.forum-nav li[data-fid="' + fid + '"]').siblings().children().attr("style", ""); // by dashen
            $('.forum-nav li[data-fid="' + fid + '"]').addClass('active').siblings().removeClass('active');
            $('#SC_loading').show();
            $('.post_list_wrapper').html("");


            barhtml = template('bottombarTpl', {url: 'forum.php?mod=post&action=newthread&fid=' + fid, title: '发表' + $('.forum-nav li[data-fid="' + fid + '"]').find('p').text() + '贴'});
            $('#bottombar').html(barhtml);
        }

        if(!isNaN(fid)) {
            $.ajax({
                url: 'source/plugin/singcere_xn/api.php?module=forumdisplay&version=1',
                data: {fid: extend.fid, page: extend.page},
                dataType: 'json',
                success: function(data) {
                    if(extend.page <= data.Variables.page.realpage) {
                        $('.post_list_wrapper').append(template('threadlistTpl', data.Variables));
                        $('.threadpreview_thumb').css('height', (document.body.clientWidth - 50)/4);
                        $('#SC_loading').hide();
                        $("img.scrollLoading").scrollLoading();
                        extend.locked = false;
                    }
                }
            })
        } else {
            $.ajax({
                url: 'source/plugin/singcere_xn/api.php?module=xn_secret&version=1',
                data: {page: extend.page},
                dataType: 'json',
                success: function(data) {
                    $('.post_list_wrapper').append(template('secretlistTpl', data.Variables));
                    $('#SC_loading').hide();
                    $('.threadpreview_thumb').css('height', (document.body.clientWidth - 50)/4);
                    $("img.scrollLoading").scrollLoading();
                    extend.locked = false;
                }
            })
        }
        
    },
    initforumdisplay: function(fid) {
        extend.fid = fid;
        extend.page = 1;
        history.replaceState({fid: fid, page: 1}, null, document.location.href);
        $(window).bind('scroll', function() {
            if(!extend.locked && $(document).scrollTop() + $(window).height() > $(document).height() - 800) {
                extend.locked = true;
                forum.loadthreadlist(extend.fid, ++extend.page);
            }
        });

        $('.forum-nav li').die().live('click', function(ev) { 
            var selfid = $(this).attr('data-fid');
            var bcolor = $(this).children().attr("bstyle"); // by dashen
            $(this).addClass('active').siblings().removeClass('active');
            $(this).children().attr("style", bcolor); // by dashen
            $(this).siblings().children().attr("style", ""); // by dashen
            $('#SC_loading').show();
            $('.post_list_wrapper').html("");


            
            if(selfid != extend.fid) {
                history.pushState({fid:selfid, page:1}, null, $(this).find('a')[0].href);
                extend.fid = selfid;
                extend.page = 1;
            }

            
            forum.loadthreadlist(selfid, 1, true);
            ev.preventDefault();
        });

        


    },
    initviewthread: function(tid) {
        extend.tid = tid;
        history.replaceState({tid: extend.tid, page: 1}, null, document.location.href);
        // 设置tid, page 变量
        
        // 返回列表页
        // 下拉滚动

    },
    // loadthreadpage: function() {
    //     if(CURMODULE == 'viewthread' || !forum.initviewthread) {
    //         return;
    //     }

    //     TC.load(TMPLPATH + 'src/tmpl/viewthread.html');
    //     JC.load(TMPLPATH + 'src/css/viewthread.css');
    //     JC.load(TMPLPATH + 'src/css/post.css');
    //     forum.initviewthread = true;
    // },
    // loadpostlist: function(tid, pnum, newthread) {
    //     if(newthread) {
    //         $('.forumdisplay-content').hide();
    //         $('.view-content').html("").show();
    //         $('#SC_loading').show();
    //     }


    //     $.ajax({
    //         url: 'source/plugin/xn_appserver/appserver.php?module=viewthread&version=1',
    //         data: {tid: tid, page: pnum},
    //         dataType: 'json',
    //         success: function(data) {
    //             $('.view-content').append(template('fastpostTpl', data.Variables));
    //             $(".fastinputd").click(function(){  
    //                 $('#fastpostform').removeClass('none');  
    //                 $('.overlay').removeClass('none');
    //                 $("#fastpostmessage").focus(); 
    //             });


    //             $(".overlay").click(function(){  
    //                 $('#fastpostform').addClass('none');  
    //                 $('.overlay').addClass('none');
    //             });
    //         }
    //     });
    // }

}

$(function() {
    forum.initpage();
})

