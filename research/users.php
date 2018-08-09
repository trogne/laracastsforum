<?php
header('Content-Type: application/json');

//echo json_encode(['foo', 'bar', 'baz']);


$usernames = [
    'jeffreyway',
    'johndoe',
    'johnseymore',
    'johnseymore2',
    'johnseymore3',
    'johnseymore4',
    'johnseymore5',
    'janedoe',
    'suziedoe'
];

$results = array_slice(array_values(array_filter($usernames, function ($name) {
    return stripos($name, $_GET['q']) === 0;
})), 0, 5); //limit on the server side results

echo json_encode($results);


/////pu besoin car array_values above
//if(count($results)==1) {
//    //foreach($results as $key => $value) {$zenames[] = $value;};
//    $zenames[] = array_values($results)[0];
//} else {
//    $zenames = $results;
//}
//echo json_encode($zenames);


////foreach + preg_match : 
//$zenames = [];
//
//if($_GET['q']!=NULL) {
//    foreach($usernames as $name) {
//        if(preg_match('/^'.$_GET['q'].'/', $name, $matches)){
//            array_push($zenames, $name);        
//        }
//    };    
//}

