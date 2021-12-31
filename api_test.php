<?php
require($_SERVER['DOCUMENT_ROOT'] . '/func.php');

$db = dbConnect();
//url
$startDay = date('Y-m-d', strtotime('-2 week'));
$endDay = date('Y-m-d');
$url = "https://kokkai.ndl.go.jp/api/meeting_list?maximumRecords=100&from={$startDay}&until={$endDay}&recordPacking=XML";
urlencode($url);
$xml_obj = simplexml_load_file($url);
$xml_ary = json_decode(json_encode($xml_obj), true);
$issueID_list = getIssueID($xml_ary);

function getIssueID($obj)
{
    $issueID = [];
    foreach ($obj['records']['record'] as $rcd) {
        $issueID[] = $rcd['recordData']['meetingRecord']['issueID'];
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
    <pre><?php
            $sql = 'INSERT INTO issueID(issueID) VALUES(:issueID)';
            foreach ($issueID_list as $issueID) {
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':issueID', $issueID);
                $stmt->execute();
            }
            ?></pre>
</body>

</html>