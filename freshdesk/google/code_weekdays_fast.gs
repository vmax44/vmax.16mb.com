function getStats() {
  
  var serviceUrl="http://spia.nl/curl/google-gisteren/getlast.php";  // Service url, that receive data from mysql and send it as json-string
  var sheetID="1V0hjQNMSItMZYbbHWDY62ZfO2-N9LBWAs9R0deA2R_E";  //ID of spreadsheet
  
  var latestHeaders = 1;  //Row in spreadsheet, containing column headers of latest data
  var latestData = 2;     //Row in spreadsheet, will filled with data from mysql for latest date
    
  var days = {          //Object - config for days of week
    "Monday": {head: 5,      //Row in spreadsheet, containing column headers for monday
               data: 6},     //Row in spreadsheet, will filled with data from mysql for monday
    "Tuesday": {head: 9,
                data: 10},
    "Wednesday": {head: 13,
                  data: 14},
    "Thursday": {head: 17,
                 data: 18},
    "Friday": {head: 21,
               data: 22},
    "Saturday": {head: 25,
                 data: 26},
    "Sunday": {head: 29,
               data: 30}
  }
  
  var fromServer={};
  
  try {
    
    // Fetch data from serviceUrl
    fromServer = getJson(serviceUrl);
    
  } catch (e) {
    Logger.log("Error while downloading json");
    return;
  }
  
  if(fromServer.status!="ok") {
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
  
  //for improving script perfomance save copy of sheet cells to variable sheetCopy
  //and in future we will work with this copy
  
  var columnsCount=sheet.getLastColumn();
  
  var rowsCount=1;
  for(var day in days) 
    rowsCount=Math.max(days[day].head, days[day].data, rowsCount);
  
  var sheetCopy = sheet.getRange("R1C1:R"+rowsCount+"C"+columnsCount).getValues();
  
  //fill first section (latest data)
  fillDay(fromServer.latest, latestHeaders, latestData, sheetCopy);
  
  //fill each day of week
  for(var day in days) if (days.hasOwnProperty(day)) {
    var dayjson=fromServer.data[day] || {};
    fillDay(dayjson, days[day].head, days[day].data, sheetCopy);
  };
  
  // Save copy of sheet to spreadsheet
  sheet.getRange("R1C1:R"+rowsCount+"C"+columnsCount).setValues(sheetCopy);
  
  return;
  
  //function to fill one section
  function fillDay(json, headersRow, dataRow, sheetData) {
    // Fill data
    var field;
    var cell;
    var col=0;
    while((col<sheetData[headersRow-1].length) && (sheetData[headersRow-1][col]!="")) {
      field=sheetData[headersRow-1][col];
      if(json[field] !== undefined) {
        sheetData[dataRow-1][col]=json[field];
      } else {
        sheetData[dataRow-1][col]="";
      };
      col++;
    }
  }
    
  function getJson(url) {
    var resp=UrlFetchApp.fetch(url);
    var responseText=resp.getContentText();
    var json=JSON.parse(responseText);
    return json;
  }

};
