// JavaScript Document
var cbox = document.getElementById('allowNewTime');
cbox.style.display = 'block';

var timeNew = document.getElementById('time_new');
timeNew.onclick = function ()
 {
  //var a1 = document.getElementById('old_time');

  var hour_label = document.getElementById('item17_label_0');
  var hour = document.getElementById('item17_number_1');
  
  
  
  var min_label = document.getElementById('item18_label_0');
  var min = document.getElementById('item18_number_0');  
  
    var ampm_label = document.getElementById('item22_label_0');
    var ampm = document.getElementById('item22_select_1');   
    
    
  var sel = timeNew.checked;
  
  //a1.disabled = sel;

  hour.parentNode.style.display = sel ? 'block' : 'none';
  hour_label.parentNode.style.display = sel ? 'block' : 'none';  
  
  min_label.parentNode.style.display = sel ? 'block' : 'none';
  min.parentNode.style.display = sel ? 'block' : 'none'; 
  
  ampm_label.parentNode.style.display = sel ? 'block' : 'none';
  ampm.parentNode.style.display = sel ? 'block' : 'none'; 

  hour.disabled = !sel;
  min.disabled = !sel;
  ampm.disabled = !sel;
  
}
