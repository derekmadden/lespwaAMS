<?php 

	$api_key = '3c1265f682ab8bfea330bb140c647dae-us1';
	 
	// Query String Perameters are here
	// for more reference please vizit http://developer.mailchimp.com/documentation/mailchimp/reference/lists/
	$data = array(
		'fields' => 'lists', // total_items, _links
		//'email' => 'misha@rudrastyh.com',
		'count' => 5 // the number of lists to return, default - all
		// 'before_date_created' => '2021-04-04 10:30:50', // only lists created before this date
		// 'after_date_created' => '2014-02-05' // only lists created after this date
	);
	 
	$url = 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/';
	$result = json_decode( rudr_mailchimp_curl_connect( $url, 'GET', $api_key, $data) );
	// print_r( $result);
	// exit();
	 
	if(!empty($result->lists) ) {
		foreach( $result->lists as $list ){
			// echo '<option value="' . $list->id . '">' . $list->name . ' (' . $list->stats->member_count . ')</option>';
			echo $list->web_id;
			// you can also use $list->date_created, $list->stats->unsubscribe_count, $list->stats->cleaned_count or vizit MailChimp API Reference for more parameters (link is above)
		}
	} elseif ( is_int( $result->status ) ) { // full error glossary is here http://developer.mailchimp.com/documentation/mailchimp/guides/error-glossary/
		echo '<strong>' . $result->title . ':</strong> ' . $result->detail;
	}




	function rudr_mailchimp_curl_connect( $url, $request_type, $api_key, $data = array() ) {
		if( $request_type == 'GET' )
			$url .= '?' . http_build_query($data);
	 
		$mch = curl_init();
		$headers = array(
			'Content-Type: application/json',
			'Authorization: Basic '.base64_encode( 'user:'. $api_key )
		);
		curl_setopt($mch, CURLOPT_URL, $url );
		curl_setopt($mch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
		curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
		curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
		curl_setopt($mch, CURLOPT_TIMEOUT, 10);
		curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection
	 
		if( $request_type != 'GET' ) {
			curl_setopt($mch, CURLOPT_POST, true);
			curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
		}
	 
		return curl_exec($mch);
	}





	$data = [
	    'email'     => 'johndoe@example.com',
	    'status'    => 'subscribed',
	    'firstname' => 'john',
	    'lastname'  => 'doe'
	];

	syncMailchimp($data);

	function syncMailchimp($data) {
	    $apiKey = '3c1265f682ab8bfea330bb140c647dae';
	    $listId = '1571074';

	    $memberId = md5(strtolower($data['email']));
	    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
	    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

	    $json = json_encode([
	        'email_address' => $data['email'],
	        'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
	        'merge_fields'  => [
	            'FNAME'     => $data['firstname'],
	            'LNAME'     => $data['lastname']
	        ]
	    ]);

	    $ch = curl_init($url);

	    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 

	    $result = curl_exec($ch);
	    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);

	    return $httpCode;
	}











?>