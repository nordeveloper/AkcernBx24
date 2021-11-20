BX.ready(function() {

    var ZonePopup;
    var StreetPopup;
    var CitiesPopup;

    BX.addCustomEvent('BX.Main.Filter:customEntityFocus', function(field) {

        var zoneInp = document.querySelector('[name="PROPERTY_ZONE_label"]');
        var streetInp = document.querySelector('[name="PROPERTY_STREET_label"]');
        var cityInp = document.querySelector('[name="PROPERTY_CITY_label"]');
        var zoneVal;
        var streetVal;
        var cityVal;

        var zone = document.querySelector('[name="PROPERTY_ZONE"]');
        var regionField = document.querySelector('[data-name="PROPERTY_REGION"]');

        if (zoneInp) {

            ZonePopup = showpPopup('ZONE_POPUP', zoneInp);

            console.log(zoneInp);

            zoneInp.setAttribute('autocomplete', 'off');            

            zoneInp.addEventListener("focus", function() {
                BX.ajax.post(
                    '/local/ajax/getFilterZones.php', {},
                    BX.delegate(
                        function(result) {
                            ZonePopup.adjustPosition();
                            ZonePopup.resizeOverlay();
                            ZonePopup.setContent(result);
                            if (ZonePopup) {
                                ZonePopup.show();
                            }
                        },
                        this
                    )
                );

                streetInp.addEventListener("focus", () => {

                    if (StreetPopup) {
                        StreetPopup.adjustPosition();
                        StreetPopup.resizeOverlay();
                        StreetPopup.show();
                    }
                });
            });

            zoneInp.addEventListener("keyup", event => {
                zoneVal = event.target.value;
                BX.ajax.post(
                    '/local/ajax/getFilterZones.php?zone=' + zoneVal, {},
                    BX.delegate(
                        function(result) {
                            ZonePopup.adjustPosition();
                            ZonePopup.resizeOverlay();
                            ZonePopup.setContent(result);
                            if (ZonePopup) {
                                ZonePopup.show();
                            }
                        },
                        this
                    )
                );
            });

        }


        StreetPopup = showpPopup('STREET_POPUP', streetInp);
        CitiesPopup = showpPopup('CITY_POPUP', cityInp);

        if (streetInp) {
            
            streetInp.setAttribute('autocomplete', 'off');

            streetInp.addEventListener("keyup", event => {
                event.stopImmediatePropagation();
                let zoneCodes = [];
                let squareItem = $(zone).closest('.main-ui-control-entity.main-ui-control').find('.main-ui-square .main-ui-square-item');
                $.each(squareItem, function(index, element){
                    zoneCodes.push(element.textContent);
                });

                let region_id = '';
                let region = $(regionField).find('.main-ui-control.main-ui-select').attr('data-value');
                if(region){
                    region_id = JSON.parse(region).VALUE;
                }
                

                streetVal = event.target.value;
                BX.ajax.post(
                    '/local/ajax/getFilterStreet.php?street=' + streetVal+'&zone='+zoneCodes+'&region_id='+region_id, {},
                    BX.delegate(
                        function(result) {
                            StreetPopup.adjustPosition();
                            StreetPopup.resizeOverlay();
                            StreetPopup.setContent(result);
                            if (StreetPopup) {
                                StreetPopup.show();
                            }
                        },
                        this
                    )
                );
            });
        }

        if (cityInp) {
            
            cityInp.setAttribute('autocomplete', 'off');

            cityInp.addEventListener("keyup", event => {
                cityVal = event.target.value;
                BX.ajax.post(
                    '/local/ajax/getFilterCities.php?city=' + cityVal, {},
                    BX.delegate(
                        function(result) {
                            CitiesPopup.adjustPosition();
                            CitiesPopup.resizeOverlay();
                            CitiesPopup.setContent(result);
                            if (CitiesPopup) {
                                CitiesPopup.show();
                            }
                        },
                        this
                    )
                );
            });
        }



        $('body').on('click', function() {
            if (StreetPopup) {
                StreetPopup.close();
            }
            if (ZonePopup) {
                ZonePopup.close();
            }
            if (CitiesPopup) {
                CitiesPopup.close();
            }
        });

    });


    var zones = [];
    $('body').on('click', '.zone-list .finder-item', function(e) {
        e.stopPropagation();
        let zone = $(this).attr('data-val');

        // if(zones.indexOf(zone)==-1){
        // }        

        let itm = '{&quot;_label&quot;:&quot;' + zone + '&quot;,&quot;_value&quot;:&quot;' + zone + '&quot;}';
        let item = `<div data-item="${itm}" class="main-ui-square"><div class="main-ui-square-item">${zone}</div><div class="main-ui-item-icon main-ui-square-delete"></div></div>`;
        let inpT = $('input[name="PROPERTY_ZONE"]');

        inpT.parent().append(item);        
        zones.push(zone);  
        $('input[name="PROPERTY_ZONE_label"]').val('');
          
        $(this).addClass('active');

        BX.ajax.post(
            '/local/ajax/getFilterStreet.php?zone=' + zones, {},
            BX.delegate(
                function(result) {
                    StreetPopup.adjustPosition();
                    StreetPopup.resizeOverlay();
                    StreetPopup.setContent(result);
                },
                this
            )
        );

    });


    var streets = [];
    $('body').on('click', '.street-list .finder-item', function(e) {
        e.stopPropagation();        

        let itm = '{&quot;_label&quot;:&quot;' + $(this).attr('data-name') + '&quot;,&quot;_value&quot;:&quot;' + $(this).attr('data-val') + '&quot;}';
        let item = `<div data-item="${itm}" class="main-ui-square"><div class="main-ui-square-item">${$(this).attr('data-name')}</div><div class="main-ui-item-icon main-ui-square-delete"></div></div>`;
        $('input[name="PROPERTY_STREET"]').parent().append(item);
        $('input[name="PROPERTY_STREET_label"]').val('');     
        
        $(this).addClass('active');

        // let street = $(this).attr('data-val');
        // if(streets.indexOf(street)==-1){
        //     streets.push(street);
        //}        
    });


    $('body').on('click', '.cities-list .finder-item', function(e) {

        e.stopPropagation();
        let itm = '{&quot;_label&quot;:&quot;' + $(this).attr('data-name') + '&quot;,&quot;_value&quot;:&quot;' + $(this).attr('data-val') + '&quot;}';
        let item = `<div data-item="${itm}" class="main-ui-square"><div class="main-ui-square-item">${$(this).attr('data-name')}</div><div class="main-ui-item-icon main-ui-square-delete"></div></div>`;
        $('input[name="PROPERTY_CITY"]').parent().append(item);
        $('input[name="PROPERTY_CITY_label"]').val('');

    });

});


function showpPopup(popup_id, InpField) {
    Popup = BX.PopupWindowManager.create(popup_id, InpField, {
        content: '<div class="popup-window-content"></div>',
        darkMode: false,
        zIndex: 2200,
        offsetLeft: 40,
        offsetTop: 0,
        angle: true,
        lightShadow: true,
        width: 250,
        height: 220,
        ontentColor: 'white',
        closeIcon: true,
        closeByEsc: true,
        events: {}
    });
    return Popup
}


$(document).ready(function() {

    $(document).on('click', '.realty-image', function(e) {
        e.stopPropagation();

        let gid = $(this).attr('data-elid');
        $.ajax({
            url: '/local/ajax/gallery.php',
            dataType: 'json',
            data: 'id=' + gid,
            success: function(data) {
                $.fancybox.open(data);
            }
        });
    });

    $('.contact-link').click(function() {
        let contactlink = $(this).attr('href');
        BX.SidePanel.Instance.open(contactlink, {
            options: {}
        });
        return false;
    });


    $('.user-link').click(function() {
        let userlink = $(this).attr('href');
        BX.SidePanel.Instance.open(userlink, {
            options: {}
        });
        return false;
    });

});