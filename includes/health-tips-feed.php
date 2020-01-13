<?php $xml = 'https://ucfhealth.com/feed/';
$doc = new DOMDocument();
$doc->load($xml);
$item = $doc->getElementsByTagName('item');

//$data = array();

for($i=0; $i<=0; $i++){
    $title = $item->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
    $link = $item->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
    $description = $item->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;

    echo '<tr>';
    echo '<td align="center" style="padding: 0; text-align: left; vertical-align: top;"><a style="text-decoration: none; color: #000;" href="' . $link . '" target="_blank"><h2>' . $title . '</h2></a></td>';

    echo '<tr><td align="center" style="padding: 0; text-align: left; vertical-align: top;">'.$description.'</td></tr>';

    echo '.<tr><td align="center" style="padding: 0; text-align: left; vertical-align: top;"><a href="' . $link . '" target="_blank">Read More...</a></td></tr>';

} ?>