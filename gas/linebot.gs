//LINE APIからのwebhookを受けてPHPサーバに転送するスクリプトです。
//セキュリティに関係するところは削ってあります。

var url = 'hogehoge.com';
var log_sheet_id = 'huga';

function doPost(e) {
  log("doPost");
  var events = JSON.parse(e.postData.contents);
  log(e.postData.contents);
  var response = UrlFetchApp.fetch(url, makeOption(events));
  log(response.getContentText());
}

function makeOption(events) {
  return {
    "muteHttpExceptions" : true,
    "method" : "post",
    "headers" : {
      "Content-Type" : "application/json"
    },
    "payload" : JSON.stringify(events)
  };
}

function log(msg){
  var ss0 = SpreadsheetApp.openById(log_sheet_id).getSheets()[0];
  ss0.appendRow(["発火", msg]);
}
