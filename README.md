# 陽和バスBot

**陽和病院周辺のバス停にあと何分でバスが来るか教えてくれるLineBot**です。   
ぜひ友達追加してご利用ください！

<p><img src="https://c1.staticflickr.com/5/4213/35048977716_32fed3c62d.jpg" height="200" border="0" /></p>
<p><a href="https://line.me/R/ti/p/%40mkn0401k"><img src="https://scdn.line-apps.com/n/line_add_friends/btn/ja.png" alt="友だち追加" height="36" border="0" /></a></p>

# ScreenShot
<p>
<img class="hatena-fotolife" title="f:id:freqmodu874:20170524001855p:plain" src="https://cdn-ak.f.st-hatena.com/images/fotolife/f/freqmodu874/20170524/20170524001855.png" alt="f:id:freqmodu874:20170524001855p:plain" width="200" />　
<img class="hatena-fotolife" title="f:id:freqmodu874:20170524001901p:plain" src="https://cdn-ak.f.st-hatena.com/images/fotolife/f/freqmodu874/20170524/20170524001901.png" alt="f:id:freqmodu874:20170524001901p:plain" width="200" />　
<img class="hatena-fotolife" title="f:id:freqmodu874:20170524001857p:plain" src="https://cdn-ak.f.st-hatena.com/images/fotolife/f/freqmodu874/20170524/20170524001857.png" alt="f:id:freqmodu874:20170524001857p:plain" width="200" />　
</p>

# 機能

* バス停にあと何分でバスがくるか、**GPSのリアルタイム接近情報**を表示します。
* アラームを設定すれば、到着5分前・10分前に通知がきます。  

使用できるバス停は以下の４つです。

1. 大泉二丁目 → 成増駅
2. 大泉二丁目 → 石神井公園駅
3. 大泉町四丁目 → 和光市駅
4. 大泉町四丁目・大泉桜高校 → 大泉学園駅

# ロジック
#### ①ボタン「バス今どこ？」をタップした場合
![qr-code](https://c1.staticflickr.com/5/4204/34282325063_e936ca792f_h.jpg)

#### ②ボタン「到着5分前アラーム」「到着10分前アラーム」をタップした場合
![qr-code](https://c1.staticflickr.com/5/4290/35052500276_0d77fc51ca_h.jpg)
  

# なんでこんな実装なの？
GASのエディタはケアレスミスしまくりで、僕には使いにくい。  
ただ、SSL証明書を買うお金をケチリたいのでwebhookはGASで受けなければならない。  
そこで、それ以外の処理はPHPの勉強がてらPHPで行うことにした。
