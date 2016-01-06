<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Insert Blog Entry</title>
<link href="admin.css" rel="stylesheet" type="text/css">
</head>

<body>
<h1>Insert New Blog Entry</h1>

<form id="form1" method="post" action="" enctype="multipart/form-data"> 

             <!-------------------------------- time ------------------------->
          <div>
            <div class="fb-header">
              <h2 style="FONT-SIZE: 15px">
                Time:
              </h2>
              <h2 style="FONT-SIZE: 15px">
                <input type="text" id="old_time" value="old_time">
              </h2>              
            </div>
          </div>
          
          
            <p id="allowNewTime">
                <input type="checkbox" name="time_new" id="time_new">
                <label for="time_new">Enter new time</label>
            </p>
          

          <div style="FILTER: " style="PADDING-LEFT: 10px" >
            <div class="fb-grouplabel" style="display:none;">
              <label style="DISPLAY: inline" id="item17_label_0">Hour</label>
            </div>
            <div class="fb-input-number" style="display:none;">
              <input id="item17_number_1" name="number17" min="1" max="12" data-hint=""
              autocomplete="off" step="1" type="number" />
            </div>
          </div>
          
          <div style="FILTER: " style="PADDING-LEFT: 10px" >
            <div class="fb-grouplabel" style="display:none;">
                    <label style="DISPLAY: inline" id="item18_label_0">Minute</label>
            </div>
            <div class="fb-input-number" style="display:none;">
                  <input id="item18_number_0" name="number18" min="0" max="59" data-hint=""
                  autocomplete="off" step="1" type="number" />
            </div>
          </div>
          
          <div style="PADDING-LEFT: 10px" class="optional">
            <div id="item22_select_1">
                <div class="fb-grouplabel" >
                  <label style="DISPLAY: inline" id="item22_label_0">AM / PM:</label>
                </div>
            
                <div class="fb-dropdown" id="item22_select_1">
                  <select  name="ampm" data-hint="" required >
                    <option id="item22_0_option" value="" selected>
                      Choose one
                    </option>
                    <option id="item22_1_option" value="AM">
                      AM
                    </option>
                    <option id="item22_2_option" value="PM">
                      PM
                    </option>
                  </select>
                </div>
            </div>
          </div>
          
          <!---------------------------- end of time ------------------------->

</form>
<script src="toggle_fields.js"></script>
</body>
</html>