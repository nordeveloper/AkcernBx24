$(document).ready(function(){

    $('.aside-show').click(function(){
        
        if( $('.aside').hasClass('mobile-mod') ){
            $('.aside').removeClass('mobile-mod');
            //$('.aside').addClass('fixed');
            //$('body').addClass('no-overflow');
        }else{
           // $('body').removeClass('no-overflow');
            $('.aside').addClass('mobile-mod');
            //$('.aside').removeClass('fixed');
        }
    });


    $('.contact-link').click(function () {
        let contactlink = $(this).attr('href');
        BX.SidePanel.Instance.open(contactlink, {
            options: {
            }
        });
        return false;
    });


    $('.add-contact').click(function () {
        let AddLink = '/crm/contact/details/0/';
        BX.SidePanel.Instance.open(AddLink, {
        });
        return false;
    });  


    // $('.add-contact').click(function () {
    //     let AddLink = '/crm/contact/details/0/';
    //     BX.SidePanel.Instance.open(AddLink, {
    //     });
    //     return false;
    // });

    // $('.contact-link').click(function () {
    //     let contactlink = $(this).attr('href');
    //     BX.SidePanel.Instance.open(contactlink, {
    //         options: {
    //         }
    //     });
    //     return false;
    // });


    // $('.select2-contact').select2({
    //     minimumInputLength: 2,
    //     ajax: {
    //         url: '/local/ajax/getContactByIdent.php',
    //         dataType: 'json',
    //         data: function (params) {
    //             var query = {q: params.term }
    //             return query;
    //         },
    //         processResults: function(response) {
    //             return {
    //                 results: response
    //             };
    //         }
    //     }
    // });

});


function ShowNotify(txt, type){
    toastr.options = {
      // "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      // "progressBar": false,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "800",
      "hideDuration": "800",
      "timeOut": "3000",
      "extendedTimeOut": "800",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
    
    toastr[type](txt);
}