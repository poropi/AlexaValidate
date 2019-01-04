<?php
include './AlexaValidate.php';

$body  = file_get_contents('php://input');
$validator = new AlexaValidate();

try {
	$validator->validate($body);
} catch (Exception $e) {
    // load cert again and validate because temp file was outdatet.
	http_response_code(400);
	return;
}

$params = json_decode($body, true);

$ssml = "outputSpeech ssml";
$title = "card title";
$content = "card content";
$text = "reprompt text";
$shouldEndSession = "true";

if ($params != null) {
	$type = $params['request']['type'];

	if ($type === "LaunchRequest") {
		// LaunchRequest sample
		$ssml = "<speak>call LaunchRequest</speak>";
		$content = "call LaunchRequest";
		$text = "reprompt text";
		$shouldEndSession = "false";
	} else if ($type === "SessionEndedRequest") {
		// SessionEndedRequest sample
		$reason = $params['request']['reason'];
		if ($reason === "USER_INITIATED") {
			$ssml = "<speak>call SessionEndedRequest USER_INITIATED</speak>";
			$content = "call SessionEndedRequest USER_INITIATED";
			$text = "reprompt text";
			$shouldEndSession = "true";
		} else {
			$ssml = "<speak>call SessionEndedRequest ERROR</speak>";
			$content = "call SessionEndedRequest ERROR";
			$text = "reprompt text";
			$shouldEndSession = "false";
		}
	} else {
		// IntentRequest sample
		$intentName = $params['request']['intent']['name'];
		if ($intentName === "AMAZON.HelpIntent") {
			$ssml = "<speak>call IntentRequest HelpIntent</speak>";
			$content = "call IntentRequest HelpIntent";
			$text = "reprompt text";
			$shouldEndSession = "false";
		} else if ($intentName === "AMAZON.CancelIntent") {
			$ssml = "<speak>call IntentRequest CancelIntent</speak>";
			$content = "call IntentRequest CancelIntent";
			$text = "reprompt text";
			$shouldEndSession = "true";
		} else if ($intentName === "AMAZON.StopIntent") {
			$ssml = "<speak>call IntentRequest StopIntent</speak>";
			$content = "call IntentRequest StopIntent";
			$text = "reprompt text";
			$shouldEndSession = "true";
		} else if ($intentName === "AMAZON.NavigateHomeIntent") {
			$ssml = "<speak>call IntentRequest NavigateHomeIntent</speak>";
			$content = "call IntentRequest NavigateHomeIntent";
			$text = "reprompt text";
			$shouldEndSession = "false";
		} else if ($intentName === "CustomIntent") {
			$ssml = "<speak>call IntentRequest CustomIntent</speak>";
			$content = "call IntentRequest CustomIntent";
			$text = "reprompt text";
			$shouldEndSession = "false";
		}

	}

} else {
	$ssml = "<speak>call ERROR Request</speak>";
	$content = "call ERROR Request";
	$text = "reprompt text";
	$shouldEndSession = "true";
}
?>
{
  "version": "1.0",
  "sessionAttributes": {
    "supportedHoriscopePeriods": {
      "daily": true,
      "weekly": false,
      "monthly": false
    }
  },
  "response": {
    "outputSpeech": {
      "type": "SSML",
      "ssml": "<?php echo $ssml ?>"
    },
    "card": {
      "type": "Simple",
      "title": "<?php echo $title ?>",
      "content": "<?php echo $content ?>"
    },
    "reprompt": {
      "outputSpeech": {
        "type": "PlainText",
        "text": "<?php echo $text ?>"
      }
    },
    "shouldEndSession": <?php echo $shouldEndSession ?>
  }
}