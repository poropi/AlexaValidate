# Alexaリクエスト検証処理プログラム

Alexaの自作スキル作成時、PHPでエンドポイントを作成する場合、<br>
以下のリクエスト検証処理を実装する必要がございます。

https://developer.amazon.com/ja/docs/custom-skills/host-a-custom-skill-as-a-web-service.html

しかし、一から組むとなると結構大変になりますが、<br>
一度組んでしまえば、使いまわしができるので、クラスとして利用できる処理を実装してみました。<br>
よろしければ、使ってやってください。

まあ、Alexa PHP とかって検索かければもっと役立つライブラリがあるので、<br>
そっちを使ってみてもいいかと思います。

こちらは、リクエストの正当性を検証するだけにとどめたコードになります。

## 実装方法
以下をダウンロードやら、コードのコピペするなりしてください。
https://github.com/poropi/AlexaValidate/blob/master/AlexaValidate/src/AlexaValidate.php

以下にサンプルコードを置いておきます。
https://github.com/poropi/AlexaValidate/blob/master/AlexaValidate/src/alexa_endpoint.php

ポイントとしては、以下になるかな。
https://github.com/poropi/AlexaValidate/blob/140543109fe007190a01dda4f0aadcd74929166b/AlexaValidate/src/alexa_endpoint.php#L2-L13

上記を実装することで、Alexa公開審査前の機能テストを通過できます。<br>
とはいえで、機能テスト自体の仕様変更等が発生することもあるので、上記の実装で通らなくなる可能性もあります。<br>
そのときは、機能テスト上に表示される内容を見て、ご自身で対応お願いしますね！

## その他
PHP初心者が作ったものになります。<br>
PHPバージョン7.3で動作検証しておりますが、不具合あれば連絡お待ちしております。
