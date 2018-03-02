$(".doForm").click(function(){
  var self = $('.operateForm1');
  var url = "{$back_url}";/*
  console.log(url);
  return ;*/
  $.post(self.attr("action"), self.serialize(), success, "json");
  toastload('注册中');
  return false;

  function success(data){
    delload();
    if(data.status){
      if(url!=''){
        window.location.href=url;
      }else{
        window.location.href = data.url;
      }
    } else {
      toastshow(data.info);
    }
  }
});

