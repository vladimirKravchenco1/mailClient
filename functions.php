<?php
/**
 * Created by PhpStorm.
 * User: Владимир
 * Date: 30.06.2016
 * Time: 17:56
 */
error_reporting (E_ERROR);
ini_set("display_errors", 1);

// Функци
function gmailConnect($user,$pass,  $dir = 'inbox'){
    $hostname = '{imap.gmail.com:993/imap/ssl}';
    $connect = imap_open($hostname,$user, $pass);
    switch($dir){
        case 'inbox': $GMAIL_DIR = 'INBOX'; break;
        case 'spam': $GMAIL_DIR = '[Gmail]/Spam'; break;
        case 'basket': $GMAIL_DIR = '[Gmail]/Trash'; break;
        default : $GMAIL_DIR = 'INBOX';
    }
    imap_reopen($connect, "{$hostname}$GMAIL_DIR") or die(implode(", ", imap_errors()));

    return $connect;
}


function check_utf8($charset){
    if(strtolower($charset) != "utf-8"){
        return false;
    }
    return true;
}

function convert_to_utf8($in_charset, $str){
    return iconv(strtolower($in_charset), "utf-8", $str);
}

function get_imap_title($str){
    $mime = imap_mime_header_decode($str);

    $title = "";
    foreach($mime as $key => $m){
        if(!check_utf8($m->charset) && strtolower($m->charset)!='default'){
            $title .= convert_to_utf8($m->charset, $m->text);
        }else{
            $title .= $m->text;
        }
    }
    return $title;
}

function recursive_search($structure){
    $encoding = "";
    $charset = "";
    if($structure->subtype == "HTML" || $structure->type == 0){
        if(strtolower($structure->parameters[0]->attribute) == "charset"){
            $charset = $structure->parameters[0]->value;
        }
        return array(
            "encoding" => $structure->encoding,
            "charset"  => strtolower($charset),
            "subtype"  => $structure->subtype
        );
    }else{
        if(isset($structure->parts[0])){
            return recursive_search($structure->parts[0]);
        }else{
            if(strtolower($structure->parameters[0]->attribute) == "charset"){
                $charset = $structure->parameters[0]->value;
            }
            return array(
                "encoding" => $structure->encoding,
                "charset"  => strtolower($charset),
                "subtype"  => $structure->subtype
            );
        }
    }
}


function structure_encoding($encoding, $msg_body){
    switch((int) $encoding){
        case 4:
            $body = imap_qprint($msg_body);
            break;
        case 3:
            $body = imap_base64($msg_body);
            break;
        case 2:
            $body = imap_binary($msg_body);
            break;
        case 1:
            $body = imap_8bit($msg_body);
            break;
        case 0:
            $body = $msg_body;
            break;
        default:
            $body = "";
            break;
    }
    return $body;
}


function getMessagesData($user, $pass, $dir, $from=0, $num = 20){
    $connect = gmailConnect($user,$pass,$dir);
    $msg_num = imap_sort($connect, SORTDATE, 1); //сортируем по дате
    $msg_num = array_slice($msg_num,0,20);
    $mails_data = array();
    foreach($msg_num as $i){
        //шапка
        $msg_header = imap_header($connect, $i);
        $mails_data[$i]["time"] = time($msg_header->MailDate);
        $mails_data[$i]["date"] = $msg_header->MailDate;
        $mails_data[$i]["title"] = get_imap_title($msg_header->subject);
        $mails_data[$i]["msg_num"] = $i;
        foreach($msg_header->to as $data){
            $mails_data[$i]["to"] = $data->mailbox."@".$data->host;
        }
        foreach($msg_header->from as $data){
            $mails_data[$i]["from"] = $data->mailbox."@".$data->host;
        }
    }
    imap_close($connect);
    return $mails_data;
}


function getMessageBodyById($user, $pass, $id, $dir){
    $connect = gmailConnect($user, $pass,$dir);
    if(!$connect){return false;}
    //шапка
    $msg_header = imap_header($connect, $id);
    $mails_data["time"] = time($msg_header->MailDate);
    $mails_data["date"] = $msg_header->MailDate;
    $mails_data["title"] = get_imap_title($msg_header->subject);
    $mails_data["msg_num"] = $id;

    foreach($msg_header->to as $data){
        $mails_data["to"] = $data->mailbox."@".$data->host;
    }
    foreach($msg_header->from as $data){
        $mails_data["from"] = $data->mailbox."@".$data->host;
    }

    // Тело письма
    $msg_structure = imap_fetchstructure($connect, $id);
    $recursive_data = recursive_search($msg_structure);
    $headersMess = imap_fetchbody($connect, $id, 0);
    $headersMess = iconv_mime_decode_headers($headersMess,0,'utf-8');
    if($headersMess['Authentication-Results'] && preg_match("#dmarc=pass#is",$headersMess['Authentication-Results'])){
        $dmark = "<div class='text-success'>Используется DMARK</div>";
    }else{
        $dmark = "<div class='text-error'>НЕ используется DMARK</div>";
    }


    $ct = $headersMess["Content-Type"];
    $cte = $headersMess["Content-Transfer-Encoding"];
    $contType = trim(explode(";",$ct)[0]);

    if($contType=='multipart/related') {
        $msg_body = imap_fetchbody($connect, $id, 1.1);
    }elseif($contType=='multipart/alternative'){
        $msg_body = imap_fetchbody($connect, $id, 1);
    }elseif($contType=='text/plain'){
        $msg_body = imap_fetchbody($connect, $id, 1);
    }elseif($contType=='text/html'){
        $msg_body = imap_fetchbody($connect, $id, 1.2);
    }else{
        $msg_body = imap_fetchbody($connect, $id, 1);
    }


    if($cte){
        switch($cte){
            case "8bit":$body = imap_8bit($msg_body); break;
            default:$body = $msg_body;
        }
    }else{
        $body = $msg_body;
    }
    if(imap_base64(quoted_printable_decode($body)) or imap_base64($body)){
        if(imap_base64(quoted_printable_decode($body))){
            $body = imap_base64(quoted_printable_decode($body));
        }else{
            $body = imap_base64($body);
        }

    }

    if(strtolower($recursive_data["charset"])!="utf-8"){
        $body = convert_to_utf8($recursive_data["charset"],quoted_printable_decode($body));
    }

    $mails_data["body"] = $body."<br>".$dmark;

    return $mails_data;
}