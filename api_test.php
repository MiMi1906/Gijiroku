<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

$db = dbConnect();
//url
$day = date('Y-m-d');
$day = date('2021-12-21');
$url = "https://kokkai.ndl.go.jp/api/meeting_list?maximumRecords=1&from={$day}&until={$day}&recordPacking=XML";
urlencode($url);
$xml_obj = simplexml_load_file($url);
$xml_ary = json_decode(json_encode($xml_obj), true);
$issueID_list = getIssueID($xml_ary);

if (!empty($issueID_list)) {
    if (is_array($issueID_list)) {
        print('check<br>');
        foreach ($issueID_list as $issueID) {
            $url = "https://kokkai.ndl.go.jp/api/speech?issueID={$issueID}";
            urlencode($url);
            $xml_obj = simplexml_load_file($url);
            $xml_ary = json_decode(json_encode($xml_obj), true);
            $i = 1;
            $reply_post_id = 0;
            $sql = 'SELECT MAX(thread_id) AS max FROM posts';
            $thread_max = $db->prepare($sql);
            $thread_max->execute();
            $thread_id = $thread_max->fetchColumn() + 1;
            if ($xml_ary['numberOfRecords'] == 1) {
                $speech = $xml_ary['records']['record']['recordData']['speechRecord']['speech'];
                $speech = preg_replace('/　+/', ' ', $speech);
                $speech = str_replace('――――◇―――――', "\n", $speech);
                $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, type, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :type, :created)';
                $message = $db->prepare($sql);
                $message->bindValue(':member_id', 15);
                $message->bindValue(':message', nl2br($speech));
                $message->bindValue(':reply_post_id', 0);
                $message->bindValue(':thread_id', $thread_id);
                $message->bindValue(':nice_num', 0);
                $message->bindValue(':quote_from_id', 0);
                $message->bindValue(':type', MSG_TYPE_RECORD);
                $message->bindValue(':created', date('Y/m/d H:i:s'));
                $message->execute();
                $sql = 'SELECT MAX(id) AS id FROM posts';
                $reply_post_id = $db->prepare($sql);
                $reply_post_id->execute();
                $reply_post_id = $reply_post_id->fetchColumn();
            } else {
                $i = 1;
                $reply_post_id = 0;
                $sql = 'SELECT MAX(thread_id) AS max FROM posts';
                $thread_max = $db->prepare($sql);
                $thread_max->execute();
                $thread_id = $thread_max->fetchColumn() + 1;
                foreach ($xml_ary['records']['record'] as $rcd) {
                    print('<h2>' . $rcd['recordData']['speechRecord']['speaker'] . '</h2>');
                    $speech = $rcd['recordData']['speechRecord']['speech'];
                    $speech = preg_replace('/　+/', ' ', $speech);
                    $speech = str_replace('――――◇―――――', "\n", $speech);
                    print('<p>' . nl2br($speech) . '</p>');
                    if ($i == 1) {
                        $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, type, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :type, :created)';
                        $message = $db->prepare($sql);
                        $message->bindValue(':member_id', 15);
                        $message->bindValue(':message', nl2br($speech));
                        $message->bindValue(':reply_post_id', 0);
                        $message->bindValue(':thread_id', $thread_id);
                        $message->bindValue(':nice_num', 0);
                        $message->bindValue(':quote_from_id', 0);
                        $message->bindValue(':type', MSG_TYPE_RECORD);
                        $message->bindValue(':created', date('Y/m/d H:i:s'));
                        $message->execute();
                    } else {
                        $res = '<a href="/profile/?id=' . '15' . '">@' . '国会会議録' . '</a> のスレッドへの返信';
                        $res_html = '<div class="thread_exp">' . $res . '</div>';
                        $sql = 'SELECT MAX(id) AS id FROM posts';
                        $reply_post_id = $db->prepare($sql);
                        $reply_post_id->execute();
                        $reply_post_id = $reply_post_id->fetchColumn();
                        $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, type, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :type, :created)';
                        $message = $db->prepare($sql);
                        $message->bindValue(':member_id', 17);
                        $message->bindValue(':message', $res_html . nl2br($speech));
                        $message->bindValue(':reply_post_id', $reply_post_id);
                        $message->bindValue(':thread_id', $thread_id);
                        $message->bindValue(':nice_num', 0);
                        $message->bindValue(':quote_from_id', 0);
                        $message->bindValue(':type', MSG_TYPE_RECORD);
                        $message->bindValue(':created', date('Y/m/d H:i:s'));
                        $message->execute();
                    }
                    $i++;
                }
            }
        }
    } else {
        $url = "https://kokkai.ndl.go.jp/api/speech?issueID={$issueID_list}";
        urlencode($url);
        $xml_obj = simplexml_load_file($url);
        $xml_ary = json_decode(json_encode($xml_obj), true);
        $i = 1;
        $reply_post_id = 0;
        $sql = 'SELECT MAX(thread_id) AS max FROM posts';
        $thread_max = $db->prepare($sql);
        $thread_max->execute();
        $thread_id = $thread_max->fetchColumn() + 1;
        if ($xml_ary['numberOfRecords'] == 1) {
            $speech = $xml_ary['records']['record']['recordData']['speechRecord']['speech'];
            $speech = preg_replace('/　+/', ' ', $speech);
            $speech = str_replace('――――◇―――――', "\n", $speech);
            $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, type, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :type, :created)';
            $message = $db->prepare($sql);
            $message->bindValue(':member_id', 15);
            $message->bindValue(':message', nl2br($speech));
            $message->bindValue(':reply_post_id', 0);
            $message->bindValue(':thread_id', $thread_id);
            $message->bindValue(':nice_num', 0);
            $message->bindValue(':quote_from_id', 0);
            $message->bindValue(':type', MSG_TYPE_RECORD);
            $message->bindValue(':created', date('Y/m/d H:i:s'));
            $message->execute();
            $sql = 'SELECT MAX(id) AS id FROM posts';
            $reply_post_id = $db->prepare($sql);
            $reply_post_id->execute();
            $reply_post_id = $reply_post_id->fetchColumn();
        } else {
            $i = 1;
            $reply_post_id = 0;
            $sql = 'SELECT MAX(thread_id) AS max FROM posts';
            $thread_max = $db->prepare($sql);
            $thread_max->execute();
            $thread_id = $thread_max->fetchColumn() + 1;
            foreach ($xml_ary['records']['record'] as $rcd) {
                print('<h2>' . $rcd['recordData']['speechRecord']['speaker'] . '</h2>');
                $speech = $rcd['recordData']['speechRecord']['speech'];
                $speech = preg_replace('/　+/', ' ', $speech);
                $speech = str_replace('――――◇―――――', "\n", $speech);
                print('<p>' . nl2br($speech) . '</p>');
                if ($i == 1) {
                    $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, type, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :type, :created)';
                    $message = $db->prepare($sql);
                    $message->bindValue(':member_id', 15);
                    $message->bindValue(':message', nl2br($speech));
                    $message->bindValue(':reply_post_id', 0);
                    $message->bindValue(':thread_id', $thread_id);
                    $message->bindValue(':nice_num', 0);
                    $message->bindValue(':quote_from_id', 0);
                    $message->bindValue(':type', MSG_TYPE_RECORD);
                    $message->bindValue(':created', date('Y/m/d H:i:s'));
                    $message->execute();
                } else {
                    $res = '<a href="/profile/?id=' . '15' . '">@' . '国会会議録' . '</a> のスレッドへの返信';
                    $res_html = '<div class="thread_exp">' . $res . '</div>';
                    $sql = 'SELECT MAX(id) AS id FROM posts';
                    $reply_post_id = $db->prepare($sql);
                    $reply_post_id->execute();
                    $reply_post_id = $reply_post_id->fetchColumn();
                    $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, type, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :type, :created)';
                    $message = $db->prepare($sql);
                    $message->bindValue(':member_id', 17);
                    $message->bindValue(':message', $res_html . nl2br($speech));
                    $message->bindValue(':reply_post_id', $reply_post_id);
                    $message->bindValue(':thread_id', $thread_id);
                    $message->bindValue(':nice_num', 0);
                    $message->bindValue(':quote_from_id', 0);
                    $message->bindValue(':type', MSG_TYPE_RECORD);
                    $message->bindValue(':created', date('Y/m/d H:i:s'));
                    $message->execute();
                }
                $i++;
            }
        }
    }
}




function getIssueID($obj)
{
    if ($obj['numberOfRecords'] == 0) {
        return NULL;
    }
    $issueID = [];
    if ($obj['numberOfReturn'] == 1) {
        $issueID = $obj['records']['record']['recordData']['meetingRecord']['issueID'];
    } else {
        foreach ($obj['records']['record'] as $rcd) {
            $issueID[] = $rcd['recordData']['meetingRecord']['issueID'];
        }
    }
    return $issueID;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API TEST</title>
</head>

<body>

</body>

</html>