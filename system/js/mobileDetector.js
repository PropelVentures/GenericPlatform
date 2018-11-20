/* 
 * When user Right click on any single row then 
 * user will see context menu 
 * 
 * &&&&&&&&&&&& if user using any mobile device
 * 
 *Then 
 *
 *user will need to holdtab to see context menu
 *
 *6/6/2016 
 *
 *@author: Yasir Khan
 */







function mobileDetector(){
    

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

return isMobile;
}

