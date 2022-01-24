<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

$db = dbConnect();
//url
$day = date('2021-12-21');
$url = "https://kokkai.ndl.go.jp/api/meeting_list?maximumRecords=1&from={$day}&until={$day}&recordPacking=XML";
urlencode($url);
$xml_obj = simplexml_load_file($url);
$xml_ary = json_decode(json_encode($xml_obj), true);
$issueID_list = getIssueID($xml_ary);

if (!empty($issueID_list)) {
    if (is_array($issueID_list)) {
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
                insertRecord($db, $xml_ary['records']['record'], ADMIN_ID, 0, $thread_id, '');
            } else {
                $thread_id = get_thread_id($db);
                foreach ($xml_ary['records']['record'] as $rcd) {
                    if ($i == 1) {
                        insertRecord($db, $rcd, ADMIN_ID, 0, $thread_id, '', '');
                        $reply_post_id = get_reply_post_id($db);
                    } else {
                        $res = '<a href="/profile/?id=' . '0' . '">@' . '国会会議録' . '</a> のスレッドへの返信';
                        $res_html = '<div class="thread_exp">' . $res . '</div>';
                        $sql = 'SELECT * FROM members WHERE name = :name';
                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(':name', $rcd['recordData']['speechRecord']['speaker']);
                        $stmt->execute();
                        $id = $stmt->fetch();
                        insertRecord($db, $rcd, $id['id'], $reply_post_id, $thread_id, $res_html, 'res');
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

        if ($xml_ary['numberOfRecords'] == 1) {
            insertRecord($db, $xml_ary['records']['record'], ADMIN_ID, 0, $thread_id, '');
        } else {
            $thread_id = get_thread_id($db);
            foreach ($xml_ary['records']['record'] as $rcd) {
                if ($i == 1) {
                    insertRecord($db, $rcd, ADMIN_ID, 0, $thread_id, '', '');
                    $reply_post_id = get_reply_post_id($db);
                } else {
                    $res = '<a href="/profile/?id=' . '0' . '">@' . '国会会議録' . '</a> のスレッドへの返信';
                    $res_html = '<div class="thread_exp">' . $res . '</div>';
                    $sql = 'SELECT * FROM members WHERE name = :name';
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':name', $rcd['recordData']['speechRecord']['speaker']);
                    $stmt->execute();
                    $id = $stmt->fetch();
                    insertRecord($db, $rcd, $id['id'], $reply_post_id, $thread_id, $res_html, 'res');
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

function insertRecord($db, $rcd, $member_id, $reply_post_id, $thread_id, $res_html, $flag)
{
    $speech = $rcd['recordData']['speechRecord']['speech'];
    $speech = preg_replace('/御異議ありませんか。/', '＊', $speech);
    $speech = preg_replace('/御異議なしと認めます。/', '＊＊', $speech);
    $speech = preg_replace('/起立を求めます。/', '＊＊＊', $speech);
    $speech = preg_replace('/。\n/', '。', $speech);
    $speech_list = explode('。', $speech);
    if (is_array($speech_list) || $flag == 'res') {
        foreach ($speech_list as $speech) {
            $speech = preg_replace(
                '/＊＊＊/',
                '起立を求めます。',
                $speech
            );
            $speech = preg_replace(
                '/＊＊/',
                "御異議なしと認めます。",
                $speech
            );
            $speech = preg_replace(
                '/＊/',
                "御異議ありませんか。",
                $speech
            );
            $speech = preg_replace('/^○[^ ]+/', '', $speech);
            $speech = str_replace('――――◇―――――', "\n", $speech);
            $speech = preg_replace('/　+/', ' ', $speech);

            $speech =
                preg_replace('/\n+/', "\n", $speech);
            $speech = preg_replace('/ +/', " ", $speech);
            $speech = trim($speech);
            if ($speech == '') {
                continue;
            }
            $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, type, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :type, :created)';
            $message = $db->prepare($sql);
            $message->bindValue(':member_id', $member_id);
            $message->bindValue(':message', $res_html . nl2br($speech));
            $message->bindValue(':reply_post_id', $reply_post_id);
            $message->bindValue(':thread_id', $thread_id);
            $message->bindValue(':nice_num', 0);
            $message->bindValue(':quote_from_id', 0);
            $message->bindValue(':type', MSG_TYPE_RECORD);
            $message->bindValue(':created', date('Y/m/d H:i:s'));
            $message->execute();
        }
    } else {
        $speech = preg_replace('/　+/', ' ', $speech);
        $speech = preg_replace('/\n+/', ' ', $speech);
        $speech = str_replace('――――◇―――――', "\n", $speech);
        $sql = 'INSERT INTO posts(member_id, message, reply_post_id, thread_id, nice_num, quote_from_id, type, created) VALUES(:member_id, :message, :reply_post_id, :thread_id, :nice_num, :quote_from_id, :type, :created)';
        $message = $db->prepare($sql);
        $message->bindValue(':member_id', $member_id);
        $message->bindValue(':message', $res_html . nl2br(
            $speech
        ));
        $message->bindValue(':reply_post_id', $reply_post_id);
        $message->bindValue(':thread_id', $thread_id);
        $message->bindValue(':nice_num', 0);
        $message->bindValue(':quote_from_id', 0);
        $message->bindValue(':type', MSG_TYPE_RECORD);
        $message->bindValue(':created', date('Y/m/d H:i:s'));
        $message->execute();
    }
}

function get_thread_id($db)
{
    $sql = 'SELECT MAX(thread_id) AS max FROM posts';
    $thread_max = $db->prepare($sql);
    $thread_max->execute();
    $thread_id = $thread_max->fetchColumn() + 1;
    return $thread_id;
}

function get_reply_post_id($db)
{
    $sql = 'SELECT MAX(id) AS id FROM posts';
    $reply_post_id = $db->prepare($sql);
    $reply_post_id->execute();
    $reply_post_id = $reply_post_id->fetchColumn();
    return $reply_post_id;
}

// header('Location: /');
// exit();
