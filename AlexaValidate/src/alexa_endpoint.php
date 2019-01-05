<?php
/**
 * Alexaエンドポイントサンプルコード
 *
 * Alexaのエンドポイントのサンプルコードになります。
 * 一応、リクエストタイプ、およびIntent種別ごとに処理のサンプルを用意しております。
 *
 */

include './AlexaValidate.php';

// BodyからAlexaリクエストデータを取得
$body  = file_get_contents('php://input');

// リクエスト検証処理
try {
	$validator = new AlexaValidate();
	$validator->validate($body);
} catch (Exception $e) {
    // 検証の内容が不正であった場合、エラーレスポンス400を設定し、処理終了
	http_response_code(400);
	return;
}

// AlexaリクエストデータをJsonオブジェクトに変換
$params = json_decode($body, true);

// Alexaレスポンスデータの初期値設定
$ssml = "outputSpeech ssml";
$title = "card title";
$content = "card content";
$text = "reprompt text";
$shouldEndSession = "true";

if ($params != null) {
	// Alexaリクエストデータより、リクエストタイプを取得
	$type = $params['request']['type'];

	if ($type === "LaunchRequest") {
		// Alexaリクエストタイプが「LaunchRequest」の場合
		$ssml = "<speak>call LaunchRequest</speak>";
		$content = "call LaunchRequest";
		$text = "reprompt text";
		$shouldEndSession = "false";
	} else if ($type === "SessionEndedRequest") {
		// Alexaリクエストタイプが「SessionEndedRequest」の場合
		$reason = $params['request']['reason'];
		if ($reason === "USER_INITIATED") {
			// Alexaリクエストのreasonが「USER_INITIATED（ユーザーの任意で終了）」の場合
			$ssml = "<speak>call SessionEndedRequest USER_INITIATED</speak>";
			$content = "call SessionEndedRequest USER_INITIATED";
			$text = "reprompt text";
			$shouldEndSession = "true";
		} else {
			// Alexaリクエストのreasonが上記以外で終了した場合
			$ssml = "<speak>call SessionEndedRequest ERROR</speak>";
			$content = "call SessionEndedRequest ERROR";
			$text = "reprompt text";
			$shouldEndSession = "false";
		}
	} else {
		// Alexaリクエストタイプが上記以外、「IntentRequest」として扱う
		// IntentRequest sample
		$intentName = $params['request']['intent']['name'];
		if ($intentName === "AMAZON.HelpIntent") {
			// AlexaリクエストIntent内容が「AMAZON.HelpIntent」の場合
			$ssml = "<speak>call IntentRequest HelpIntent</speak>";
			$content = "call IntentRequest HelpIntent";
			$text = "reprompt text";
			$shouldEndSession = "false";
		} else if ($intentName === "AMAZON.CancelIntent") {
			// AlexaリクエストIntent内容が「AMAZON.CancelIntent」の場合
			$ssml = "<speak>call IntentRequest CancelIntent</speak>";
			$content = "call IntentRequest CancelIntent";
			$text = "reprompt text";
			$shouldEndSession = "true";
		} else if ($intentName === "AMAZON.StopIntent") {
			// AlexaリクエストIntent内容が「AMAZON.StopIntent」の場合
			$ssml = "<speak>call IntentRequest StopIntent</speak>";
			$content = "call IntentRequest StopIntent";
			$text = "reprompt text";
			$shouldEndSession = "true";
		} else if ($intentName === "AMAZON.NavigateHomeIntent") {
			// AlexaリクエストIntent内容が「AMAZON.NavigateHomeIntent」の場合
			$ssml = "<speak>call IntentRequest NavigateHomeIntent</speak>";
			$content = "call IntentRequest NavigateHomeIntent";
			$text = "reprompt text";
			$shouldEndSession = "false";
		} else if ($intentName === "CustomIntent") {
			// AlexaリクエストIntent内容が「CustomIntent（ユーザー独自Intent）」の場合
			$ssml = "<speak>call IntentRequest CustomIntent</speak>";
			$content = "call IntentRequest CustomIntent";
			$text = "reprompt text";
			$shouldEndSession = "false";
		}

	}

} else {
	// Alexaリクエストデータが存在しなかった場合（通常ありえないが・・・）
	$ssml = "<speak>call ERROR Request</speak>";
	$content = "call ERROR Request";
	$text = "reprompt text";
	$shouldEndSession = "true";
}
// 以下にて、Alexaに返却するレスポンスデータを生成
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