<?php


	$to = 'derekm@missionofhope.com'; 
	$from = 'accounts@Lespwa.io'; 
	$fromName = 'LespwaAMS'; 
	 
	$subject = "Test Invitation to LespwaAMS"; 
	 
	// $message = file_get_contents("invitationEmail.html");
	$message = '<h1 style="text-align: center;">You are Invited to join the team!</h1>
				<div style="text-align: center;"><br>
					A member of your team has invited you to take part in the launch of LespwaAMS! Click the link below to be transported to the registration page and begin taking hold of your teams assets!<br><br>
					<a href="https://lespwa.io" target="_blank"><strong><span style="font-size:18px">http://lespwa.io</span></strong></a>
				</div>';
	// echo $message;
	// exit();
	 
	// Set content-type header for sending HTML email 
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
	"Reply-To: accounts@lespwa.io" . "\r\n" .
	"X-Mailer: PHP/" . phpversion();
	 
	// Additional headers 
	$headers .= 'From: '.$fromName.'<'.$from.'>' . "\r\n";
	 
	// Send email 
	if(mail($to, $subject, $message, $headers)){ 
	    echo 'Email has sent successfully.'; 
	}else{ 
	   echo 'Email sending failed.'; 
	}

?>