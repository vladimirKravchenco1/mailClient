/**
 * Created by Владимир on 30.06.2016.
 */
var loading = false;
function loadAnimation(state){
    if(state==true){
        loading = true;
        $("#loading").show();
    }else{
        loading = false;
        $("#loading").hide();
    }
}

function userAuth(thiss){
    if(loading==true) return;
    loadAnimation(true);
    var form = thiss.serialize();
    $.ajax({
        type: 'POST',
        url: 'ajax.php?func=getGmailConnect',
        data: form,
        success: function(data) {
            data = $.parseJSON(data);
            if(data['state']=="true"){
                alert(data['data']);
                thiss[0].reset();
                $("#authForm").hide();
                $("#app").show(function(){
                    getMessages('inbox',$("ul.nav.nav-sidebar li.active"));
                });

            }else{
                alert(data['errors']);
            }
            loadAnimation(false);
        },
        error: function(xhr, str){
            alert('Возникла ошибка: ' + xhr.responseCode);
            loadAnimation(false);
        }
    });
    return false;
}

function getMessages(dir, thiss){
    if(loading==true) return;
    loadAnimation(true);
    thiss.parent().find('li').removeClass('active');
    thiss.addClass('active');
    $('input[name=dir]').val(dir);
    $.ajax({
        type: 'POST',
        url: 'ajax.php?func=getMessages',
        data: "dir="+dir,
        success: function(data) {
            data = $.parseJSON(data);
            if(data['state']=="true"){
                $('#messagesBlock').html(data['data']);
            }else{
                alert(data['errors']);
            }
            loadAnimation(false);
        },
        error: function(xhr, str){
            alert('Возникла ошибка: ' + xhr.responseCode);
            loadAnimation(false);
        }
    });
    return false;
}

function getMessageBody(id, thiss){
    if(thiss.find('.messageBody').length){
        thiss.find('.messageBody').remove();
        return false;
    }
    if(loading==true) return;
    loadAnimation(true);
    var dir = $('input[name=dir]').val();
    $.ajax({
        type: 'POST',
        url: 'ajax.php?func=getMessageBody',
        data: "id="+id+"&dir="+dir,
        success: function(data) {
            data = $.parseJSON(data);
            if(data['state']=="true"){
                thiss.append('<div class=messageBody>'+data['data']+"</div>");
                //alert(data['data'])
            }else{
                alert(data['errors']);
            }
            loadAnimation(false);
        },
        error: function(xhr, str){
            alert('Возникла ошибка: ' + xhr.responseCode);
            loadAnimation(false);
        }
    });
    return false;
}

function deleteMess(id, thiss){
    if(loading==true) return;
    loadAnimation(true);
    var dir = $('input[name=dir]').val();
    $.ajax({
        type: 'POST',
        url: 'ajax.php?func=deleteMess',
        data: "id="+id+"&dir="+dir,
        success: function(data) {
            data = $.parseJSON(data);
            if(data['state']=="true"){
                thiss.parent().parent('tr').remove();
                alert(data['data'])
            }else{
                alert(data['errors']);
            }
            loadAnimation(false);
        },
        error: function(xhr, str){
            alert('Возникла ошибка: ' + xhr.responseCode);
            loadAnimation(false);
        }
    });
    return false;
}
$('document').ready(function(){
    if($('#app').css('display')=='none' || $('#app').length==0) return;
    getMessages('inbox',$("ul.nav.nav-sidebar li.active"));
});