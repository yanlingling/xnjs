og.common = og.common ||{};
og.common.lateThenNow = function(dateStr){
    var now = new Date();
    var nowYear = now.getFullYear();
    var nowMonth = now.getMonth()+1;
    var nowDate = now.getDate();
    var nowDate = nowYear + '-' + nowMonth + '-' + nowDate;


    if (new Date(nowDate).getTime() >= new Date($.trim(dateStr)).getTime()) {
        return false;
    }
    return true;
}
og.common.getCheckboxValue = function (name){
    var s='';
    $('input[name="'+name+'"]:checked').each(function(){
        if(s == ''){
            s+=$(this).val();
        }else{
            s+=','+$(this).val();
        }
    });
    return s;
}
