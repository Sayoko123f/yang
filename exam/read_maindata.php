<?php
require_once('$con.php');

$csv = fopen('Q4.csv', 'r');
$rownum = 0;
$field = array(
    'ab', 'year', 'questionType', 'title', 'word',
    'words', 'count', 'level', 'pos', 'fromChoices',
    'numOfYears', 'srcQuestions', 'srcChoices'
);

while ($row = fgetcsv($csv)) {
    if (++$rownum === 1) {
        continue;
    }
    echo $rownum . PHP_EOL;
    // if ($rownum > 5) {
    //     break;
    // }
    $row[1] = intval($row[1]);
    $row[6] = intval($row[6]);
    $row[7] = intval($row[7]);
    $row[9] = intval($row[9]);
    $row[10] = intval($row[10]);
    $row[11] = intval($row[11]);
    $row[12] = intval($row[12]);
    $stmt = $con->prepare('INSERT INTO `exam_maindata`(`ab`, `year`, `questionType`, `title`, `word`,`words`, `count`, `level`, `pos`, `fromChoices`,`numOfYears`, `srcQuestions`, `srcChoices`)
                    VALUES (:ab, :year, :questionType, :title, :word,:words, :count, :level, :pos, :fromChoices,:numOfYears, :srcQuestions, :srcChoices);');
    for ($i = 0; $i < count($field); $i++) {
        $stmt->bindParam(":$field[$i]", $row[$i]);
    }
    //$stmt->debugDumpParams();
    $stmt->execute();
}

unset($con);
fclose($csv);
