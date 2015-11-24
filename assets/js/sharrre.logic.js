function initShareButton(){
  $('#googleplus').sharrre({
    share: {
      googlePlus: true
    },
    template: '<a class="box" href="#"><div class="share"><span></span>Google+</div></a>',
    enableHover: false,
    enableTracking: true,
    urlCurl: '',
    click: function(api, options){
      api.simulateClick();
      api.openPopup('googlePlus');
    }
  });
  $('#twitter').sharrre({
    share: {
      twitter: true
    },
    template: '<a class="box" href="#"><div class="share"><span></span>Twitter</div></a>',
    enableHover: false,
    enableTracking: true,
    click: function(api, options){
      api.simulateClick();
      api.openPopup('twitter');
    }
  });
  $('#facebook').sharrre({
    share: {
      facebook: true
    },
    template: '<a class="box" href="#"><div class="share"><span></span>Facebook</div></a>',
    enableHover: false,
    enableTracking: true,
    click: function(api, options){
      api.simulateClick();
      api.openPopup('facebook');
    }
  });
}