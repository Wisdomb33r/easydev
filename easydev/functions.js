

function checkall(checked){
  var checklist = document.getElementsByTagName('input');
  for(i=0; i < checklist.length; i++){
    if(checked){
      checklist[i].checked = true;
    }
    else{
      checklist[i].checked = false;
    }
  }
}