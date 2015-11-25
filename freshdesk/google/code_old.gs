function getStats() {
  var serviceUrl="http://spia.nl/curl/google-gisteren/getlast.php";  // Service url, that receive data from mysql and send it as json-string
  var sheetID="1V0hjQNMSItMZYbbHWDY62ZfO2-N9LBWAs9R0deA2R_E";  //ID of spreadsheet
  var headersRow=1;  //Row in spreadsheet, containing headers;
  var dataRow=2;   //Row in spreadsheet, will filled with data from mysql
  
  try {
    
    // Fetch data from serviceUrl
    var json=getJson(serviceUrl);
    
  } catch (e) {
    Logger.log("Error while downloading json");
    return;
  }
  
  if(json.status!="ok") {
    Logger.log("Wrong answer from server. "+json.error);
    return;
  }
  
  
  // Open Google Spreadsheet
  var sheet={};
  var serverAv = false;
  while (serverAv == false){
    try {
      sheet=SpreadsheetApp.openById(sheetID);
      serverAv=true;
    } catch (e) {
      Logger.log('Error while opening document');
    }
  };
  
  // Fill data
  var col=1;
  var field="";
  var cell=null;
  while(!(cell=sheet.getRange("R"+headersRow+"C"+col)).isBlank()) {
    field=cell.getValue();
    sheet.getRange("R"+dataRow+"C"+col).setValue(json.data[field]);
    col++;
  }
  
  function getJson(url) {
    var resp=UrlFetchApp.fetch(url);
    var responseText=resp.getContentText();
    var json=JSON.parse(responseText);
    return json;
  }
  
}