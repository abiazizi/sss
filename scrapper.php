<?php
$headers 	= array(
	'sec-fetch-site: same-origin',
	'sec-fetch-mode: cors',
	'sec-fetch-dest: empty',
	'sec-ch-ua-platform: "Android"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua: "Chromium",v="94", "Google Chrome",v="94", ",Not A Brand",v="99"',
	'referer: https://m.nl.aliexpress.com/wholesale/charger+iphone.html?pr=10-9000&style=list&sortType=orders&sortOrder=desc&page=2',
);
$domain 	= array(
	"id.aliexpress.com",
	"ko.aliexpress.com",
	"ar.aliexpress.com",
	"de.aliexpress.com",
	"es.aliexpress.com",
	"fr.aliexpress.com",
	"it.aliexpress.com",
	"nl.aliexpress.com",
	"pt.aliexpress.com",
	"th.aliexpress.com",
	"tr.aliexpress.com",
	"vi.aliexpress.com",
	"he.aliexpress.com",
	"ja.aliexpress.com",
	"pl.aliexpress.com",
);
cli_set_process_title("CRL+C to Stop");
$delay 	= 2;
foreach(file('idn.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $vall ){
// while( true ){
	flush();
	echo "======================================","\n";
	$keytool 	= @file('keyword-scrap.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if( (empty($vall)) or (strlen($vall)<3) ){ continue; }
	// $keyword 	= ask("Keyword : ");
	$keyword 	= $vall;
	if( in_array($keyword, $keytool) ){ echo $keyword.' Keyword Scrapped'."\n======================================\n"; continue; }
	// else{ $open_new = fopen('product'.DIRECTORY_SEPARATOR.$keyword.'.txt', 'a+'); }
	else{ $open_new = fopen('bulk.txt', 'a+'); }

	$i = 1;
	$q = 0;
	$new_text 	= '';
	while( true ){
		shuffle($domain);
		$ali 	= $domain[0];
		$link 	= 'https://m.'.$ali.'/api/search/main/items?appId=18539&params='.urlencode('{"pageId":"4jpogcdrsxqcawee17c548842cb20bf3e54122c25f","clientType":"msite","searchBizScene":"mainSearch","abBucket":"unifiedRapidFilter,plus","page":"'.$i.'","pageSize":100,"refine_conf":0,"osf":"direct","q":"'.$keyword.'","pr":"40-9000","style":"list","sortType":"orders","sortOrder":"desc"}');
		$scrap 	= json_decode(curl($link, $headers));

		if(@$scrap->code == 200){
			$data 	= @$scrap->data->searchResult->mods->itemList->content;

			if( $data ){
				if( count($data)>0 ){
					foreach( $data as $id ){
						$q += 1;
						$new_text .= $id->productId."\n";
					}
					fwrite($open_new, $new_text);
					$last_word 	= $keyword." [".number_format($q)."] ProductID";
					echo $last_word."\r";
					flush();
					sleep($delay);
					$new_text = '';
					$i += 1;
				}else{
					$q = 0;
					$new_text = '';
					break;
				}
			}else{
				break;
			}
		}else{
			if( !$last_word ){
				$last_word = $keyword." [".number_format('0')."] ProductID 1"."\n";
			}
			$new_text = '';
			break;
		}
	}
	echo $last_word." Done !!!","\n";
	echo "======================================","\n";
	fclose($open_new);
	$handle = fopen('keyword-scrap.txt', 'a+');
	fwrite($handle, $keyword."\n");
	fclose($handle);
	sleep($delay);
}

function ask($question){
	echo $question;
	$get 	= trim(fgets(STDIN));
	return $get;
}
function curl($link, $header=false){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	// curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'aliexpress.cookie');
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'aliexpress.cookie');
	if( $header ){
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Mobile Safari/537.36");
	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}